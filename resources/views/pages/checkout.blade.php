@extends('layouts.app')

@section('title','Checkout')

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
            background-image: url("{{ asset('images/cart.png') }}");
            background-size: cover;
            background-position: top;
            background-repeat: no-repeat;
            min-height: 0;
            display: flex;
            flex-direction: column;            
            &::before {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
@endonce
