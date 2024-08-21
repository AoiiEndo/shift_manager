@extends('layouts.app')

@section('content')
<div class="container">
    <h2>ユーザ一覧</h2>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="text-center">
        <button class="btn-create-user" onclick="window.location.href='{{ route('admin.create') }}'">ユーザ作成</button>
    </div>
    <div class="table-container">
        <table id="usersTable" class="display">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>作成日時</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('styles')
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
}

.container {
    margin-top: 50px;
}

.text-center {
    text-align: center;
    margin-bottom: 20px;
}

.btn-create-user {
    background-color: #8bc34a; /* 黄緑色 */
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.btn-create-user:hover {
    background-color: #7cb342;
}

.table-container {
    margin: 0 auto;
    width: 80%;
}

table {
    width: 100%;
    border-collapse: collapse;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    background-color: white;
    border-radius: 5px;
    overflow: hidden;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
}

table thead th {
    background-color: #8bc34a; /* 黄緑色 */
    color: white;
}

table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tbody tr:hover {
    background-color: #eaf5ea;
}
</style>
@endsection