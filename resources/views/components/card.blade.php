@props(['title' => 'title', 'text' => 'text', 'image' => 'image', 'imagePosition' => 'imagePosition', 'url' => null, 'color' => 'one'])

@php
    $contentBgKey = match ($color) {
        'one', 'two', 'three', 'four', 'five', 'tree', 'action' => $color,
        default => 'one',
    };
@endphp

<div class="x-components-card flex items-center justify-center bg-cover bg-center w-full h-3/4 rounded-[0.1rem] z-1 bg-white transition-all duration-300 hover:scale-102 0 hover:z-2">
    <div class="x-image flex flex-col items-center justify-between border-0 p-[1.5rem] bg-cover bg-center w-full h-full rounded-[0.1rem] border-[#464646] background-position-{{ $imagePosition }}"
            style="background-image: url('{{ asset($image) }}')">
        {{ $slot }}
        @if($url)
            <x-button text="Find out more" url="{{ $url }}" />
        @endif
    </div>
    <div class="x-content w-full min-h-0 p-[1rem] rounded-[0.1rem]" style="--content-base: var(--color-{{ $contentBgKey }})">
        <div class="x-title-wrap">
            <div class="x-title">{{ $title }}</div>
        </div>
        <div class="x-text-wrap">
            <div class="x-text">{{ $text }}</div>
        </div>
    </div>
</div>



@once
<style>
    .x-components-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 0;
        height: 30em;
        & > .x-image {
            flex: 0 0 66%;
            min-height: 0;
            border-radius: 0.3em 0.3em 0 0;
        }
        & > .x-content {
            --content-base: var(--color-one);
            position: relative;
            isolation: isolate;
            flex: 0 0 34%;
            min-height: 0;
            overflow: hidden;
            color: white;
            font-family: "Lato", sans-serif;
            font-weight: 400;
            font-size: 0.85rem;
            line-height: 1.45;
            border-radius: 0;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            border-radius: 0 0 0.3em 0.3em;
            &::before {
                content: "";
                position: absolute;
                inset: -30%;
                z-index: 0;
                pointer-events: none;
                background:
                    radial-gradient(
                        ellipse 95% 75% at 12% 18%,
                        color-mix(in srgb, var(--content-base) 72%, transparent) 0%,
                        transparent 52%
                    ),
                    radial-gradient(
                        ellipse 85% 70% at 88% 82%,
                        color-mix(in srgb, var(--content-base) 58%, transparent) 0%,
                        transparent 48%
                    ),
                    radial-gradient(
                        ellipse 110% 90% at 48% 42%,
                        color-mix(in srgb, var(--content-base) 42%, transparent) 0%,
                        transparent 58%
                    ),
                    color-mix(in srgb, var(--content-base) 28%, rgb(12, 14, 18));
                filter: blur(36px);
                transform: translateZ(0);
            }
            & > .x-title-wrap {
                position: relative;
                z-index: 1;
                flex: 0 0 50%;
                min-height: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                overflow-y: auto;
            }
            & > .x-title-wrap .x-title {
                font-size: 1.2rem;
                font-weight: 700;
                text-align: center;
            }
            & > .x-text-wrap {
                position: relative;
                z-index: 1;
                flex: 0 0 50%;
                min-height: 0;
                overflow-y: auto;
                display: flex;
                align-items: start;
                justify-content: center;
                width: 100%;
            }
            & > .x-text-wrap .x-text {
                font-family: "Lato", sans-serif;
                font-weight: 400;
                font-size: 0.85rem;
                color: white;
                text-align: center;
                line-height: 1.45;
            }
        }
    }
</style>
@endonce