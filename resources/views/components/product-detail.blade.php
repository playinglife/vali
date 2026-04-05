@props(['product', 'image' => null, 'lightboxMaxZoom' => 12, 'lightboxMinZoom' => 1])

@once
    @push('styles')
        @vite(['resources/scss/product-detail.scss'])
    @endpush
@endonce



<!-- PHP -->
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
        $optionValueIdsByOptionId = [];
        foreach ($v->optionValues as $ov) {
            $optionValueIdsByOptionId[$ov->product_option_id] = $ov->id;
        }

        return [
            'id' => $v->id,
            'sku' => $v->sku,
            'price' => round((float) ($v->price ?? $product->price), 2),
            'listPrice' => $before !== null ? round((float) $before, 2) : null,
            'description' => $v->localizedDescription() ?? '',
            'imageUrl' => $v->displayImageUrl(),
            'optionValueIdsByOptionId' => $optionValueIdsByOptionId,
            'brackets' => $brackets->map(fn ($b) => [
                'start' => (int) $b->start_quantity,
                'end' => $b->end_quantity !== null ? (int) $b->end_quantity : null,
                'price' => round((float) $b->price, 2),
            ])->values()->all(),
        ];
    })->values()->all();

    $usedOptionValueIds = $variantsSorted->flatMap(fn ($v) => $v->optionValues->pluck('id'))->unique();
    $optionsConfig = [];
    $optionsForProduct = $product->options->sortBy('sort_order')->values();
    foreach ($optionsForProduct as $opt) {
        $vals = $opt->values
            ->filter(fn ($v) => $usedOptionValueIds->contains($v->id))
            ->sortBy('sort_order')
            ->values();
        if ($vals->isEmpty()) {
            continue;
        }

        $optionsConfig[] = [
            'id' => $opt->id,
            'name' => $opt->name,
            'values' => $vals->map(function ($v) use ($variantsSorted) {
                $imageUrl = null;
                $useImage = false;
                foreach ($variantsSorted as $var) {
                    $pivOv = $var->optionValues->firstWhere('id', $v->id);
                    if (! $pivOv || ! $pivOv->pivot) {
                        continue;
                    }
                    if ($pivOv->pivot->with_image !== null) {
                        $useImage = true;
                        $path = $pivOv->pivot->with_image;
                        if ($path !== '' && $imageUrl === null) {
                            $imageUrl = asset('storage/'.ltrim((string) $path, '/'));
                        }
                    }
                }

                return [
                    'id' => $v->id,
                    'label' => $v->value,
                    'imageUrl' => $imageUrl,
                    'useImage' => $useImage,
                ];
            })->all(),
        ];
    }

    $optionsOrphan = [];
    foreach ($optionsForProduct as $opt) {
        $optionValueIds = $opt->values->pluck('id');
        $anyValueUsedOnVariant = $optionValueIds->intersect($usedOptionValueIds)->isNotEmpty();
        if ($anyValueUsedOnVariant) {
            continue;
        }
        $vals = $opt->values->sortBy('sort_order')->values();
        if ($vals->isEmpty()) {
            continue;
        }
        $optionsOrphan[] = [
            'id' => $opt->id,
            'name' => $opt->name,
            'values' => $vals->map(fn ($v) => [
                'id' => $v->id,
                'label' => $v->value,
            ])->all(),
        ];
    }

    $initialSelectionByOptionId = [];
    if ($variantsSorted->isNotEmpty()) {
        $iv = $activeThumbIndex !== null
            ? $variantsSorted->get($activeThumbIndex)
            : $variantsSorted->first();
        foreach ($iv->optionValues as $ov) {
            $initialSelectionByOptionId[$ov->product_option_id] = $ov->id;
        }
    }

    $optionsUiEnabled = $variantsSorted->isNotEmpty() && $optionsConfig !== [];

    $hasOptionSwatches = false;
    foreach ($optionsConfig as $oc) {
        foreach ($oc['values'] as $v) {
            if (! empty($v['useImage'])) {
                $hasOptionSwatches = true;
                break 2;
            }
        }
    }
    $showProductHeroSwatch = $optionsUiEnabled && $hasOptionSwatches && $variantsSorted->isNotEmpty();
    $productHeroVariantIndex = $variantsSorted->isEmpty()
        ? null
        : ($activeThumbIndex !== null ? $activeThumbIndex : 0);

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
        'optionsUi' => $optionsUiEnabled,
        'options' => $optionsConfig,
        'productHeroImageUrl' => $showProductHeroSwatch ? $imageUrl : null,
        'productHeroVariantIndex' => $showProductHeroSwatch ? $productHeroVariantIndex : null,
    ];
@endphp



<!-- TEMPLATE -->
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
            @if ($optionsUiEnabled)
                <div class="root-product-detail__options" role="group" aria-label="{{ __('components.product.product_options') }}">
                    @if ($showProductHeroSwatch)
                        <div class="root-product-detail__option root-product-detail__option--product-hero">
                            <div
                                class="root-product-detail__product-hero-row"
                                role="group"
                                aria-label="{{ __('components.product.product_photo') }}"
                            >
                                <button
                                    type="button"
                                    class="root-product-detail__option-swatch root-product-detail__option-swatch--active"
                                    data-product-hero-swatch="1"
                                    aria-pressed="true"
                                    aria-label="{{ __('components.product.product_default_photo') }}"
                                >
                                    <img
                                        class="root-product-detail__option-swatch-img"
                                        src="{{ $imageUrl }}"
                                        alt=""
                                        width="72"
                                        height="108"
                                        loading="eager"
                                        decoding="async"
                                    />
                                </button>
                            </div>
                        </div>
                    @endif
                    @foreach ($optionsConfig as $opt)
                        @php
                            $imageVals = [];
                            $selectVals = [];
                            foreach ($opt['values'] as $v) {
                                if (! empty($v['useImage'])) {
                                    $imageVals[] = $v;
                                } else {
                                    $selectVals[] = $v;
                                }
                            }
                            $mixed = $imageVals !== [] && $selectVals !== [];
                            $selId = $initialSelectionByOptionId[$opt['id']] ?? null;
                            $selectIdSet = array_fill_keys(array_map('intval', array_column($selectVals, 'id')), true);
                            $selectHasSelection = $selId !== null && isset($selectIdSet[(int) $selId]);
                        @endphp
                        <div class="root-product-detail__option" data-product-option-block="{{ $opt['id'] }}">
                            <h3 class="root-product-detail__option-heading">{{ $opt['name'] }}</h3>
                            @if ($imageVals !== [] || $selectVals !== [])
                                <div
                                    class="root-product-detail__option-interactive"
                                    data-product-option-interactive="{{ $opt['id'] }}"
                                >
                                    @if ($imageVals !== [])
                                        <div
                                            class="root-product-detail__option-images"
                                            data-product-option-images="{{ $opt['id'] }}"
                                            role="group"
                                            aria-label="{{ $opt['name'] }}"
                                        >
                                            @foreach ($imageVals as $val)
                                                @php
                                                    $isActive = $selId !== null && (int) $selId === (int) $val['id'];
                                                @endphp
                                                <button
                                                    type="button"
                                                    class="root-product-detail__option-swatch {{ $isActive ? 'root-product-detail__option-swatch--active' : '' }}"
                                                    data-option-id="{{ $opt['id'] }}"
                                                    data-option-value-id="{{ $val['id'] }}"
                                                    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                                    aria-label="{{ __('components.product.option_value_label', ['option' => $opt['name'], 'value' => $val['label']]) }}"
                                                >
                                                    @if (! empty($val['imageUrl']))
                                                        <img
                                                            class="root-product-detail__option-swatch-img"
                                                            src="{{ $val['imageUrl'] }}"
                                                            alt=""
                                                            width="72"
                                                            height="108"
                                                            loading="lazy"
                                                            decoding="async"
                                                        />
                                                    @else
                                                        <span class="root-product-detail__option-swatch-label">{{ $val['label'] }}</span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if ($selectVals !== [])
                                        <select
                                            class="root-product-detail__option-select {{ $mixed ? 'root-product-detail__option-select--after-images' : '' }}"
                                            data-product-option-select="{{ $opt['id'] }}"
                                            aria-label="{{ $opt['name'] }}"
                                        >
                                            @if ($mixed)
                                                <option
                                                    value=""
                                                    @selected(! $selectHasSelection)
                                                >
                                                    {{ __('components.product.option_select_placeholder', ['option' => $opt['name']]) }}
                                                </option>
                                            @endif
                                            @foreach ($selectVals as $val)
                                                <option
                                                    value="{{ $val['id'] }}"
                                                    @selected($selectHasSelection && (int) $selId === (int) $val['id'])
                                                >
                                                    {{ $val['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div
                                    class="root-product-detail__option-locked"
                                    hidden
                                    data-product-option-locked="{{ $opt['id'] }}"
                                    data-option-value-id=""
                                >
                                    <span class="root-product-detail__option-locked-text"></span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif ($variantsSorted->isNotEmpty())
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
                    <span
                        class="root-product-detail__compare"
                        @if (! $showCompareDisplay) hidden @endif
                        ><s class="root-product-detail__compare-strike"
                            ><span class="root-product-detail__compare-value">{{ $showCompareDisplay ? number_format($displayList, 2) : '' }}</span
                            >&nbsp;<span class="root-product-detail__compare-currency">{{ __('components.product.currency') }}</span></s
                        ></span
                    >
                    <span class="root-product-detail__price"
                        ><span class="root-product-detail__price-value">{{ number_format($displayPrice, 2) }}</span
                        >&nbsp;<span class="root-product-detail__currency">{{ __('components.product.currency') }}</span></span
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

            @if ($optionsOrphan !== [])
                <section
                    class="root-product-detail__options-extra"
                    aria-label="{{ __('components.product.options_extra_region') }}"
                >
                    <h3 class="root-product-detail__options-extra-heading">{{ __('components.product.options_extra_heading') }}</h3>
                    @foreach ($optionsOrphan as $group)
                        <div class="root-product-detail__options-extra-group" data-product-option-extra="{{ $group['id'] }}">
                            <h4 class="root-product-detail__options-extra-name">{{ $group['name'] }}</h4>
                            <ul class="root-product-detail__options-extra-values">
                                @foreach ($group['values'] as $val)
                                    <li class="root-product-detail__options-extra-value">{{ $val['label'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </section>
            @endif
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



<!-- SCRIPT -->
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

        function variantOptionMapGet(map, optId) {
            if (!map) {
                return null;
            }
            const m = map[optId] != null ? map[optId] : map[String(optId)];
            return m != null ? Number(m) : null;
        }

        function variantIsComplete(cfg, v) {
            const map = v.optionValueIdsByOptionId;
            if (!map || !cfg.options) {
                return false;
            }
            for (let i = 0; i < cfg.options.length; i++) {
                const oid = cfg.options[i].id;
                const m = variantOptionMapGet(map, oid);
                if (!Number.isFinite(m)) {
                    return false;
                }
            }
            return true;
        }

        function matchesPartialVariant(v, partialSelection) {
            const map = v.optionValueIdsByOptionId;
            if (!map) {
                return false;
            }
            const keys = Object.keys(partialSelection);
            for (let k = 0; k < keys.length; k++) {
                const oid = keys[k];
                const sel = partialSelection[oid];
                if (!Number.isFinite(sel)) {
                    continue;
                }
                const m = variantOptionMapGet(map, oid);
                if (m == null || String(m) !== String(sel)) {
                    return false;
                }
            }
            return true;
        }

        function getFilteredCompleteVariants(cfg, partialSelection) {
            return cfg.variants.filter(function (v) {
                return variantIsComplete(cfg, v) && matchesPartialVariant(v, partialSelection);
            });
        }

        function getAvailableValueIdsForOption(cfg, selection, optId) {
            const partial = {};
            cfg.options.forEach(function (opt) {
                if (Number(opt.id) === Number(optId)) {
                    return;
                }
                const s = selection[opt.id] != null ? selection[opt.id] : selection[String(opt.id)];
                if (Number.isFinite(s)) {
                    partial[opt.id] = s;
                }
            });
            const filtered = getFilteredCompleteVariants(cfg, partial);
            const ids = [];
            const seen = {};
            filtered.forEach(function (v) {
                const vid = variantOptionMapGet(v.optionValueIdsByOptionId, optId);
                if (vid != null && !seen[vid]) {
                    seen[vid] = true;
                    ids.push(Number(vid));
                }
            });
            return ids;
        }

        function propagateSelection(cfg, selection) {
            let changed = true;
            while (changed) {
                changed = false;
                cfg.options.forEach(function (opt) {
                    const avail = getAvailableValueIdsForOption(cfg, selection, opt.id);
                    if (avail.length === 1) {
                        const only = avail[0];
                        const cur = selection[opt.id] != null ? selection[opt.id] : selection[String(opt.id)];
                        if (!Number.isFinite(cur) || Number(cur) !== Number(only)) {
                            selection[opt.id] = only;
                            changed = true;
                        }
                    }
                });
            }
        }

        function findOptionValueLabel(cfg, optId, valueId) {
            for (let i = 0; i < cfg.options.length; i++) {
                if (Number(cfg.options[i].id) !== Number(optId)) {
                    continue;
                }
                const vals = cfg.options[i].values;
                if (!vals) {
                    return '';
                }
                for (let j = 0; j < vals.length; j++) {
                    if (Number(vals[j].id) === Number(valueId)) {
                        return String(vals[j].label != null ? vals[j].label : '');
                    }
                }
            }
            return '';
        }

        function applyOptionControlsFromSelection(root, cfg, selection) {
            if (!cfg || !cfg.options) {
                return;
            }
            cfg.options.forEach(function (opt) {
                const interactive = root.querySelector('[data-product-option-interactive="' + opt.id + '"]');
                const locked = root.querySelector('[data-product-option-locked="' + opt.id + '"]');
                if (!interactive || !locked) {
                    return;
                }
                const avail = getAvailableValueIdsForOption(cfg, selection, opt.id);
                const availSet = {};
                avail.forEach(function (id) {
                    availSet[id] = true;
                });
                const imgWrap = interactive.querySelector('[data-product-option-images="' + opt.id + '"]');
                const sel = interactive.querySelector('[data-product-option-select="' + opt.id + '"]');
                const lockedText = locked.querySelector('.root-product-detail__option-locked-text');

                if (avail.length <= 1) {
                    interactive.hidden = true;
                    locked.hidden = false;
                    if (avail.length === 1) {
                        const valId = avail[0];
                        locked.setAttribute('data-option-value-id', String(valId));
                        if (lockedText) {
                            lockedText.textContent = findOptionValueLabel(cfg, opt.id, valId);
                        }
                    } else {
                        locked.setAttribute('data-option-value-id', '');
                        if (lockedText) {
                            lockedText.textContent = '\u2014';
                        }
                    }
                    return;
                }

                interactive.hidden = false;
                locked.hidden = true;
                locked.setAttribute('data-option-value-id', '');

                if (imgWrap) {
                    imgWrap.querySelectorAll('.root-product-detail__option-swatch').forEach(function (btn) {
                        const id = parseInt(btn.getAttribute('data-option-value-id'), 10);
                        const show = availSet[id];
                        btn.hidden = !show;
                        if (!show) {
                            btn.classList.remove('root-product-detail__option-swatch--active');
                            btn.setAttribute('aria-pressed', 'false');
                        }
                    });
                }
                if (sel) {
                    sel.querySelectorAll('option').forEach(function (optEl) {
                        const v = optEl.value;
                        if (v === '') {
                            const hasActiveImg =
                                imgWrap && imgWrap.querySelector('.root-product-detail__option-swatch--active');
                            optEl.hidden = !!(hasActiveImg || sel.value !== '');
                            return;
                        }
                        const id = parseInt(v, 10);
                        optEl.hidden = !availSet[id];
                    });
                }

                const valId = selection[opt.id] != null ? selection[opt.id] : selection[String(opt.id)];
                const valStr = Number.isFinite(valId) ? String(valId) : null;
                if (imgWrap && valStr) {
                    const swatchForVal = availSet[Number(valId)]
                        ? imgWrap.querySelector('.root-product-detail__option-swatch[data-option-value-id="' + valStr + '"]')
                        : null;
                    imgWrap.querySelectorAll('.root-product-detail__option-swatch').forEach(function (btn) {
                        if (btn.hidden) {
                            return;
                        }
                        const on = swatchForVal != null && btn === swatchForVal;
                        btn.classList.toggle('root-product-detail__option-swatch--active', on);
                        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
                    });
                }
                if (sel && valStr) {
                    const optEl = sel.querySelector('option[value="' + valStr + '"]');
                    if (optEl && !optEl.hidden) {
                        sel.value = valStr;
                    } else {
                        sel.value = '';
                    }
                }
            });
        }

        function setOptionControlsFromVariant(root, cfg, variantIndex) {
            const v = cfg.variants[variantIndex];
            if (!v || !v.optionValueIdsByOptionId || !cfg.options) {
                return;
            }
            const selection = {};
            cfg.options.forEach(function (opt) {
                const m = variantOptionMapGet(v.optionValueIdsByOptionId, opt.id);
                if (m != null) {
                    selection[opt.id] = m;
                }
            });
            propagateSelection(cfg, selection);
            applyOptionControlsFromSelection(root, cfg, selection);
        }

        function collectSelectionFromControls(root, cfg) {
            const selection = {};
            if (!cfg || !cfg.options) {
                return selection;
            }
            cfg.options.forEach(function (opt) {
                const locked = root.querySelector('[data-product-option-locked="' + opt.id + '"]');
                if (locked && !locked.hidden) {
                    const raw = locked.getAttribute('data-option-value-id');
                    const n = parseInt(raw, 10);
                    if (Number.isFinite(n)) {
                        selection[opt.id] = n;
                    }
                    return;
                }
                const sel = root.querySelector('[data-product-option-select="' + opt.id + '"]');
                const imgWrap = root.querySelector('[data-product-option-images="' + opt.id + '"]');
                if (imgWrap) {
                    const active = imgWrap.querySelector('.root-product-detail__option-swatch--active');
                    if (active && !active.hidden) {
                        const n = parseInt(active.getAttribute('data-option-value-id'), 10);
                        if (Number.isFinite(n)) {
                            selection[opt.id] = n;
                            return;
                        }
                    }
                }
                if (sel && sel.value !== '') {
                    const n = parseInt(sel.value, 10);
                    if (Number.isFinite(n)) {
                        selection[opt.id] = n;
                    }
                }
            });
            return selection;
        }

        function variantMatchesSelection(v, cfg, selection) {
            const map = v.optionValueIdsByOptionId;
            if (!map || !cfg.options) {
                return false;
            }
            for (let i = 0; i < cfg.options.length; i++) {
                const oid = cfg.options[i].id;
                const sel = selection[oid] != null ? selection[oid] : selection[String(oid)];
                if (!Number.isFinite(sel)) {
                    return false;
                }
                const m = variantOptionMapGet(map, oid);
                if (m == null || String(m) !== String(sel)) {
                    return false;
                }
            }
            return true;
        }

        function findVariantIndexBySelection(cfg, selection) {
            for (let i = 0; i < cfg.variants.length; i++) {
                if (variantMatchesSelection(cfg.variants[i], cfg, selection)) {
                    return i;
                }
            }
            return -1;
        }

        function refreshOptionAvailability(root) {
            const cfg = parseProductDetailConfig(root);
            if (!cfg || !cfg.optionsUi) {
                return;
            }
            const selection = collectSelectionFromControls(root, cfg);
            propagateSelection(cfg, selection);
            applyOptionControlsFromSelection(root, cfg, selection);
            const idx = findVariantIndexBySelection(cfg, selection);
            if (idx < 0) {
                return;
            }
            applyProductDetailVariant(root, idx, { skipOptionControls: true });
            syncProductDetailLightbox(root);
        }

        function initProductDetailOptions(root) {
            const cfg = parseProductDetailConfig(root);
            if (!cfg || !cfg.optionsUi) {
                return;
            }
            root.addEventListener('change', function (e) {
                if (!e.target || !e.target.matches || !e.target.matches('.root-product-detail__option-select')) {
                    return;
                }
                const optId = e.target.getAttribute('data-product-option-select');
                const imgWrap = root.querySelector('[data-product-option-images="' + optId + '"]');
                if (imgWrap) {
                    imgWrap.querySelectorAll('.root-product-detail__option-swatch').forEach(function (btn) {
                        btn.classList.remove('root-product-detail__option-swatch--active');
                        btn.setAttribute('aria-pressed', 'false');
                    });
                }
                refreshOptionAvailability(root);
            });
            root.addEventListener('click', function (e) {
                const heroBtn = e.target.closest('[data-product-hero-swatch]');
                if (heroBtn && root.contains(heroBtn)) {
                    e.preventDefault();
                    const cfgHero = parseProductDetailConfig(root);
                    const hIdx = cfgHero && cfgHero.productHeroVariantIndex;
                    if (cfgHero && hIdx != null && cfgHero.variants && cfgHero.variants[hIdx]) {
                        applyProductDetailVariant(root, hIdx);
                        if (cfgHero.productHeroImageUrl) {
                            const mainImg = root.querySelector('.root-product-detail__img');
                            if (mainImg) {
                                mainImg.removeAttribute('srcset');
                                mainImg.src = cfgHero.productHeroImageUrl;
                            }
                        }
                        syncProductDetailLightbox(root);
                    }
                    return;
                }
                const sw = e.target.closest('.root-product-detail__option-swatch');
                if (!sw || !root.contains(sw)) {
                    return;
                }
                e.preventDefault();
                const wrap = sw.closest('[data-product-option-images]');
                if (!wrap) {
                    return;
                }
                wrap.querySelectorAll('.root-product-detail__option-swatch').forEach(function (btn) {
                    const on = btn === sw;
                    btn.classList.toggle('root-product-detail__option-swatch--active', on);
                    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
                const optId = wrap.getAttribute('data-product-option-images');
                const sel = optId != null ? root.querySelector('[data-product-option-select="' + optId + '"]') : null;
                if (sel) {
                    sel.value = '';
                }
                refreshOptionAvailability(root);
                const swImg = sw.querySelector('.root-product-detail__option-swatch-img');
                if (swImg) {
                    const mainImg = root.querySelector('.root-product-detail__img');
                    if (mainImg) {
                        const url = swImg.currentSrc || swImg.src || swImg.getAttribute('src');
                        if (url) {
                            mainImg.removeAttribute('srcset');
                            mainImg.src = url;
                        }
                    }
                }
                syncProductDetailLightbox(root);
            });
            refreshOptionAvailability(root);
        }

        function updateProductHeroSwatchState(root, cfg, variantIndex) {
            const hero = root.querySelector('[data-product-hero-swatch]');
            if (!hero || !cfg || cfg.productHeroVariantIndex == null) {
                return;
            }
            const on = Number(variantIndex) === Number(cfg.productHeroVariantIndex);
            hero.classList.toggle('root-product-detail__option-swatch--active', on);
            hero.setAttribute('aria-pressed', on ? 'true' : 'false');
        }

        function applyProductDetailVariant(root, variantIndex, opts) {
            opts = opts || {};
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
            const mainImg = root.querySelector('.root-product-detail__img');
            if (mainImg && v.imageUrl) {
                mainImg.src = v.imageUrl;
            }
            if (cfg.optionsUi && cfg.options && v.optionValueIdsByOptionId && !opts.skipOptionControls) {
                setOptionControlsFromVariant(root, cfg, variantIndex);
            }
            updateProductHeroSwatchState(root, cfg, variantIndex);
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
            initProductDetailOptions(root);
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
