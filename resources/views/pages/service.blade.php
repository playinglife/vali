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
            position: relative;
            isolation: isolate;
            &::before {
                position: absolute;
                inset: 0;
                z-index: 0;
                content: '';
                background-image: url("{{ asset('images/service.jpg') }}");
                background-size: cover;
                background-position: top;
                background-repeat: no-repeat;
                filter: blur(10px);
            }
        }
    </style>
@endonce