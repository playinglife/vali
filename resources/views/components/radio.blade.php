@props([
    'id' => 'radio-id',
    'label' => 'label',
    'name' => 'radio-group',
    'value' => '',
    'checked' => false,
    'onChange' => null,
])

<div class="root-products__radio">
  <div class="round">
    <input
      type="radio"
      id="{{ $id }}"
      name="{{ $name }}"
      value="{{ $value }}"
      @checked($checked)
      @if ($onChange)
      onchange="{{ $onChange }}(event, this)"
      @endif
    />
    <label class="root-products__radio-label" for="{{ $id }}">
      <span class="root-products__checkbox-visual" aria-hidden="true"></span>
      <span class="text-tiny">{{ $label }}</span>
    </label>
  </div>
</div>

@include('components.partials.root-products-toggle-styles')
