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

// check if product has at least one variant
    $hasVariants = $product->Variants()->exists();
    if ($hasVariants) {
        $pricing = \App\Support\VariantPricing::forVariantId($product->DefaultVariant->id);
        $price = $pricing['price'];
        $discount_price = $pricing['discount_price'];
        $showCompare = $discount_price !== null && $discount_price < $price;
        $inStock = $product->resolvedStockQuantity() > 0;
        $showBadges = $product->is_featured || ! $inStock;
        $excerpt = $product->localizedShortDescription()
            ?? \Illuminate\Support\Str::limit(strip_tags((string) ($product->localizedDescription() ?? '')), 120);
    }else{
        $price = (float) $product->price;
        $list = $product->listPriceBeforeDiscount();
        $showCompare = $list !== null && $list > $price;
        $inStock = $product->resolvedStockQuantity() > 0;
        $showBadges = $product->is_featured || ! $inStock;
        $excerpt = $product->localizedShortDescription()
            ?? \Illuminate\Support\Str::limit(strip_tags((string) ($product->localizedDescription() ?? '')), 120);
    }

    $transferData = [
        'product' => $product,
    ];

    $productOptions = $product->groupedOptions();
@endphp

<article class="root-product-card" data-product-id="{{ $product->id }}">
    <script type="application/json" class="product-card-json">
        @json($transferData)
    </script>

    @if ($href)
        <a href="{{ $href }}" class="root-product-card__media root-product-card__media-link" tabindex="-1" aria-hidden="true">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="root-product-card__img" loading="lazy" decoding="async" width="640" height="960" />
        </a>
    @else
        <div class="root-product-card__media" aria-hidden="true"><img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="root-product-card__img" loading="lazy" decoding="async" width="640" height="960" /></div>
    @endif

    <!-- PRODUCT OPTIONS THAT SHOW ON PRODUCTS -->
    @foreach ($productOptions as $option)
        @if ($option->show_on_products)
            <div class="root-product-card__option">
                <span class="root-product-card__option-title">{{ $option->name }}</span>
                <div class="root-product-card__option-values">
                    @foreach ($option->Values as $value)
                        @if (($option->type ?? 'text') === 'image' && filled($value->image))
                            <img src="{{ $value->image }}" alt="{{ $value->value }}" class="root-product-card__option-value-image" loading="lazy" decoding="async" />
                        @elseif (($option->type ?? 'text') === 'icon' && filled($value->icon))
                            <x-icon name="{{ explode(':', $value->icon)[0] }}" aria-hidden="true" class="medium-icon" style="color: {{ explode(':', $value->icon)[1] }};" />
                        @else
                            <span class="root-product-card__option-value">{{ $value->value }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach


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
                @if ($showCompare)
                    <span class="root-product-card__compare"
                        ><s class="root-product-card__compare-strike">{{ number_format($price, 2) }}&nbsp;{{ __('components.product.currency') }}</s></span
                    >
                @endif
                <span class="root-product-card__price">{{ number_format( $showCompare ? $discount_price : $price, 2) }}&nbsp;{{ __('components.product.currency') }}</span>
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
            padding-top: calc(var(--padding-medium) * 0.66);
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
            font-size: var(--text-size-normal);
            color: var(--color-one);
        }
        .root-product-card__compare {
            font-size: var(--text-size-small);
            opacity: 0.65;
        }
        .root-product-card__compare-strike {
            text-decoration: line-through;
            text-decoration-thickness: from-font;
        }
        .root-product-card__sku {
            font-size: var(--text-size-tiny);
        }
        .root-product-card__option-title {
            font-family: var(--font-family-one);
            font-size: var(--text-size-small);
            font-weight: var(--font-weight-bold);
        }
        .root-product-card__option-values {
            font-size: var(--text-size-tiny);
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            align-items: center;
        }
        .root-product-card__option-value-image {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid color-mix(in srgb, var(--color-border) 60%, transparent);
        }
        .root-product-card__option-value-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.75rem;
            min-height: 1.75rem;
            font-size: var(--text-size-small);
        }
    </style>
@endonce


<!-- SCRIPT -->
@once
    <script>
        const root = document.querySelector('[data-product-id="{{ $product->id }}"]');
        let data = null;


        // Initialization
        function pageInitializationBefore() {
            const scriptEl = root.querySelector('script.product-card-json[type="application/json"]');
            if (scriptEl && scriptEl.textContent) {
                try {
                    data = JSON.parse(scriptEl.textContent.trim());
                } catch (err) {
                    data = null;
                }
            }
            quantityElem = root.querySelector('#product-detail-qty-{{ $product->id }}');
        }
        function pageInitializationAfter() {}

        pageInitializationBefore();
        pageInitializationAfter();
    </script>
@endonce