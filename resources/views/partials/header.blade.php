<header class="site-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="{{ route('home') }}" class="navbar-brand brand">
                <span class="brand-icon">WS</span>
                <span>
                    {{ config('site.name') }}
                    <small>{{ config('site.tagline') }}</small>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="siteNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" @class(['nav-link', 'active' => request()->routeIs('home')])>Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('themes.index') }}" @class(['nav-link', 'active' => request()->routeIs('themes.*')])>Kho theme</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('categories.index') }}" @class(['nav-link', 'active' => request()->routeIs('categories.*')])>Chủ đề</a>
                    </li>
                    @auth
                        <li class="nav-item d-lg-none">
                            <span class="nav-link nav-user disabled">{{ auth()->user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('account.dashboard') }}" @class(['nav-link', 'active' => request()->routeIs('account.*')])>Tài khoản</a>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" @class(['nav-link', 'active' => request()->routeIs('admin.*')])>Admin</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="nav-logout">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm w-100">Đăng xuất</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" @class(['nav-link', 'active' => request()->routeIs('login')])>Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" @class(['nav-link btn btn-primary btn-sm', 'active' => request()->routeIs('register')])>Đăng ký</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
