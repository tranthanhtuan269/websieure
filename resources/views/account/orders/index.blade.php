@extends('layouts.account')

@section('title', 'Đơn hàng của tôi')

@section('content')
<h1 style="margin-bottom:1rem;">Đơn hàng của tôi</h1>
<table class="admin-table">
    <thead>
        <tr>
            <th>Mã đơn</th>
            <th>Theme</th>
            <th>Số tiền</th>
            <th>Giới thiệu bởi</th>
            <th>Hoa hồng</th>
            <th>Trạng thái</th>
            <th>Ngày đặt</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <td>{{ $order->order_code }}</td>
            <td>{{ $order->theme?->name }}</td>
            <td>{{ number_format($order->amount, 0, ',', '.') }} ₫</td>
            <td>{{ $order->referrer?->name ?? '—' }}</td>
            <td>
                @if($order->commission)
                    {{ number_format($order->commission->commission_amount, 0, ',', '.') }} ₫
                @else
                    —
                @endif
            </td>
            <td>{{ $order->statusLabel() }}</td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="7">Bạn chưa có đơn hàng nào.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $orders->links() }}
@endsection
