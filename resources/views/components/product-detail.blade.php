@props(['Product', 'image' => null, 'lightboxMaxZoom' => 12, 'lightboxMinZoom' => 1])

@once
    @push('styles')
        @vite(['resources/scss/product-detail.scss'])
    @endpush
@endonce



<!-- PHP -->
@php
    /** @var \App\Models\Product $Product */
    $Product->load(['ProductImages', 'Variants.VariantImages', 'Variants.Values.Option', 'Variants.PriceBrackets', 'PriceBrackets', 'OptionValues.Option']);

    function computeAvailability(array $variants, array $selected): array
{
    $availability = [];

    if ($variants === []) {
        return [];
    }

    // Union of option names from every row (variant rows may be [] if the variant has no Values).
    $optionSet = [];
    foreach ($variants as $variant) {
        foreach (array_keys(array_diff_key($variant, ['id' => true])) as $optName) {
            $optionSet[$optName] = true;
        }
    }
    $options = array_keys($optionSet);
    if ($options === []) {
        return [];
    }

    foreach ($options as $currentOption) {

        // remove current option from filtering
        $filteredSelected = $selected;
        unset($filteredSelected[$currentOption]);

        // Step 1: find valid variants (based on other selections)
        $validVariants = array_filter($variants, function ($variant) use ($filteredSelected) {
            foreach ($filteredSelected as $opt => $val) {
                if (! array_key_exists($opt, $variant) || $variant[$opt] !== $val) {
                    return false;
                }
            }
            return true;
        });

        // Step 2: initialize all values as false (skip variants missing this option)
        foreach ($variants as $variant) {
            if (! array_key_exists($currentOption, $variant)) {
                continue;
            }
            $value = $variant[$currentOption];
            $availability[$currentOption][$value] = false;
        }

        // Step 3: mark valid ones as true
        foreach ($validVariants as $variant) {
            if (! array_key_exists($currentOption, $variant)) {
                continue;
            }
            $value = $variant[$currentOption];
            $availability[$currentOption][$value] = true;
        }
    }

    return $availability;
}

    // Is a product only product or a product with variants
    $hasVariants = $Product->Variants->isNotEmpty();

    if ($hasVariants) {
        // Options and values that appear on at least one product variant (same shape as groupedOptions).
        $valuesByOptionId = collect();
        foreach ($Product->Variants as $variant) {
            foreach ($variant->Values as $value) {
                if ($value->Option === null) {
                    continue;
                }
                $oid = (int) $value->product_option_id;
                if (! $valuesByOptionId->has($oid)) {
                    $valuesByOptionId->put($oid, collect());
                }
                $valuesByOptionId->get($oid)->put((int) $value->id, $value);
            }
        }
        $productOptions = $valuesByOptionId
            ->map(function ($values) {
                $values = $values->sortBy('sort_order')->values();
                $first = $values->first();
                if ($first === null || $first->Option === null) {
                    return null;
                }
                $opt = $first->Option;

                return (object) [
                    'id' => (int) $opt->id,
                    'name' => (string) $opt->name,
                    'show_on_products' => (bool) $opt->show_on_products,
                    'type' => $opt->type?->value ?? 'text',
                    'sort_order' => (int) ($opt->sort_order ?? 0),
                    'Values' => $values,
                ];
            })
            ->filter()
            ->sortBy('sort_order')
            ->values();
    }else{
        $productOptions = $Product->groupedOptions();
    }
    
    // Create index map of variants
    $variantMap = [];
    foreach ($Product->Variants as $variant) {
        $variantMap[$variant->id] = [];
        foreach ($variant->Values as $value) {
            $variantMap[$variant->id][$value->Option->name] = $value->id;
        }
    }
    // First variant selected
    $selectedVariant = $Product->Variants->firstWhere('id', array_key_first($variantMap));

    // Narrow down possible values based on selected variant
    $availableValues = computeAvailability($variantMap, $variantMap[array_key_first($variantMap)]);

    // Set displayed images
    $displayedImages = [];
    if ($hasVariants) {
        $displayedImages = $selectedVariant->VariantImages;        
    }else{
        $displayedImages = $Product->ProductImages;
    }
    $lightboxMinZoom = max(0.05, min(50.0, (float) $lightboxMinZoom));
    $lightboxMaxZoom = max(1.0, min(50.0, (float) $lightboxMaxZoom));
    if ($lightboxMinZoom > $lightboxMaxZoom) {
        $lightboxMinZoom = $lightboxMaxZoom;
    }

    $detailI18n = [
        'currency' => __('components.product.currency'),
        'qtyFromTo' => __('components.product.qty_from_to', ['start' => ':start', 'end' => ':end']),
        'qtyAndUp' => __('components.product.qty_and_up', ['start' => ':start']),
        'qtyPlusOpen' => __('components.product.qty_plus_open'),
    ];

    $transferData = [
        'hasVariants' => $hasVariants,
        'displayedImages' => $displayedImages,
        'Product' => $Product,
        'ProductOptions' => $productOptions,
        'detailI18n' => $detailI18n,
        'selectedVariant' => $selectedVariant,
        'genericProductImageUrl' => \App\Models\Product::genericProductImageUrl(),
        'variantMap' => $variantMap,
        'availableValues' => $availableValues,
    ];

    $productDetailThumbTemplatePlaceholder = (object) ['id' => 0, 'image' => null, 'alt' => ''];

    //dd($availableValues);
@endphp



<!-- TEMPLATE -->
<div data-reference="product-detail" class="root-product-detail">
    <script type="application/json" class="product-detail-json">
        @json($transferData)
    </script>


    <!-- PRODUCT DETAIL -->
    <div class="grid root-product-detail__grid">
        <div class="col">


            <!-- MAIN IMAGE -->
            <div data-reference="product-detail-media" class="root-product-detail__media">
                <button type="button" class="root-product-detail__media-open" aria-haspopup="dialog" aria-expanded="false" aria-controls="product-detail-lightbox-{{ $Product->id }}">
                    <img src="{{ $displayedImages->first()?->image ?? \App\Models\Product::genericProductImageUrl() }}" alt="{{ $Product->name }}" class="root-product-detail__img" width="640" height="960" loading="eager" decoding="async" />
                </button>
            </div>
            <!-- THUMBNAIL IMAGES -->
            <div data-reference="product-detail-thumbs" class="root-product-detail__thumbs" role="group" aria-label="{{ __('components.product.variant_photos') }}">
                @foreach ($displayedImages as $image)
                    @include('components.partials.product-detail-thumb', ['image' => $image, 'index' => $loop->index])
                @endforeach
            </div>
            <template data-reference="product-detail-thumb-template">
                @include('components.partials.product-detail-thumb', ['image' => $productDetailThumbTemplatePlaceholder, 'index' => 0])
            </template>
        </div>


        <!-- PRODUCT INFO -->
        <div class="col">


            <h2 class="dark">{{ $Product->name }}</h2>
            <h4 data-reference="product-detail-variant-description" class="dark">{{ $selectedVariant?->localizedDescription() ?? '' }}</h4>


            <!-- SHORT DESCRIPTION -->
            <x-miniviews.group>
            @if (filled($Product->localizedShortDescription()))
                <p class="text-small">{{ $Product->localizedShortDescription() }}</p>
            @endif

            @if (filled($Product->localizedDescription()))
                <p class="text-small">{!! nl2br(e($Product->localizedDescription())) !!}</p>
            @endif
            </x-miniviews.group>


            <!-- SKU AND PRICES -->
            <x-miniviews.group>
                <!-- BADGES -->
                <!--<div class="root-product-detail__badges">
                    @if (true || $selectedVariant->is_featured)
                        <span class="root-product-detail__badge root-product-detail__badge--featured">{{ __('components.product.featured') }}</span>
                    @endif
                    @if (true || ! $selectedVariant->stock_quantity || $selectedVariant->stock_quantity <= 0)
                        <span class="root-product-detail__badge root-product-detail__badge--out">{{ __('components.product.out_of_stock') }}</span>
                    @endif
                </div>-->


                <!-- SKU -->
                <p class="root-product-detail__sku">
                    <span class="text-tiny">{{ __('components.product.sku') }}</span>
                    <span class="text-tiny">{{ $Product->sku }}</span>
                </p>


                <!-- PRICES -->
                <div class="root-product-detail__prices" aria-label="{{ __('components.product.price') }}">
                    <!-- COMPARE PRICE -->
                    <span data-reference="product-detail-compare-price" class="root-product-detail__compare" 
                        @if ($selectedVariant->discount_type === null || $selectedVariant->discount === null || $selectedVariant->discount == 0) hidden @endif
                        @php
                            $comparePrice = $selectedVariant->discount_type === \App\Enums\DiscountType::Percentage ? $selectedVariant->price * (1 - $selectedVariant->discount / 100) : $selectedVariant->price - $selectedVariant->discount;
                        @endphp
                        <s>
                            <span data-reference="product-detail-compare-price-value">{{ number_format($comparePrice, 2) }}</span>
                            <span data-reference="product-detail-compare-price-currency">{{ __('components.product.currency') }}</span>
                        </s>
                    </span>
                    <span class="root-product-detail__price">
                        <span data-reference="product-detail-price-value">{{ number_format($selectedVariant->price, 2) }}</span>
                        <span>{{ __('components.product.currency') }}</span>
                        </span>
                </div>
            </x-miniviews.panel>


            <!-- PRODUCT OPTIONS -->
            @if ($productOptions->isNotEmpty())
                <div data-reference="product-detail-options" class="root-product-detail__meta">
                    <h4 class="dark label">{{ __('components.product.product_options') }}</h4>
                    <div class="root-product-detail__options-content">
                        @foreach ($productOptions as $option)
                            <fieldset data-reference="product-detail-option-{{ $option->id }}">
                                <legend class="text-tiny">
                                    {{ $option->name }}
                                </legend>
                                @if ($option->type === \App\Enums\ProductOptionType::Icon->value)
                                    @php $className = 'root-products__icon-list'; @endphp
                                @else
                                    @php $className = 'root-products__radio-list'; @endphp
                                @endif
                                <div data-reference="product-detail-option-list" class="{{ $className }}">
                                    @foreach ($option->Values as $val)
                                        @php
                                            $available = $availableValues[$option->name][$val->id] ?? false;
                                        @endphp
                                        @if ($option->type === \App\Enums\ProductOptionType::Image->value)
                                            <img src="{{ $val->image }}" alt="{{ $val->value }}" class="root-product-detail__option-image" />
                                        @elseif ($option->type === \App\Enums\ProductOptionType::Icon->value)
                                            @php
                                                [$iconName, $iconColor] = array_pad(explode(':', $val->icon, 2), 2, '');
                                            @endphp
                                            <x-radio :id="'product-detail-' . $Product->id . '-opt-' . $option->id . '-v-' . $val->id" :name="'product-detail-' . $Product->id . '-opt-' . $option->id"
                                                :value="(string) $val->id" :label="$val->value" :checked="$selectedVariant->Values->pluck('id')->contains($val->id)"
                                                on-change="onOptionChange" :dimmed="! $available">
                                                @if ($iconColor !== '')
                                                    <x-icon name="{{ $iconName }}" aria-hidden="true" class="medium-icon" style="color: {{ $iconColor }};" />
                                                @else
                                                    <x-icon name="{{ $iconName }}" aria-hidden="true" class="medium-icon" />
                                                @endif
                                            </x-radio>
                                        @else
                                        <x-radio :id="'product-detail-' . $Product->id . '-opt-' . $option->id . '-v-' . $val->id" :name="'product-detail-' . $Product->id . '-opt-' . $option->id" 
                                            :value="(string) $val->id" :label="$val->value" :checked="$selectedVariant->Values->pluck('id')->contains($val->id)"
                                            on-change="onOptionChange" :dimmed="! $available" />
                                        @endif
                                    @endforeach
                                </div>
                            </fieldset>
                        @endforeach
                    </div>
                </div>
            @endif


            <!-- Price brackets -->
            @foreach ($Product->Variants as $variant)
            <x-price-brackets :variant="$variant" :price_brackets="$variant->PriceBrackets" :show="$selectedVariant->id == $variant->id" />
            @endforeach


            <!-- ORDER QUANTITY -->
            <div class="root-product-detail__order-qty">
                <label class="root-product-detail__order-qty-label text-normal" for="product-detail-qty-{{ $Product->id }}">
                    {{ __('components.product.order_quantity') }}
                </label>
                <div class="root-product-detail__order-qty-value">
                    <input id="product-detail-qty-{{ $Product->id }}" class="root-product-detail__order-qty-input" type="text" name="quantity" 
                        form="product-detail-cart-{{ $Product->id }}" min="1" max="999" maxlength="3" value="1" @disabled(false) inputmode="numeric" autocomplete="off"
                        onchange="onQuantityChange(event, this)"
                    />
                </div>
            </div>


        </div>
    </div>
   

    <!-- CART ACTIONS -->
    <div class="root-product-detail__actions">
        <form id="product-detail-cart-{{ $Product->id }}" method="post" action="{{ route('cart.add') }}" class="root-product-detail__cart-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $Product->id }}" />
            <input type="hidden" name="product_variant_id" value="{{ $selectedVariant->id }}" class="root-product-detail__variant-id" />
            <button type="submit" class="root-product-detail__add-btn" @disabled(! $selectedVariant->stock_quantity || $selectedVariant->stock_quantity <= 0) >
                {{ __('components.product.add_to_cart') }}
            </button>
        </form>
    </div>


    <!-- PRODUCT LIGHTBOX -->
    <x-modal-dialog
        id="product-detail-lightbox-{{ $Product->id }}"
        mode="zoom"
        :aria-label="$Product->name"
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
        /** Backing values of App\Enums\DiscountType — PHP enums are not available in the browser. */
        const DISCOUNT_TYPE_FIXED = @json(\App\Enums\DiscountType::Fixed->value);
        const DISCOUNT_TYPE_PERCENTAGE = @json(\App\Enums\DiscountType::Percentage->value);

        const root = document.querySelector('[data-reference="product-detail"]');
        let data = null;
        let selectedVariant = null;
        let quantityElem = null;
        let displayedImages = null;
        
        // Initialization
        function pageInitializationBefore() {
            const scriptEl = root.querySelector('script.product-detail-json[type="application/json"]');
            if (scriptEl && scriptEl.textContent) {
                try {
                    data = JSON.parse(scriptEl.textContent.trim());
                    if (data && data.hasVariants && data.selectedVariant && data.Product && Array.isArray(data.Product.Variants)) {
                        const sid = data.selectedVariant.id;
                        selectedVariant = data.Product.Variants.find(function (v) {
                            return v.id == sid;
                        }) || data.selectedVariant;
                    }
                } catch (err) {
                    data = null;
                }
            }
            quantityElem = root.querySelector('#product-detail-qty-{{ $Product->id }}');
            displayedImages = data ? data.displayedImages : null;
        }
        function pageInitializationAfter() {
            // Add event listener to product options content
            /*const optionsContent = root.querySelector('[data-reference="product-detail-options"]');
            if (optionsContent) {
                optionsContent.addEventListener('change', function (e) {
                    const input = e.target;
                    if (input && input.matches('input[type="radio"]')) {
                        onOptionChange(e, input);
                    }
                });
            }*/

            syncVariantThumbHighlight();
            updateVisiblePriceBracket();
            updateVisiblePriceBracketRowHighlight();
            if (data && data.hasVariants) {
                syncProductOptionRadiosAvailabilityDimmed(buildVariantMapFromData(), readSelectedByOptionNameFromDom());
            }
        }

        function syncVariantThumbHighlight() {
            const thumbs = root.querySelectorAll('[data-reference="product-detail-thumb"]');
            const activeKey = selectedVariant ? String(selectedVariant.id) : 'null';
            thumbs.forEach(function (btn) {
                const vid = btn.dataset.variantId;
                const isActive = vid === activeKey;
                btn.classList.toggle('root-product-detail__thumb--active', isActive);
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        }



        // Start
        pageInitializationBefore();


        // Update visible price bracket
        function updateVisiblePriceBracket() {
            const priceBrackets = root.querySelectorAll('.root-product-detail__brackets-wrap[data-variant-price-brackets-id]');
            priceBrackets.forEach(function (priceBrackets) {
                const variantId = parseInt(priceBrackets.dataset.variantPriceBracketsId);
                if (selectedVariant === null || variantId !== selectedVariant.id || (selectedVariant.PriceBrackets && selectedVariant.PriceBrackets.length === 0)) {
                    priceBrackets.hidden = true;
                } else {
                    
                    priceBrackets.hidden = false;
                }
            });
        }

        // Update price bracket row highlight
        function updateVisiblePriceBracketRowHighlight() {
            if (selectedVariant === null) { return; }
            const selectedPriceBracketElem = root.querySelector('.root-product-detail__brackets-wrap[data-variant-price-brackets-id = "' + selectedVariant.id + '"]');
            if (!selectedPriceBracketElem) { return; }
            selectedVariant.PriceBrackets.forEach(function (priceBracket, index) {
                if (quantityElem.value >= priceBracket.start_quantity && (quantityElem.value <= priceBracket.end_quantity || priceBracket.end_quantity === null)) {
                    selectedPriceBracketElem.querySelector('.root-product-detail__brackets-row:nth-child(' + (index + 1) + ')').classList.add('root-product-detail__brackets-row--active');
                } else {
                    selectedPriceBracketElem.querySelector('.root-product-detail__brackets-row:nth-child(' + (index + 1) + ')').classList.remove('root-product-detail__brackets-row--active');
                }
            });
        }


        // Main image zoom in/out
        function resetProductDetailLightboxZoom(lbImgOpt) {
            const lbImg = lbImgOpt || root.querySelector('.modal-dialog__lightbox-img');
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



        // Update product option selection
        function onVariantSelect(variantId) {
            selectedVariant = data.Product.Variants.find(variant => variant.id == variantId);
            if (selectedVariant) {
                const variantDescription = root.querySelector('.root-product-detail__variant-description');
                const variant = root.querySelector(`[data-variant-id="${variantId}"]`);
                if (variant && variantDescription)  {
                    variantDescription.textContent = variant.dataset.variantDescription == 'null' ? '' : variant.dataset.variantDescription;
                }
                const listPrice = root.querySelector('[data-reference="product-detail-price-value"]');
                if (listPrice && selectedVariant) {
                    listPrice.textContent = Number(selectedVariant.price).toFixed(2);
                }

                const compareWrap = root.querySelector('[data-reference="product-detail-compare-price"]');
                if (selectedVariant.discount_type === null || selectedVariant.discount === null || selectedVariant.discount == 0) {
                    const comparePrice = root.querySelector('[data-reference="product-detail-compare-price-value"]');
                    if (comparePrice && selectedVariant) {
                        compareWrap.hidden = true;
                        comparePrice.textContent = Number(selectedVariant.discount_type === DISCOUNT_TYPE_PERCENTAGE ? selectedVariant.price * (1 - selectedVariant.discount / 100) : selectedVariant.price - selectedVariant.discount).toFixed(2);
                    }
                }else{
                    compareWrap.hidden = false;
                }
            }
            syncSelectedvariantWithProductOptions();
            syncVariantThumbHighlight();
            updateVisiblePriceBracket();
            updateVisiblePriceBracketRowHighlight();
        }


        function detailProductOptions() {
            if (!data) {
                return [];
            }
            const fromVariants = data.ProductOptions;
            if (Array.isArray(fromVariants) && fromVariants.length > 0) {
                return fromVariants;
            }
            return data.ProductOptions || [];
        }

        function syncSelectedvariantWithProductOptions() {
            const options = detailProductOptions();
            if (selectedVariant === null) {
                options.forEach(function (option) {
                    const optionSectionElem = root.querySelector(`[data-reference="product-detail-option-${option.id}"]`);
                    if (!optionSectionElem) {
                        return;
                    }
                    const optionRadioListElem = optionSectionElem.querySelector('[data-reference="product-detail-option-list"]');
                    enableRadio(optionRadioListElem, true, false);
                });
            } else {
                const variantOptions = [...new Set(selectedVariant.Values.map(value => value.Option.id))];
                options.forEach(function (option) {
                    const optionSectionElem = root.querySelector(`[data-reference="product-detail-option-${option.id}"]`);
                    if (!optionSectionElem) {
                        return;
                    }
                    const optionRadioListElem = optionSectionElem.querySelector('[data-reference="product-detail-option-list"]');
                    if (variantOptions.includes(option.id)) {
                        const variantValue = selectedVariant.Values.find(value => value.Option.id == option.id);
                        if (variantValue) {
                            enableRadio(optionRadioListElem, true, variantValue.id);
                        }
                    } else {
                        enableRadio(optionRadioListElem, false, false);
                    }
                });
            }
        }

        function enableRadio(element, enable, select) {
            if (!element) {
                return;
            }
            element.querySelectorAll('input[type="radio"]').forEach(function (radio) {
                if (enable) {
                    if (select !== false && select !== null && select !== undefined) {
                        radio.checked = parseInt(radio.value, 10) === parseInt(select, 10);
                    }
                } else {
                    radio.checked = false;
                }
            });
        }


        // Update quantity change
        function onQuantityChange(event, element) {
            updateVisiblePriceBracket()
            updateVisiblePriceBracketRowHighlight();
        }


        // Update main image from variant image
        function updateMainImage(event, element) {
            resetProductDetailLightboxZoom();
            const imageId = element.dataset.imageId;
            const image = data.displayedImages.find(image => image.id == imageId);
            const mainImg = root.querySelector('.root-product-detail__img');
            if (data.hasVariants) {
                const variant = data.Product.Variants.find(variant => variant.VariantImages.some(image => image.id == imageId));
                if (variant) {
                    mainImg.src = variant.image;
                    mainImg.alt = variant.name;
                }
            }else{
                const productImage = data.Product.ProductImages.find(image => image.id == imageId);
                if (productImage) {
                    mainImg.src = productImage.image;
                    mainImg.alt = productImage.alt;
                }
            }
        }

        function cloneProductDetailThumb(image, index) {
            const tmpl = root.querySelector('template[data-reference="product-detail-thumb-template"]');
            if (!tmpl || !tmpl.content || !data) {
                return null;
            }
            const seed = tmpl.content.querySelector('[data-reference="product-detail-thumb"]');
            if (!seed) {
                return null;
            }
            const node = seed.cloneNode(true);
            const alt = image && image.alt != null ? String(image.alt) : '';
            const rawSrc = image && image.image != null ? String(image.image) : '';
            const src = rawSrc !== '' ? rawSrc : String(data.genericProductImageUrl || '');
            node.setAttribute('data-image-id', String(image.id));
            node.setAttribute('data-thumb-index', String(index));
            node.setAttribute('aria-label', alt);
            node.setAttribute('aria-pressed', 'false');
            const img = node.querySelector('img');
            if (img) {
                img.src = src;
                img.alt = alt;
            }
            return node;
        }


        document.querySelectorAll('.root-product-detail .modal-dialog--zoom').forEach(function (dlg) {
            const rootForDlg = dlg.closest('.root-product-detail');
            const openBtn = rootForDlg ? rootForDlg.querySelector('.root-product-detail__media-open') : null;
            const mainImg = rootForDlg ? rootForDlg.querySelector('.root-product-detail__img') : null;
            if (openBtn && mainImg && typeof dlg.showModal === 'function') {
                openBtn.addEventListener('click', function () {
                    const lbImg = dlg.querySelector('.modal-dialog__lightbox-img');
                    if (lbImg) {
                        lbImg.src = mainImg.currentSrc || mainImg.src;
                        lbImg.alt = mainImg.alt || '';
                    }
                    resetProductDetailLightboxZoom(lbImg);
                    dlg.showModal();
                    openBtn.setAttribute('aria-expanded', 'true');
                });
            }

            dlg.addEventListener('close', function () {
                if (!rootForDlg) {
                    return;
                }
                if (openBtn) {
                    openBtn.setAttribute('aria-expanded', 'false');
                }
                const lbImg = dlg.querySelector('.modal-dialog__lightbox-img');
                resetProductDetailLightboxZoom(lbImg);
            });

            const closeBtn = dlg.querySelector('.modal-dialog__close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    dlg.close();
                });
            }

            dlg.addEventListener('click', function (e) {
                if (e.target === dlg) {
                    dlg.close();
                }
            });

            const pane = dlg.querySelector('.modal-dialog__pane');
            if (pane) {
                pane.addEventListener('click', function (e) {
                    if (e.target === pane) {
                        dlg.close();
                    }
                });
            }

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


        /**
         * Mirrors @php computeAvailability($variantMap, $selected) — same steps and comparisons.
         * @param {Record<string, Record<string, number>>} variantMap variant id -> { option name -> value id }
         * @param {Record<string, number>} selected option name -> selected value id
         */
        function computeAvailability(variantMap, selected) {
            const availability = {};
            const rows = Object.keys(variantMap).map(function (vid) {
                return variantMap[vid];
            });
            if (rows.length === 0) {
                return availability;
            }

            const optionSet = {};
            rows.forEach(function (variant) {
                Object.keys(variant).forEach(function (optName) {
                    if (optName === 'id') {
                        return;
                    }
                    optionSet[optName] = true;
                });
            });
            const options = Object.keys(optionSet);
            if (options.length === 0) {
                return availability;
            }

            options.forEach(function (currentOption) {
                const filteredSelected = {};
                Object.keys(selected).forEach(function (k) {
                    if (k !== currentOption) {
                        filteredSelected[k] = selected[k];
                    }
                });

                const validVariants = rows.filter(function (variant) {
                    for (const opt in filteredSelected) {
                        if (!Object.prototype.hasOwnProperty.call(filteredSelected, opt)) {
                            continue;
                        }
                        const val = filteredSelected[opt];
                        if (!Object.prototype.hasOwnProperty.call(variant, opt) || Number(variant[opt]) !== Number(val)) {
                            return false;
                        }
                    }
                    return true;
                });

                rows.forEach(function (variant) {
                    if (!Object.prototype.hasOwnProperty.call(variant, currentOption)) {
                        return;
                    }
                    const value = variant[currentOption];
                    if (!availability[currentOption]) {
                        availability[currentOption] = {};
                    }
                    availability[currentOption][value] = false;
                });

                validVariants.forEach(function (variant) {
                    if (!Object.prototype.hasOwnProperty.call(variant, currentOption)) {
                        return;
                    }
                    const value = variant[currentOption];
                    if (!availability[currentOption]) {
                        availability[currentOption] = {};
                    }
                    availability[currentOption][value] = true;
                });
            });

            return availability;
        }

        function isValueAvailableInMatrix(availability, optionName, valueId) {
            const row = availability[optionName];
            if (!row || typeof row !== 'object') {
                return false;
            }
            if (Object.prototype.hasOwnProperty.call(row, valueId)) {
                return !!row[valueId];
            }
            const asStr = String(valueId);
            if (Object.prototype.hasOwnProperty.call(row, asStr)) {
                return !!row[asStr];
            }
            return false;
        }

        function syncProductOptionRadiosAvailabilityDimmed(variantMap, selectedByOptionName) {
            if (!root || !data || !data.hasVariants) {
                return;
            }
            const availability = computeAvailability(variantMap, selectedByOptionName);
            detailProductOptions().forEach(function (opt) {
                const fieldset = root.querySelector('[data-reference="product-detail-option-' + opt.id + '"]');
                if (!fieldset) {
                    return;
                }
                (opt.Values || []).forEach(function (val) {
                    const available = isValueAvailableInMatrix(availability, opt.name, val.id);
                    const input = fieldset.querySelector('input[type="radio"][value="' + String(val.id) + '"]');
                    if (!input) {
                        return;
                    }
                    const wrap = input.closest('.root-products__radio');
                    if (wrap) {
                        wrap.classList.toggle('dimmed', !available);
                    }
                });
            });
        }

        function findProductOptionValueMeta(valueId) {
            const idNum = parseInt(valueId, 10);
            if (!Number.isFinite(idNum)) {
                return null;
            }
            const options = detailProductOptions();
            for (let i = 0; i < options.length; i++) {
                const opt = options[i];
                const vals = opt.Values || [];
                for (let j = 0; j < vals.length; j++) {
                    if (vals[j].id == idNum) {
                        return { option: opt, value: vals[j] };
                    }
                }
            }
            return null;
        }

        function buildVariantMapFromData() {
            const variantMap = {};
            (data.Product.Variants || []).forEach(function (variant) {
                variantMap[variant.id] = {};
                (variant.Values || []).forEach(function (value) {
                    variantMap[variant.id][value.Option.name] = value.id;
                });
            });
            return variantMap;
        }

        function readSelectedByOptionNameFromDom() {
            const selectedByOptionName = {};
            root.querySelectorAll('[data-reference^="product-detail-option-"]').forEach(function (fieldset) {
                const checked = fieldset.querySelector('input[type="radio"]:checked');
                if (!checked) {
                    return;
                }
                const name = checked.getAttribute('name') || '';
                const m = name.match(/-opt-(\d+)$/);
                if (!m) {
                    return;
                }
                const optionId = parseInt(m[1], 10);
                const valueId = parseInt(checked.value, 10);
                if (!Number.isFinite(optionId) || !Number.isFinite(valueId)) {
                    return;
                }
                const optionMeta = detailProductOptions().find(function (o) {
                    return o.id === optionId;
                });
                if (optionMeta) {
                    selectedByOptionName[optionMeta.name] = valueId;
                }
            });
            return selectedByOptionName;
        }

        function findVariantMatchingSelection(variantMap, selectedByOptionName) {
            let match = null;
            Object.keys(variantMap).forEach(function (vid) {
                if (match) {
                    return;
                }
                const vm = variantMap[vid];
                const vKeys = Object.keys(vm);
                const sKeys = Object.keys(selectedByOptionName);
                if (vKeys.length !== sKeys.length) {
                    return;
                }
                let ok = true;
                for (let i = 0; i < vKeys.length; i++) {
                    const k = vKeys[i];
                    if (vm[k] != selectedByOptionName[k]) {
                        ok = false;
                        break;
                    }
                }
                if (ok) {
                    const idNum = parseInt(vid, 10);
                    match = data.Product.Variants.find(function (v) {
                        return v.id == idNum;
                    });
                }
            });
            return match;
        }

        function applyVariantSelectionToDom(variantMap, variantId) {
            const vm = variantMap[variantId];
            if (!vm) {
                return;
            }
            detailProductOptions().forEach(function (opt) {
                const valueId = vm[opt.name];
                if (valueId === undefined) {
                    return;
                }
                const fieldset = root.querySelector('[data-reference="product-detail-option-' + opt.id + '"]');
                if (!fieldset) {
                    return;
                }
                fieldset.querySelectorAll('input[type="radio"]').forEach(function (radio) {
                    radio.checked = parseInt(radio.value, 10) == valueId;
                });
            });
        }

        /**
         * When the current radios do not match any variant, pick a variant that includes the
         * value the user just chose and keeps as many other selections as possible, then sync radios.
         */
        function coerceSelectionToValidVariant(variantMap, selectedByOptionName, updatedValueId) {
            const meta = findProductOptionValueMeta(updatedValueId);
            const allIds = Object.keys(variantMap);
            if (allIds.length === 0) {
                return null;
            }
            let candidateIds = allIds;
            if (meta) {
                const needName = meta.option.name;
                const needVal = meta.value.id;
                candidateIds = allIds.filter(function (vid) {
                    return variantMap[vid][needName] == needVal;
                });
            }
            if (candidateIds.length === 0) {
                candidateIds = allIds;
            }
            let bestVid = null;
            let bestScore = -1;
            candidateIds.forEach(function (vid) {
                const vm = variantMap[vid];
                let score = 0;
                Object.keys(selectedByOptionName).forEach(function (oname) {
                    if (vm[oname] == selectedByOptionName[oname]) {
                        score++;
                    }
                });
                const idNum = parseInt(vid, 10);
                const bestNum = bestVid === null ? Infinity : parseInt(bestVid, 10);
                if (bestVid === null || score > bestScore || (score === bestScore && idNum < bestNum)) {
                    bestScore = score;
                    bestVid = vid;
                }
            });
            if (bestVid === null) {
                bestVid = candidateIds[0];
            }
            applyVariantSelectionToDom(variantMap, bestVid);
            const idNum = parseInt(bestVid, 10);
            return data.Product.Variants.find(function (v) {
                return v.id == idNum;
            });
        }

        function resolveVariantFromProductOptionRadios(updatedValueId) {
            if (!data.hasVariants) {
                //onVariantSelect(null);
                return;
            }

            const variantMap = buildVariantMapFromData();
            let selectedByOptionName = readSelectedByOptionNameFromDom();
            let match = findVariantMatchingSelection(variantMap, selectedByOptionName);

            if (!match) {
                match = coerceSelectionToValidVariant(variantMap, selectedByOptionName, updatedValueId);
            }
            if (!match && (data.Product.Variants || []).length > 0) {
                const fallback = data.Product.Variants[0];
                applyVariantSelectionToDom(variantMap, String(fallback.id));
                match = fallback;
            }

            const variantIdInput = root.querySelector('.root-product-detail__variant-id');
            if (variantIdInput && match) {
                variantIdInput.value = String(match.id);
            }

            const thumbnailImages = root.querySelector('[data-reference="product-detail-thumbs"]');
            thumbnailImages.innerHTML = '';

            if (match) {
                data.displayedImages = match.VariantImages;
                match.VariantImages.forEach(function (image, index) {
                    const thumb = cloneProductDetailThumb(image, index);
                    if (thumb) {
                        thumbnailImages.appendChild(thumb);
                    }
                });
                const mainImg = root.querySelector('.root-product-detail__img');
                mainImg.style.display = 'block';
                mainImg.src = match.image;
                mainImg.alt = match.name;
            } else {
                const mainImg = root.querySelector('.root-product-detail__img');
                mainImg.style.display = 'none';
            }

            syncProductOptionRadiosAvailabilityDimmed(variantMap, readSelectedByOptionNameFromDom());
            onVariantSelect(match ? match.id : null);
        }

        // Update product option selection
        function onOptionChange(event, element) {
            resolveVariantFromProductOptionRadios(element.value);
        }


        pageInitializationAfter();
    </script>
@endonce
