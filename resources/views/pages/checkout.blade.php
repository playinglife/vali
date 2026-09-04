@extends('layouts.app')

@section('title', __('pages.checkout.title1'))
@section('robots','noindex, nofollow')
@section('description', __('pages.checkout.meta_description'))

@section('content')

    <div class="root-views-checkout">

        <!-- Checkout Page -->
        <x-checkout />

        <!-- Footer -->
        @include('layouts.footer', ['backgroundImage' => 'none'])

    </div>

@endsection

@once
    <style lang="scss" scoped>
        .root-views-checkout {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            flex: 1;
            overflow: auto;
            position: relative;
            isolation: isolate;
            min-height: 0;
            display: flex;
            flex-direction: column;
            &::before {
                content: '';
                position: fixed;
                inset: 0;
                background-image: url("{{ asset('images/cart.jpg') }}");
                background-size: cover;
                background-position: top;
                background-repeat: no-repeat;
                filter: blur(8px);
                transform: scale(1.05);
                z-index: -1;
                pointer-events: none;
            }
            & > * {
                position: relative;
                z-index: 1;
            }
        }
    </style>
@endonce
