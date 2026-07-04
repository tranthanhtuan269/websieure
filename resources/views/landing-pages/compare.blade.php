@extends('layouts.landing')

@section('content')
@php
    $compare = $compare ?? [];
    $productName = $compare['product_name'] ?? $page->title;
    $competitorName = $compare['competitor_name'] ?? 'Other options';
    $rows = $compare['rows'] ?? [];
@endphp
<article class="lp-compare">
    <header class="lp-compare__hero">
        @if($page->hero_image)
            <img src="{{ $page->hero_image }}" alt="{{ $page->title }}">
        @endif
        <div class="lp-compare__hero-text">
            <div class="lp-container">
                <h1>{{ $page->title }}</h1>
                @if(!empty($compare['intro']))
                    <p class="lp-compare__intro">{{ $compare['intro'] }}</p>
                @endif
            </div>
        </div>
    </header>

    <div class="lp-container">
        <div class="lp-compare__table-wrap">
            <table class="lp-compare__table">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th class="lp-compare__col-ours">{{ $productName }}</th>
                        <th class="lp-compare__col-theirs">{{ $competitorName }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    <tr>
                        <td>{{ $row['feature'] ?? '' }}</td>
                        <td class="lp-compare__cell-yes">{!! ($row['ours'] ?? false) ? '&#10003;' : '&#10007;' !!}</td>
                        <td class="lp-compare__cell-no">{!! ($row['theirs'] ?? false) ? '&#10003;' : '&#10007;' !!}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">Comparison table coming soon...</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!empty($compare['summary']))
        <div class="lp-prose lp-compare__summary">
            {!! nl2br(e($compare['summary'])) !!}
        </div>
        @endif

        <div class="lp-compare__cta">
            <h2>{{ $compare['cta_title'] ?? 'Choose the better option today' }}</h2>
            <p>{{ $compare['cta_text'] ?? 'Sign up now and claim your exclusive offer.' }}</p>
            <a href="{{ $affiliateUrl }}" class="btn btn-primary lp-cta">{{ $compare['cta_button'] ?? 'Claim your offer →' }}</a>
        </div>
    </div>
</article>
@endsection
