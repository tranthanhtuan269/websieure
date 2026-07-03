@extends('layouts.landing')

@push('styles')
<style>
.lp-popup-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; }
.lp-popup-page img { width: 100%; height: 100vh; object-fit: cover; position: fixed; inset: 0; }
</style>
@endpush

@section('content')
@php
    $popup = $page->popup_settings ?? [];
@endphp
<div class="lp-popup-page">
    @if($page->hero_image)
        <img src="{{ $page->hero_image }}" alt="{{ $page->title }}">
    @endif

    <div class="cookie-overlay" id="cookieOverlay" aria-hidden="false">
        <div class="cookie-modal" role="dialog" aria-labelledby="cookieTitle" aria-modal="true">
            <div class="cookie-modal__icon">🍪</div>
            <h2 id="cookieTitle">{{ $popup['title'] ?? 'Cookie Settings' }}</h2>
            <p>{{ $popup['message'] ?? 'We use cookies to improve your experience.' }}</p>
            <div class="cookie-modal__actions">
                <button type="button" class="btn btn-primary" id="cookieAccept">
                    {{ $popup['button_text'] ?? 'Yes, I accept' }}
                </button>
                <button type="button" class="btn btn-outline" id="cookieDecline">
                    {{ $popup['decline_text'] ?? 'Manage preferences' }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var affiliateUrl = @json($affiliateUrl);
    var delay = {{ (int) config('landing.popup_delay_ms', 800) }};

    function goAffiliate() {
        window.location.href = affiliateUrl;
    }

    setTimeout(function () {
        document.getElementById('cookieOverlay').classList.add('is-visible');
    }, delay);

    document.getElementById('cookieAccept').addEventListener('click', goAffiliate);
    document.getElementById('cookieDecline').addEventListener('click', goAffiliate);
})();
</script>
@endpush
