@extends('layouts.admin')

@section('title', 'Tạo gói 3 landing page')

@section('content')
<h1>Tạo gói 3 landing page (AI)</h1>

<p style="color:var(--muted);margin-bottom:1.25rem;max-width:720px;line-height:1.6;">
    Nhập link affiliate — hệ thống sẽ crawl ảnh từ trang đích, dùng AI viết nội dung và tạo gói zip gồm 3 landing page:
    <strong>Chuẩn ~3000 từ (3 ảnh trong bài + CTA)</strong>,
    <strong>So sánh tính năng vs đối thủ + CTA</strong>,
    <strong>Popup Cookie Notice (1 ảnh affiliate)</strong>.
</p>

@if(!$aiConfigured)
<div class="alert alert-error" style="margin-bottom:1rem;">
    Chưa cấu hình <code>GEMINI_API_KEY</code> trong .env — hệ thống sẽ dùng nội dung mẫu thay AI.
    Lấy key miễn phí tại <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener">Google AI Studio</a>.
</div>
@else
<div class="alert alert-success" style="margin-bottom:1rem;">
    Đang dùng <strong>Google Gemini</strong> ({{ config('landing.ai.model') }}) để tạo nội dung.
</div>
@endif

<form id="generatorForm" style="max-width:560px;margin-bottom:1.5rem;">
    @csrf
    <div class="form-group">
        <label>Link affiliate <span style="color:#ef4444">*</span></label>
        <input type="url" name="affiliate_url" id="affiliateUrl" required
               placeholder="https://example.com/affiliate-product"
               value="{{ old('affiliate_url', config('landing.default_affiliate_url')) }}">
    </div>
    <button type="submit" class="btn btn-primary" id="startBtn">Bắt đầu tạo gói</button>
    <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-outline">← Danh sách LP</a>
</form>

<div id="progressPanel" hidden style="max-width:640px;">
    <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
            <strong id="progressLabel">Đang xử lý...</strong>
            <span id="progressPercent">0%</span>
        </div>
        <div style="height:10px;background:#e2e8f0;border-radius:999px;overflow:hidden;">
            <div id="progressBar" style="height:100%;width:0%;background:linear-gradient(90deg,#7c3aed,#4f46e5);transition:width .35s ease;"></div>
        </div>
        <p id="progressMessage" style="margin:.85rem 0 0;color:var(--muted);font-size:.92rem;"></p>
        <p id="progressTopic" style="margin:.35rem 0 0;font-size:.9rem;" hidden></p>
        <p id="progressError" style="margin:.75rem 0 0;color:#dc2626;font-size:.9rem;" hidden></p>
        <div id="previewWrap" style="margin-top:1rem;" hidden>
            <p style="margin:0 0 .65rem;font-weight:600;">Xem trước trước khi tải:</p>
            <ul id="previewLinks" style="margin:0;padding-left:1.25rem;line-height:1.9;"></ul>
        </div>
        <div id="downloadWrap" style="margin-top:1rem;" hidden>
            <a href="#" id="downloadBtn" class="btn btn-primary">Tải zip (3 landing page)</a>
        </div>
        <div id="deployWrap" style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);" hidden>
            <p style="margin:0 0 .85rem;font-weight:600;">Đẩy trực tiếp lên hosting</p>
            <p style="margin:0 0 1rem;color:var(--muted);font-size:.9rem;line-height:1.55;">
                Điền thông tin SFTP hoặc chọn deploy local (nếu server hỗ trợ). Mỗi lần deploy 1 trong 3 landing page.
            </p>
            @include('admin.landing-pages._deploy-form', [
                'generationId' => 'pending',
                'defaultPageType' => 'standard',
                'deployLocalEnabled' => $deployLocalEnabled,
                'variant' => 'light',
            ])
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('generatorForm');
    const panel = document.getElementById('progressPanel');
    const bar = document.getElementById('progressBar');
    const percent = document.getElementById('progressPercent');
    const message = document.getElementById('progressMessage');
    const topic = document.getElementById('progressTopic');
    const error = document.getElementById('progressError');
    const downloadWrap = document.getElementById('downloadWrap');
    const downloadBtn = document.getElementById('downloadBtn');
    const previewWrap = document.getElementById('previewWrap');
    const previewLinks = document.getElementById('previewLinks');
    const deployWrap = document.getElementById('deployWrap');
    const startBtn = document.getElementById('startBtn');
    let deployForm = document.querySelector('.lp-deploy-form');

    const stepLabels = {
        crawl: 'Crawl ảnh affiliate',
        standard: 'Landing chuẩn 3000 từ (3 ảnh)',
        compare: 'Bảng so sánh đối thủ',
        popup: 'Landing popup Cookie Notice',
        zip: 'Đóng gói zip',
    };

    function csrf() {
        return document.querySelector('input[name=_token]').value;
    }

    function updateUi(data) {
        bar.style.width = data.progress + '%';
        percent.textContent = data.progress + '%';
        message.textContent = data.message || '';
        if (data.topic) {
            topic.hidden = false;
            topic.textContent = 'Chủ đề: ' + data.topic;
        }
        if (data.error) {
            error.hidden = false;
            error.textContent = data.error;
            startBtn.disabled = false;
            return;
        }
        if (data.download_url) {
            if (Array.isArray(data.preview_links) && data.preview_links.length) {
                previewWrap.hidden = false;
                previewLinks.innerHTML = data.preview_links.map(function (link) {
                    return '<li><a href="' + link.url + '" target="_blank" rel="noopener">' + link.label + '</a></li>';
                }).join('');
            }
            downloadWrap.hidden = false;
            downloadBtn.href = data.download_url;
            deployWrap.hidden = false;
            deployForm = document.querySelector('.lp-deploy-form');
            if (deployForm) deployForm.dataset.generationId = data.id;
            startBtn.disabled = false;
            message.textContent = 'Đã tạo xong gói 3 landing page. Xem trước, deploy lên hosting, hoặc tải zip.';
        }
    }

    async function runStep(generationId, step) {
        const res = await fetch(`/admin/landing-pages/generator/${generationId}/step/${step}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf(),
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || data.message || 'Lỗi bước ' + step);
        updateUi(data);
        if (data.error) throw new Error(data.error);
        return data;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        startBtn.disabled = true;
        panel.hidden = false;
        error.hidden = true;
        downloadWrap.hidden = true;
        previewWrap.hidden = true;
        previewLinks.innerHTML = '';
        deployWrap.hidden = true;
        if (deployForm) deployForm.dataset.generationId = 'pending';
        topic.hidden = true;
        bar.style.width = '0%';
        percent.textContent = '0%';

        try {
            const startRes = await fetch('{{ route('admin.landing-pages.generator.start') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    affiliate_url: document.getElementById('affiliateUrl').value,
                }),
            });
            const startData = await startRes.json();
            if (!startRes.ok) throw new Error(startData.message || 'Không thể bắt đầu');

            const steps = startData.steps || [];
            for (const step of steps) {
                message.textContent = 'Đang chạy: ' + (stepLabels[step] || step) + '...';
                await runStep(startData.id, step);
            }
        } catch (err) {
            error.hidden = false;
            error.textContent = err.message || 'Có lỗi xảy ra.';
            startBtn.disabled = false;
        }
    });
})();
</script>
@endpush
@endsection
