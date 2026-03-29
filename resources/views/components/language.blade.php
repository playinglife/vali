@props([
    'label' => null,
])

@php
    $locales = config('app.supported_locales', []);
    $current = app()->getLocale();
@endphp

@if (count($locales) > 1)
    <form
        method="post"
        action="{{ route('locale.update') }}"
        class="language-switcher"
    >
        @csrf
        <select
            id="locale-select"
            name="locale"
            class="language-switcher__select"
            aria-label="{{ $label ?? __('Language') }}"
            onchange="this.form.submit()"
        >
            @foreach ($locales as $code => $name)
                <option value="{{ $code }}" @selected($current === $code)>{{ $name }}</option>
            @endforeach
        </select>
        <noscript>
            <button type="submit" class="language-switcher__submit">{{ __('Apply') }}</button>
        </noscript>
    </form>

    @once
        <style>
            .language-switcher {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .language-switcher__select {
                appearance: none;
                -webkit-appearance: none;
                font-family: var(--font-family-one);
                font-size: 0.65rem;
                letter-spacing: 0.05em;
                color: var(--color-text);
                background-color: transparent;
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: var(--border-radius-small);
                padding: 0.35em 1.75em 0.35em 0.75em;
                cursor: pointer;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.5em center;
                background-size: 0.65em;
            }
            .language-switcher__select:focus {
                outline: 1px solid var(--color-action);
                outline-offset: 2px;
            }
            .language-switcher__submit {
                font-size: 0.65rem;
                text-decoration: underline;
                color: var(--color-text);
                background: none;
                border: none;
                padding: 0;
                cursor: pointer;
                font-family: inherit;
            }
        </style>
    @endonce
@endif
