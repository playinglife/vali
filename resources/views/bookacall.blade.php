@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="w-full h-full z-1">


        <!-- Page 1 -->
        <div class="aaa flex w-full h-screen items-center justify-center overflow-hidden relative">
            <div class="flex flex-col absolute left-[40%] top-[50%] h-[80%] items-center justify-between transform -translate-x-1/2 -translate-y-1/2 z-2">
                <div class="flex flex-1 flex-col items-center justify-center">
                    <div class="flex text-white items-start w-full">
                        <h2 class="leading-[1.8rem] tracking-[0.1rem]">BOOK A PRIVATE CONVERSATION</h2>
                    </div>
                </div>
                <div class="p-[2rem]">
                    <div class="text-white text-[0.8rem] font-normal text-center flex items-end justify-center">
                        <h5 class="!font-normal">See how Clef Play fits your classroom or microschool.<br>During the call, we’ll review session structure, program materials, and how to launch your first group successfully.</h5>
                    </div>
                    <div class="text-white text-[0.8rem] font-normal text-center flex items-end justify-center">
                        <h5>
                            <span class="text-tree">Explore fit.</span>
                            <span class="text-one">Get clarity.</span>
                            <span class="text-two">Decide next steps.</span>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="flex absolute left-0 top-0 right-0 bottom-0 items-center justify-end z-1 overflow-hidden">
                <img src="{{ asset('images/violin-transparent.png') }}" alt="Welcome" class="p-[3em] h-full opacity-100 transform">
            </div>
        </div>

        

        <!-- Page 2 -->
        <x-page 
            style="light"
            title=""
            subtitle="">
            <x-slot:title>
                <div class="flex text-center items-center justify-center w-full text-one">
                    <h4>Book a free call and discover how to get started</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-row w-full items-center justify-center gap-[2em]">
                    <img src="{{ asset('images/image6.jpg') }}" alt="Book a free call and discover how to get started" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col w-full items-start justify-center gap-[0.5em] pt-[5em]">
                    <div class="flex w-full items-center gap-2">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current opacity-70" aria-hidden="true"></span>
                        <span>Plan how to test demand and fill your first group</span>
                    </div>
                    <div class="flex w-full items-center gap-2">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current opacity-70" aria-hidden="true"></span>
                        <span>See how Clef Play fits your classroom or microschool</span>
                    </div>
                    <div class="flex w-full items-center gap-2">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current opacity-70" aria-hidden="true"></span>
                        <span>Clarify session structure and pricing</span>
                    </div>
                    <div class="flex w-full items-center gap-2">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current opacity-70" aria-hidden="true"></span>
                        <span>Decide if the program is the right next step</span>
                    </div>
                    <div class="flex w-full items-center gap-2">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current opacity-70" aria-hidden="true"></span>
                        <span>Ask questions about running your program</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('bookacall.submit') }}" class="flex w-full flex-col items-center pt-[5em]">
                    @csrf
                    <x-call-booking-picker name="booking" class="w-full max-w-none" />
                    <div class="mt-[5em]">
                        <x-button text="Request this time" :url="null" type="submit" light="false" />
                    </div>
                </form>
            </x-slot:content>
        </x-page>



        <!-- Page 3 -->
        <x-page 
            style="dark"
            title=""
            subtitle="">
            <x-slot:title>
                <div class="flex text-center items-center justify-center w-full text-one">
                    <h4>Not ready to schedule a call?</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-row w-full items-center justify-center gap-[2em]">
                    <img src="{{ asset('images/image7.jpg') }}" alt="Book a free call and discover how to get started" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col w-full items-start justify-center gap-[0.5em] pt-[5em]">
                    <span>Explore how Clef Play works, preview lesson materials, games, and enrichment activities, and see how it fits your teaching style.</span>
                </div>
            </x-slot:content>
            <x-slot:buttons>    
                <x-button text="Download the free program guide" url="/bookacall" light="false" />
            </x-slot:buttons>
        </x-page>


        <!-- Footer -->
        @include('layouts.footer')

    </div>
@endsection 