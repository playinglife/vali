@extends('layouts.app')

@section('title', __('pages.thankyou.meta_title'))
@section('robots','noindex, nofollow')
@section('description', __('pages.thankyou.meta_description'))

@section('content')
    <div class="root-views-thankyou">
        <x-thankyou />
        @include('layouts.footer', ['backgroundImage' => 'none'])
    </div>
@endsection

@once
    <style lang="scss" scoped>
        .root-views-thankyou {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            flex: 1;
            overflow: auto;
            position: relative;
            isolation: isolate;
            min-height: 0;
            display: flex;
            flex-direction: column;
            &::before {
                content: '';
                position: fixed;
                inset: 0;
                background-image: url("{{ asset('images/thankyou.jpg') }}");
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
                filter: blur(8px);
                transform: scale(1.05);
                z-index: -1;
                pointer-events: none;
            }
            & > * {
                position: relative;
                z-index: 1;
            }
        }
    </style>
@endonce
