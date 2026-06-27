@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<section class="section">
    <div class="container" style="max-width:640px;">
        <div class="form-card">
            <h1 style="margin-bottom:.75rem;">Đặt mua thành công!</h1>
            <p>Mã đơn: <strong>{{ $order->order_code }}</strong></p>
            <p>Gói: <strong>{{ $order->packageLabel() }}</strong></p>
            @if($order->domain)
                <p>Tên miền: <strong>{{ $order->domain }}</strong></p>
            @endif
            <p>Theme: <strong>{{ $order->theme->name }}</strong></p>
            <p>Số tiền: <strong>{{ number_format($order->amount, 0, ',', '.') }} ₫</strong></p>
            <p>Trạng thái: <strong>{{ $order->statusLabel() }}</strong></p>
            <p style="margin-top:1rem;">Chúng tôi sẽ liên hệ qua <strong>{{ $order->customer_email }}</strong> / <strong>{{ $order->customer_phone }}</strong>.</p>
            <div style="margin-top:1.25rem;display:flex;gap:.6rem;flex-wrap:wrap;">
                @if($order->isFullPackage() && $order->provisioning_token)
                    <a href="{{ route('provisioning.show', ['order' => $order, 'token' => $order->provisioning_token]) }}" class="btn btn-primary">Theo dõi cài đặt</a>
                @endif
                <a href="{{ route('themes.index') }}" class="btn btn-primary">Tiếp tục xem theme</a>
                <a href="{{ route('home') }}" class="btn btn-outline">Về trang chủ</a>
            </div>
        </div>
    </div>
</section>
@endsection
