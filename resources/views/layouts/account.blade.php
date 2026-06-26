<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Tài khoản</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
@include('partials.header')
<main>
    <div class="container account-layout">
        <aside class="account-sidebar">
            <strong style="display:block;margin-bottom:1rem;">Tài khoản</strong>
            <a href="{{ route('account.dashboard') }}" @class(['active' => request()->routeIs('account.dashboard')])>Tổng quan</a>
            <a href="{{ route('account.orders.index') }}" @class(['active' => request()->routeIs('account.orders.*')])>Đơn hàng</a>
            <a href="{{ route('account.affiliate.index') }}" @class(['active' => request()->routeIs('account.affiliate.*')])>Affiliate</a>
            <a href="{{ route('account.payouts.index') }}" @class(['active' => request()->routeIs('account.payouts.*')])>Rút tiền</a>
            @if(auth()->user()->isAdmin())
                <hr style="border-color:var(--border);margin:1rem 0;">
                <a href="{{ route('admin.dashboard') }}">← Vào Admin</a>
            @endif
        </aside>
        <section class="account-main">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
            @yield('content')
        </section>
    </div>
</main>
@include('partials.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
