<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script>document.documentElement.classList.add('js');</script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>ShirtHouse - @yield('title')</title>
        @vite(['resources/scss/app.scss','resources/js/app.js'])
        {{-- @livewireStyles --}}

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    </head>
    <body>
        <div class="root-views-layouts-app">
            <x-flash-messages />
            <!-- Menu -->
            @include('layouts.menu')
            <!-- Content -->
            <div id="main-content" @class(['main-content', 'main-content--scroll' => $scroll ?? false])>
            <!--<div class="flex flex-1 relative">-->
                @yield('content')
            </div>

            @php
                $cartAdded = session('cart_added');
            @endphp
            @if (filled($cartAdded))
                @php
                    $pricing = \App\Support\VariantPricing::forVariantId((int) ($cartAdded['variant_id'] ?? $cartAdded['product_variant_id'] ?? 0), (int) ($cartAdded['quantity'] ?? 1));
                    $productName = (string) ($pricing['product_name'] ?? $cartAdded['product_name'] ?? '');
                    $quantity = (int) ($pricing['quantity'] ?? $cartAdded['quantity'] ?? 1);
                    $price = (float) ($pricing['price'] ?? $cartAdded['price'] ?? 0);
                    $discount = (float) ($pricing['discount'] ?? $cartAdded['discount'] ?? 0);
                    $discountType = (string) ($pricing['discount_type'] ?? $cartAdded['discount_type'] ?? '');
                    $discountPrice = (float) ($pricing['discount_price'] ?? $cartAdded['discount_price'] ?? 0);
                    $total = (float) ($pricing['total'] ?? $cartAdded['total'] ?? 0);
                    $currency = (string) ($pricing['currency'] ?? $cartAdded['currency'] ?? '');
                @endphp
                <x-modal-dialog
                    id="layout-cart-added-dialog"
                    mode="message"
                    :aria-label="__('components.product.cart_added_title')"
                    :close-label="__('components.flash.dismiss')"
                    :open-on-load="true"
                    :show-close-button="false"
                    :show-ok-button="true"
                    :ok-label="__('components.product.cart_added_ok')"
                    :dismiss-on-backdrop="true"
                    :allow-escape-key="false"
                >
                    <div class="modal-dialog__cart-text" role="status">
                        @if (filled($cartAdded))
                            <table class="modal-dialog__cart-table">
                                <tr>
                                    <td colspan="2" class="modal-dialog__cart-table-title">
                                        <h4 class="light">{{ __('components.product.cart_added_title') }}</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('components.product.product') }}:</strong></td>
                                    <td><strong>{{ $productName }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('components.product.quantity') }}:</strong></td>
                                    <td><strong>{{ $quantity }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('components.product.price') }}:</strong></td>
                                    <td><strong>{{ number_format((float) $price, 2, '.', '') }} {{ $currency }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('components.product.discounted_price') }}:</strong></td>
                                    <td><strong>{{ number_format((float) $discountPrice, 2, '.', '') }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('components.product.total') }}:</strong></td>
                                    <td><strong>{{ number_format((float) $total, 2, '.', '') }} {{ $currency }}</strong></td>
                                </tr>
                            </table>
                        @endif
                    </div>
                </x-modal-dialog>
            @endif
        </div>
        @stack('styles')
        {{-- @livewireScripts --}}
    </body>
</html>
@once
    <style lang="scss" scoped>
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .root-views-layouts-app {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            flex: 1;
            z-index: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .main-content {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            flex: 1;
            z-index: 0;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            overflow: hidden;
            position: relative;
            isolation: isolate;
        }
        .main-content--scroll {
            overflow: auto;
        }
        .modal-dialog__cart-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            & > tbody > tr {
                & > td {
                    color: var(--color-text-light);
                    font-family: var(--font-family-one);
                    font-weight: var(--font-weight-bold);
                    font-size: var(--text-size-tiny);
                }
            }
            .modal-dialog__cart-table-title {
                text-align: center;
                padding: var(--padding-small);
            }
        }
    </style>
@endonce
