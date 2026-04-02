@props([
    'product',
    'image' => null,
    'href' => null,
])

@php
    /** @var \App\Models\Product $product */
    $imageUrl = $image;
    if ($imageUrl === null || $imageUrl === '') {
        $imageUrl = $product->firstVariantStorageImageUrl();
    }

    $price = (float) $product->price;
    $list = $product->listPriceBeforeDiscount();
    $showCompare = $list !== null && $list > $price;

    $inStock = $product->isInStock();
    $showBadges = $product->is_featured || ! $inStock;
    $excerpt = $product->localizedShortDescription()
        ?? \Illuminate\Support\Str::limit(strip_tags((string) ($product->localizedDescription() ?? '')), 120);
@endphp

<article class="root-product-card" data-product-id="{{ $product->id }}">
    @if ($href)
        <a href="{{ $href }}" class="root-product-card__media root-product-card__media-link" tabindex="-1" aria-hidden="true"><img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="root-product-card__img" loading="lazy" decoding="async" width="640" height="960" /></a>
    @else
        <div class="root-product-card__media" aria-hidden="true"><img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="root-product-card__img" loading="lazy" decoding="async" width="640" height="960" /></div>
    @endif

    <div class="root-product-card__body">
        @if ($showBadges)
            <!--<div class="root-product-card__badges">
                @if ($product->is_featured)
                    <span class="root-product-card__badge root-product-card__badge--featured">{{ __('components.product.featured') }}</span>
                @endif
                @if (! $inStock)
                    <span class="root-product-card__badge root-product-card__badge--out">{{ __('components.product.out_of_stock') }}</span>
                @endif
            </div>-->
        @endif

        @if ($href)
            <h3 class="root-product-card__title">
                <a href="{{ $href }}" class="root-product-card__title-link">{{ $product->name }}</a>
            </h3>
        @else
            <h3 class="root-product-card__title">{{ $product->name }}</h3>
        @endif

        @if ($excerpt !== '')
            <p class="root-product-card__excerpt">{{ $excerpt }}</p>
        @endif

        <div class="root-product-card__footer">
            <div class="root-product-card__prices" aria-label="{{ __('components.product.price') }}">
                <span class="root-product-card__price">{{ number_format($price, 2) }}&nbsp;{{ __('components.product.currency') }}</span>
                @if ($showCompare)
                    <span class="root-product-card__compare">{{ number_format($list, 2) }}&nbsp;{{ __('components.product.currency') }}</span>
                @endif
            </div>
            <span class="root-product-card__sku">{{ $product->sku }}</span>
        </div>

        {{ $slot }}
    </div>
</article>

@once
    <style>
        .root-product-card {
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            background-color: var(--color-background-transparent-light);
            border-radius: var(--border-radius-small);
            border: 1px solid var(--color-background-transparent-light-border);
            overflow: hidden;
            width: 100%;
            flex: 1 1 auto;
            min-height: 0;
            max-width: 100%;
            padding: var(--padding-medium);
            box-sizing: border-box;
        }
        .root-product-card__media-link {
            text-decoration: none;
            color: inherit;
        }
        .root-product-card__media {
            position: relative;
            display: block;
            aspect-ratio: 2 / 3;
            overflow: hidden;
            line-height: 0;
        }
        .root-product-card__img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .root-product-card__body {
            display: flex;
            flex-direction: column;
            gap: calc(var(--gap-small) * 0.66);
            padding: calc(var(--padding-medium) * 0.66);
            flex: 1;
            color: var(--color-text-dark);
            font-family: var(--font-family-two);
            font-size: calc(0.85rem * 0.66);
            line-height: 1.45;
        }
        .root-product-card__badges {
            display: flex;
            flex-wrap: wrap;
            gap: calc(0.35em * 0.66);
            min-height: calc(1.25em * 0.66);
        }
        .root-product-card__badge {
            font-family: var(--font-family-one);
            font-size: calc(0.65rem * 0.66);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: calc(0.2em * 0.66) calc(0.55em * 0.66);
            border-radius: var(--border-radius-small);
        }
        .root-product-card__badge--featured {
            background: color-mix(in srgb, var(--color-two) 22%, white);
            color: var(--color-text-dark);
        }
        .root-product-card__badge--out {
            background: color-mix(in srgb, var(--color-error) 25%, white);
            color: var(--color-text-dark);
        }
        .root-product-card__title {
            margin: 0;
            font-family: var(--font-family-one);
            font-size: var(--text-size-normal) !important;
            color: var(--color-text-dark);
        }
        /* Beats global `a:not(.button)` in app.scss (same specificity + later load, or tie) */
        .root-product-card a.root-product-card__title-link {
            color: var(--color-text-dark);
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            letter-spacing: inherit;
        }
        .root-product-card a.root-product-card__title-link:hover {
            color: var(--color-action) !important;
        }
        .root-product-card__excerpt {
            margin: 0;
            flex: 1;
            opacity: 0.92;
            font-size: var(--text-size-small);
            font-weight: var(--font-weight-normal);
        }
        .root-product-card__footer {
            display: flex;
            flex-direction: column;
            gap: calc(0.35em * 0.66);
            margin-top: auto;
            padding-top: calc(var(--gap-small) * 0.66);
            border-top: 1px solid color-mix(in srgb, var(--color-border) 40%, transparent);
        }
        .root-product-card__prices {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: calc(0.5em * 0.66);
        }
        .root-product-card__price {
            font-family: var(--font-family-one);
            font-size: calc(1.15rem * 0.66);
            color: var(--color-one);
        }
        .root-product-card__compare {
            font-size: calc(0.85rem * 0.66);
            text-decoration: line-through;
            opacity: 0.65;
        }
        .root-product-card__sku {
            font-size: calc(0.7rem * 0.66);
            letter-spacing: 0.04em;
            opacity: 0.75;
        }
    </style>
@endonce
