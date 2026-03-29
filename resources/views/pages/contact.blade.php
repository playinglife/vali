@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="w-full h-full z-1">
        <!-- Page 1 -->
        <div class="aaa flex w-full h-screen items-center justify-center overflow-hidden relative">
            <div class="flex flex-col absolute left-[40%] top-[50%] h-[80%] items-center justify-between transform -translate-x-1/2 -translate-y-1/2 z-2">
                <div class="flex flex-1 flex-col items-center justify-center">
                    <div class="flex text-white text-center items-start w-full leading-[4.8rem]">
                        <h1 class="!text-[5rem] font-bold">GET IN TOUCH</h1>
                    </div>
                    <div class="flex text-center items-start w-full text-tree">
                        <h3>We'd love to hear from you!</h3>
                    </div>
                </div>
            </div>
            <div class="flex absolute left-0 top-0 right-0 bottom-0 items-center justify-end z-1 overflow-hidden">
                <img src="{{ asset('images/envelope.png') }}" alt="Welcome" class="p-[3em] h-full opacity-100 transform">
            </div>
        </div>

        <!-- Page 2 -->
        <x-page style="light">
            <x-slot:title>
            <div class="flex text-center items-center justify-center w-full text-one">
                    <h4>Questions about the program?</h4>
                </div>

                <div class="flex text-center items-center justify-center w-full text-two">
                    <h4>Want to discuss your classroom or business?</h4>
                </div>
                <div class="flex text-center items-center justify-center w-full text-three">
                    <h4>Reach out and we’ll guide you.</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex p-[2rem] flex-col items-center justify-center gap-[0.5rem] w-full">
                    <span class="text-center text-five font-bold">Email us directly at: <a href="mailto:info@clefplay.com">info@clefplay.com</a></span>
                    @if(false)
                    @if($errors->any())
                        <div class="w-[50%] p-[0.5rem] bg-red-100 border border-red-400 text-red-700 rounded-md text-sm mb-2">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('contact.submit') }}" method="POST" class="flex flex-col items-center justify-center gap-[0.5rem] w-full">
                        @csrf
                        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required class="w-[50%] p-[0.2rem] border-1 border-gray-300 rounded-md text-sm" />
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required class="w-[50%] p-[0.2rem] border-1 border-gray-300 rounded-md text-sm" />
                        <textarea name="message" placeholder="Message" required class="w-[50%] p-[0.2rem] border-1 border-gray-300 rounded-md text-sm min-h-[4rem]">{{ old('message') }}</textarea>
                        <span>We typically respond within 24 hours.</span>
                        <x-button text="Send" type="submit" light="false" :url="null" />
                    </form>
                    @endif
                </div>
            </x-slot:content>
        </x-page>



        <!-- Page 3 -->
        <x-page 
            style="dark"
            title=""
            subtitle="">
            <x-slot:title>
                <div class="flex text-center items-center justify-center w-full text-three">
                    <h4>OR BOOK A FREE DISCOVERY CALL</h4>
                </div>
            </x-slot:title>
            <x-slot:content>
                <div class="flex flex-col w-full items-start justify-center gap-[0.2em]">
                    <div class="flex-1">
                        <span class="text-one font-bold">Get clarity on pricing and how to structure your sessions.</span>
                    </div>
                    <div class="flex-1">
                        <span class="text-two font-bold">Receive a simple plan to test demand and see if you can fill your first group — even in a small town</span>
                    </div>
                    <div class="flex-1">
                        <span class="text-three font-bold">Understand how Clef Play would work in your specific situation</span>
                    </div>
                    <div class="flex-1">
                        <span class="text-five font-bold">Decide whether this program is the right next step for you</span>
                    </div>
                </div>
            </x-slot:content>
            <x-slot:buttons>
                <x-button text="Book a free call" url="/bookacall" light="false" />
            </x-slot:buttons>
        </x-page>
        
        

    @include('layouts.footer')

</div>
@endsection 