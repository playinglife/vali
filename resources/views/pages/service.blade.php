@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="root-views-service">


        <!-- Page 1 -->
        <div class="page-1">
            <h1> {{ __('pages.service.title1') }} </h1>
        </div>

        <x-miniviews.section type="one">
            <x-slot:content>
                <x-products />
            </x-slot:content>
        </x-miniviews.section>

        <!-- Footer -->
        @include('layouts.footer', ['backgroundImage' => asset('images/home.png')])

    </div>
    
@endsection 

@once
    <style lang="scss" scoped>
        .root-views-service {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            background-image: url("{{ asset('images/service.jpg') }}");
            background-size: cover;
            background-position: top;
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