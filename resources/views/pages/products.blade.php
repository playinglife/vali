@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="root-views-products">


        <!-- Page 1 -->
        <!--<div class="page-1">
            <h1> {{ __('pages.products.title1') }} </h1>
        </div>-->

        <x-miniviews.section type="one" blur="true">
            <x-slot:content>
                <x-products />
            </x-slot:content>
        </x-miniviews.section>

        <!-- Footer -->
        @include('layouts.footer', ['backgroundImage' => 'none'])

    </div>
    
@endsection 

@once
    <style lang="scss" scoped>
        .root-views-products {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            flex: 1;
            min-height: 0;
            overflow: auto;
            position: relative;
            isolation: isolate;
            background-image: url("{{ asset('images/service.jpg') }}");
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