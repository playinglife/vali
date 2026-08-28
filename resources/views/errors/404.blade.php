@extends('layouts.app')

@section('title', __('pages.not_found.meta_title'))
@section('description', __('pages.not_found.meta_description'))
@section('robots', 'noindex, follow')

@section('content')
    <div class="root-views-not-found">
        <div class="grid root-views-not-found__grid">
            <div class="grid grid-middle grid-center root-views-not-found__title">
                <h1 class="dark"> {{ __('pages.not_found.title1') }} </h1>
            </div>
            <x-miniviews.panel :padding="false">
                <div class="root-views-not-found__panel">
                    <p class="text-small dark">{{ __('pages.not_found.message') }}</p>
                    <div class="root-views-not-found__actions">
                        <x-button text="{{ __('pages.footer.home') }}" url="{{ route('home') }}" light="false" />
                        <x-button text="{{ __('pages.home.cta_products') }}" url="{{ route('products.index') }}" light="false" />
                    </div>
                </div>
            </x-miniviews.panel>
        </div>
        @include('layouts.footer', ['backgroundImage' => 'none'])
    </div>
@endsection

@once
    <style lang="scss" scoped>
        .root-views-not-found {
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
                background-image: url("{{ asset('images/home.jpg') }}");
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
        .root-views-not-found__grid {
            flex: 1 0 auto;
            gap: var(--gap-large);
            padding: 4em 25% var(--padding-huge) 25%;
        }
        .root-views-not-found__title {
            padding-top: var(--padding-medium);
            & > h1 {
                color: var(--color-text-light);
            }
        }
        .root-views-not-found__panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--gap-medium);
            padding: var(--padding-huge);
            box-sizing: border-box;
            text-align: center;
        }
        .root-views-not-found__actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: var(--gap-medium);
        }
    </style>
@endonce
