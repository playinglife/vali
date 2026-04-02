@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="root-views-cart">


        <!-- Page 1 -->
        <div class="page-1">
            <h1> {{ __('pages.cart.title1') }} </h1>
        </div>

        <x-miniviews.section type="one">
            <x-slot:content>
                <x-cart-items />
            </x-slot:content>
        </x-miniviews.section>

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

            &::before {
                content: "";
                position: absolute;
                inset: 0;
                z-index: 0;
                background-image: url("{{ asset('images/cart.png') }}");
                background-size: cover;
                background-position: top;
                background-repeat: no-repeat;
                filter: blur(3px);
                pointer-events: none;
            }

            & > * {
                position: relative;
                z-index: 1;
            }

            & > .page-1 {
                width: 100%;
                height: 15em;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
@endonce