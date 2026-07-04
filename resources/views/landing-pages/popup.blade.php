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

    <div class="cookie-overlay cookie-overlay--center" id="cookieOverlay" aria-hidden="false">
        <div class="cookie-modal cookie-modal--notice" role="dialog" aria-labelledby="cookieTitle" aria-modal="true">
            <div class="cookie-modal__header">
                <h2 id="cookieTitle">{{ $popup['title'] ?? 'Cookie Notice' }}</h2>
                <button type="button" class="cookie-modal__close" id="cookieClose">Close</button>
            </div>
            <p class="cookie-modal__body">
                {{ $popup['message'] ?? 'This website uses cookies to personalize content and ads, provide social media features, and analyze our traffic. By clicking Accept, you agree to the use of cookies. For more information, visit our Cookie Policy' }}
                @if(!empty($popup['policy_url']))
                    <a href="{{ $popup['policy_url'] }}" class="cookie-modal__link">Cookie Policy</a>
                @else
                    <a href="{{ $affiliateUrl }}" class="cookie-modal__link">Cookie Policy</a>
                @endif
            </p>
            <button type="button" class="cookie-modal__accept" id="cookieAccept">
                {{ $popup['button_text'] ?? 'Accept and Continue' }}
            </button>
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
    document.getElementById('cookieClose').addEventListener('click', goAffiliate);
})();
</script>
@endpush
