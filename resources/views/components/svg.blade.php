@props(['name'])

@php
    $stem = is_string($name) ? preg_replace('/^svg-/', '', $name) : '';
    if ($stem === '' || ! preg_match('/^[\w.-]+$/', $stem)) {
        throw new \InvalidArgumentException('svg: invalid name (e.g. logo → svg-logo).');
    }
    $iconName = 'svg-' . $stem;
@endphp

{{-- Blade Icons: use <x-icon>, not <x-dynamic-component> (icons are not class/view path components). --}}
<x-icon name="{{ $iconName }}" {{ $attributes }} />
