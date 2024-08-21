<!-- resources/views/admin/users/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center">新規ユーザ作成</h2>
    <form method="POST" action="{{ route('admin.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">ユーザ名</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="name">組織名</label>
            <input type="text" class="form-control" id="oganization" name="oganization" required>
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">作成</button>
    </form>
</div>
@endsection

@section('styles')
<style>
    body {
        background-color: #f8f9fa;
    }
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .text-center {
        color: #4caf50;
    }
    .form-group {
        display: grid;
        margin-bottom: 1.5rem;
    }
    .form-group label {
        color: #4caf50;
    }
    .form-control {
        border: 1px solid #ced4da;
        border-radius: 5px;
        height: 40px;
    }
    .btn-primary {
        background-color: #8bc34a;
        border-color: #8bc34a;
        border-radius: 5px;
        color: #fff;
        font-weight: bold;
        padding: 10px 20px;
    }
    .btn-primary:hover {
        background-color: #7cb342;
        border-color: #7cb342;
    }
</style>
@endsection
