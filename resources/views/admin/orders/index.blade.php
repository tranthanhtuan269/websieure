@extends('layouts.admin')

@section('title', 'Đơn hàng')

@section('content')
<h1 style="margin-bottom:1rem;">Đơn hàng</h1>
<table class="admin-table">
    <thead>
        <tr>
            <th>Mã</th>
            <th>Gói</th>
            <th>Theme</th>
            <th>Khách / Domain</th>
            <th>Số tiền</th>
            <th>TT đơn</th>
            <th>Cài đặt</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->order_code }}</td>
            <td>{{ $order->packageLabel() }}</td>
            <td>{{ $order->theme?->name }}</td>
            <td>
                {{ $order->customer_name }}<br>
                <small>{{ $order->customer_email }}</small>
                @if($order->domain)<br><small>{{ $order->domain }}</small>@endif
            </td>
            <td>{{ number_format($order->amount, 0, ',', '.') }} ₫</td>
            <td>{{ $order->statusLabel() }}</td>
            <td>
                @if($order->isFullPackage())
                    {{ $order->provisioningStatusEnum()?->label() ?? '—' }}
                @else
                    —
                @endif
            </td>
            <td><a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-outline btn-sm">Sửa</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $orders->links() }}
@endsection
