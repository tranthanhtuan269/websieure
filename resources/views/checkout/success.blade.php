@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<section class="section">
    <div class="container" style="max-width:640px;">
        <div class="form-card">
            <h1 style="margin-bottom:.75rem;">Đặt mua thành công!</h1>
            <p>Mã đơn: <strong>{{ $order->order_code }}</strong></p>
            <p>Theme: <strong>{{ $order->theme->name }}</strong></p>
            <p>Số tiền: <strong>{{ number_format($order->amount, 0, ',', '.') }} ₫</strong></p>
            <p>Trạng thái: <strong>{{ $order->statusLabel() }}</strong></p>
            <p style="margin-top:1rem;">Chúng tôi sẽ liên hệ qua <strong>{{ $order->customer_email }}</strong> / <strong>{{ $order->customer_phone }}</strong> trong thời gian sớm nhất.</p>
            <div style="margin-top:1.25rem;display:flex;gap:.6rem;flex-wrap:wrap;">
                <a href="{{ route('themes.index') }}" class="btn btn-primary">Tiếp tục xem theme</a>
                <a href="{{ route('home') }}" class="btn btn-outline">Về trang chủ</a>
            </div>
        </div>
    </div>
</section>
@endsection
