@php
    $thumbSrc = $image->image ?? \App\Models\Product::genericProductImageUrl();
    $thumbAlt = $image->alt ?? '';
@endphp
<button type="button" data-reference="product-detail-thumb" class="root-product-detail__thumb" aria-pressed="false" onclick="updateMainImage(event, this)"
    aria-label="{{ $thumbAlt }}"
    data-image-id="{{ $image->id }}" data-thumb-index="{{ $index }}">
    <img src="{{ $thumbSrc }}" alt="{{ $thumbAlt }}" width="80" height="120" loading="lazy" decoding="async" class="root-product-detail__thumb-img" />
</button>
