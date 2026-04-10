@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
    <div class="admin-login-page">
        <h1>Admin Login</h1>

        <form method="POST" action="{{ route('admin.login.submit') }}" class="admin-login-form">
            @csrf

            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>

            <label>
                <span>Password</span>
                <input type="password" name="password" required>
            </label>

            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>

            <button type="submit">Log In</button>
        </form>
    </div>
@endsection

@once
    <style>
        .admin-login-page {
            max-width: 560px;
            margin: 2rem auto;
            padding: 1rem;
        }

        .admin-login-form {
            display: grid;
            gap: 0.75rem;
        }

        .admin-login-form label {
            display: grid;
            gap: 0.35rem;
        }

        .admin-login-form input[type='email'],
        .admin-login-form input[type='password'] {
            width: 100%;
            padding: 0.5rem 0.6rem;
        }

        .admin-login-form .remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-login-form button {
            width: fit-content;
            padding: 0.55rem 1rem;
            cursor: pointer;
        }
    </style>
@endonce
