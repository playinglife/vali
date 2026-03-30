@props(['color' => 'var(--color-border)', 'height' => '3', 'reverse' => false])
@php
    $isReverse = filter_var($reverse, FILTER_VALIDATE_BOOLEAN);
@endphp
<div class="root-components-dividers-divider1">
    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 {{ $height }}" preserveAspectRatio="none">
        <g @if($isReverse) transform="translate(1200, 0) scale(-1, 1)" @endif>
            <path
                d="M1200 {{ $height }} L0 {{ $height / 2 }} L0 0 L1200 0 L1200 {{ $height }} z"
                class="shape-fill"
                style="fill: {{ $color }};"
            ></path>
        </g>
    </svg>
</div>

@once
    <style lang="scss" scoped>
        .root-components-dividers-divider1 {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }
    </style>
@endonce
