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
            <!--<@include('layouts.menu')-->
            <!-- Content -->
            <div id="main-content" class="main-content">
            <!--<div class="flex flex-1 relative">-->
                @yield('content')
            </div>

            @php
                $cartAdded = session('cart_added');
            @endphp
            @if (filled($cartAdded))
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
                    <p class="modal-dialog__cart-text" role="status">
                        {{ __('components.product.cart_added', [
                            'product' => $cartAdded['product_name'],
                            'qty' => $cartAdded['quantity'],
                            'total' => number_format((float) $cartAdded['line_total'], 2, '.', ''),
                            'currency' => $cartAdded['currency'],
                        ]) }}
                    </p>
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
            & > .main-content {
                width: 100%;
                height: 100%;
                box-sizing: border-box;
                flex: 1;
                z-index: 0;
                position: relative;
                isolation: isolate;
                display: flex;
                flex-direction: column;
                overflow: auto;
                box-sizing: border-box;

                &::before {
                    content: "";
                    position: absolute;
                    inset: -8%;
                    pointer-events: none;
                    z-index: -1;
                    background:
                        radial-gradient(circle 50rem at 50% 30%, rgba(255, 159, 67, 0.55), transparent 62%),
                        radial-gradient(circle 50rem at 38% 68%, rgba(255, 99, 71, 0.5), transparent 64%),
                        radial-gradient(circle 50rem at 82% 78%, rgba(255, 214, 102, 0.48), transparent 64%);
                    filter: blur(10px);
                }
            }
        }
    </style>
@endonce
