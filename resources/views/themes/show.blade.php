@extends('layouts.app')

@section('title', $theme->name)
@section('meta_description', $theme->tagline)

@section('content')
<section class="section">
    <div class="container detail-layout">
        <div>
            @if($theme->thumbnailUrl())
                <div class="detail-preview detail-preview--image">
                    <img src="{{ $theme->thumbnailUrl() }}" alt="Ảnh demo {{ $theme->name }}">
                </div>
                @if($theme->preview_url)
                    <div class="live-preview" style="margin-top:1rem;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;gap:.5rem;flex-wrap:wrap;">
                            <strong>Xem trực tiếp</strong>
                            <a href="{{ $theme->preview_url }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm">Mở demo full màn hình ↗</a>
                        </div>
                        <iframe src="{{ $theme->preview_url }}" title="Demo {{ $theme->name }}" loading="lazy" sandbox="allow-scripts allow-same-origin allow-forms allow-popups"></iframe>
                    </div>
                @endif
            @else
                <div class="detail-preview" style="background: linear-gradient(135deg, {{ $theme->thumbnail_color }}, {{ $theme->category->color }});">
                    {{ $theme->thumbnail_label ?: $theme->name }}
                </div>
            @endif
            <div style="margin-top:1rem;">
                <p style="color:var(--muted);margin-bottom:.5rem;">Chủ đề: <a href="{{ route('categories.show', $theme->category) }}">{{ $theme->category->name }}</a></p>
                <p>{{ $theme->description }}</p>
                @if($theme->features)
                    <h3 style="margin:1rem 0 .5rem;">Tính năng</h3>
                    <ul class="feature-list">
                        @foreach($theme->features as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <aside class="detail-panel">
            <h1 style="font-size:1.5rem;margin-bottom:.35rem;">{{ $theme->name }}</h1>
            <p style="color:var(--muted);margin-bottom:1rem;">{{ $theme->tagline }}</p>
            <div class="price" style="font-size:1.4rem;margin-bottom:1rem;">
                @if($theme->isOnSale())
                    <span class="price-old">{{ number_format($theme->price, 0, ',', '.') }} ₫</span>
                    <span class="badge-sale" style="position:static;display:inline-block;margin-left:.35rem;">-{{ $theme->discountPercent() }}%</span><br>
                @endif
                {{ $theme->formattedPrice() }}
            </div>
            <div style="display:flex;flex-direction:column;gap:.6rem;">
                <a href="{{ route('checkout.create', $theme) }}" class="btn btn-primary">Mua theme ngay</a>
                @if($theme->preview_url)
                    <a href="{{ $theme->preview_url }}" target="_blank" rel="noopener" class="btn btn-outline">Xem demo</a>
                @endif
            </div>
            <p style="margin-top:1rem;font-size:.88rem;color:var(--muted);">✓ Giao source trong 24h &nbsp; ✓ Hỗ trợ cài đặt &nbsp; ✓ Tùy chỉnh logo/màu</p>
        </aside>
    </div>
</section>

@if($related->isNotEmpty())
<section class="section" style="padding-top:0;">
    <div class="container">
        <h2 style="margin-bottom:1rem;">Theme cùng chủ đề</h2>
        <div class="theme-grid">
            @foreach($related as $item)
                @include('partials.theme-card', ['theme' => $item])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
