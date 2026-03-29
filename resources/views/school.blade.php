@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="w-full h-full z-1">
        <!-- Page 1 -->
        <div class="aaa flex w-full h-screen items-center justify-center overflow-hidden relative">
            <div class="flex flex-col absolute left-[40%] top-[50%] h-[80%] items-center justify-between transform -translate-x-1/2 -translate-y-1/2 z-2">
                <div class="flex flex-1 flex-col items-center justify-center">
                    <span class="text-white text-center items-center justify-center">
                        <h1 class="!text-[5rem] font-bold leading-[4.8rem]">START<br>YOUR<br>MUSIC<br>SCHOOL</h1>
                    </span>
                </div>
                <div class="text-white text-[0.8rem] font-normal leading-[1rem] tracking-[0.1rem] text-center p-[2rem] flex items-end justify-center">
                    <span>
                        <x-button text="Take the survey" url="/survey/school" />
                    </span>
                </div>
            </div>
            <div class="flex absolute left-0 top-0 right-0 bottom-0 items-center justify-end z-1 overflow-hidden">
                <img src="{{ asset('images/school.png') }}" alt="Welcome" class="p-[3em] h-full opacity-100 transform">
            </div>
        </div>

        <!-- Page 2 -->
        <x-page 
            title="HOW IT WORKS"
            subtitle="Your Vision. Your Studio. your way.">
                <x-slot:content>
                Whether you want to run a few weekly clubs, open a full preschool studio, or simply integrate our app — we're creating a complete system to help you build and grow your own music school.
                </x-slot:content>
                <x-slot:buttons>
                    <x-button text="Take the survey" url="/survey/teacher" light="false" />
                    <x-button text="Get the free guide" url="/download-the-guide" light="false" />
                    <x-button text="Book a free call" url="/book-a-free-call" light="false" />
                </x-slot:buttons>
        </x-page>

        <!-- Page 3 -->
        <x-page 
            style="dark"
            title="CLEF PLAY - STUDIO PACK"
            subtitle="Everything You Need to Build a Music School That Thrives">
            <x-slot:content>
                <div class="flex p-[2rem]">
                    <div class="flex-1 justify-center items-center">
                        <ul>
                            <li class="font-family-two">Printed Program & Games</li>
                            <li class="font-family-two">Step-by-step class guides</li>
                            <li class="font-family-two">Marketing Materials & Planner</li>
                            <li class="font-family-two">Teacher Support & Community</li>
                            <li class="font-family-two">Lifetime App Access</li>
                        </ul>
                    </div>
                    <div class="flex-1 justify-center items-center">
                        <img src="{{ asset('images/program.png') }}" alt="Studio" class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="flex p-[2rem] items-center justify-center">
                    <x-button text="Get the free guide" url="/download-the-guide" light="false" />
                </div>
            </x-slot:content>
        </x-page>

        <!-- Page 4 -->
        <x-page
            style="dark"
            title="Make it Your Way">
            <x-slot:content>
                Tell us your goals, and we’ll craft a custom package that fits your studio perfectly.
            </x-slot:content>
        </x-page>

        <!-- Page 4 -->
        <x-page
            style="dark"
            title="EXCLUSIVE EARLY ACCESS – HOLIDAY OFFER"
            :titleImportant="true">
            <x-slot:content>
                Secure your CLEF PLAY STUDIO PACK by December 31 and enjoy a special holiday offer, tailored to your studio’s needs.
                <div class="flex items-center justify-center w-full">
                    <x-countdown :date="date('2025-12-31')" />
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Join the survey & get early access" url="/download-the-guide" light="false" />
            </x-slot:buttons>
        </x-page>

        <!-- Page 5 -->
        <x-page
            style="light"
            title="CLEF PLAY - APP"
            subtitle="FOR TEACHERS">
            <x-slot:content>
                <div class="flex p-[2rem]">
                    <div class="flex-1 justify-center items-center">
                    </div>
                    <div class="flex-1 justify-center items-center">
                        <img src="{{ asset('images/app.png') }}" alt="Studio" class="w-full h-full object-cover">
                    </div>
                </div>
                <h5>
                    For teaching & studio management — all in one place.
                </h5>
                <span>
                    Unlock a complete digital hub for your students: games, stories, assignments, and progress tracking — all in one place. Take control of your lessons, save time on admin, and make learning fun.
                </span>
                <div class="divider"></div>
                <h5>
                    Take the quick survey and help us create the app teachers actually need.
                </h5>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Take the app survey" url="/survey/app" light="false" />
            </x-slot:buttons>
        </x-page>

        <!-- Footer -->
        @include('layouts.footer')

    </div>
@endsection 