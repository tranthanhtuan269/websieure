@extends('layouts.landing')

@section('content')
<article class="lp-scroll">
    <header class="lp-scroll__hero">
        @if($page->hero_image)
            <img src="{{ $page->hero_image }}" alt="{{ $page->title }}">
        @endif
        <div class="lp-scroll__hero-text">
            <div class="lp-container">
                <h1>{{ $page->title }}</h1>
            </div>
        </div>
    </header>

    <div class="lp-container">
        <div class="lp-prose lp-scroll__body">
            @if($page->body)
                {!! nl2br(e($page->body)) !!}
            @endif
        </div>

        <div class="lp-scroll__cta">
            <a href="{{ $affiliateUrl }}" class="btn btn-primary lp-cta">Xem theme phù hợp →</a>
        </div>
    </div>
</article>
@endsection
