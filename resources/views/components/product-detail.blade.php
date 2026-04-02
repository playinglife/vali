@props([
    'product',
    'image' => null,
    'lightboxMaxZoom' => 12,
    'lightboxMinZoom' => 1,
])

@php
    /** @var \App\Models\Product $product */
    $lightboxMinZoom = max(0.05, min(50.0, (float) $lightboxMinZoom));
    $lightboxMaxZoom = max(1.0, min(50.0, (float) $lightboxMaxZoom));
    if ($lightboxMinZoom > $lightboxMaxZoom) {
        $lightboxMinZoom = $lightboxMaxZoom;
    }
    $imageUrl = $image;
    if ($imageUrl === null || $imageUrl === '') {
        $imageUrl = $product->firstVariantStorageImageUrl();
    }

    $price = (float) $product->price;
    $list = $product->listPriceBeforeDiscount();
    $inStock = $product->isInStock();
    $categoryNames = $product->categories->pluck('name')->filter()->values();

    $variantsSorted = $product->variants->sortBy('id')->values();
    $thumbUrls = $variantsSorted->map(fn ($v) => $v->displayImageUrl());
    $firstMatchIndex = $thumbUrls->search(fn ($url) => $url === $imageUrl);
    $activeThumbIndex = $firstMatchIndex !== false ? $firstMatchIndex : null;

    $productBrackets = $product->priceBrackets;
    if ($variantsSorted->isNotEmpty()) {
        $initialVariant = $activeThumbIndex !== null
            ? $variantsSorted->get($activeThumbIndex)
            : $variantsSorted->first();
        $displaySku = $initialVariant->sku;
        $displayPrice = (float) ($initialVariant->price ?? $product->price);
        $displayList = $initialVariant->listPriceBeforeDiscount();
        $bracketsForDisplay = $initialVariant->priceBrackets->isNotEmpty()
            ? $initialVariant->priceBrackets
            : $productBrackets;
        $variantDescriptionDisplay = $initialVariant->localizedDescription();
    } else {
        $displaySku = $product->sku;
        $displayPrice = $price;
        $displayList = $list;
        $bracketsForDisplay = $productBrackets;
        $variantDescriptionDisplay = null;
    }
    $showCompareDisplay = $displayList !== null && $displayList > $displayPrice;

    $variantDetailPayload = $variantsSorted->map(function ($v) use ($product, $productBrackets) {
        $brackets = $v->priceBrackets->isNotEmpty() ? $v->priceBrackets : $productBrackets;
        $before = $v->listPriceBeforeDiscount();

        return [
            'id' => $v->id,
            'sku' => $v->sku,
            'price' => round((float) ($v->price ?? $product->price), 2),
            'listPrice' => $before !== null ? round((float) $before, 2) : null,
            'description' => $v->localizedDescription() ?? '',
            'brackets' => $brackets->map(fn ($b) => [
                'start' => (int) $b->start_quantity,
                'end' => $b->end_quantity !== null ? (int) $b->end_quantity : null,
                'price' => round((float) $b->price, 2),
            ])->values()->all(),
        ];
    })->values()->all();

    $detailI18n = [
        'currency' => __('components.product.currency'),
        'qtyFromTo' => __('components.product.qty_from_to', ['start' => ':start', 'end' => ':end']),
        'qtyAndUp' => __('components.product.qty_and_up', ['start' => ':start']),
        'qtyPlusOpen' => __('components.product.qty_plus_open'),
    ];

    $productDetailConfig = [
        'hasVariants' => $variantsSorted->isNotEmpty(),
        'variants' => $variantDetailPayload,
        'detailI18n' => $detailI18n,
    ];
@endphp

<div class="root-product-detail">
    {{-- JSON in a script tag avoids HTML-attribute encoding issues that break JSON.parse on variant click --}}
    <script type="application/json" class="product-detail-json">
@json($productDetailConfig)
    </script>
    <div class="root-product-detail__grid">
        <div class="root-product-detail__media-col">
            <div class="root-product-detail__media">
                <button
                    type="button"
                    class="root-product-detail__media-open"
                    aria-haspopup="dialog"
                    aria-expanded="false"
                    aria-controls="product-detail-lightbox-{{ $product->id }}"
                    aria-label="{{ __('components.product.enlarge_photo') }}"
                >
                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $product->name }}"
                        class="root-product-detail__img"
                        width="640"
                        height="960"
                        loading="eager"
                        decoding="async"
                    />
                </button>
            </div>
            @if ($variantsSorted->isNotEmpty())
                <div class="root-product-detail__thumbs" role="group" aria-label="{{ __('components.product.variant_photos') }}">
                    @foreach ($variantsSorted as $variant)
                        @php
                            $thumbUrl = $variant->displayImageUrl();
                            $isActive = $activeThumbIndex !== null && $loop->index === $activeThumbIndex;
                        @endphp
                        <button
                            type="button"
                            class="root-product-detail__thumb {{ $isActive ? 'root-product-detail__thumb--active' : '' }}"
                            data-main-src="{{ $thumbUrl }}"
                            data-thumb-index="{{ $loop->index }}"
                            aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                            aria-label="{{ __('components.product.show_variant_photo', ['label' => $variant->sku ?: '#' . $variant->id]) }}"
                        >
                            <img
                                src="{{ $thumbUrl }}"
                                alt=""
                                width="80"
                                height="120"
                                loading="lazy"
                                decoding="async"
                                class="root-product-detail__thumb-img"
                            />
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="root-product-detail__info">
            @if ($product->is_featured || ! $inStock)
                <div class="root-product-detail__badges">
                    @if ($product->is_featured)
                        <span class="root-product-detail__badge root-product-detail__badge--featured">{{ __('components.product.featured') }}</span>
                    @endif
                    @if (! $inStock)
                        <span class="root-product-detail__badge root-product-detail__badge--out">{{ __('components.product.out_of_stock') }}</span>
                    @endif
                </div>
            @endif

            <h2 class="root-product-detail__title">{{ $product->name }}</h2>

            @if ($variantsSorted->isNotEmpty())
                <h2
                    class="root-product-detail__variant-description"
                    @if (blank($variantDescriptionDisplay)) hidden @endif
                >{{ $variantDescriptionDisplay }}</h2>
            @endif

            <!--@if ($categoryNames->isNotEmpty())
                <p class="root-product-detail__categories">
                    {{ $categoryNames->implode(', ') }}
                </p>
            @endif-->

            @if (filled($product->localizedShortDescription()))
                <p class="root-product-detail__lead">{{ $product->localizedShortDescription() }}</p>
            @endif

            @if (filled($product->localizedDescription()))
                <div class="root-product-detail__body">{!! nl2br(e($product->localizedDescription())) !!}</div>
            @endif

            <div class="root-product-detail__meta">
                <p class="root-product-detail__sku">
                    <span class="root-product-detail__meta-label">{{ __('components.product.sku') }}</span>
                    <span class="root-product-detail__sku-value">{{ $displaySku }}</span>
                </p>
                <div class="root-product-detail__prices" aria-label="{{ __('components.product.price') }}">
                    <span class="root-product-detail__price"
                        ><span class="root-product-detail__price-value">{{ number_format($displayPrice, 2) }}</span
                        >&nbsp;<span class="root-product-detail__currency">{{ __('components.product.currency') }}</span></span
                    >
                    <span
                        class="root-product-detail__compare"
                        @if (! $showCompareDisplay) hidden @endif
                        ><span class="root-product-detail__compare-value">{{ $showCompareDisplay ? number_format($displayList, 2) : '' }}</span
                        >&nbsp;<span class="root-product-detail__compare-currency">{{ __('components.product.currency') }}</span></span
                    >
                </div>
            </div>

            <div
                class="root-product-detail__brackets-wrap"
                @if ($bracketsForDisplay->isEmpty()) hidden @endif
            >
                <h3 class="root-product-detail__brackets-heading">{{ __('components.product.price_brackets') }}</h3>
                <table class="root-product-detail__brackets-table">
                    <colgroup>
                        <col />
                        <col />
                        <col />
                        <col class="root-product-detail__brackets-col-price" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="root-product-detail__brackets-th-qty" colspan="3" scope="colgroup">
                                {{ __('components.product.quantity_range') }}
                            </th>
                            <th class="root-product-detail__brackets-th-price" scope="col">
                                {{ __('components.product.bracket_price') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="root-product-detail__brackets-body">
                        @foreach ($bracketsForDisplay as $b)
                            <tr
                                class="root-product-detail__brackets-row"
                                data-qty-min="{{ $b->start_quantity }}"
                                data-qty-max="{{ $b->end_quantity !== null ? $b->end_quantity : '' }}"
                            >
                                <td class="root-product-detail__brackets-q-start">{{ $b->start_quantity }}</td>
                                <td class="root-product-detail__brackets-q-sep" aria-hidden="true">
                                    @if ($b->end_quantity !== null)
                                        –
                                    @endif
                                </td>
                                <td class="root-product-detail__brackets-q-end">
                                    @if ($b->end_quantity !== null)
                                        {{ $b->end_quantity }}
                                    @else
                                        {{ __('components.product.qty_plus_open') }}
                                    @endif
                                </td>
                                <td class="root-product-detail__brackets-price">
                                    {{ number_format((float) $b->price, 2) }}&nbsp;{{ __('components.product.currency') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="root-product-detail__order-qty">
                <label class="root-product-detail__order-qty-label" for="product-detail-qty-{{ $product->id }}">
                    {{ __('components.product.order_quantity') }}
                </label>
                <input id="product-detail-qty-{{ $product->id }}" class="root-product-detail__order-qty-input" type="text" name="quantity" 
                    form="product-detail-cart-{{ $product->id }}" 
                    min="1" max="999" maxlength="3" value="1" @disabled(! $inStock) inputmode="numeric" autocomplete="off"
                />
            </div>
        </div>
    </div>

    <div class="root-product-detail__actions">
        <form
            id="product-detail-cart-{{ $product->id }}"
            method="post"
            action="{{ route('cart.add') }}"
            class="root-product-detail__cart-form"
        >
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}" />
            @if ($variantsSorted->isNotEmpty())
                <input
                    type="hidden"
                    name="product_variant_id"
                    value="{{ $initialVariant->id }}"
                    class="root-product-detail__variant-id"
                />
            @endif
            <button
                type="submit"
                class="root-product-detail__add-btn"
                @disabled(! $inStock)
            >
                {{ __('components.product.add_to_cart') }}
            </button>
        </form>
    </div>

    <x-modal-dialog
        id="product-detail-lightbox-{{ $product->id }}"
        mode="zoom"
        :aria-label="$product->name"
        :close-label="__('components.product.close_lightbox')"
        :lightbox-min-zoom="$lightboxMinZoom"
        :lightbox-max-zoom="$lightboxMaxZoom"
    >
        <img
            class="modal-dialog__lightbox-img"
            src=""
            alt=""
            width="1280"
            height="1920"
            decoding="async"
        />
    </x-modal-dialog>
</div>

@once
    <style>
        .root-product-detail {
            box-sizing: border-box;
            width: 100%;
            max-width: 56rem;
            margin: 0 auto;
            padding: var(--padding-large);
            color: var(--color-text-dark);
        }
        .root-product-detail__grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: var(--gap-large);
            align-items: start;
        }
        @media (min-width: 48rem) {
            .root-product-detail__grid {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1.1fr);
                gap: var(--padding-large);
            }
        }
        .root-product-detail__media-col {
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            min-width: 0;
        }
        .root-product-detail__media {
            position: relative;
            width: 100%;
            aspect-ratio: 2 / 3;
            border-radius: var(--border-radius-medium);
            overflow: hidden;
        }
        button.root-product-detail__media-open {
            position: absolute;
            inset: 0;
            display: block;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            border: 0;
            border-radius: inherit;
            background: transparent;
            cursor: zoom-in;
            background-color: none;
            border: none;
        }
        .root-product-detail__media-open:focus-visible {
            outline: 2px solid var(--color-one);
            outline-offset: 2px;
        }
        .root-product-detail__img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            pointer-events: none;
        }
        .root-product-detail__thumbs {
            display: flex;
            flex-wrap: wrap;
            gap: var(--gap-small);
            justify-content: center;
        }
        button.root-product-detail__thumb {
            flex: 0 0 auto;
            margin: 0;
            padding: 0;
            border: 0.1em solid var(--color-background-transparent-light-border);
            border-radius: var(--border-radius-small);
            background: var(--color-background-transparent-light);
            cursor: pointer;
            overflow: hidden;
            width: 4rem;
            aspect-ratio: 2 / 3;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .root-product-detail__thumb:hover {
            border-color: color-mix(in srgb, var(--color-one) 45%, var(--color-border));
        }
        .root-product-detail__thumb--active {
            border-color: var(--color-one);
            box-shadow: 0 0 0 1px color-mix(in srgb, var(--color-one) 35%, transparent);
        }
        .root-product-detail__thumb-img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .root-product-detail__info {
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            min-width: 0;
        }
        .root-product-detail__badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35em;
        }
        .root-product-detail__badge {
            font-family: var(--font-family-one);
            font-size: 0.65rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.2em 0.55em;
            border-radius: var(--border-radius-small);
        }
        .root-product-detail__badge--featured {
            background: color-mix(in srgb, var(--color-two) 22%, white);
            color: var(--color-text-dark);
        }
        .root-product-detail__badge--out {
            background: color-mix(in srgb, var(--color-error) 25%, white);
            color: var(--color-text-dark);
        }
        .root-product-detail__title {
            color: var(--color-one);
        }
        .root-product-detail__variant-description {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.5;
            opacity: 0.92;
            white-space: pre-line;
            color: var(--color-one);
        }
        .root-product-detail__categories {
            margin: 0;
            font-size: 0.8rem;
            opacity: 0.85;
            font-family: var(--font-family-two);
        }
        .root-product-detail__lead {
            margin: 0;
            font-size: 1rem;
            opacity: 0.92;
            line-height: 1.45;
        }
        .root-product-detail__body {
            font-size: 0.9rem;
            line-height: 1.55;
            margin: 0;
        }
        .root-product-detail__meta {
            margin-top: auto;
            padding-top: var(--gap-medium);
            border-top: 1px solid color-mix(in srgb, var(--color-border) 50%, transparent);
        }
        .root-product-detail__sku {
            margin: 0 0 var(--gap-small);
            font-size: 0.65rem;
            letter-spacing: 0.04em;
            opacity: 0.8;
        }
        .root-product-detail__meta-label {
            margin-right: 0.35em;
            text-transform: uppercase;
            font-family: var(--font-family-one);
        }
        .root-product-detail__prices {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 0.5em;
        }
        .root-product-detail__price {
            font-family: var(--font-family-one);
            font-size: 1.35rem;
            color: var(--color-one);
        }
        .root-product-detail__compare {
            font-size: 0.95rem;
            text-decoration: line-through;
            opacity: 0.65;
        }
        .root-product-detail__actions {
            display: flex;
            justify-content: center;
            margin-top: var(--padding-large);
            padding-top: var(--gap-medium);
            border-top: 1px solid color-mix(in srgb, var(--color-border) 40%, transparent);
        }
        .root-product-detail__cart-form {
            margin: 0;
        }
        .root-product-detail__add-btn {

        }
        .root-product-detail__add-btn:hover:not(:disabled) {
        }
        .root-product-detail__add-btn:disabled {
        }
        .root-product-detail__brackets-wrap {
            margin-top: var(--gap-medium);
            padding-top: var(--gap-medium);
            border-top: 1px solid color-mix(in srgb, var(--color-border) 50%, transparent);
        }
        .root-product-detail__brackets-heading {
            margin: 0 0 var(--gap-small);
            font-family: var(--font-family-one);
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--color-text-dark);
            opacity: 0.9;
        }
        .root-product-detail__brackets-table {
            width: 100%;
            table-layout: auto;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        /* Last column grows; quantity columns size to content (never zero-width) */
        .root-product-detail__brackets-col-price {
            width: 100%;
        }
        .root-product-detail__brackets-table th,
        .root-product-detail__brackets-table td {
            padding: 0.35em 0.45em;
            border-bottom: 1px solid color-mix(in srgb, var(--color-border) 35%, transparent);
            vertical-align: baseline;
        }
        .root-product-detail__brackets-table th {
            font-family: var(--font-family-one);
            font-size: 0.65rem;
            text-transform: uppercase;
            opacity: 0.85;
        }
        .root-product-detail__brackets-th-qty {
            text-align: center;
        }
        .root-product-detail__brackets-th-price {
            text-align: right;
        }
        .root-product-detail__brackets-q-start,
        .root-product-detail__brackets-q-sep,
        .root-product-detail__brackets-q-end {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, 'Cascadia Code', 'Consolas', monospace;
            font-size: 0.92em;
            white-space: nowrap;
            width: auto;
            min-width: min-content;
        }
        .root-product-detail__brackets-q-sep {
            padding-left: 0.2em;
            padding-right: 0.2em;
        }
        .root-product-detail__brackets-price {
            text-align: right;
            font-family: var(--font-family-one);
            font-variant-numeric: tabular-nums;
            color: var(--color-one);
        }
        .root-product-detail__brackets-table tbody tr.root-product-detail__brackets-row--active td {
            background: color-mix(in srgb, var(--color-one) 12%, transparent);
        }
        .root-product-detail__order-qty {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: var(--gap-small);
            margin-top: var(--gap-medium);
            padding-top: var(--gap-medium);
            border-top: 1px solid color-mix(in srgb, var(--color-border) 50%, transparent);
        }
        .root-product-detail__order-qty-label {
            font-family: var(--font-family-one);
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            opacity: 0.9;
        }
        .root-product-detail__order-qty-input {
            box-sizing: border-box;
            width: 3.5rem;
            max-width: 100%;
        }
        .root-product-detail__order-qty-input:focus-visible {
            outline: 2px solid var(--color-one);
            outline-offset: 2px;
        }
        .root-product-detail__order-qty-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
@endonce

@once
    <script>
        function resetProductDetailLightboxZoom(lbImg) {
            if (!lbImg) {
                return;
            }
            lbImg.dataset.lbZoom = '1';
            const wrap = lbImg.closest('.modal-dialog__lightbox-zoom-wrap');
            if (wrap) {
                wrap.style.setProperty('--lb-zoom', '1');
            }
        }

        function setProductDetailLightboxZoom(lbImg, z) {
            lbImg.dataset.lbZoom = String(z);
            const wrap = lbImg.closest('.modal-dialog__lightbox-zoom-wrap');
            if (wrap) {
                wrap.style.setProperty('--lb-zoom', String(z));
            }
        }

        function syncProductDetailLightbox(root) {
            const dlg = root.querySelector('.modal-dialog--zoom');
            const mainImg = root.querySelector('.root-product-detail__img');
            const lbImg = root.querySelector('.modal-dialog__lightbox-img');
            if (!dlg || !mainImg || !lbImg || !dlg.open) {
                return;
            }
            lbImg.src = mainImg.currentSrc || mainImg.src;
            lbImg.alt = mainImg.alt;
            resetProductDetailLightboxZoom(lbImg);
        }

        function parseProductDetailConfig(root) {
            const scriptEl = root.querySelector('script.product-detail-json[type="application/json"]');
            if (scriptEl && scriptEl.textContent) {
                try {
                    return JSON.parse(scriptEl.textContent.trim());
                } catch (err) {
                    return null;
                }
            }
            const raw = root.getAttribute('data-product-detail-config');
            if (!raw) {
                return null;
            }
            try {
                return JSON.parse(raw);
            } catch (err) {
                return null;
            }
        }

        function getProductDetailOrderQty(root) {
            const input = root.querySelector('.root-product-detail__order-qty-input');
            if (!input) {
                return NaN;
            }
            const digits = String(input.value).replace(/\D/g, '');
            if (digits === '') {
                return NaN;
            }
            const n = parseInt(digits, 10);
            if (!Number.isFinite(n)) {
                return NaN;
            }
            const min = parseInt(input.getAttribute('min') || '1', 10);
            const max = parseInt(input.getAttribute('max') || '999999', 10);
            if (n < min || n > max) {
                return NaN;
            }
            return n;
        }

        function sanitizeProductDetailOrderQtyInput(input) {
            if (input.disabled) {
                return;
            }
            const min = parseInt(input.getAttribute('min') || '1', 10);
            const max = parseInt(input.getAttribute('max') || '99', 10);
            const digits = String(input.value).replace(/\D/g, '');
            if (digits === '') {
                input.value = '';
                return;
            }
            let n = parseInt(digits, 10);
            if (!Number.isFinite(n)) {
                input.value = '';
                return;
            }
            if (n > max) {
                n = max;
            }
            input.value = String(n);
        }

        function finalizeProductDetailOrderQty(input) {
            if (!input || input.disabled) {
                return;
            }
            const min = parseInt(input.getAttribute('min') || '1', 10);
            const max = parseInt(input.getAttribute('max') || '99', 10);
            const digits = String(input.value).replace(/\D/g, '');
            if (digits === '') {
                input.value = String(min);
                return;
            }
            let n = parseInt(digits, 10);
            if (!Number.isFinite(n)) {
                input.value = String(min);
                return;
            }
            n = Math.max(min, Math.min(max, n));
            input.value = String(n);
        }

        function productDetailOrderQtyKeydown(e) {
            const allowed = [
                'Backspace',
                'Delete',
                'Tab',
                'Escape',
                'Enter',
                'ArrowLeft',
                'ArrowRight',
                'ArrowUp',
                'ArrowDown',
                'Home',
                'End',
            ];
            if (allowed.indexOf(e.key) !== -1) {
                return;
            }
            if (e.ctrlKey || e.metaKey) {
                return;
            }
            if (e.key.length === 1 && /[0-9]/.test(e.key)) {
                return;
            }
            e.preventDefault();
        }

        function bracketRowMatchesQty(qty, tr) {
            const min = parseInt(tr.getAttribute('data-qty-min'), 10);
            if (!Number.isFinite(min)) {
                return false;
            }
            const maxRaw = tr.getAttribute('data-qty-max');
            if (maxRaw === null || maxRaw === '') {
                return qty >= min;
            }
            const max = parseInt(maxRaw, 10);
            if (!Number.isFinite(max)) {
                return qty >= min;
            }
            return qty >= min && qty <= max;
        }

        function updateBracketRowHighlight(root) {
            const tbody = root.querySelector('.root-product-detail__brackets-body');
            if (!tbody) {
                return;
            }
            const rows = tbody.querySelectorAll('tr.root-product-detail__brackets-row');
            const qty = getProductDetailOrderQty(root);
            rows.forEach(function (tr) {
                tr.classList.remove('root-product-detail__brackets-row--active');
            });
            if (!Number.isFinite(qty)) {
                return;
            }
            for (let i = 0; i < rows.length; i++) {
                if (bracketRowMatchesQty(qty, rows[i])) {
                    rows[i].classList.add('root-product-detail__brackets-row--active');
                    break;
                }
            }
        }

        function appendBracketRow(tbody, b, i18n) {
            const tr = document.createElement('tr');
            tr.className = 'root-product-detail__brackets-row';
            tr.setAttribute('data-qty-min', String(b.start));
            tr.setAttribute('data-qty-max', b.end == null ? '' : String(b.end));
            const tdStart = document.createElement('td');
            tdStart.className = 'root-product-detail__brackets-q-start';
            tdStart.textContent = String(b.start);
            const tdSep = document.createElement('td');
            tdSep.className = 'root-product-detail__brackets-q-sep';
            tdSep.setAttribute('aria-hidden', 'true');
            const tdEnd = document.createElement('td');
            tdEnd.className = 'root-product-detail__brackets-q-end';
            const tdPrice = document.createElement('td');
            tdPrice.className = 'root-product-detail__brackets-price';
            if (b.end == null) {
                tdSep.textContent = '';
                tdEnd.textContent = i18n.qtyPlusOpen != null ? String(i18n.qtyPlusOpen) : '+';
            } else {
                tdSep.textContent = '\u2013';
                tdEnd.textContent = String(b.end);
            }
            tdPrice.textContent = Number(b.price).toFixed(2) + '\u00a0' + i18n.currency;
            tr.appendChild(tdStart);
            tr.appendChild(tdSep);
            tr.appendChild(tdEnd);
            tr.appendChild(tdPrice);
            tbody.appendChild(tr);
        }

        function applyProductDetailVariant(root, variantIndex) {
            const cfg = parseProductDetailConfig(root);
            if (!cfg || !cfg.hasVariants || !cfg.variants[variantIndex]) {
                return;
            }
            const v = cfg.variants[variantIndex];
            const i18n = cfg.detailI18n;
            const skuEl = root.querySelector('.root-product-detail__sku-value');
            if (skuEl) {
                skuEl.textContent = v.sku || '';
            }
            const priceVal = root.querySelector('.root-product-detail__price-value');
            if (priceVal) {
                priceVal.textContent = Number(v.price).toFixed(2);
            }
            const cmpWrap = root.querySelector('.root-product-detail__compare');
            const cmpVal = root.querySelector('.root-product-detail__compare-value');
            if (cmpWrap && cmpVal) {
                const show = v.listPrice != null && v.listPrice > v.price;
                cmpWrap.hidden = !show;
                if (show) {
                    cmpVal.textContent = Number(v.listPrice).toFixed(2);
                }
            }
            const wrap = root.querySelector('.root-product-detail__brackets-wrap');
            const tbody = root.querySelector('.root-product-detail__brackets-body');
            if (wrap && tbody) {
                if (!v.brackets || v.brackets.length === 0) {
                    wrap.hidden = true;
                    tbody.innerHTML = '';
                } else {
                    wrap.hidden = false;
                    tbody.innerHTML = '';
                    v.brackets.forEach(function (b) {
                        appendBracketRow(tbody, b, i18n);
                    });
                }
            }
            const varDesc = root.querySelector('.root-product-detail__variant-description');
            if (varDesc) {
                const d = v.description != null ? String(v.description) : '';
                if (d) {
                    varDesc.hidden = false;
                    varDesc.textContent = d;
                } else {
                    varDesc.hidden = true;
                    varDesc.textContent = '';
                }
            }
            const variantIdInput = root.querySelector('.root-product-detail__variant-id');
            if (variantIdInput && v.id != null) {
                variantIdInput.value = String(v.id);
            }
            updateBracketRowHighlight(root);
        }

        function initProductDetailBracketHighlight(root) {
            const input = root.querySelector('.root-product-detail__order-qty-input');
            if (input) {
                input.addEventListener('keydown', productDetailOrderQtyKeydown);
                input.addEventListener('input', function () {
                    sanitizeProductDetailOrderQtyInput(input);
                    updateBracketRowHighlight(root);
                });
                input.addEventListener('change', function () {
                    finalizeProductDetailOrderQty(input);
                    updateBracketRowHighlight(root);
                });
                input.addEventListener('blur', function () {
                    finalizeProductDetailOrderQty(input);
                    updateBracketRowHighlight(root);
                });
                input.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const t = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = String(t).replace(/\D/g, '');
                    input.value = digits;
                    sanitizeProductDetailOrderQtyInput(input);
                    updateBracketRowHighlight(root);
                });
            }
            const form = root.querySelector('.root-product-detail__cart-form');
            if (form && input) {
                form.addEventListener('submit', function () {
                    finalizeProductDetailOrderQty(input);
                });
            }
            updateBracketRowHighlight(root);
        }

        document.querySelectorAll('.root-product-detail').forEach(function (root) {
            initProductDetailBracketHighlight(root);
        });

        document.addEventListener('click', function (e) {
            const openBtn = e.target.closest('.root-product-detail__media-open');
            if (openBtn) {
                const root = openBtn.closest('.root-product-detail');
                const dlg = root && root.querySelector('.modal-dialog--zoom');
                const mainImg = root && root.querySelector('.root-product-detail__img');
                const lbImg = root && root.querySelector('.modal-dialog__lightbox-img');
                if (root && dlg && mainImg && lbImg && typeof dlg.showModal === 'function') {
                    lbImg.src = mainImg.currentSrc || mainImg.src;
                    lbImg.alt = mainImg.alt;
                    resetProductDetailLightboxZoom(lbImg);
                    dlg.showModal();
                    openBtn.setAttribute('aria-expanded', 'true');
                }
                return;
            }

            const closeBtn = e.target.closest('.modal-dialog__close');
            if (closeBtn) {
                const dlg = closeBtn.closest('dialog');
                if (dlg && typeof dlg.close === 'function') {
                    dlg.close();
                }
                return;
            }

            const btn = e.target.closest('.root-product-detail__thumb');
            if (!btn) {
                return;
            }
            const root = btn.closest('.root-product-detail');
            if (!root) {
                return;
            }
            const img = root.querySelector('.root-product-detail__img');
            if (!img) {
                return;
            }
            const url = btn.getAttribute('data-main-src');
            if (url) {
                img.src = url;
            }
            root.querySelectorAll('.root-product-detail__thumb').forEach(function (b) {
                const on = b === btn;
                b.classList.toggle('root-product-detail__thumb--active', on);
                b.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            const thumbIdx = btn.getAttribute('data-thumb-index');
            if (thumbIdx !== null && thumbIdx !== '') {
                applyProductDetailVariant(root, parseInt(thumbIdx, 10));
            }
            syncProductDetailLightbox(root);
        });

        document.querySelectorAll('.root-product-detail .modal-dialog--zoom').forEach(function (dlg) {
            dlg.addEventListener('close', function () {
                const root = dlg.closest('.root-product-detail');
                if (!root) {
                    return;
                }
                const openBtn = root.querySelector('.root-product-detail__media-open');
                if (openBtn) {
                    openBtn.setAttribute('aria-expanded', 'false');
                }
                const lbImg = root.querySelector('.modal-dialog__lightbox-img');
                resetProductDetailLightboxZoom(lbImg);
            });
            dlg.addEventListener('click', function () {
                if (dlg.open && typeof dlg.close === 'function') {
                    dlg.close();
                }
            });
            dlg.addEventListener(
                'wheel',
                function (e) {
                    if (!dlg.open) {
                        return;
                    }
                    const lbImg = dlg.querySelector('.modal-dialog__lightbox-img');
                    if (!lbImg) {
                        return;
                    }
                    e.preventDefault();
                    const minZ = parseFloat(dlg.getAttribute('data-lightbox-min-zoom') || '0.25');
                    const maxZ = parseFloat(dlg.getAttribute('data-lightbox-max-zoom') || '6');
                    let minZoom = Number.isFinite(minZ) && minZ > 0 ? minZ : 0.25;
                    let maxZoom = Number.isFinite(maxZ) && maxZ >= 1 ? maxZ : 6;
                    if (minZoom > maxZoom) {
                        minZoom = maxZoom;
                    }
                    let z = parseFloat(lbImg.dataset.lbZoom || '1');
                    const sensitivity = 0.0012;
                    z += -e.deltaY * sensitivity;
                    z = Math.max(minZoom, Math.min(maxZoom, z));
                    setProductDetailLightboxZoom(lbImg, z);
                },
                { passive: false }
            );
        });
    </script>
@endonce
