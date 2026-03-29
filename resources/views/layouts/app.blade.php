<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Clef Play - @yield('title')</title>
        @vite(['resources/css/app.css','resources/js/app.js'])
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    </head>
    <body>
        <div class="flex flex-col w-full h-full overflow-y-auto bg-black">
            <!-- Menu -->
            @include('layouts.menu')
            <!-- Content -->
            <div id="main-content" class="w-full h-full flex absolute top-0 left-0 right-0 bottom-0 z-1 overflow-y-auto">
            <!--<div class="flex flex-1 relative">-->
                @yield('content')
            </div>
        </div>
    </body>
</html>