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
        & > p:first-child {
            margin-top: 0;
        }
        & > p:last-child {
            margin-bottom: 0;
        }
        & > h4:first-child { 
            margin-top: 0;
        }
    }
    </style>
@endonce