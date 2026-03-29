@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="w-full h-full z-1">
        <!-- Page 1 -->
        <div class="aaa flex w-full h-screen items-center justify-center overflow-hidden relative">
            <div class="flex flex-col absolute left-[40%] top-[50%] h-[80%] items-center justify-between transform -translate-x-1/2 -translate-y-1/2 z-2">
                <div class="flex flex-1 flex-col items-center justify-center">
                    <span class="text-white text-center items-center justify-center">
                        <h1 class="!text-[5rem] font-bold leading-[4.8rem]">CLEF PLAY<br>APP</h1>
                    </span>
                </div>
                <div class="text-white text-[0.8rem] font-normal leading-[1rem] tracking-[0.1rem] text-center p-[2rem] flex items-end justify-center">
                    <span>Explore music theory with our app.<br>Practice made fun and easy, like playing a game!</span>
                </div>
                <div>
                    <x-button text="Start a free trial" url="/start-a-music-school" />
                </div>
            </div>
            <div class="flex absolute left-0 top-0 right-0 bottom-0 items-center justify-end z-1">
                <img src="{{ asset('images/app.png') }}" alt="Music theory app" class="h-[80%] object-contain p-[5rem]">
            </div>
        </div>

        <!-- Page 2 -->
        <x-page 
            style="light"
            title="FOR STUDENTS"
            subtitle="Practice Music Theory, Play Games, and Shine Bright">
            <x-slot:content>
            <div class="flex p-[2rem]">
                    <div class="flex-1 justify-center items-center">
                        <ul>
                            <li class="font-family-two">Interactive games and stories make learning music fun</li>
                            <li class="font-family-two">Assignments and practice tracking for students</li>
                            <li class="font-family-two">Saves teachers time on lesson planning and admin</li>
                            <li class="font-family-two">Supports structured, playful lessons from the Clef Play program</li>
                            <li class="font-family-two">Parents can follow progress at a glance</li>
                        </ul>
                    </div>
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Take the app survey and join the waitlist" url="/download-the-app" light="false" />
            </x-slot:buttons>
        </x-page>

        <!-- Page 3 -->
        <x-page 
            style="dark"
            title="FOR TEACHERS"
            subtitle="Teach, Track, and Inspire — All in One Place">
            <x-slot:content>
                <div class="flex flex-col p-[2rem]">
                    The Clef Play App helps teachers guide lessons and students practice music through fun games and interactive activities.
                    <div class="divider"></div>
                    <h5>
                        Why Your Input Matters
                    </h5>
                    <span>  
                        We’re designing the app with teachers like you in mind. Take the quick survey and help us build the tools you’ll actually use.
                    </span>
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Take the app survey and join the waitlist" url="/download-the-app" light="false" />
            </x-slot:buttons>
        </x-page>

        <!-- Page 4 -->
        <x-page 
            style="light"
            title="FOR PARENTS"
            subtitle="The Clef Play App will bring at-home learning, musical games, and stories to your child’s fingertips.">
            <x-slot:content>
                <div class="flex p-[2rem]">
                    We’re shaping the Clef Play app and want your insights to make it perfect for both parents and teachers.
                    Your feedback will help us create features that parents love and students benefit from.
                </div>
                <h5>
                    Why Your Input Matters
                </h5>
                <span>  
                    Take our quick survey and join the waitlist to be among the first to experience the app!
                </span>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Take the survey" url="/download-the-app" light="false" />
            </x-slot:buttons>
        </x-page>


        <!-- Footer -->
        @include('layouts.footer')

    </div>
@endsection 