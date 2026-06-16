@extends('layouts.account')

@section('title', 'Rút tiền')

@section('content')
<h1 style="margin-bottom:.35rem;">Yêu cầu rút tiền</h1>
<p style="color:var(--muted);margin-bottom:1.25rem;">Số dư khả dụng: <strong>{{ number_format($availableBalance, 0, ',', '.') }} ₫</strong> — Tối thiểu {{ number_format($minPayout, 0, ',', '.') }} ₫</p>

@if($availableBalance >= $minPayout)
<div class="form-card" style="max-width:520px;margin-bottom:2rem;">
    <form method="POST" action="{{ route('account.payouts.store') }}">
        @csrf
        <div class="form-group">
            <label for="amount">Số tiền rút (₫)</label>
            <input type="number" id="amount" name="amount" min="{{ $minPayout }}" max="{{ $availableBalance }}" value="{{ old('amount', $availableBalance) }}" required>
            @error('amount')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label for="payment_method">Phương thức nhận tiền</label>
            <select id="payment_method" name="payment_method" required>
                <option value="bank" @selected(old('payment_method') === 'bank')>Chuyển khoản ngân hàng</option>
                <option value="momo" @selected(old('payment_method') === 'momo')>MoMo</option>
                <option value="zalopay" @selected(old('payment_method') === 'zalopay')>ZaloPay</option>
            </select>
        </div>
        <div class="form-group">
            <label for="payment_info">Thông tin nhận tiền</label>
            <textarea id="payment_info" name="payment_info" rows="4" placeholder="VD: Vietcombank - 0123456789 - NGUYEN VAN A" required>{{ old('payment_info') }}</textarea>
            @error('payment_info')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
    </form>
</div>
@else
<div class="alert alert-error" style="max-width:520px;margin-bottom:2rem;">
    Số dư chưa đủ để rút tiền. Tiếp tục giới thiệu khách hàng để nhận hoa hồng!
</div>
@endif

<h2 style="margin-bottom:.75rem;">Lịch sử yêu cầu rút tiền</h2>
<table class="admin-table">
    <thead><tr><th>#</th><th>Số tiền</th><th>Phương thức</th><th>Trạng thái</th><th>Ngày gửi</th></tr></thead>
    <tbody>
        @forelse($payouts as $payout)
        <tr>
            <td>{{ $payout->id }}</td>
            <td>{{ number_format($payout->amount, 0, ',', '.') }} ₫</td>
            <td>{{ strtoupper($payout->payment_method) }}</td>
            <td>{{ $payout->statusLabel() }}</td>
            <td>{{ $payout->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="5">Chưa có yêu cầu rút tiền.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $payouts->links() }}
@endsection
