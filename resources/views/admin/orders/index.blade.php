@extends('layouts.admin')

@section('title', 'Đơn hàng')

@section('content')
<h1 style="margin-bottom:1rem;">Đơn hàng</h1>
<table class="admin-table">
    <thead><tr><th>Mã</th><th>Theme</th><th>Khách</th><th>Liên hệ</th><th>Số tiền</th><th>TT</th><th></th></tr></thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->order_code }}</td>
            <td>{{ $order->theme?->name }}</td>
            <td>{{ $order->customer_name }}</td>
            <td>{{ $order->customer_email }}<br>{{ $order->customer_phone }}</td>
            <td>{{ number_format($order->amount, 0, ',', '.') }} ₫</td>
            <td>{{ $order->statusLabel() }}</td>
            <td><a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-outline btn-sm">Sửa</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $orders->links() }}
@endsection
