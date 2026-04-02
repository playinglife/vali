<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script>document.documentElement.classList.add('js');</script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>ShirtHouse - @yield('title')</title>
        @vite(['resources/scss/app.scss','resources/js/app.js'])
        {{-- @livewireStyles --}}

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    </head>
    <body>
        <div class="root-views-layouts-app">
            <!-- Menu -->
            @include('layouts.menu')
            <!-- Content -->
            <div id="main-content" class="main-content">
            <!--<div class="flex flex-1 relative">-->
                @yield('content')
            </div>
        </div>
        {{-- @livewireScripts --}}
    </body>
</html>
@once
    <style lang="scss" scoped>
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .root-views-layouts-app {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            flex: 1;
            z-index: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            & > .main-content {
                width: 100%;
                height: 100%;
                box-sizing: border-box;
                flex: 1;
                z-index: 0;
                display: flex;
                flex-direction: column;
                overflow: auto;
                box-sizing: border-box;
            }
        }
    </style>
@endonce
