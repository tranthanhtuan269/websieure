@extends('layouts.app')

@section('title', 'Đặt mua ' . $theme->name)

@section('content')
<section class="section">
    <div class="container" style="max-width:720px;">
        <h1 style="margin-bottom:.5rem;">Đặt mua theme</h1>
        <p style="margin-bottom:1.25rem;">{{ $theme->name }} — <strong>{{ $theme->formattedPrice() }}</strong></p>

        <div class="form-card">
            <form method="POST" action="{{ route('checkout.store', $theme) }}" id="checkout-form">
                @csrf

                <div class="form-group">
                    <label>Chọn gói *</label>
                    <div class="package-options">
                        <label class="package-option">
                            <input type="radio" name="package_type" value="theme" @checked(old('package_type', 'theme') === 'theme')>
                            <span>
                                <strong>Chỉ mua theme</strong>
                                <small>File theme / hướng dẫn cài — {{ $theme->formattedPrice() }}</small>
                            </span>
                        </label>
                        <label class="package-option package-option--featured">
                            <input type="radio" name="package_type" value="full" @checked(old('package_type') === 'full')>
                            <span>
                                <strong>Gói Full — 1 chạm</strong>
                                <small>Domain + host + cài WordPress + HTTPS — {{ number_format($theme->currentPrice() + $hostingFee, 0, ',', '.') }} ₫</small>
                            </span>
                        </label>
                    </div>
                    @error('package_type')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group" id="domain-field" style="display:none;">
                    <label for="domain">Tên miền website *</label>
                    <input type="text" id="domain" name="domain" value="{{ old('domain') }}" placeholder="vidu.com">
                    <p style="font-size:.85rem;color:var(--muted);margin-top:.35rem;">Nhập domain bạn sở hữu hoặc muốn dùng. Hệ thống tự cấu hình DNS (Cloudflare) và cài WordPress.</p>
                    @error('domain')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="customer_name">Họ tên *</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required>
                    @error('customer_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="customer_email">Email *</label>
                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', $user?->email) }}" required>
                    @error('customer_email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="customer_phone">Số điện thoại / Zalo *</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                    @error('customer_phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="note">Ghi chú (tùy chọn)</label>
                    <textarea id="note" name="note" rows="3" placeholder="Yêu cầu tùy chỉnh, màu sắc...">{{ old('note') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary" id="submit-btn">Gửi đơn đặt mua</button>
                <a href="{{ route('themes.show', $theme) }}" class="btn btn-outline" style="margin-left:.5rem;">Quay lại</a>
            </form>
        </div>
        <p style="margin-top:1rem;color:var(--muted);font-size:.9rem;" id="checkout-note">Chúng tôi sẽ liên hệ xác nhận và hướng dẫn thanh toán.</p>
    </div>
</section>
@endsection

@push('styles')
<style>
.package-options { display: flex; flex-direction: column; gap: .75rem; }
.package-option { display: flex; gap: .75rem; align-items: flex-start; padding: 1rem; border: 2px solid var(--border); border-radius: 12px; cursor: pointer; transition: .2s; }
.package-option:has(input:checked) { border-color: var(--primary-light); background: #f5f3ff; }
.package-option input { margin-top: .25rem; }
.package-option strong { display: block; }
.package-option small { color: var(--muted); font-size: .85rem; }
.package-option--featured strong { color: var(--primary); }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const form = document.getElementById('checkout-form');
    const domainField = document.getElementById('domain-field');
    const domainInput = document.getElementById('domain');
    const submitBtn = document.getElementById('submit-btn');
    const note = document.getElementById('checkout-note');

    function syncPackage() {
        const full = form.package_type.value === 'full';
        domainField.style.display = full ? 'block' : 'none';
        domainInput.required = full;
        submitBtn.textContent = full ? 'Mua & kích hoạt ngay' : 'Gửi đơn đặt mua';
        note.textContent = full
            ? 'Website sẽ tự động cài đặt: DNS → WordPress → HTTPS. Theo dõi tiến độ ngay sau khi đặt.'
            : 'Chúng tôi sẽ liên hệ xác nhận và hướng dẫn thanh toán.';
    }

    form.querySelectorAll('input[name="package_type"]').forEach(el => el.addEventListener('change', syncPackage));
    syncPackage();
})();
</script>
@endpush
