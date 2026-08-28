@props(['backgroundImage' => 'none'])
@php
    $hasFooterBg = $backgroundImage && $backgroundImage !== 'none';
@endphp
<div
    class="root-views-layouts-footer grid grid-3 grid-noGutter {{ $hasFooterBg ? 'root-views-layouts-footer--has-bg' : '' }}"
    @if ($hasFooterBg) style="--footer-bg-image: url('{{ $backgroundImage }}');" @endif
>
    <div class="col">
        <div class="grid grid-1 grid-noGutter">
            <div class="col">
                <h4>{{ __('pages.privacy.footer_heading') }}</h4>
            </div>
            <div class="col">
                <div class="grid grid-1 grid-noGutter grid-center column">
                    <div class="col">
                        <a href="{{ route('privacy') }}">{{ __('pages.privacy.footer_link') }}</a>
                    </div>
                    <div class="col">
                        <a href="{{ route('terms') }}">{{ __('pages.terms.footer_link') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="grid grid-1 grid-noGutter">
            <div class="col">
                <h4>{{ __('pages.footer.quicklinks') }}</h4>
            </div>
            <div class="col">
                <div class="grid grid-1 grid-noGutter grid-center column">
                    <div class="col">
                        <a href="{{ route('home') }}">{{ __('pages.footer.home') }}</a>
                    </div>
                    <div class="col">
                        <a href="{{ route('aboutus') }}">{{ __('pages.footer.about') }}</a>
                    </div>
                    <div class="col">
                        <a href="{{ route('contact') }}">{{ __('pages.footer.contact') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="grid grid-1 grid-noGutter">
            <div class="col">
                <h4>{{ __('pages.footer.shop') }}</h4>
            </div>
            <div class="col">
                <div class="grid grid-1 grid-noGutter grid-center column">
                    <div class="col">
                        <a href="{{ route('products.index') }}">{{ __('pages.footer.products') }}</a>
                    </div>
                    <div class="col">
                        <a href="{{ route('custom') }}">{{ __('pages.footer.custom') }}</a>
                    </div>
                    <div class="col">
                        <a href="{{ route('size-chart') }}">{{ __('pages.footer.size_chart') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    <style>
        .root-views-layouts-footer {
            /*position: absolute;
            bottom: 0;
            left: 0;*/
            box-sizing: border-box;
            z-index: 1;
            width: 100%;
            height: auto;
            flex: 0 0 auto;
            margin-top: auto;
            min-height: 4em;
            padding: var(--padding-small);
            padding-bottom: var(--padding-medium);
            text-align: center;
            text-decoration: none;
            background-color: var(--color-background-transparent-dark);
            backdrop-filter: blur(10px);
            border-top: 1px solid var(--color-border);
            align-items: stretch;
            position: relative;
            overflow: visible;

            &.root-views-layouts-footer--has-bg::before {
                content: '';
                position: absolute;
                inset: 0;
                z-index: 0;
                background-image: var(--footer-bg-image);
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                filter: blur(100px);
                transform: scale(1.06);
                pointer-events: none;
            }

            & > .col {
                position: relative;
                z-index: 1;
            }

            & .column {
                display: flex;
                gap: var(--gap-small);
            }
        }
    </style>
@endonce
