@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <h1>Login</h1>
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>
@endsection

@section('styles')
<style>
    body {
        background-color: #ffffff;
        color: #333;
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 400px;
        margin: 100px auto;
        padding: 20px;
        border: 1px solid #e3e3e3;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    .container h1 {
        color: #4caf50;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
    }
    .form-group input {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
    }
    .btn {
        width: 100%;
        padding: 10px;
        background-color: #4caf50;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #45a049;
    }
</style>
@endsection
@push('scripts')
@endpush