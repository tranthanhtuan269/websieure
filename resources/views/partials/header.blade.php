<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-icon">WS</span>
            <span>
                {{ config('site.name') }}
                <small>{{ config('site.tagline') }}</small>
            </span>
        </a>
        <nav class="nav">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Trang chủ</a>
            <a href="{{ route('themes.index') }}" @class(['active' => request()->routeIs('themes.*')])>Kho theme</a>
            <a href="{{ route('categories.index') }}" @class(['active' => request()->routeIs('categories.*')])>Chủ đề</a>
        </nav>
    </div>
</header>
