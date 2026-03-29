@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="w-full h-full z-1">


        <!-- Page 1 -->
        <div class="aaa flex w-full h-screen items-center justify-center overflow-hidden relative">
            <div class="flex flex-col absolute left-[40%] top-[50%] h-[80%] items-center justify-between transform -translate-x-1/2 -translate-y-1/2 z-2">
                <div class="flex flex-1 flex-col items-center justify-center">
                    <div class="flex text-white items-start w-full">
                        <h2 class="leading-[1.8rem] tracking-[0.1rem]">CLEF PLAY</h2>
                    </div>
                    <div class="flex text-white text-center items-start w-full leading-[4.8rem]">
                        <h1 class="!text-[5rem] font-bold">PROGRAM</h1>
                    </div>
                    <div class="flex text-center items-start w-full text-tree">
                        <h3>Built to teach. Designed to earn.</h3>
                    </div>
                </div>
                <div class="p-[2rem]">
                    <div class="text-white text-[0.8rem] font-normal text-center flex items-end justify-center">
                        <h5 class="!font-normal">Launch a paid enrichment program for 3-4 year olds using a ready-to-run system designed to generate income.</h5>
                    </div>
                    <div class="text-white text-[0.8rem] font-normal text-center flex items-end justify-center">
                        <h5>
                            <span class="text-tree">Ready-to-run.</span>
                            <span class="text-one">Simple to deliver. </span>
                            <span class="text-two">No music background required.</span>
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
                    <h4>Fill your first class</h4>
                </div>

                <div class="flex text-center items-center justify-center w-full text-two">
                    <h4>Attract paying families</h4>
                </div>
                <div class="flex text-center items-center justify-center w-full text-three">
                    <h4>Ready-to-run system</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-row w-full items-center justify-center gap-[2em]">
                    <x-card image="images/image1.jpg" imagePosition="left" class="cursor-pointer" 
                        title="Kindergarten, Daycare Owners & Microschool"
                        text="Add a high-value enrichment program that parents understand and are willing to pay for, without adding complexity to your team"
                        color="one"
                        ></x-card>
                    <x-card image="images/image2.jpg" imagePosition="center" class="cursor-pointer" 
                        title="Educators"
                        text="Launch a ready-to-run program you can teach with confidence and turn it into a reliable income stream."
                        color="two"
                        ></x-card>
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Book a private conversation" url="/bookacall" light="false" />
            </x-slot:buttons>
        </x-page>



        <!-- Page 3 -->
        <x-page 
            style="dark"
            title=""
            subtitle="">
            <x-slot:title>
                <div class="flex text-center items-center justify-center w-full text-one">
                    <h4>Simple sessions</h4>
                </div>

                <div class="flex text-center items-center justify-center w-full text-two">
                    <h4>Real results</h4>
                </div>
                <div class="flex text-center items-center justify-center w-full text-three">
                    <h4>Designed for meaningful learning</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-row w-full items-center justify-center gap-[2em]">
                    <img src="{{ asset('images/image3.jpg') }}" alt="Simple sessions" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-row w-full items-start justify-center gap-[2em] pt-[5em]">
                    <div class="flex-1">
                        <span class="text-one font-bold">Stories</span> Story-based lessons to learn piano keys, rhythm, and music fundamentals.
                    </div>
                    <div class="flex-1">
                        <span class="text-two font-bold">Music</span> Rhythm, melody, and sound are tied to real music theory  understanding, not just games.
                    </div>
                </div>
                <div class="flex flex-row w-full items-start justify-center gap-[0.5em] pt-[2em]">
                    <div class="flex-1">
                        <span class="text-three font-bold">Growth</span> Focus, confidence, cooperation, and creative thinking develop over time.
                    </div>
                    <div class="flex-1">
                        <span class="text-five font-bold">Ready to Teach</span> Step-by-step plans and materials that make delivery simple.
                    </div>
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Get the free guide" url="/bookacall" light="false" />
            </x-slot:buttons>
        </x-page>



        <!-- Page 4 -->
        <x-page 
            style="light"
            title=""
            subtitle="">
            <x-slot:title>
                <div class="flex text-center items-center justify-center w-full text-one">
                    <h4>What you get with the Clef Play Program</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-row w-full items-center justify-center">
                    <img src="{{ asset('images/image4.jpg') }}" alt="What you get with the Clef Play Program" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-row w-full text-center items-center justify-center pt-[5em] text-one">
                    <h4>A complete system to learn, launch, and run your own enrichment program.</h4>
                </div>

                <div class="flex flex-row w-full items-start justify-center gap-[2em] pt-[5em]">
                    <div class="flex-1">
                        <span class="text-one font-bold">Program materials</span> Books, printables, and activities ready to use
                    </div>
                    <div class="flex-1">
                        <span class="text-two font-bold">Live guidance</span> Step-by-step teaching of what and how to deliver the program
                    </div>
                </div>
                <div class="flex flex-row w-full items-start justify-center gap-[0.5em] pt-[2em]">
                    <div class="flex-1">
                        <span class="text-three font-bold">Launch support</span> Tips to attract families and fill your first class
                    </div>
                    <div class="flex-1">
                        <span class="text-five font-bold">Ongoing resources</span> Access to new activities and community support
                    </div>
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Book a private conversation" url="/book-a-private-conversation" light="false" />
            </x-slot:buttons>
        </x-page>



        <!-- Page 5 -->
        <x-page 
            style="dark"
            title=""
            subtitle="">
            <x-slot:title>
                <div class="flex text-center items-center justify-center w-full text-one">
                    <h4>Questions & Answers</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-row w-full items-center justify-center">
                    <img src="{{ asset('images/image5.jpg') }}" alt="Questions & Answers" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col w-full items-start justify-center gap-[2em] pt-[5em]">
                    <div class="flex-1 flex flex-col items-start justify-start">
                        <span class="text-two font-bold">Do I need a music degree to run this program?</span>
                        <span class="text-five">
                            <span class="text-two font-bold">No</span> - Clef Play is ready-to-run and designed for educators of any background.
                        </span>
                    </div>

                    <div class="flex-1 flex flex-col items-start justify-start">
                        <span class="text-three font-bold">How long does it take to get started?</span>
                        <span class="text-five">
                            You can launch your first class within a few weeks with our step-by-step guidance.
                        </span>
                    </div>

                    <div class="flex-1 flex flex-col items-start justify-start">
                        <span class="text-one font-bold">Can this program fit into my existing microschool schedule?</span>
                        <span class="text-five">
                            <span class="text-one font-bold">Yes</span> - the system is flexible and designed to fit around your current classes.
                        </span>
                    </div>

                    <div class="flex-1 flex flex-col items-start justify-start">
                        <span class="text-two font-bold">Will this help me attract families?</span>
                        <span class="text-five">
                            <span class="text-two font-bold">Yes</span> - Clef Play is parent-friendly, easy to understand, and high-value. We guide you on how to reach families, promote your program, and enroll your first group.
                        </span>
                    </div>

                    <div class="flex-1 flex flex-col items-start justify-start">
                        <span class="text-three font-bold">What kind of support will I get after purchasing the program?</span>
                        <span class="text-five">
                            You’ll get full access to the program materials, step-by-step guidance, and our teachers’ community for ongoing support. We also provide new resources and updates as they become available, so you’ll never be on your own.
                        </span>
                    </div>

                    <div class="flex-1 flex flex-col items-start justify-start">
                        <span class="text-one font-bold">Is ClefPlay a franchise?</span>
                        <span class="text-five">
                            <span class="text-one font-bold">No</span> - Clef Play is a complete program that gives you everything you need to run your own music and enrichment classes. There are no ongoing fees, no franchise agreements—just the tools, guidance, and support to build your program and grow your business independently.
                        </span>
                    </div>

                    <div class="flex-1 flex flex-col items-start justify-start">
                        <span class="text-two font-bold">What Happens During the Discovery Call?</span>
                        <span class="text-five">
                            <ul class="leading-[1rem]"><li>Check the fit</li></ul>
                            <ul class="leading-[1rem]"><li>Test parent interest</li></ul>
                            <ul class="leading-[1rem]"><li>Move forward with confidence</li></ul>
                        </span>
                    </div>
                </div>
            </x-slot:content>
        </x-page>

        

        <!-- Footer -->
        @include('layouts.footer')

    </div>
@endsection 