@extends('layouts.admin')

@section('title', 'Sửa đơn ' . $order->order_code)

@section('content')
<h1 style="margin-bottom:1rem;">Đơn {{ $order->order_code }}</h1>
<div class="form-card" style="margin-bottom:1rem;">
    <p><strong>Theme:</strong> {{ $order->theme?->name }}</p>
    <p><strong>Khách:</strong> {{ $order->customer_name }} — {{ $order->customer_email }} — {{ $order->customer_phone }}</p>
    <p><strong>Số tiền:</strong> {{ number_format($order->amount, 0, ',', '.') }} ₫</p>
</div>
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
