@props([
    'id' => 'radio-id',
    'label' => 'label',
    'name' => 'radio-group',
    'value' => '',
    'checked' => false,
])
<div class="root-products__radio">
  <div class="round">
    <input
      type="radio"
      id="{{ $id }}"
      name="{{ $name }}"
      value="{{ $value }}"
      @checked($checked)
    />
    <label for="{{ $id }}">
      <span class="root-products__checkbox-visual" aria-hidden="true"></span>
      <span class="root-products__checkbox-text">{{ $label }}</span>
    </label>
  </div>
</div>

@include('components.partials.root-products-toggle-styles')
