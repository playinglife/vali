@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="root-views-service">


        <!-- Page 1 -->
        <!--<div class="page-1">
            <h1> {{ __('pages.service.title1') }} </h1>
        </div>-->

        <x-miniviews.section type="one">
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
        .root-views-service {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            flex: 1;
            min-height: 0;
            overflow: auto;
            background-image: url("{{ asset('images/service.jpg') }}");
            background-size: cover;
            background-position: top;
            background-repeat: no-repeat;
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