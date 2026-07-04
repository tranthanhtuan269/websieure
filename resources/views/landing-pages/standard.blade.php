@extends('layouts.landing')

@section('content')
<article class="lp-standard">
    <header class="lp-hero lp-hero--text">
        <div class="lp-hero__overlay">
            <div class="lp-container">
                <h1>{{ $page->title }}</h1>
                @if($page->intro)
                    <p class="lp-lead">{{ \Illuminate\Support\Str::limit(strip_tags($page->intro), 220) }}</p>
                @endif
                <a href="{{ $affiliateUrl }}" class="btn btn-primary lp-cta">See it in action</a>
            </div>
        </div>
    </header>

    @if($page->intro)
    <section class="lp-section lp-section--intro">
        <div class="lp-container lp-prose">
            {!! nl2br(e($page->intro)) !!}
        </div>
    </section>
    @endif

    @foreach($page->sections ?? [] as $index => $section)
    <section class="lp-section {{ $index % 2 === 1 ? 'lp-section--alt' : '' }}">
        <div class="lp-container lp-section__grid {{ $index % 2 === 1 ? 'lp-section__grid--reverse' : '' }}">
            <figure class="lp-section__media">
                <img src="{{ $section['image'] ?? 'assets/images/image-' . ($index + 1) . '.jpg' }}"
                     alt="{{ $section['title'] ?? $page->title }}"
                     loading="lazy">
            </figure>
            <div class="lp-prose">
                <h2>{{ $section['title'] ?? 'Section ' . ($index + 1) }}</h2>
                {!! nl2br(e($section['content'] ?? '')) !!}
                <a href="{{ $affiliateUrl }}" class="btn btn-primary lp-cta lp-cta--inline">Get a free consultation</a>
            </div>
        </div>
    </section>
    @endforeach

    <footer class="lp-footer">
        <div class="lp-container">
            <h2>Ready to get started?</h2>
            <p>Pick the right theme and launch a professional site in 24 hours.</p>
            <a href="{{ $affiliateUrl }}" class="btn btn-primary lp-cta">Shop themes now →</a>
        </div>
    </footer>
</article>
@endsection
