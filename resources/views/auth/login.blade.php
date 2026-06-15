<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="container" style="max-width:400px;padding:3rem 1rem;">
    <h1 style="margin-bottom:1rem;">Admin — {{ config('site.name') }}</h1>
    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('login') }}" class="form-card">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Đăng nhập</button>
    </form>
</div>
</body>
</html>
