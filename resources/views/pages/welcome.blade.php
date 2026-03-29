@extends('layouts.app')

@section('title','Dashboard')

@section('content')

    <div class="root-views-welcome">


        <!-- Page 1 -->
        <div class="">
        </div>

        <!-- Footer -->
        @include('layouts.footer')

    </div>
@endsection 

@once
    <style lang="scss" scoped>
        .root-views-welcome {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            background-image: url("{{ asset('images/home.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
@endonce