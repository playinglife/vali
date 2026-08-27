@extends('layouts.app')

@section('title', __('pages.contact_success.meta_title'))
@section('robots','noindex, nofollow')
@section('description', __('pages.contact_success.meta_description'))

@section('content')

    <div class="w-full h-full z-1">
        <!-- Success Page -->
        <div class="flex w-full h-screen items-center justify-center overflow-hidden relative">
            <div class="flex flex-col items-center justify-center text-center text-white">
                <h1 class="text-5xl font-bold text-white mb-[1rem]">Thank You!</h1>
                <span class="mb-[1.5rem]">Your message has been sent successfully.</span>
            </div>
        </div>
    </div>

@endsection

