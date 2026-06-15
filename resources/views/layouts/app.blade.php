<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — {{ config('site.name') }}</title>
    <meta name="description" content="@yield('meta_description', config('site.tagline'))">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
</head>
<body>
@include('partials.header')
<main>
    @if(session('success'))
        <div class="container" style="padding-top:1rem;"><div class="alert alert-success">{{ session('success') }}</div></div>
    @endif
    @yield('content')
</main>
@include('partials.footer')
@stack('scripts')
</body>
</html>
