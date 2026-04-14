@props(['text' => 'text', 'url' => null, 'target' => '_self', 'light' => 'true', 'type' => 'button', 'icon' => null])

@if($url)
    <a href="{{ $url }}" target="{{ $target }}" {{ $attributes->merge(['class' => 'button']) }}>
        @if($icon)
            <x-icon name="{{ $icon }}" class="" aria-hidden="true" />
        @else
            <span class="button__text">{{ $text }}</span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'button']) }}>
        @if($icon)
            <x-icon name="{{ $icon }}" class="" aria-hidden="true" />
        @else
            <span class="button__text">{{ $text }}</span>
        @endif
    </button>
@endif
