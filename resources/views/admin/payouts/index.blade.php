@extends('layouts.admin')

@section('title', 'Yêu cầu rút tiền')

@section('content')
<h1 style="margin-bottom:1rem;">Yêu cầu rút tiền</h1>
<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Thành viên</th>
            <th>Số tiền</th>
            <th>Phương thức</th>
            <th>Trạng thái</th>
            <th>Ngày</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($payouts as $payout)
        <tr>
            <td>{{ $payout->id }}</td>
            <td>{{ $payout->user?->name }}<br><small>{{ $payout->user?->email }}</small></td>
            <td>{{ number_format($payout->amount, 0, ',', '.') }} ₫</td>
            <td>{{ strtoupper($payout->payment_method) }}</td>
            <td>{{ $payout->statusLabel() }}</td>
            <td>{{ $payout->created_at->format('d/m/Y H:i') }}</td>
            <td><a href="{{ route('admin.payouts.edit', $payout) }}" class="btn btn-outline btn-sm">Xử lý</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $payouts->links() }}
@endsection
