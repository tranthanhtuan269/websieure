<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if(!empty($staticExport))
        <link rel="stylesheet" href="{{ $cssApp }}">
        <link rel="stylesheet" href="{{ $cssLanding }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
    @endif
    @stack('styles')
</head>
<body class="lp-body">
@include('admin.landing-pages._review-bar')
@yield('content')
@stack('scripts')
</body>
</html>
