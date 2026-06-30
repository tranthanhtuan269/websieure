@extends('layouts.admin')

@section('title', 'Sửa đơn ' . $order->order_code)

@section('content')
<h1 style="margin-bottom:1rem;">Đơn {{ $order->order_code }}</h1>
<div class="form-card" style="margin-bottom:1rem;">
    <p><strong>Gói:</strong> {{ $order->packageLabel() }}</p>
    <p><strong>Theme:</strong> {{ $order->theme?->name }}</p>
    @if($order->domain)
        <p><strong>Domain:</strong> {{ $order->domain }}</p>
    @endif
    <p><strong>Khách:</strong> {{ $order->customer_name }} — {{ $order->customer_email }} — {{ $order->customer_phone }}</p>
    <p><strong>Số tiền:</strong> {{ number_format($order->amount, 0, ',', '.') }} ₫</p>
    <p><strong>Affiliate:</strong> {{ $order->referrer?->name ?? 'Không có' }} @if($order->referrer)({{ $order->referrer->email }})@endif</p>
    @if($order->commission)
        <p><strong>Hoa hồng:</strong> {{ number_format($order->commission->commission_amount, 0, ',', '.') }} ₫ — {{ $order->commission->statusLabel() }}</p>
    @endif
    @if($order->isFullPackage())
        <p><strong>Cài đặt:</strong> {{ $order->provisioningStatusEnum()?->label() ?? 'Chưa chạy' }}</p>
        @if($order->site_url)
            <p><strong>Website:</strong> <a href="{{ $order->site_url }}" target="_blank">{{ $order->site_url }}</a></p>
        @endif
        @if($order->wp_admin_user)
            <p><strong>WP Admin:</strong> {{ $order->wp_admin_user }} / {{ $order->decryptedWpPassword() ?? '—' }}</p>
        @endif
        @if($order->provisioning_error)
            <p style="color:#991b1b;"><strong>Lỗi:</strong> {{ $order->provisioning_error }}</p>
        @endif
        @if($order->provisioning_token)
            <p><a href="{{ route('provisioning.show', ['order' => $order, 'token' => $order->provisioning_token]) }}" target="_blank">Xem trang theo dõi khách →</a></p>
        @endif
    @endif
</div>

@if($order->isFullPackage())
<form method="POST" action="{{ route('admin.orders.provision', $order) }}" style="margin-bottom:1rem;">
    @csrf
    <button class="btn btn-primary" type="submit">Kích hoạt / cài lại website</button>
</form>
@endif

<form method="POST" action="{{ route('admin.orders.update', $order) }}" class="form-card" style="max-width:520px;">
    @csrf @method('PUT')
    <div class="form-group">
        <label>Trạng thái</label>
        <select name="status">
            <option value="pending" @selected($order->status==='pending')>Chờ thanh toán</option>
            <option value="paid" @selected($order->status==='paid')>Đã thanh toán</option>
            <option value="delivered" @selected($order->status==='delivered')>Đã giao theme</option>
            <option value="cancelled" @selected($order->status==='cancelled')>Đã hủy</option>
        </select>
    </div>
    <div class="form-group"><label>Ghi chú admin</label><textarea name="note" rows="3">{{ old('note', $order->note) }}</textarea></div>
    <button class="btn btn-primary">Cập nhật</button>
</form>
@endsection
