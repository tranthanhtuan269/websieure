@extends('layouts.app')

@section('title', 'Đặt mua ' . $theme->name)

@section('content')
<section class="section">
    <div class="container" style="max-width:720px;">
        <h1 style="margin-bottom:.5rem;">Đặt mua theme</h1>
        <p style="margin-bottom:1.25rem;">{{ $theme->name }} — <strong>{{ $theme->formattedPrice() }}</strong></p>

        <div class="form-card">
            <form method="POST" action="{{ route('checkout.store', $theme) }}">
                @csrf
                <div class="form-group">
                    <label for="customer_name">Họ tên *</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                    @error('customer_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="customer_email">Email *</label>
                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                    @error('customer_email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="customer_phone">Số điện thoại / Zalo *</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                    @error('customer_phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="note">Ghi chú (tùy chọn)</label>
                    <textarea id="note" name="note" rows="3" placeholder="Yêu cầu tùy chỉnh, tên miền, màu sắc...">{{ old('note') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Gửi đơn đặt mua</button>
                <a href="{{ route('themes.show', $theme) }}" class="btn btn-outline" style="margin-left:.5rem;">Quay lại</a>
            </form>
        </div>
        <p style="margin-top:1rem;color:var(--muted);font-size:.9rem;">Chúng tôi sẽ liên hệ xác nhận và hướng dẫn thanh toán. Theme được giao sau khi nhận tiền.</p>
    </div>
</section>
@endsection
