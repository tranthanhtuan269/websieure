@extends('layouts.account')

@section('title', 'Affiliate')

@section('content')
<h1 style="margin-bottom:.35rem;">Chương trình Affiliate</h1>
<p style="color:var(--muted);margin-bottom:1.25rem;">Nhận <strong>{{ $commissionRate }}%</strong> hoa hồng khi người bạn giới thiệu hoàn tất thanh toán đơn hàng.</p>

<div class="stats-grid">
    <div class="stat-card">
        <small style="color:var(--muted);">Tổng hoa hồng</small>
        <strong style="font-size:1.3rem;">{{ number_format($user->affiliate_total_earned, 0, ',', '.') }} ₫</strong>
    </div>
    <div class="stat-card">
        <small style="color:var(--muted);">Số dư khả dụng</small>
        <strong style="font-size:1.3rem;">{{ number_format($availableBalance, 0, ',', '.') }} ₫</strong>
    </div>
    <div class="stat-card">
        <small style="color:var(--muted);">Đơn giới thiệu</small>
        <strong style="font-size:1.3rem;">{{ $referredOrders->total() }}</strong>
    </div>
</div>

<div class="form-card" style="margin-bottom:1.5rem;">
    <h2 style="font-size:1.05rem;margin-bottom:.5rem;">Link giới thiệu của bạn</h2>
    <input type="text" readonly value="{{ $referralLink }}" style="width:100%;padding:.65rem .75rem;border:1px solid var(--border);border-radius:8px;" onclick="this.select()">
    <p style="margin-top:.5rem;font-size:.88rem;color:var(--muted);">Mã: <strong>{{ $user->referral_code }}</strong></p>
    <a href="{{ route('account.payouts.index') }}" class="btn btn-primary btn-sm" style="margin-top:.75rem;">Yêu cầu rút tiền</a>
</div>

<h2 style="margin-bottom:.75rem;">Đơn hàng từ giới thiệu</h2>
<table class="admin-table" style="margin-bottom:1.5rem;">
    <thead><tr><th>Mã</th><th>Khách</th><th>Theme</th><th>Số tiền</th><th>TT đơn</th></tr></thead>
    <tbody>
        @forelse($referredOrders as $order)
        <tr>
            <td>{{ $order->order_code }}</td>
            <td>{{ $order->customer_name }}</td>
            <td>{{ $order->theme?->name }}</td>
            <td>{{ number_format($order->amount, 0, ',', '.') }} ₫</td>
            <td>{{ $order->statusLabel() }}</td>
        </tr>
        @empty
        <tr><td colspan="5">Chưa có đơn từ giới thiệu.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $referredOrders->links() }}

<h2 style="margin:1.5rem 0 .75rem;">Lịch sử hoa hồng</h2>
<table class="admin-table">
    <thead><tr><th>Đơn</th><th>Giá trị đơn</th><th>Tỷ lệ</th><th>Hoa hồng</th><th>Trạng thái</th></tr></thead>
    <tbody>
        @forelse($commissions as $commission)
        <tr>
            <td>{{ $commission->order?->order_code }}</td>
            <td>{{ number_format($commission->order_amount, 0, ',', '.') }} ₫</td>
            <td>{{ $commission->commission_rate }}%</td>
            <td>{{ number_format($commission->commission_amount, 0, ',', '.') }} ₫</td>
            <td>{{ $commission->statusLabel() }}</td>
        </tr>
        @empty
        <tr><td colspan="5">Chưa có hoa hồng.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $commissions->links() }}
@endsection
