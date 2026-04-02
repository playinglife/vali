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
    $showCompare = $list !== null && $list > $price;
    $inStock = $product->isInStock();
    $categoryNames = $product->categories->pluck('name')->filter()->values();

    $variantsSorted = $product->variants->sortBy('id')->values();
    $thumbUrls = $variantsSorted->map(fn ($v) => $v->displayImageUrl());
    $firstMatchIndex = $thumbUrls->search(fn ($url) => $url === $imageUrl);
    $activeThumbIndex = $firstMatchIndex !== false ? $firstMatchIndex : null;
@endphp

<div class="root-product-detail">
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

            <!--@if ($categoryNames->isNotEmpty())
                <p class="root-product-detail__categories">
                    {{ $categoryNames->implode(', ') }}
                </p>
            @endif-->

            @if ($product->short_description)
                <p class="root-product-detail__lead">{{ $product->short_description }}</p>
            @endif

            @if ($product->description)
                <div class="root-product-detail__body">{!! nl2br(e($product->description)) !!}</div>
            @endif

            <div class="root-product-detail__meta">
                <p class="root-product-detail__sku">
                    <span class="root-product-detail__meta-label">{{ __('components.product.sku') }}</span>
                    {{ $product->sku }}
                </p>
                <div class="root-product-detail__prices" aria-label="{{ __('components.product.price') }}">
                    <span class="root-product-detail__price">{{ number_format($price, 2) }}&nbsp;{{ __('components.product.currency') }}</span>
                    @if ($showCompare)
                        <span class="root-product-detail__compare">{{ number_format($list, 2) }}&nbsp;{{ __('components.product.currency') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="root-product-detail__actions">
        <form method="post" action="{{ route('cart.add') }}" class="root-product-detail__cart-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}" />
            <input type="hidden" name="quantity" value="1" />
            <button
                type="submit"
                class="root-product-detail__add-btn"
                @disabled(! $inStock)
            >
                {{ __('components.product.add_to_cart') }}
            </button>
        </form>
    </div>

    <dialog
        id="product-detail-lightbox-{{ $product->id }}"
        class="root-product-detail__lightbox"
        aria-modal="true"
        aria-label="{{ $product->name }}"
        data-lightbox-min-zoom="{{ $lightboxMinZoom }}"
        data-lightbox-max-zoom="{{ $lightboxMaxZoom }}"
    >
        <div class="root-product-detail__lightbox-pane">
            <button
                type="button"
                class="root-product-detail__lightbox-close"
                aria-label="{{ __('components.product.close_lightbox') }}"
            >
                ×
            </button>
            <div class="root-product-detail__lightbox-zoom-wrap">
                <img
                    class="root-product-detail__lightbox-img"
                    src=""
                    alt=""
                    width="1280"
                    height="1920"
                    decoding="async"
                />
            </div>
        </div>
    </dialog>
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
            max-height: min(70vh, 36rem);
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
        dialog.root-product-detail__lightbox {
            margin: 0;
            padding: 0;
            border: none;
            max-width: none;
            max-height: none;
            width: 100%;
            height: 100%;
            background: transparent;
            box-sizing: border-box;
        }
        dialog.root-product-detail__lightbox::backdrop {
            background: color-mix(in srgb, var(--color-text-dark) 55%, transparent);
        }
        .root-product-detail__lightbox-pane {
            box-sizing: border-box;
            width: 100%;
            min-height: 100%;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(0.75rem, 4vw, 2rem);
            cursor: zoom-out;
            overflow: auto;
            overscroll-behavior: contain;
        }
        .root-product-detail__lightbox-zoom-wrap {
            --lb-zoom: 1;
            box-sizing: border-box;
            flex-shrink: 0;
            width: calc(80vw * var(--lb-zoom));
            height: min(calc(80vh * var(--lb-zoom)), calc(80dvh * var(--lb-zoom)));
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .root-product-detail__lightbox-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            display: block;
            cursor: default;
        }
        .root-product-detail__lightbox-close {
            position: fixed;
            top: clamp(0.5rem, 1.5vw, 1rem);
            right: clamp(0.5rem, 1.5vw, 1rem);
            z-index: 1;
            margin: 0;
            padding: 0.2em 0.55em;
            border: 0;
            border-radius: var(--border-radius-small);
            background: color-mix(in srgb, var(--color-background) 92%, white);
            color: var(--color-text-dark);
            font-family: var(--font-family-one);
            font-size: 1.35rem;
            line-height: 1;
            cursor: pointer;
            opacity: 0.92;
        }
        .root-product-detail__lightbox-close:hover {
            opacity: 1;
        }
        .root-product-detail__lightbox-close:focus-visible {
            outline: 2px solid var(--color-one);
            outline-offset: 2px;
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
            color: var(--color-text-dark);
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
            opacity: 0.85;
        }
        .root-product-detail__add-btn:disabled {
            opacity: 0.45;
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
            const wrap = lbImg.closest('.root-product-detail__lightbox-zoom-wrap');
            if (wrap) {
                wrap.style.setProperty('--lb-zoom', '1');
            }
        }

        function setProductDetailLightboxZoom(lbImg, z) {
            lbImg.dataset.lbZoom = String(z);
            const wrap = lbImg.closest('.root-product-detail__lightbox-zoom-wrap');
            if (wrap) {
                wrap.style.setProperty('--lb-zoom', String(z));
            }
        }

        function syncProductDetailLightbox(root) {
            const dlg = root.querySelector('.root-product-detail__lightbox');
            const mainImg = root.querySelector('.root-product-detail__img');
            const lbImg = root.querySelector('.root-product-detail__lightbox-img');
            if (!dlg || !mainImg || !lbImg || !dlg.open) {
                return;
            }
            lbImg.src = mainImg.currentSrc || mainImg.src;
            lbImg.alt = mainImg.alt;
            resetProductDetailLightboxZoom(lbImg);
        }

        document.addEventListener('click', function (e) {
            const openBtn = e.target.closest('.root-product-detail__media-open');
            if (openBtn) {
                const root = openBtn.closest('.root-product-detail');
                const dlg = root && root.querySelector('.root-product-detail__lightbox');
                const mainImg = root && root.querySelector('.root-product-detail__img');
                const lbImg = root && root.querySelector('.root-product-detail__lightbox-img');
                if (root && dlg && mainImg && lbImg && typeof dlg.showModal === 'function') {
                    lbImg.src = mainImg.currentSrc || mainImg.src;
                    lbImg.alt = mainImg.alt;
                    resetProductDetailLightboxZoom(lbImg);
                    dlg.showModal();
                    openBtn.setAttribute('aria-expanded', 'true');
                }
                return;
            }

            const closeBtn = e.target.closest('.root-product-detail__lightbox-close');
            if (closeBtn) {
                const dlg = closeBtn.closest('dialog');
                if (dlg && typeof dlg.close === 'function') {
                    dlg.close();
                }
                return;
            }

            const pane = e.target.closest('.root-product-detail__lightbox-pane');
            if (pane && e.target === pane) {
                const dlg = pane.closest('dialog');
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
            syncProductDetailLightbox(root);
        });

        document.querySelectorAll('.root-product-detail__lightbox').forEach(function (dlg) {
            dlg.addEventListener('close', function () {
                const root = dlg.closest('.root-product-detail');
                if (!root) {
                    return;
                }
                const openBtn = root.querySelector('.root-product-detail__media-open');
                if (openBtn) {
                    openBtn.setAttribute('aria-expanded', 'false');
                }
                const lbImg = root.querySelector('.root-product-detail__lightbox-img');
                resetProductDetailLightboxZoom(lbImg);
            });
            dlg.addEventListener('click', function (e) {
                if (e.target === dlg) {
                    dlg.close();
                }
            });
            dlg.addEventListener(
                'wheel',
                function (e) {
                    if (!dlg.open) {
                        return;
                    }
                    const lbImg = dlg.querySelector('.root-product-detail__lightbox-img');
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
