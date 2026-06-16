@extends('layouts.admin')

@section('title', 'Hoa hồng Affiliate')

@section('content')
<h1 style="margin-bottom:1rem;">Hoa hồng Affiliate</h1>
<table class="admin-table">
    <thead>
        <tr>
            <th>Đơn hàng</th>
            <th>Người giới thiệu</th>
            <th>Khách mua</th>
            <th>Giá trị đơn</th>
            <th>Hoa hồng</th>
            <th>Trạng thái</th>
            <th>Ngày</th>
        </tr>
    </thead>
    <tbody>
        @foreach($commissions as $commission)
        <tr>
            <td>{{ $commission->order?->order_code }}</td>
            <td>{{ $commission->referrer?->name }}</td>
            <td>{{ $commission->order?->customer_name }}</td>
            <td>{{ number_format($commission->order_amount, 0, ',', '.') }} ₫</td>
            <td>{{ number_format($commission->commission_amount, 0, ',', '.') }} ₫ ({{ $commission->commission_rate }}%)</td>
            <td>{{ $commission->statusLabel() }}</td>
            <td>{{ $commission->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $commissions->links() }}
@endsection
