<!DOCTYPE html>
<html>
<head>
    <title>アカウント作成</title>
</head>
<body>
    <h1>{{ $user->name }}さん</h1>
    <p>アカウントが作成されました。下記のURLでログイン後パスワードを変更してください。</p>
    <p><a href="{{ $loginUrl }}">ログインはこちら</a></p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>
</body>
</html>
