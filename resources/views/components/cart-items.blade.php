@php

use App\Http\Controllers\CartController;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductOptionValue;
use App\Models\ProductOption;
    
    $currency = __('components.product.currency');
    $sessionLines = session('cart', []);
    $lines = [];

    $cartIsEmpty = $sessionLines === [];
    $total = 0;
    $totalItems = 0;

    if (!$cartIsEmpty) {
        foreach ($sessionLines as &$sessionLine) {
            $product = Product::find($sessionLine['product_id']);
            $variant = ProductVariant::with('Values.Option')->find($sessionLine['variant_id'] ?? $sessionLine['product_variant_id'] ?? null);
            if (!isset($lines[$sessionLine['product_id']])) {
                $lines[$sessionLine['product_id']] = [
                    'product' => $product,
                    'items' => [],
                ];
            }
            $lines[$sessionLine['product_id']]['items'][] = [
                'variant' => $variant,
                'sessionLine' => $sessionLine,
            ];

            $totalItems += $sessionLine['quantity'];
            $total += $sessionLine['quantity'] * $variant->price;
        }
    }

    $transferData = [
        'cartIsEmpty' => $cartIsEmpty,
        'lines' => $lines,
        'currency' => $currency,
        'total' => $total,
        'totalItems' => $totalItems,
    ];

    //dd($lines);
    //(new CartController())->clear();
@endphp



<!-- TEMPLATE -->
<x-menu-height-compensator />
<div data-reference="cart-items" class="root-cart-items">
    <script type="application/json" class="product-detail-json">
        @json($transferData)
    </script>
    <div class="grid root-cart-items__grid">
        <div class="grid grid-middle grid-center root-cart-items__main-title">
            <h2 class="dark"> {{ __('pages.cart.title1') }} </h2>
        </div>



        <!-- Lines -->
        @foreach ($lines as $line)
            <x-miniviews.panel :padding="false">
                <div class="root-cart-items__line">
                    <div class="root-cart-items__title">
                        <h3 class="dark"> {{ $line['product']->name }} </h3>
                    </div>
                    @foreach ($line['items'] as $item)
                        @php
                            $variant = $item['variant'];
                            $sessionLine = $item['sessionLine'];
                        @endphp
                        <div class="root-cart-items__group grid">
                            <div class="col">
                                <div class="grid grid-column grid-middle">
                                    <img src="{{ $variant->storageImageUrl() }}" alt="{{ $line['product']->name }}" width="150em" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="grid grid-noGutter root-cart-items__remove-button">
                                    <form method="post" action="{{ route('cart.remove') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $sessionLine['id'] ?? 0 }}">
                                        <x-button type="submit" icon="gmdi-close" class="button-icon root-cart-items__remove" aria-label="{{ __('pages.cart.remove_item') }}" />
                                    </form>
                                </div>
                                <p class="text-small"> {{ $variant->product->localizedDescription() }} </p>
                                <p class="text-small"> {{ $variant->localizedDescription() }} </p>
                                <p class="root-cart-items__line-total"> {{ $sessionLine['quantity'] }}  {{ __('pages.cart.unit') }}  x  {{ $sessionLine['price'] }} {{ __('components.product.currency') }} = {{ $sessionLine['quantity'] * $variant->price }} {{ __('components.product.currency') }} </p>

                                <div class="root-cart-items__options">
                                    @foreach ($variant->Values as $value)
                                    <fieldset data-reference="product-detail-option-{{ $value->Option->id }}">
                                        <legend class="text-tiny">
                                            {{ $value->Option->name }}
                                        </legend>
                                        @if ($value->Option->type === \App\Enums\ProductOptionType::Icon->value)
                                            @php $className = 'root-products__icon-list'; @endphp
                                        @else
                                            @php $className = 'root-products__radio-list'; @endphp
                                        @endif
                                        <div data-reference="product-detail-option-list" class="{{ $className }}">
                                            @if ($value->Option->type->value === \App\Enums\ProductOptionType::Image->value)
                                                <img src="{{ $value->image }}" alt="{{ $value->value }}" class="root-product-detail__option-image" />
                                            @elseif ($value->Option->type->value === \App\Enums\ProductOptionType::Icon->value)
                                                @php
                                                    [$iconName, $iconColor] = array_pad(explode(':', $value->icon, 2), 2, '');
                                                @endphp
                                                <x-radio :name="'product-detail-' . $variant->id . '-opt-' . $value->Option->id" :value="(string) $value->id" :label="$value->value" :checked="false" :displayOnly="true">
                                                    @if ($iconColor !== '')
                                                        <x-icon name="{{ $iconName }}" aria-hidden="true" class="medium-icon" style="color: {{ $iconColor }};" />
                                                    @else
                                                        <x-icon name="{{ $iconName }}" aria-hidden="true" class="medium-icon" />
                                                    @endif
                                                </x-radio>
                                            @else
                                            <x-radio :name="'product-detail-' . $variant->id . '-opt-' . $value->Option->id" :value="(string) $value->id" :label="$value->value" :checked="true" />
                                            @endif
                                        </div>
                                    </fieldset>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </x-miniviews.panel>
        @endforeach



        <!-- Total -->
        <x-miniviews.panel :padding="false">
            <div class="grid grid-row grid-middle grid-noGutter grid-center root-cart-items__total">
                <div class="col">
                    <table>
                        <tr>
                            <td class="root-cart-items__total-text"> 
                                {{ __('pages.cart.unit') }}:
                            <td class="root-cart-items__total-value"> 
                                {{ $totalItems }} 
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="root-cart-items__total-text"> 
                                {{ __('pages.cart.grand_total') }}:
                            </td>
                            <td class="root-cart-items__total-value"> 
                                {{ $total }}
                            </td>
                            <td class="root-cart-items__total-currency"> {{ __('components.product.currency') }} </td>
                        </tr>
                    </table>
                </div>
                <div class="col root-cart-items__total-buttons">
                    <form method="post" action="{{ route('cart.clear') }}">
                        @csrf
                        <x-button type="submit" text="Clear cart" aria-label="{{ __('pages.cart.clear_cart') }}" />
                    </form>
                    <x-button text="{{ __('pages.cart.checkout') }}" url="{{ route('checkout') }}" aria-label="{{ __('pages.cart.checkout') }}" />
                </div>
            </div>
        </x-miniviews.panel>
    </div>
</div>



<!-- STYLES -->
@once
    <style lang="scss" scoped>
        .root-cart-items__title {
            padding: var(--padding-large);
        }
        .root-cart-items {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            flex: 1 0 auto;
        }
        .root-cart-items__grid {
            gap: var(--gap-large);
            padding: 0 25% var(--padding-huge) 25%;
        }
        .root-cart-items__group {
            padding: var(--padding-small) var(--padding-large);
            gap: var(--gap-large);

            &.grid > .col:first-child {
                flex: 0 1 auto;
                max-width: none;
            }

            &.grid > .col:last-child {
                flex: 1 1 0%;
                min-width: 0;
            }
        }
        .root-cart-items__main-title {
            flex: 1 1 auto;
            min-width: 0;
            padding-right: var(--gap-medium);
            padding-top: var(--padding-medium);
        }
        .root-cart-items__line {
            padding-bottom: var(--padding-medium);
        }
        .root-cart-items__line-title {
            flex: 1 1 auto;
            min-width: 0;
            margin: 0;
            padding-right: var(--gap-medium);
        }

        .root-cart-items__remove {
            flex-shrink: 0;
        }

        .root-cart-items__line-total {
            font-family: var(--font-family-one);
            font-weight: var(--font-weight-bold);
            font-size: var(--text-size-small);
            color: var(--color-text-dark);
        }

        .root-cart-items__remove-button {
            justify-content: flex-end;
        }

        .root-cart-items__total {
            width: 100%;
            gap: var(--gap-medium);
            & > .col {
                display: flex;
            }
            & > .col:last-child {
                justify-content: flex-end;
            }
            & > .col:first-child > table > tbody > tr > td {
                font-family: var(--font-family-one);
                font-weight: var(--font-weight-bold);
                font-size: var(--text-size-normal);
                color: var(--color-text-dark);
                padding: 0 var(--padding-tiny);
            }
            & > .col:first-child > table > tbody > tr > td.root-cart-items__total-currency {
                padding-left: 0;
            }
        }

        .root-cart-items__total-text {
            text-align: right;
        }
        .root-cart-items__total-value {
            text-align: right;
        }
        .root-cart-items__total {
            padding: var(--padding-large);
        }
        .root-cart-items__options {
            display: flex;
            flex-direction: column;
            font-size: var(--text-size-tiny);
            gap: var(--gap-medium);
        }
        .root-cart-items__total-buttons {
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
            gap: var(--gap-medium);
            padding: var(--padding-small);
            & > form {
                margin: 0;
                padding: 0;
            }
        }
    </style>
@endonce
