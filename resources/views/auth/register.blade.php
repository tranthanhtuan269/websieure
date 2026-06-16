@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<section class="section">
    <div class="container" style="max-width:420px;">
        <h1 style="margin-bottom:.35rem;">Đăng ký</h1>
        <p style="color:var(--muted);margin-bottom:1.25rem;">Tạo tài khoản mới tại {{ config('site.name') }}</p>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:1rem;">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="form-card">
            @csrf
            <div class="form-group">
                <label for="name">Họ tên</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Đăng ký</button>
        </form>

        <p style="margin-top:1rem;text-align:center;color:var(--muted);font-size:.92rem;">
            Đã có tài khoản?
            <a href="{{ route('login') }}">Đăng nhập</a>
        </p>
    </div>
</section>
@endsection
