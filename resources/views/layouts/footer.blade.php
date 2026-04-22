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
                <h4>PRIVACY POLICY</h4>
            </div>
            <div class="col">
                <div class="footer__social-row">
                    <a href="/privacy-policy">Read more</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="grid grid-1 grid-noGutter">
            <div class="col">
                <h4>QUICKLINKS</h4>
            </div>
            <div class="col">
                <div class="grid grid-1 grid-noGutter grid-center column">
                    <div class="col">
                        <a href="/">Home</a>
                    </div>
                    <div class="col">
                        <a href="/about">About</a>
                    </div>
                    <div class="col">
                        <a href="/contact">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="grid grid-1 grid-noGutter">
            <div class="col">
                <h4>SOCIALS</h4>
            </div>
            <div class="col">
                <div class="grid grid-3 grid-noGutter grid-center column">
                    <x-svg name="facebook" class="svg-linkedin" />
                    <x-svg name="twitter" class="svg-linkedin" />
                    <x-svg name="youtube" class="svg-linkedin" />
                </div>
            </div>
        </div>
    </div>
</div>

@once
    <style>
        .svg-linkedin {
            width: 1.5em;
            height: 1.5em;
        }

        .root-views-layouts-footer {
            /*position: absolute;
            bottom: 0;
            left: 0;*/
            box-sizing: border-box;
            z-index: 1;
            width: 100%;
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
            overflow: hidden;

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
