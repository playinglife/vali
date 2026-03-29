@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="w-full h-full z-1">
        <!-- Page 1 -->
        <div class="aaa flex w-full h-screen items-center justify-center overflow-hidden relative">
            <div class="flex flex-col absolute left-[40%] top-[50%] h-[80%] items-center justify-between transform -translate-x-1/2 -translate-y-1/2 z-2">
                <div class="flex flex-1 flex-col items-center justify-center">
                    <span class="text-white text-[6em] font-bold z-1 leading-[1em] tracking-[0.1em] text-center items-center justify-center">
                        <h1>START YOUR MUSIC SCHOOL</h1>
                    </span>
                </div>
                <div>
                    <x-button text="Book a free call" url="/start-a-music-school" />
                </div>
            </div>
            <div class="flex absolute left-0 top-0 right-0 bottom-0 items-center justify-end z-1">
                <img src="{{ asset('images/piano-keyboard.png') }}" alt="Welcome" class="h-full object-contain transform rotate-180 translate-x-[30%] opacity-40">
            </div>
        </div>

        <!-- Page 2 -->
        <div class="bbb flex flex-col w-full items-start justify-center bg-[#ebe6dd] text-black p-[5em] pl-[25vw] pr-[25vw] gap-[5em]">
            <div>
                <h3 class="text-shadow-lg-dark color-dark">HOW IT WORKS</h3> 
            </div>
            <span class="font-family-two">
                A complete system for teachers to start and grow their own music school<br>with the <b>Clef Play Studio</b>, <b>Clef Play App</b>, and <b>Marketing Tools</b> all ready to use.
            </span>
        </div>

        <!-- Page 3 -->
        <div class="bbb flex flex-col w-full items-start justify-center bg-white text-black p-[5em] pl-[25vw] pr-[25vw] gap-[5em]">
            <div>
                <h3 class="text-shadow-lg-dark color-dark">CLEF PLAY - STUDIO</h3> 
                <h5 class="color-dark">THE COMPLETE PRESCHOOL MUSIC PROGRAM FOR TEACHERS WHO WANT TO GROW</h5>
            </div>
            <div class="flex items-center justify-center w-full">
                <img src="{{ asset('images/program.png') }}" class="h-[20em] object-cover" alt="Clef Play Studio"
                    style="filter: drop-shadow(0 0 5px #777);">
            </div>
            <span class="font-family-two">
            A turnkey system for teachers — not a course.<br>
            With structured lessons, playful materials, and ready-to-use guides, you can run confident, engaging classes from day one and build a thriving music studio with ease.
            </span>
            <div class="flex items-center justify-center w-full gap-[2rem]">
                <x-button text="Download the guide" url="/start-a-music-school" light="false" />
                <x-button text="Book a free call" url="/start-a-music-school" light="false" />
            </div>
        </div>

        <!-- Page 4 -->
        <div class="bbb flex flex-col w-full items-start justify-center bg-[#ebe6dd] text-black p-[5em] pl-[25vw] pr-[25vw] gap-[5em]">
            <div>
                <h3 class="text-shadow-lg-dark color-dark">CLEF PLAY - APP</h3> 
                <h5 class="color-dark">FOR TEACHERS</h5>
            </div>
            <div class="flex items-center justify-center w-full">
                <img src="{{ asset('images/app.jpg') }}" class="h-[20em] object-cover shadow-xl" alt="Clef Play App">
            </div>
            <span class="font-family-two">
            Everything in one place. Track attendance, progress, payments, and give assignments with one intuitive platform.<br>
            </span>
            <x-button text="Start a free trial" url="https://play.clefplay.com" target="_blank" light="false" />
        </div>

        <!-- Page 5 -->
        <div class="bbb flex flex-col w-full items-start justify-center bg-white text-black p-[5em] pl-[25vw] pr-[25vw] gap-[5em]">
            <div>
                <h3 class="text-shadow-lg-dark color-dark">FAQ</h3> 
            </div>
            <x-button text="Book a free call" url="https://play.clefplay.com" target="_blank" light="false" />
        </div>

        <!-- Footer -->
        @include('layouts.footer')

    </div>
@endsection 