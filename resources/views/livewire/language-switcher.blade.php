<div {{ $attributes->class('language-switcher-host') }}>
@if (count($locales) > 1)
    <div class="language-switcher">
        <div
            class="language-switcher__live"
            wire:click.outside="close"
        >
            <button
                type="button"
                class="ignore-global language-switcher__trigger"
                wire:click="toggle"
                aria-haspopup="listbox"
                aria-expanded="{{ $open ? 'true' : 'false' }}"
                aria-controls="language-switcher-listbox"
                aria-label="{{ $label ?? __('Language') }}"
            >
                <span class="language-switcher__value">
                    @if (($currentLocale['icon'] ?? null))
                        <x-icon name="{{ $currentLocale['icon'] }}" class="language-switcher__flag" aria-hidden="true" />
                    @endif
                    {{ $currentLocale['name'] ?? $current }}
                </span>
                <span class="language-switcher__chevron" aria-hidden="true"></span>
            </button>

            @if ($open)
                <ul
                    id="language-switcher-listbox"
                    class="language-switcher__list"
                    role="listbox"
                    tabindex="-1"
                >
                    @foreach ($locales as $locale)
                        <li role="presentation">
                            <button
                                type="button"
                                class="ignore-global language-switcher__option @if ($current === $locale['code']) language-switcher__option--active @endif"
                                role="option"
                                aria-selected="{{ $current === $locale['code'] ? 'true' : 'false' }}"
                                wire:click="selectLocale('{{ $locale['code'] }}')"
                            >
                                @if ($locale['icon'])
                                    <x-icon name="{{ $locale['icon'] }}" class="language-switcher__flag" aria-hidden="true" />
                                @endif
                                {{ $locale['name'] }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="language-switcher__fallback">
            <form method="post" action="{{ route('locale.update') }}" class="language-switcher__fallback-form">
                @csrf
                <select
                    name="locale"
                    aria-label="{{ $label ?? __('Language') }}"
                    onchange="this.form.submit()"
                >
                    @foreach ($locales as $locale)
                        <option value="{{ $locale['code'] }}" @selected($current === $locale['code'])>{{ $locale['name'] }}</option>
                    @endforeach
                </select>
                <noscript>
                    <button type="submit" class="language-switcher__submit">{{ __('Apply') }}</button>
                </noscript>
            </form>
        </div>
    </div>

    @once
        <style>
            html:not(.js) .language-switcher__live {
                display: none !important;
            }
            html.js .language-switcher__fallback {
                display: none !important;
            }
            .language-switcher {
                position: relative;
                display: flex;
                align-items: center;
                gap: var(--padding-small);
                box-sizing: border-box;
            }
            .language-switcher__live {
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: stretch;
            }
            .language-switcher__trigger {
                display: flex;
                align-items: center;
                gap: var(--padding-small);
                width: 100%;
                box-sizing: border-box;
                appearance: none;
                font-family: var(--font-family-one);
                font-size: 0.65rem;
                letter-spacing: 0.05em;
                color: var(--color-text-light);
                background-color: var(--color-background-transparent);
                border: 1px solid var(--color-border);
                backdrop-filter: blur(10px);
                border-radius: var(--border-radius-small);
                padding: var(--padding-small);
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
                width: 1.5em;
                height: 0.85em;
                flex-shrink: 0;
                border-radius: 1px;
                object-fit: cover;
                margin-right: var(--padding-small);
            }
            .language-switcher__chevron {
                display: inline-block;
                width: 0.65em;
                height: 0.65em;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: center;
                background-size: contain;
            }
            .language-switcher__list {
                position: absolute;
                top: calc(100% + 0.1rem);
                left: 0;
                right: 0;
                width: auto;
                margin: 0;
                padding: 0;
                list-style: none;
                z-index: 50;
                box-sizing: border-box;
                background-color: var(--color-background-transparent);
                backdrop-filter: blur(10px);
                border: 1px solid var(--color-border);
                border-radius: var(--border-radius-small);
                box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.35);
                overflow-wrap: break-word;
            }
            .language-switcher__option {
                display: flex;
                align-items: center;
                gap: 0em;
                width: 100%;
                text-align: left;
                font-family: var(--font-family-one);
                font-size: 0.65rem;
                letter-spacing: 0.05em;
                color: var(--color-text-light);
                background: var(--color-background-transparent);
                backdrop-filter: blur(10px);
                border: none;
                padding: var(--padding-small);
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
            .language-switcher__fallback-form {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .language-switcher__fallback-form select {
                appearance: none;
                -webkit-appearance: none;
                font-family: var(--font-family-one);
                font-size: 0.65rem;
                color: var(--color-text-light);
                background-color: transparent;
                border: 1px solid var(--color-border);
                border-radius: var(--border-radius-small);
                padding: 0.35em 1.75em 0.35em 0.75em;
            }
            .language-switcher__submit {
                font-size: 0.65rem;
                text-decoration: underline;
                color: var(--color-text-light);
                background: none;
                border: none;
                padding: 0;
                cursor: pointer;
                font-family: inherit;
            }
        </style>
    @endonce
@endif
</div>
