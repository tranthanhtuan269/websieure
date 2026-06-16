@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 style="margin-bottom:1.25rem;">Dashboard</h1>
<div class="stats-grid">
    <div class="stat-card"><strong>{{ $stats['themes'] }}</strong> Themes</div>
    <div class="stat-card"><strong>{{ $stats['categories'] }}</strong> Chủ đề</div>
    <div class="stat-card"><strong>{{ $stats['orders'] }}</strong> Đơn hàng</div>
    <div class="stat-card"><strong>{{ $stats['pending_orders'] }}</strong> Chờ TT</div>
    <div class="stat-card"><strong>{{ number_format($stats['revenue'], 0, ',', '.') }} ₫</strong> Doanh thu</div>
    <div class="stat-card"><strong>{{ number_format($stats['commissions'], 0, ',', '.') }} ₫</strong> Hoa hồng</div>
    <div class="stat-card"><strong>{{ $stats['pending_payouts'] }}</strong> Rút tiền chờ</div>
</div>

<h2 style="margin-bottom:.75rem;">Đơn hàng gần đây</h2>
<table class="admin-table">
    <thead><tr><th>Mã</th><th>Theme</th><th>Khách</th><th>Số tiền</th><th>TT</th></tr></thead>
    <tbody>
        @forelse($recentOrders as $order)
        <tr>
            <td><a href="{{ route('admin.orders.edit', $order) }}">{{ $order->order_code }}</a></td>
            <td>{{ $order->theme?->name }}</td>
            <td>{{ $order->customer_name }}</td>
            <td>{{ number_format($order->amount, 0, ',', '.') }} ₫</td>
            <td>{{ $order->statusLabel() }}</td>
        </tr>
        @empty
        <tr><td colspan="5">Chưa có đơn hàng.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
