@extends('layouts.app')

@section('title', $category->name)

@section('content')
<section class="section">
    <div class="container">
        <p><a href="{{ route('categories.index') }}">← Tất cả chủ đề</a></p>
        <h1 style="margin:.5rem 0;">{{ $category->icon }} {{ $category->name }}</h1>
        <p style="color:var(--muted);margin-bottom:1.5rem;">{{ $category->description }}</p>

        @if($themes->isEmpty())
            <p>Chưa có theme trong chủ đề này.</p>
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
