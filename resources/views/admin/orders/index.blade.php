@extends('layouts.admin')

@section('title', 'Đơn hàng')

@section('content')
<h1 style="margin-bottom:1rem;">Đơn hàng</h1>
<table class="admin-table">
    <thead>
        <tr>
            <th>Mã</th>
            <th>Theme</th>
            <th>Khách</th>
            <th>Affiliate</th>
            <th>Số tiền</th>
            <th>Hoa hồng</th>
            <th>TT</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->order_code }}</td>
            <td>{{ $order->theme?->name }}</td>
            <td>{{ $order->customer_name }}<br><small>{{ $order->customer_email }}</small></td>
            <td>{{ $order->referrer?->name ?? '—' }}</td>
            <td>{{ number_format($order->amount, 0, ',', '.') }} ₫</td>
            <td>
                @if($order->commission)
                    {{ number_format($order->commission->commission_amount, 0, ',', '.') }} ₫
                @else
                    —
                @endif
            </td>
            <td>{{ $order->statusLabel() }}</td>
            <td><a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-outline btn-sm">Sửa</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $orders->links() }}
@endsection
