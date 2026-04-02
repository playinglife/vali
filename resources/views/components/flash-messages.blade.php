@php
    $raw = session('notify');
    $items = [];
    if (is_array($raw)) {
        if (isset($raw['type'], $raw['message']) && is_string($raw['message'])) {
            $items = [$raw];
        } elseif (array_is_list($raw)) {
            foreach ($raw as $n) {
                if (is_array($n) && isset($n['type'], $n['message']) && is_string($n['message'])) {
                    $items[] = $n;
                }
            }
        }
    }
    $allowedTypes = ['success', 'warning', 'error', 'info'];
    $flashAssertive = false;
    foreach ($items as $i) {
        $t = $i['type'] ?? '';
        if ($t === 'information') {
            $t = 'info';
        }
        if (in_array($t, ['error', 'warning'], true)) {
            $flashAssertive = true;
            break;
        }
    }
@endphp

@if ($items !== [])
    <div
        class="app-flash-region"
        aria-live="{{ $flashAssertive ? 'assertive' : 'polite' }}"
        aria-relevant="additions text"
    >
        @foreach ($items as $item)
            @php
                $typeRaw = $item['type'] ?? 'info';
                if ($typeRaw === 'information') {
                    $typeRaw = 'info';
                }
                $type = in_array($typeRaw, $allowedTypes, true) ? $typeRaw : 'info';
                $isAlert = in_array($type, ['error', 'warning'], true);
            @endphp
            <div
                class="app-flash app-flash--{{ $type }}"
                data-app-flash
                role="{{ $isAlert ? 'alert' : 'status' }}"
            >
                <p class="app-flash__text">{{ $item['message'] }}</p>
                <button type="button" class="app-flash__dismiss" aria-label="{{ __('components.flash.dismiss') }}">
                    ×
                </button>
            </div>
        @endforeach
    </div>
@endif

@once
    <style>
        .app-flash-region {
            position: fixed;
            top: clamp(4.25rem, 8vw, 5.5rem);
            right: clamp(0.75rem, 3vw, 1.5rem);
            left: clamp(0.75rem, 3vw, 1.5rem);
            z-index: 200;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: var(--gap-small);
            pointer-events: none;
            box-sizing: border-box;
        }
        .app-flash {
            pointer-events: auto;
            box-sizing: border-box;
            max-width: 28rem;
            width: 100%;
            margin-left: auto;
            display: flex;
            align-items: flex-start;
            gap: var(--gap-small);
            padding: var(--padding-small) var(--padding-medium);
            border-radius: var(--border-radius-medium);
            border: 1px solid color-mix(in srgb, var(--color-border) 35%, transparent);
            font-family: var(--font-family-two);
            font-size: 0.95rem;
            line-height: 1.45;
            color: var(--color-text-dark);
            box-shadow: 0 0.35rem 1rem color-mix(in srgb, black 12%, transparent);
        }
        .app-flash--success {
            background: color-mix(in srgb, var(--color-success) 55%, var(--color-background));
        }
        .app-flash--warning {
            background: color-mix(in srgb, var(--color-warning) 45%, var(--color-background));
        }
        .app-flash--error {
            background: color-mix(in srgb, var(--color-error) 50%, var(--color-background));
        }
        .app-flash--info {
            background: color-mix(in srgb, var(--color-info) 45%, var(--color-background));
        }
        .app-flash__text {
            margin: 0;
            flex: 1;
            min-width: 0;
        }
        .app-flash__dismiss {
            flex: 0 0 auto;
            margin: 0;
            padding: 0 0.15em;
            border: 0;
            background: transparent;
            color: inherit;
            opacity: 0.75;
            font-family: var(--font-family-one);
            font-size: 1.25rem;
            line-height: 1;
            cursor: pointer;
        }
        .app-flash__dismiss:hover {
            opacity: 1;
        }
        .app-flash__dismiss:focus-visible {
            outline: 2px solid var(--color-one);
            outline-offset: 2px;
        }
    </style>
@endonce

@once
    <script>
        (function () {
            function initAutoDismiss(el) {
                const type = el.classList.contains('app-flash--error') || el.classList.contains('app-flash--warning');
                if (type) {
                    return;
                }
                const ms = 6500;
                window.setTimeout(function () {
                    if (el && el.parentNode) {
                        el.remove();
                    }
                    const region = document.querySelector('.app-flash-region');
                    if (region && region.querySelectorAll('[data-app-flash]').length === 0) {
                        region.remove();
                    }
                }, ms);
            }

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.app-flash__dismiss');
                if (!btn) {
                    return;
                }
                const row = btn.closest('[data-app-flash]');
                if (row && row.parentNode) {
                    row.remove();
                }
                const region = document.querySelector('.app-flash-region');
                if (region && region.querySelectorAll('[data-app-flash]').length === 0) {
                    region.remove();
                }
            });

            document.querySelectorAll('.app-flash-region [data-app-flash]').forEach(function (el) {
                initAutoDismiss(el);
            });
        })();
    </script>
@endonce
