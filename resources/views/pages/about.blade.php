@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="w-full h-full z-1">
        <!-- Page 1 -->
        <div class="aaa flex w-full h-screen items-center justify-center overflow-hidden relative">
            <div class="flex flex-col absolute left-[40%] top-[50%] h-[80%] items-center justify-between transform -translate-x-1/2 -translate-y-1/2 z-2">
                <div class="flex flex-1 flex-col items-center justify-center">
                <div class="flex text-white text-center items-start w-full leading-[4.8rem]">
                        <h1 class="!text-[5rem] font-bold">ABOUT</h1>
                    </div>
                    <div class="flex text-center items-start w-full text-tree">
                        <h3>CLEF PLAY</h3>
                    </div>
                </div>
                <div class="text-white text-[0.8rem] font-normal leading-[1rem] tracking-[0.1rem] text-center p-[2rem] flex items-end justify-center">
                    <span>
                        We make teaching music fun and help educators build thriving schools.
                        <br><br>
                        Our preschool program and interactive app give teachers ready-to-use lessons, management tools, and marketing resources, while students practice music theory in a fun, engaging way.
                    </span>
                </div>
            </div>
            <div class="flex absolute left-0 top-0 right-0 bottom-0 items-center justify-end z-1 overflow-hidden">
                <img src="{{ asset('images/about.png') }}" alt="Welcome" class="p-[3em] h-full opacity-100 transform">
            </div>
        </div>



        <!-- Page 2 -->
        <x-page 
            style="dark"
            title=""
            subtitle="">
            <x-slot:title>
                <div class="flex text-center items-center justify-center w-full text-one">
                    <h4>Whether you run a microschool, early learning center, or teach independently, Clef Play makes it simple to add a high-quality music and creative enrichment program.</h4>
                </div>

                <div class="flex text-center items-center justify-center w-full text-two">
                    <h4>Story-based lessons engage children while giving educators clear, structured sessions they can deliver with confidence.</h4>
                </div>
                <div class="flex text-center items-center justify-center w-full text-three">
                    <h4>All core materials are provided, so you can focus on teaching, not planning.</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-col w-full items-center justify-center">
                    <span class="text-center text-five font-bold text-center">See how Clef Play could work in your setting</span>
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Book a private conversation" url="/bookacall" light="false" />
            </x-slot:buttons>
        </x-page>



        <!-- Page 3 -->
        <x-page style="light">
            <x-slot:content>
                <div class="flex flex-col gap-[2rem]">
                    <div class="sss flex flex-row w-full h-full items-stretch justify-start gap-[2%]">
                        <div class="flex-1 w-1/2">
                            <img src="{{ asset('images/team1.png') }}" alt="Team 1" class="w-full object-cover border-[0.3rem] border-[#fff] shadow-xl aspect-3/4">
                        </div>
                        <div class="flex-1 flex flex-col w-1/2 items-center justify-start p-[2rem]">
                            <h4>Alexandra Dascal</h4>
                            <h5>(Founder)</h5>
                            <span class="font-family-two">
                                We make teaching music fun for kids and help educators build thriving music school.<br><br>
                                Our preschool program equips teachers with everything they need to succeed, including engaging games, ready-to-use lesson plans, detailed marketing materials, and tools to grow their music school with ease.                        
                            </span>
                        </div>
                    </div>

                    <div class="sss flex flex-row w-full h-full items-stretch justify-start gap-[2%]">
                        <div class="flex-1 w-1/2">
                            <img src="{{ asset('images/team2.png') }}" alt="Team 1" class="w-full object-cover border-[0.3rem] border-[#fff] shadow-xl aspect-3/4">
                        </div>
                        <div class="flex-1 flex flex-col w-1/2 items-center justify-start p-[2rem]">
                            <h4>Daniel Toma</h4>
                            <h5>(Co-Founder & Software Developer)</h5>
                        </div>
                    </div>

                    <div class="sss flex flex-row w-full h-full items-stretch justify-start gap-[2%]">
                        <div class="flex-1 w-1/2">
                            <img src="{{ asset('images/team3.png') }}" alt="Team 1" class="w-full object-cover border-[0.3rem] border-[#fff] shadow-xl aspect-3/4">
                        </div>
                        <div class="flex-1 flex flex-col w-1/2 items-center justify-start p-[2rem]">
                            <h4>Maria Bejan</h4>
                            <h5>(Sales Representative)</h5>
                        </div>
                    </div>

                    <div class="sss flex flex-row w-full h-full items-stretch justify-start gap-[2%]">
                        <div class="flex-1 w-1/2">
                            <img src="{{ asset('images/team4.png') }}" alt="Team 1" class="w-full object-cover border-[0.3rem] border-[#fff] shadow-xl aspect-3/4">
                        </div>
                        <div class="flex-1 flex flex-col w-1/2 items-center justify-start p-[2rem]">
                            <h4>Nicole Ouellette</h4>
                            <h5>(Marketing Specialist)</h5>
                            <span class="font-family-two">
                                Nicole Ouellette is the owner of TechNicole Support (formerly Breaking Even Communications), a marketing company she started in January of 2008. 
                                <br><br>
                                Since then, she’s worked with hundreds of clients on marketing strategy and implementation on everything from developing websites to writing newsletters to creating social media content to optimizing local business listings. She’s mainly worked with small businesses to make the most of their marketing efforts and budgets. 
                                <br><br>
                                Besides marketing, Nicole also owns two coworking spaces and a trending video service called Trend To Send. An amateur stand-up comedian, Nicole lives in upstate New York with her short dog.                        
                            </span>
                        </div>
                    </div>
                </div>
            </x-slot:content>
        </x-page>



        <!-- Page 4 -->
        <x-page style="dark">
            <x-slot:title>
                <div class="flex text-center items-center justify-center w-full text-one">
                    <h4>OUR PROMISE</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-col w-full items-center justify-center">
                    <span class="text-center text-five font-bold">We empower teachers, inspire students, and help build thriving music schools - creating a meaningful, joyful experience for everyone involved.</span>
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Contact us" url="/contact" light="false" />
            </x-slot:buttons>
        </x-page>



        <!-- Footer -->
        @include('layouts.footer')

    </div>
@endsection 