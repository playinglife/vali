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
    if ($imageUrl === null || $imageUrl === '') {
        $imageUrl =
            'data:image/svg+xml,' .
            rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="640" height="960" viewBox="0 0 640 960"><rect fill="#E7DCC0" width="640" height="960"/><path fill="#97B39E" opacity=".35" d="M0 720 L160 480 L320 600 L480 360 L640 600 L640 960 L0 960 Z"/><circle fill="#546A6F" opacity=".25" cx="320" cy="330" r="72"/></svg>',
            );
    }

    $price = (float) $product->price;
    $list = $product->listPriceBeforeDiscount();
    $showCompare = $list !== null && $list > $price;

    $inStock = $product->isInStock();
    $showBadges = $product->is_featured || ! $inStock;
    $excerpt = $product->short_description ?? \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 120);
@endphp

<article class="root-product-card" data-product-id="{{ $product->id }}">
    @if ($href)
        <a href="{{ $href }}" class="root-product-card__media root-product-card__media-link" tabindex="-1" aria-hidden="true"><img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="root-product-card__img" loading="lazy" decoding="async" width="640" height="960" /></a>
    @else
        <div class="root-product-card__media" aria-hidden="true"><img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="root-product-card__img" loading="lazy" decoding="async" width="640" height="960" /></div>
    @endif

    <div class="root-product-card__body">
        @if ($showBadges)
            <div class="root-product-card__badges">
                @if ($product->is_featured)
                    <span class="root-product-card__badge root-product-card__badge--featured">{{ __('components.product.featured') }}</span>
                @endif
                @if (! $inStock)
                    <span class="root-product-card__badge root-product-card__badge--out">{{ __('components.product.out_of_stock') }}</span>
                @endif
            </div>
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
            --product-scale: 0.66;
            --product-card-bg: #fff;
            --product-card-border: var(--color-border);
            --product-card-shadow: rgba(64, 76, 89, 0.12);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            max-width: calc(22rem * var(--product-scale));
            background: var(--product-card-bg);
            border: 1px solid var(--product-card-border);
            border-radius: var(--border-radius-medium);
            overflow: hidden;
            box-shadow: 0 calc(4px * var(--product-scale)) calc(14px * var(--product-scale)) var(--product-card-shadow);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .root-product-card:hover {
            box-shadow: 0 calc(8px * var(--product-scale)) calc(24px * var(--product-scale)) rgba(64, 76, 89, 0.18);
            transform: translateY(calc(-2px * var(--product-scale)));
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
            background: var(--color-background);
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
            gap: calc(var(--gap-small) * var(--product-scale));
            padding: calc(var(--padding-medium) * var(--product-scale));
            flex: 1;
            color: var(--color-text-dark);
            font-family: var(--font-family-two);
            font-size: calc(0.85rem * var(--product-scale));
            line-height: 1.45;
        }
        .root-product-card__badges {
            display: flex;
            flex-wrap: wrap;
            gap: calc(0.35em * var(--product-scale));
            min-height: calc(1.25em * var(--product-scale));
        }
        .root-product-card__badge {
            font-family: var(--font-family-one);
            font-size: calc(0.65rem * var(--product-scale));
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: calc(0.2em * var(--product-scale)) calc(0.55em * var(--product-scale));
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
            font-size: calc(1rem * var(--product-scale));
            font-weight: 700;
            line-height: 1.25;
            color: var(--color-text-dark);
        }
        .root-product-card__title-link {
            color: var(--color-text-dark) !important;
            text-decoration: none;
        }
        .root-product-card__title-link:hover {
            color: var(--color-action) !important;
        }
        .root-product-card__excerpt {
            margin: 0;
            flex: 1;
            opacity: 0.92;
            font-size: calc(2em * var(--product-scale));
        }
        .root-product-card__footer {
            display: flex;
            flex-direction: column;
            gap: calc(0.35em * var(--product-scale));
            margin-top: auto;
            padding-top: calc(var(--gap-small) * var(--product-scale));
            border-top: 1px solid color-mix(in srgb, var(--color-border) 40%, transparent);
        }
        .root-product-card__prices {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: calc(0.5em * var(--product-scale));
        }
        .root-product-card__price {
            font-family: var(--font-family-one);
            font-size: calc(1.15rem * var(--product-scale));
            color: var(--color-one);
        }
        .root-product-card__compare {
            font-size: calc(0.85rem * var(--product-scale));
            text-decoration: line-through;
            opacity: 0.65;
        }
        .root-product-card__sku {
            font-size: calc(0.7rem * var(--product-scale));
            letter-spacing: 0.04em;
            opacity: 0.75;
        }
    </style>
@endonce
