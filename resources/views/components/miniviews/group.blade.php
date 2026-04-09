<div class="root-product-detail__meta">
    {{ $slot }}
</div>


@once
    <style lang="scss" scoped>
    .root-product-detail__meta {
        margin-top: auto;
        padding-top: var(--padding-small);
        padding-bottom: var(--padding-small);
        border-top: 1px solid color-mix(in srgb, var(--color-border) 20%, transparent);
    }
    </style>
@endonce