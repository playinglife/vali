@props(['label' => null])

@php
    use Illuminate\Support\Collection;
    $locales = \App\Http\Middleware\SetLocale::supportedLocalesWithFlags();
    $current = app()->getLocale();
    $currentLocale = Collection::make($locales)->firstWhere('code', $current);
@endphp

<div {{ $attributes->class('language-switcher-host') }}>
@if (count($locales) > 1)
    <details class="language-switcher">
        <summary
            class="ignore-global language-switcher__trigger"
            aria-haspopup="listbox"
            aria-label="{{ $label ?? __('Language') }}"
        >
            <span class="language-switcher__value">
                @if (data_get($currentLocale, 'icon'))
                    <x-icon name="{{ $currentLocale['icon'] }}" class="language-switcher__flag" aria-hidden="true" />
                @endif
                {{ data_get($currentLocale, 'name', $current) }}
            </span>
            <span class="language-switcher__chevron" aria-hidden="true"></span>
        </summary>

        <ul
            id="language-switcher-listbox"
            class="language-switcher__list"
            role="listbox"
        >
            @foreach ($locales as $locale)
                <li role="presentation">
                    <form
                        method="post"
                        action="{{ route('locale.update') }}"
                        class="language-switcher__option-form"
                    >
                        @csrf
                        <input type="hidden" name="locale" value="{{ $locale['code'] }}" />
                        <button
                            type="submit"
                            class="ignore-global language-switcher__option @if ($current === $locale['code']) language-switcher__option--active @endif"
                            role="option"
                            aria-selected="{{ $current === $locale['code'] ? 'true' : 'false' }}"
                        >
                            @if ($locale['icon'])
                                <x-icon name="{{ $locale['icon'] }}" class="language-switcher__flag" aria-hidden="true" />
                            @endif
                            {{ $locale['name'] }}
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </details>

    @once
        <style>
            .language-switcher {
                position: relative;
                display: flex;
                align-items: center;
                box-sizing: border-box;
            }

            .language-switcher > summary {
                list-style: none;
            }

            .language-switcher > summary::-webkit-details-marker {
                display: none;
            }

            .language-switcher__trigger {
                display: flex;
                align-items: center;
                gap: var(--padding-small);
                width: 100%;
                box-sizing: border-box;
                appearance: none;
                font-family: var(--font-family-one);
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                color: var(--color-text-dark);
                background-color: var(--color-background-light);
                border: 1px solid var(--color-border);
                backdrop-filter: blur(10px);
                border-radius: var(--border-radius-small);
                padding: var(--padding-tiny) var(--padding-small);
                cursor: pointer;
            }

            .language-switcher__trigger:focus {
                outline: 1px solid var(--color-action);
                outline-offset: 2px;
            }

            .language-switcher__value {
                display: inline-flex;
                align-items: center;
                gap: 0.35em;
            }

            .language-switcher__flag {
                width: 1.8em;
                flex-shrink: 0;
                border-radius: 1px;
                object-fit: cover;
                margin-right: var(--padding-small);
            }

            .language-switcher__chevron {
                display: inline-block;
                width: 0.75em;
                height: 0.75em;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23546A6F' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: center;
                background-size: contain;
            }

            .language-switcher__list {
                position: absolute;
                top: calc(100% + 0.1rem);
                right: 0;
                left: auto;
                width: max-content;
                min-width: 100%;
                margin: 0;
                padding: 0;
                list-style: none;
                z-index: 200;
                box-sizing: border-box;
                background-color: var(--color-background-transparent-dark);
                backdrop-filter: blur(10px);
                border: 1px solid var(--color-border);
                border-radius: var(--border-radius-small);
                box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.35);
                overflow-wrap: break-word;
            }

            .language-switcher__option-form {
                margin: 0;
                padding: 0;
            }

            .language-switcher__option {
                display: flex;
                align-items: center;
                gap: 0em;
                width: 100%;
                text-align: left;
                font-family: var(--font-family-one);
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                color: var(--color-text-dark);
                background: var(--color-background-light);
                backdrop-filter: blur(10px);
                border: none;
                padding: var(--padding-tiny) var(--padding-small);
                cursor: pointer;
            }

            .language-switcher__option:hover,
            .language-switcher__option:focus {
                background-color: var(--color-interact-1);
                backdrop-filter: blur(10px);
                outline: none;
            }

            .language-switcher__option--active {
                color: var(--color-action);
            }
        </style>

        <script>
            document.addEventListener('click', function (event) {
                const switchers = document.querySelectorAll('.language-switcher[open]');

                switchers.forEach(function (switcher) {
                    if (!switcher.contains(event.target)) {
                        switcher.removeAttribute('open');
                    }
                });
            });
        </script>
    @endonce
@endif
</div>
