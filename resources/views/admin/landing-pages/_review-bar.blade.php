@if(!empty($reviewMode) && !empty($reviewGeneration))
<div class="lp-review-bar" id="lpReviewBar">
    <div class="lp-review-bar__inner">
        <div class="lp-review-bar__info">
            <strong>Review mode</strong>
            <span>{{ $reviewPageLabel ?? $reviewPageType }}</span>
            <a href="{{ route('admin.landing-pages.generator') }}" class="lp-review-bar__back">← Quay lại generator</a>
        </div>
        <button type="button" class="lp-review-bar__toggle" id="deployPanelToggle" aria-expanded="false">
            Đẩy lên hosting
        </button>
    </div>
    <div class="lp-review-bar__panel" id="deployPanel" hidden>
        @include('admin.landing-pages._deploy-form', [
            'generationId' => $reviewGeneration->id,
            'defaultPageType' => $reviewPageType,
            'deployLocalEnabled' => config('landing.deploy.local_enabled'),
        ])
    </div>
</div>
<style>
.lp-review-bar {
    position: sticky;
    top: 0;
    z-index: 9999;
    background: #1e293b;
    color: #f8fafc;
    font-family: 'Be Vietnam Pro', system-ui, sans-serif;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
}
.lp-review-bar__inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: .65rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.lp-review-bar__info { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; font-size: .9rem; }
.lp-review-bar__info span { color: #94a3b8; }
.lp-review-bar__back { color: #a5b4fc; text-decoration: none; font-size: .85rem; }
.lp-review-bar__back:hover { text-decoration: underline; }
.lp-review-bar__toggle {
    background: linear-gradient(90deg,#7c3aed,#4f46e5);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .45rem .9rem;
    font-weight: 600;
    cursor: pointer;
    font-size: .88rem;
}
.lp-review-bar__panel {
    border-top: 1px solid #334155;
    background: #0f172a;
    padding: 1rem;
}
.lp-review-bar__panel[hidden] { display: none; }
body.lp-body { padding-top: 0; }
</style>
<script>
(function () {
    var toggle = document.getElementById('deployPanelToggle');
    var panel = document.getElementById('deployPanel');
    if (!toggle || !panel) return;
    toggle.addEventListener('click', function () {
        var open = panel.hidden;
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.textContent = open ? 'Ẩn form deploy' : 'Đẩy lên hosting';
    });
})();
</script>
@endif
