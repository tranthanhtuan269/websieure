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
            @auth
                <span class="nav-user">{{ auth()->user()->name }}</span>
                <a href="{{ route('account.dashboard') }}" @class(['active' => request()->routeIs('account.*')])>Tài khoản</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.*')])>Admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="nav-logout">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">Đăng xuất</button>
                </form>
            @else
                <a href="{{ route('login') }}" @class(['active' => request()->routeIs('login')])>Đăng nhập</a>
                <a href="{{ route('register') }}" @class(['btn btn-primary btn-sm', 'active' => request()->routeIs('register')])>Đăng ký</a>
            @endauth
        </nav>
    </div>
</header>
