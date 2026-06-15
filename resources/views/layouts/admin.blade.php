<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <strong style="display:block;margin-bottom:1rem;">{{ config('site.name') }} Admin</strong>
        <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
        <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])>Chủ đề</a>
        <a href="{{ route('admin.themes.index') }}" @class(['active' => request()->routeIs('admin.themes.*')])>Themes</a>
        <a href="{{ route('admin.orders.index') }}" @class(['active' => request()->routeIs('admin.orders.*')])>Đơn hàng</a>
        <hr style="border-color:#374151;margin:1rem 0;">
        <a href="{{ route('home') }}">← Về website</a>
        <form action="{{ route('logout') }}" method="POST" style="margin-top:.5rem;">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm" style="width:100%;">Đăng xuất</button>
        </form>
    </aside>
    <main class="admin-main">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
