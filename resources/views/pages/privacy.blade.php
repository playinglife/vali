@extends('layouts.app')

@section('title', __('pages.privacy.meta_title'))
@section('description', __('pages.privacy.meta_description'))

@section('content')
    <div class="root-views-legal">
        <x-legal-page :title="__('pages.privacy.title1')" :body="__('pages.privacy.body')" />
        @include('layouts.footer', ['backgroundImage' => 'none'])
    </div>
@endsection

@once
    <style lang="scss" scoped>
        .root-views-legal {
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
                background-image: url("{{ asset('images/aboutus.jpg') }}");
                background-size: cover;
                background-position: top;
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
