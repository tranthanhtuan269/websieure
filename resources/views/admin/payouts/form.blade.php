@extends('layouts.admin')

@section('title', 'Xử lý rút tiền #' . $payout->id)

@section('content')
<h1 style="margin-bottom:1rem;">Yêu cầu rút tiền #{{ $payout->id }}</h1>

<div class="form-card" style="max-width:640px;margin-bottom:1rem;">
    <p><strong>Thành viên:</strong> {{ $payout->user?->name }} ({{ $payout->user?->email }})</p>
    <p><strong>Số tiền:</strong> {{ number_format($payout->amount, 0, ',', '.') }} ₫</p>
    <p><strong>Phương thức:</strong> {{ strtoupper($payout->payment_method) }}</p>
    <p><strong>Thông tin nhận tiền:</strong><br>{{ $payout->payment_info }}</p>
    <p><strong>Số dư hiện tại:</strong> {{ number_format($payout->user?->affiliate_balance ?? 0, 0, ',', '.') }} ₫</p>
</div>

<form method="POST" action="{{ route('admin.payouts.update', $payout) }}" class="form-card" style="max-width:640px;">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="status">Trạng thái</label>
        <select id="status" name="status" required>
            @foreach(['pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối', 'paid' => 'Đã thanh toán'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $payout->status->value) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="admin_note">Ghi chú admin</label>
        <textarea id="admin_note" name="admin_note" rows="3">{{ old('admin_note', $payout->admin_note) }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="{{ route('admin.payouts.index') }}" class="btn btn-outline" style="margin-left:.5rem;">Quay lại</a>
</form>
@endsection
