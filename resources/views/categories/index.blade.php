@extends('layouts.app')

@section('title', 'Chủ đề theme')

@section('content')
<section class="section">
    <div class="container">
        <h1 style="margin-bottom:1rem;">Chọn theme theo chủ đề</h1>
        <div class="category-grid" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="category-card" style="text-align:left;">
                    <div class="category-icon">{{ $category->icon }}</div>
                    <strong>{{ $category->name }}</strong>
                    <p style="color:var(--muted);font-size:.88rem;margin:.35rem 0;">{{ $category->description }}</p>
                    <span>{{ $category->themes_count }} theme</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
