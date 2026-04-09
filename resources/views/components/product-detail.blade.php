@props(['Product', 'image' => null, 'lightboxMaxZoom' => 12, 'lightboxMinZoom' => 1])

@once
    @push('styles')
        @vite(['resources/scss/product-detail.scss'])
    @endpush
@endonce



<!-- PHP -->
@php
    /** @var \App\Models\Product $Product */
    $Product->load(['Variants.Values.Option', 'Variants.PriceBrackets', 'PriceBrackets', 'OptionValues.Option']);
    $productOptions = $Product->groupedOptions();

    $lightboxMinZoom = max(0.05, min(50.0, (float) $lightboxMinZoom));
    $lightboxMaxZoom = max(1.0, min(50.0, (float) $lightboxMaxZoom));
    if ($lightboxMinZoom > $lightboxMaxZoom) {
        $lightboxMinZoom = $lightboxMaxZoom;
    }


    $config = [];
    $selectedVariant = $Product->DefaultVariant;

    $detailI18n = [
        'currency' => __('components.product.currency'),
        'qtyFromTo' => __('components.product.qty_from_to', ['start' => ':start', 'end' => ':end']),
        'qtyAndUp' => __('components.product.qty_and_up', ['start' => ':start']),
        'qtyPlusOpen' => __('components.product.qty_plus_open'),
    ];

    $transferData = [
        'config' => $config,
        'Product' => $Product,
        'ProductOptions' => $productOptions,
        'detailI18n' => $detailI18n,
        'selectedVariant' => $selectedVariant,
    ];
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
                    <img src="{{ $Product->image }}" alt="{{ $Product->name }}" class="root-product-detail__img" width="640" height="960" loading="eager" decoding="async" />
                </button>
            </div>


            <!-- VARIANTS IMAGES -->
            <div class="root-product-detail__thumbs" role="group" aria-label="{{ __('components.product.variant_photos') }}">
                <button type="button" data-reference="product-detail-thumb" class="root-product-detail__thumb root-product-detail__thumb--active" aria-pressed="true" onclick="updateMainImage(event, this)"
                    aria-label="{{ __('components.product.show_variant_photo', ['label' => $Product->sku ?: '#' . $Product->id]) }}"
                    data-variant-id="null" data-variant-description="null" data-thumb-index="0" >
                    <img src="{{ $Product->image }}" alt="" width="80" height="120" loading="lazy" decoding="async" class="root-product-detail__thumb-img" />
                </button>
                @foreach ($Product->Variants as $variant)
                    <button type="button" data-reference="product-detail-thumb" class="root-product-detail__thumb root-product-detail__thumb--active" aria-pressed="true" aria-label="{{ __('components.product.show_variant_photo', ['label' => $variant->sku ?: '#' . $variant->id]) }}" onclick="updateMainImage(event, this)"
                        data-variant-id="{{ $variant->id }}" data-variant-description="{{ $variant->localizedDescription() }}" data-thumb-index="{{ $loop->index }}">
                        <img src="{{ $variant->image }}" alt="" width="80" height="120" loading="lazy" decoding="async" class="root-product-detail__thumb-img" />
                    </button>
                @endforeach
            </div>
        </div>


        <!-- PRODUCT INFO -->
        <div class="col">


            <h2 class="dark">{{ $Product->name }}</h2>
            <h4 data-reference="product-detail-variant-description" class="dark">{{ $selectedVariant->localizedDescription() }}</h4>


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
                                     <span data-reference="product-detail-option-note" class="root-product-detail__options-legend-note hidden"> - Does not apply to this variant</span>
                                </legend>
                                <div class="root-products__radio-list">
                                    @foreach ($option->Values as $val)
                                        <x-radio :id="'product-detail-' . $Product->id . '-opt-' . $option->id . '-v-' . $val->id" :name="'product-detail-' . $Product->id . '-opt-' . $option->id" 
                                            :value="(string) $val->id" :label="$val->value" :checked="$selectedVariant->Values->pluck('id')->contains($val->id)"
                                            on-change="onOptionChange" />
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

        
        // Initialization
        function pageInitializationBefore() {
            const scriptEl = root.querySelector('script.product-detail-json[type="application/json"]');
            if (scriptEl && scriptEl.textContent) {
                try {
                    data = JSON.parse(scriptEl.textContent.trim());
                    console.log(data);
                    selectedVariant = data.Product.Variants.find(variant => variant.id == data.selectedVariant.id);
                } catch (err) {
                    data = null;
                }
            }
            quantityElem = root.querySelector('#product-detail-qty-{{ $Product->id }}');
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
        function resetProductDetailLightboxZoom() {
            const lbImg = root.querySelector('.modal-dialog__lightbox-img');
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
                //Update main image
                const mainImg = root.querySelector('.root-product-detail__img');
                mainImg.src = selectedVariant.image;
                mainImg.alt = selectedVariant.name;
                
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
            }else{
                selectedVariant = null;
                const mainImg = root.querySelector('.root-product-detail__img');
                mainImg.src = data.Product.image;
                mainImg.alt = data.Product.name;
            }
            syncSelectedvariantWithProductOptions();
            syncVariantThumbHighlight();
            updateVisiblePriceBracket();
            updateVisiblePriceBracketRowHighlight();
        }


        function syncSelectedvariantWithProductOptions() {
            if (selectedVariant === null) {
                data.ProductOptions.forEach(function (option) {
                    const optionSectionElem = root.querySelector(`[data-reference="product-detail-option-${option.id}"]`);
                    const optionRadioListElem = optionSectionElem.querySelector('.root-products__radio-list');
                    enableRadio(optionSectionElem, optionRadioListElem, true, false);
                });
            }else{
                const variantOptions = [...new Set(selectedVariant.Values.map(value => value.Option.id))];
                data.ProductOptions.forEach(function (option) {
                    const optionSectionElem = root.querySelector(`[data-reference="product-detail-option-${option.id}"]`);
                    const optionRadioListElem = optionSectionElem.querySelector('.root-products__radio-list');
                    if (variantOptions.includes(option.id) && optionSectionElem) {
                        const variantValue = selectedVariant.Values.find(value => value.Option.id == option.id);
                        if (variantValue) {
                            enableRadio(optionSectionElem, optionRadioListElem, true, variantValue.id);
                        }
                    }else{
                        enableRadio(optionSectionElem, optionRadioListElem, false, false);
                    }
                });
            }
        }

        function enableRadio(optionSectionElem, element, enable, select) {
            element.querySelectorAll('input[type="radio"]').forEach(function (radio) {
                if (enable) {
                    optionSectionElem.querySelector('[data-reference="product-detail-option-note"]').classList.add('hidden');
                    //radio.disabled = true;
                    //element.style.opacity = enable ? 1 : 0.5;
                    if (select) {
                        radio.checked = select;
                    }
                }else{
                    optionSectionElem.querySelector('[data-reference="product-detail-option-note"]').classList.remove('hidden');
                    radio.checked = false;
                    //element.style.opacity = 0.5;
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
            const variantId = element.dataset.variantId;
            resetProductDetailLightboxZoom();
            onVariantSelect(variantId);
        }


        document.querySelectorAll('.root-product-detail .modal-dialog--zoom').forEach(function (dlg) {
            dlg.addEventListener('close', function () {
                const root = dlg.closest('.root-product-detail');
                if (!root) {
                    return;
                }
                const openBtn = root.querySelector('[data-reference="product-detail-media"]');
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


        // Update product option selection
        function onOptionChange(event, element) {
            if (!data || !data.Product || !Array.isArray(data.Product.Variants)) {
                onVariantSelect(null);
                return;
            }

            // Build map: optionId -> selected valueId (from checked radios)
            const selected = new Map();
            root.querySelectorAll('[data-reference^="product-detail-option-"]').forEach(function (fieldset) {
                const checked = fieldset.querySelector('input[type="radio"]:checked');
                if (!checked) {
                    return;
                }
                const name = checked.getAttribute('name') || '';
                // name format: product-detail-{productId}-opt-{optionId}
                const m = name.match(/-opt-(\d+)$/);
                if (!m) {
                    return;
                }
                const optionId = parseInt(m[1], 10);
                const valueId = parseInt(checked.value, 10);
                if (Number.isFinite(optionId) && Number.isFinite(valueId)) {
                    selected.set(optionId, valueId);
                }
            });

            // Find variant whose option/value pairs match exactly
            const match = data.Product.Variants.find(function (variant) {
                if (!variant || !Array.isArray(variant.Values)) {
                    return false;
                }

                const variantMap = new Map();
                variant.Values.forEach(function (val) {
                    const optionId = val && val.Option ? parseInt(val.Option.id, 10) : NaN;
                    const valueId = val ? parseInt(val.id, 10) : NaN;
                    if (Number.isFinite(optionId) && Number.isFinite(valueId)) {
                        variantMap.set(optionId, valueId);
                    }
                });

                if (variantMap.size !== selected.size) {
                    return false;
                }

                for (const [optId, valId] of selected.entries()) {
                    if (variantMap.get(optId) !== valId) {
                        return false;
                    }
                }

                return true;
            });

            onVariantSelect(match ? match.id : null);
        }


        pageInitializationAfter();
    </script>
@endonce
