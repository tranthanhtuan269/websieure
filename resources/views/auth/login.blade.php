@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<section class="section">
    <div class="container" style="max-width:420px;">
        <h1 style="margin-bottom:.35rem;">Đăng nhập</h1>
        <p style="color:var(--muted);margin-bottom:1.25rem;">Truy cập tài khoản {{ config('site.name') }}</p>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:1rem;">
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
            <label style="display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;font-size:.92rem;">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                Ghi nhớ đăng nhập
            </label>
            <button type="submit" class="btn btn-primary" style="width:100%;">Đăng nhập</button>
        </form>

        <p style="margin-top:1rem;text-align:center;color:var(--muted);font-size:.92rem;">
            Chưa có tài khoản?
            <a href="{{ route('register') }}">Đăng ký ngay</a>
        </p>
    </div>
</section>
@endsection
