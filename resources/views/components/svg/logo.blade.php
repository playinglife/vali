@props(['mode' => 'light'])

<x-svg-logo {{ $attributes->merge(['class' => 'logo-svg logo-' . $mode]) }} role="img" aria-label="{{ __('Logo') }}" />

@once
    <style>
        .logo-svg {
            shape-rendering: geometricPrecision;
            width: 3.5em;
            height: 3.5em;
        }

        .logo-svg.logo-light {
            --logo-ring-stroke: #fff;
            --logo-disc-fill: #fff;
            --logo-motif-fill: #000;
        }

        .logo-svg.logo-dark {
            --logo-ring-stroke: #000;
            --logo-disc-fill: #000;
            --logo-motif-fill: #fff;
        }

        .logo-ring {
            fill: none;
            stroke: var(--logo-ring-stroke, currentColor);
            stroke-width: 1068.44;
            stroke-miterlimit: 2.61313;
        }

        .logo-disc {
            fill: var(--logo-disc-fill, currentColor);
        }

        .logo-motif {
            fill: var(--logo-motif-fill, #fbfbfb);
        }
    </style>
@endonce
