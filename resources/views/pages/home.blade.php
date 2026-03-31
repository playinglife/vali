@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="root-views-home">

        <!-- Page 1 -->
        <div class="page-1">
            <h1> {{ __('pages.home.title1') }} </h1>
        </div>

        <x-miniviews.section type="one">
            <x-slot:title>
                {{ __('pages.home.subtitle1') }}
            </x-slot:title>
            <x-slot:text>
                {{ __('pages.home.text1') }}
            </x-slot:text>
        </x-miniviews.section>

        <x-miniviews.section type="two"> 
            <x-slot:title> {{ __('pages.home.subtitle2') }} </x-slot:title>
            <x-slot:text>
                {{ __('pages.home.text2') }}
            </x-slot:text>
        </x-miniviews.section>


        <div class="page-2">
            <h1> {{ __('pages.home.title2') }} </h1>
        </div>

        <x-miniviews.section type="one">
            <x-slot:title> {{ __('pages.home.subtitle3') }} </x-slot:title>
            <x-slot:text>
                {{ __('pages.home.text3') }}
            </x-slot:text>
        </x-miniviews.section>



        <div class="page-3 grid grid-3 grid-noGutter">
            <div class="page3-content col">
                <h1> {{ __('pages.home.title3') }} </h1>
                <form action="">
                    <div>
                        <input type="text" placeholder="Name" class="name-input">
                    </div>
                    <div>
                        <input type="email" placeholder="Email" class="email-input">
                    </div>
                    <div>
                        <button type="submit" class="subscribe-button">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>



        <!-- Footer -->
        @include('layouts.footer', ['backgroundImage' => asset('images/home.png')])

    </div>
    
@endsection 

@once
    <style lang="scss" scoped>
        .root-views-home {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            background-image: url("{{ asset('images/home.jpg') }}");
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
            & > .page-2 {
                width: 100%;
                height: 70%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                background-image: url("{{ asset('images/home1.png') }}");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }
            & > .page-3 {
                width: 100%;
                height: 70%;
                align-items: stretch;
                align-content: stretch;
                background-image: url("{{ asset('images/home2.png') }}");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                /* grid-equalHeight applies height:100% to every direct child of .col (h1, form, …); we only want the column to stretch */
                & > .page3-content {
                    align-self: stretch;
                    height: 100%;
                    min-height: 0;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    
                    & > form {
                        width: 50%;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        gap: var(--gap-small);
                        & > div {
                            width: 100%;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                        }
                        & > div > .name-input {
                            width: 100%;
                        }
                        & > div > .email-input {
                            width: 100%;
                        }
                        & > .subscribe-button {
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                        }
                    }
                }
            }
        }
    </style>
@endonce