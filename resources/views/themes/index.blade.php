@extends('layouts.app')

@section('title', 'Kho theme')

@section('content')
<section class="section">
    <div class="container">
        <h1 style="margin-bottom:1rem;">Kho theme website</h1>

        <form class="filters" method="GET" action="{{ route('themes.index') }}">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Tìm theme..." style="min-width:220px;">
            <select name="category">
                <option value="">Tất cả chủ đề</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="sort">
                <option value="">Mới nhất</option>
                <option value="popular" @selected(request('sort') === 'popular')>Bán chạy</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá thấp → cao</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá cao → thấp</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
        </form>

        @if($themes->isEmpty())
            <p>Không tìm thấy theme phù hợp.</p>
        @else
            <div class="theme-grid">
                @foreach($themes as $theme)
                    @include('partials.theme-card', ['theme' => $theme])
                @endforeach
            </div>
            <div style="margin-top:1.5rem;">{{ $themes->links() }}</div>
        @endif
    </div>
</section>
@endsection
