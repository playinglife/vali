@props([
    'id' => 'checkbox-id',
    'label' => 'label',
    'value' => null,
])
@php
    $merge = ['id' => $id, 'type' => 'checkbox'];
    if ($value !== null) {
        $merge['value'] = $value;
    }
@endphp
<div {{ $attributes->only('class')->merge(['class' => 'root-products__checkbox']) }}>
  <div class="round">
    <input {{ $attributes->except('class')->merge($merge) }} />
    <label for="{{ $id }}">
      <span class="root-products__checkbox-visual" aria-hidden="true"></span>
      <span class="">{{ $label }}</span>
    </label>
  </div>
</div>

@include('components.partials.root-products-toggle-styles')
