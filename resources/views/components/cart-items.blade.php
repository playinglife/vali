@php
    $cartIsEmpty = $cartIsEmpty ?? true;
    $groups = $groups ?? [];
    $grandTotal = $grandTotal ?? 0.0;
    $currency = $currency ?? __('components.product.currency');
@endphp

<div class="cart-items">
    @if ($cartIsEmpty)
        <p class="cart-items__empty">{{ __('pages.cart.empty') }}</p>
    @else
        @foreach ($groups as $group)
            @php($product = $group['product'])
            <section class="cart-items__product-group" aria-labelledby="cart-product-heading-{{ $product->id }}">
                <h2 id="cart-product-heading-{{ $product->id }}" class="cart-items__product-name">
                    {{ $product->name }}
                </h2>
                <ul class="cart-items__lines" role="list">
                    @foreach ($group['rows'] as $row)
                        <li class="cart-items__line">
                            <div class="cart-items__thumb-wrap">
                                <img
                                    class="cart-items__thumb"
                                    src="{{ $row['image_url'] }}"
                                    alt=""
                                    width="96"
                                    height="96"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                            <div class="cart-items__line-body">
                                <span class="cart-items__variant-label">{{ $row['label'] }}</span>
                                <div class="cart-items__meta">
                                    <span class="cart-items__qty">
                                        <span class="cart-items__meta-label">{{ __('pages.cart.quantity') }}</span>
                                        {{ $row['quantity'] }}
                                    </span>
                                    <span class="cart-items__unit-price">
                                        <span class="cart-items__meta-label">{{ __('pages.cart.unit_price') }}</span>
                                        {{ number_format($row['unit_price'], 2, '.', '') }} {{ $currency }}
                                    </span>
                                    <span class="cart-items__line-total">
                                        <span class="cart-items__meta-label">{{ __('pages.cart.line_total') }}</span>
                                        {{ number_format($row['line_total'], 2, '.', '') }} {{ $currency }}
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach

        <div class="cart-items__grand-total-wrap">
            <p class="cart-items__grand-total">
                <span class="cart-items__grand-total-label">{{ __('pages.cart.grand_total') }}</span>
                {{ number_format($grandTotal, 2, '.', '') }} {{ $currency }}
            </p>
        </div>
    @endif
</div>

@once
    <style lang="scss" scoped>
        .cart-items {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .cart-items__empty {
            color: var(--color-text-dark);
            text-align: center;
            margin: 0;
            padding: var(--padding-large) 0;
        }

        .cart-items__product-group {
            margin-bottom: calc(var(--padding-large) * 1.5);
        }

        .cart-items__product-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-text-dark);
            margin: 0 0 var(--padding-large);
            letter-spacing: 0.05em;
        }

        .cart-items__lines {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: var(--padding-large);
        }

        .cart-items__line {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: var(--padding-large);
        }

        .cart-items__thumb-wrap {
            flex-shrink: 0;
            width: 6rem;
            height: 6rem;
            border-radius: 4px;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.06);
        }

        .cart-items__thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cart-items__line-body {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .cart-items__variant-label {
            font-size: 0.85em;
            color: var(--color-text-dark);
            line-height: 1.35;
        }

        .cart-items__meta {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 1rem 1.5rem;
            font-weight: 400;
            font-variant-numeric: tabular-nums;
            color: var(--color-text-dark);
            font-size: 0.85em;
        }

        .cart-items__meta-label {
            display: block;
            font-size: 0.75em;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            opacity: 0.75;
            margin-bottom: 0.15rem;
        }

        .cart-items__grand-total-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: calc(var(--padding-large) * 2);
            padding-top: var(--padding-large);
            border-top: 1px solid rgba(0, 0, 0, 0.12);
        }

        .cart-items__grand-total {
            margin: 0;
            font-size: 1.1em;
            font-weight: 600;
            color: var(--color-text-dark);
            font-variant-numeric: tabular-nums;
            text-align: center;
        }

        .cart-items__grand-total-label {
            display: block;
            font-size: 0.75em;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.35rem;
            opacity: 0.85;
        }
    </style>
@endonce
