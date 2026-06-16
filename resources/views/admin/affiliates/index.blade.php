@extends('layouts.admin')

@section('title', 'Affiliate')

@section('content')
<h1 style="margin-bottom:1rem;">Thành viên Affiliate</h1>

<div class="form-card" style="max-width:420px;margin-bottom:1.5rem;">
    <h2 style="font-size:1.05rem;margin-bottom:.75rem;">Cấu hình hoa hồng</h2>
    <form method="POST" action="{{ route('admin.affiliates.settings') }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="commission_rate">Tỷ lệ hoa hồng (%)</label>
            <input type="number" id="commission_rate" name="commission_rate" min="1" max="100" value="{{ old('commission_rate', $commissionRate) }}" required>
            @error('commission_rate')<p class="form-error">{{ $message }}</p>@enderror
            <p style="margin-top:.35rem;font-size:.85rem;color:var(--muted);">Áp dụng cho đơn mới và đơn đang chờ thanh toán. Đơn đã duyệt hoa hồng không đổi.</p>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Lưu cấu hình</button>
    </form>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Thành viên</th>
            <th>Mã GT</th>
            <th>Đơn giới thiệu</th>
            <th>Hoa hồng đã duyệt</th>
            <th>Số dư</th>
            <th>Tổng kiếm được</th>
        </tr>
    </thead>
    <tbody>
        @foreach($affiliates as $affiliate)
        <tr>
            <td>{{ $affiliate->name }}<br><small>{{ $affiliate->email }}</small></td>
            <td>{{ $affiliate->referral_code }}</td>
            <td>{{ $affiliate->referred_orders_count }}</td>
            <td>{{ number_format($affiliate->approved_commissions_sum ?? 0, 0, ',', '.') }} ₫</td>
            <td>{{ number_format($affiliate->affiliate_balance, 0, ',', '.') }} ₫</td>
            <td>{{ number_format($affiliate->affiliate_total_earned, 0, ',', '.') }} ₫</td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $affiliates->links() }}
@endsection
