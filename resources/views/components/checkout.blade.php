@php

use App\Http\Controllers\CartController;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductOptionValue;
use App\Models\ProductOption;
   
    $transferData = [];
@endphp



<!-- TEMPLATE -->
<x-menu-height-compensator />
<div data-reference="checkout" class="root-checkout">
    <script type="application/json" class="product-detail-json">
        @json($transferData)
    </script>
    <div class="grid root-checkout__grid">
        <div class="grid grid-middle grid-center root-checkout__main-title">
            <h2 class="dark"> {{ __('pages.checkout.title1') }} </h2>
        </div>



        <!-- checkout form -->
            <x-miniviews.panel :padding="false">
                <div class="root-checkout__line">
                    <div class="root-checkout__title">
                        <h3 class="dark"> </h3>
                    </div>
                </div>
            </x-miniviews.panel>



        <!-- Confirmation -->
        <x-miniviews.panel :padding="false">
            <div class="grid grid-row grid-middle grid-noGutter grid-center root-checkout__total">
                <div class="col root-checkout__total-buttons">
                    <form method="post" action="{{ route('confirm_order') }}">
                        @csrf
                        <x-button type="submit" text="{{ __('pages.checkout.confirm_order') }}" aria-label="{{ __('pages.checkout.confirm_order') }}" />
                    </form>
                </div>
            </div>
        </x-miniviews.panel>
    </div>
</div>



<!-- STYLES -->
@once
    <style lang="scss" scoped>
        .root-checkout__title {
            padding: var(--padding-large);
        }
        .root-checkout {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
        }
        .root-checkout__grid {
            gap: var(--gap-large);
            padding: 0 25% var(--padding-huge) 25%;
        }
        .root-checkout__group {
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
        .root-checkout__main-title {
            flex: 1 1 auto;
            min-width: 0;
            padding-right: var(--gap-medium);
            padding-top: var(--padding-medium);
        }
        .root-checkout__line {
            padding-bottom: var(--padding-medium);
        }
        .root-checkout__line-title {
            flex: 1 1 auto;
            min-width: 0;
            margin: 0;
            padding-right: var(--gap-medium);
        }

        .root-checkout__remove {
            flex-shrink: 0;
        }

        .root-checkout__line-total {
            font-family: var(--font-family-one);
            font-weight: var(--font-weight-bold);
            font-size: var(--text-size-small);
            color: var(--color-text-dark);
        }

        .root-checkout__remove-button {
            justify-content: flex-end;
        }

        .root-checkout__total {
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
            & > .col:first-child > table > tbody > tr > td.root-checkout__total-currency {
                padding-left: 0;
            }
        }

        .root-checkout__total-text {
            text-align: right;
        }
        .root-checkout__total-value {
            text-align: right;
        }
        .root-checkout__total {
            padding: var(--padding-large);
        }
        .root-checkout__options {
            display: flex;
            flex-direction: column;
            font-size: var(--text-size-tiny);
            gap: var(--gap-medium);
        }
        .root-checkout__total-buttons {
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
