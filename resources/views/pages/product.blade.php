@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="root-views-product">


        <!-- Page 1 -->
        <x-miniviews.panel>
            <x-product-detail :Product="$product" />
        </x-miniviews.panel>

        <!-- Footer -->
        @include('layouts.footer', ['backgroundImage' => 'none'])

    </div>
    
@endsection 

@once
    <style lang="scss" scoped>
        .root-views-product {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            flex: 1;
            min-height: 0;
            overflow: auto;
            background-image: url("{{ asset('images/detailed.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            & > .page-1 {
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