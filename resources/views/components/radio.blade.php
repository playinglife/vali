@props(['id' => 'radio-id', 'label' => 'label', 'name' => 'radio-group', 'value' => '', 'checked' => false, 'onChange' => null, 'dimmed' => false, 'displayOnly' => false ])

@php
    $hasCustomLabel = isset($slot) && $slot->isNotEmpty();
@endphp

<div class="root-products__radio {{ $dimmed ? 'dimmed' : '' }} {{ $hasCustomLabel ? 'root-products__radio--icon' : '' }} {{ $displayOnly ? 'root-products__radio--display-only' : '' }}">
  <div class="round">
    <input type="radio" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" @checked($checked) @if ($onChange) onchange="{{ $onChange }}(event, this)" @endif />
    <label class="root-products__radio-label" for="{{ $id }}" @if ($hasCustomLabel) aria-label="{{ $label }}" @endif>
      @if ($hasCustomLabel)
        {{ $slot }}
      @else
        <span class="root-products__checkbox-visual" aria-hidden="true"></span>
        <span class="text-tiny">{{ $label }}</span>
      @endif
    </label>
  </div>
</div>

@include('components.partials.root-products-toggle-styles')
