@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="root-views-cart">

        <!-- Page 1 -->
        <x-cart-items />

        <!-- Footer -->
        @include('layouts.footer', ['backgroundImage' => 'none'])

    </div>
    
@endsection 

@once
    <style lang="scss" scoped>
        .root-views-cart {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            flex: 1;
            min-height: 0;
            overflow: auto;
            position: relative;
            isolation: isolate;
            background-image: url("{{ asset('images/cart.png') }}");
            background-size: cover;
            background-position: top;
            background-repeat: no-repeat;
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