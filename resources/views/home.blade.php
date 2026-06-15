@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Theme website theo chủ đề — giá siêu rẻ, giao nhanh</h1>
        <p>Chọn giao diện phù hợp ngành nghề: bán hàng, nhà hàng, spa, bất động sản, giáo dục... Cài đặt và tùy chỉnh trong 24h.</p>
        <div class="hero-actions">
            <a href="{{ route('themes.index') }}" class="btn btn-primary">Xem kho theme</a>
            <a href="{{ route('categories.index') }}" class="btn btn-outline">Duyệt theo chủ đề</a>
        </div>
        <form class="search-bar" action="{{ route('themes.index') }}" method="GET">
            <input type="search" name="q" placeholder="Tìm theme: shop thời trang, nhà hàng, landing page...">
            <button type="submit">Tìm</button>
        </form>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Chủ đề phổ biến</h2>
            <a href="{{ route('categories.index') }}">Xem tất cả →</a>
        </div>
        <div class="category-grid">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="category-card">
                    <div class="category-icon">{{ $category->icon }}</div>
                    <strong>{{ $category->name }}</strong>
                    <span>{{ $category->themes_count }} theme</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

@if($featuredThemes->isNotEmpty())
<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="section-head">
            <h2>Theme nổi bật</h2>
            <a href="{{ route('themes.index', ['sort' => 'popular']) }}">Xem thêm →</a>
        </div>
        <div class="theme-grid">
            @foreach($featuredThemes as $theme)
                @include('partials.theme-card', ['theme' => $theme])
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="section-head">
            <h2>Mới cập nhật</h2>
            <a href="{{ route('themes.index') }}">Xem tất cả →</a>
        </div>
        <div class="theme-grid">
            @foreach($latestThemes as $theme)
                @include('partials.theme-card', ['theme' => $theme])
            @endforeach
        </div>
    </div>
</section>
@endsection
