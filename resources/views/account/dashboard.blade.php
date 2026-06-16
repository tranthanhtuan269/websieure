@extends('layouts.account')

@section('title', 'Tổng quan')

@section('content')
<h1 style="margin-bottom:1rem;">Xin chào, {{ $user->name }}</h1>

<div class="stats-grid">
    <div class="stat-card">
        <small style="color:var(--muted);">Đơn hàng của bạn</small>
        <strong style="font-size:1.4rem;">{{ $orders->count() }}</strong>
    </div>
    <div class="stat-card">
        <small style="color:var(--muted);">Hoa hồng đã kiếm</small>
        <strong style="font-size:1.4rem;">{{ number_format($user->affiliate_total_earned, 0, ',', '.') }} ₫</strong>
    </div>
    <div class="stat-card">
        <small style="color:var(--muted);">Số dư khả dụng</small>
        <strong style="font-size:1.4rem;">{{ number_format($availableBalance, 0, ',', '.') }} ₫</strong>
    </div>
    <div class="stat-card">
        <small style="color:var(--muted);">Mã giới thiệu</small>
        <strong style="font-size:1.1rem;">{{ $user->referral_code }}</strong>
    </div>
</div>

<div class="form-card" style="margin-bottom:1.5rem;">
    <h2 style="font-size:1.1rem;margin-bottom:.75rem;">Link giới thiệu</h2>
    <p style="color:var(--muted);font-size:.9rem;margin-bottom:.75rem;">Chia sẻ link này — bạn nhận {{ \App\Models\Setting::commissionRate() }}% hoa hồng khi người được giới thiệu hoàn tất thanh toán.</p>
    <input type="text" readonly value="{{ $referralLink }}" style="width:100%;padding:.65rem .75rem;border:1px solid var(--border);border-radius:8px;font:inherit;" onclick="this.select()">
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    <div>
        <div class="section-head"><h2>Đơn hàng gần đây</h2><a href="{{ route('account.orders.index') }}">Xem tất cả</a></div>
        <table class="admin-table">
            <thead><tr><th>Mã</th><th>Theme</th><th>TT</th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->theme?->name }}</td>
                    <td>{{ $order->statusLabel() }}</td>
                </tr>
                @empty
                <tr><td colspan="3">Chưa có đơn hàng.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>
        <div class="section-head"><h2>Hoa hồng gần đây</h2><a href="{{ route('account.affiliate.index') }}">Chi tiết</a></div>
        <table class="admin-table">
            <thead><tr><th>Đơn</th><th>Hoa hồng</th><th>TT</th></tr></thead>
            <tbody>
                @forelse($commissions as $commission)
                <tr>
                    <td>{{ $commission->order?->order_code }}</td>
                    <td>{{ number_format($commission->commission_amount, 0, ',', '.') }} ₫</td>
                    <td>{{ $commission->statusLabel() }}</td>
                </tr>
                @empty
                <tr><td colspan="3">Chưa có hoa hồng.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
