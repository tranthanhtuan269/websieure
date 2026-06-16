<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PayoutStatus;
use App\Http\Controllers\Controller;
use App\Models\AffiliatePayoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutRequestController extends Controller
{
    public function index(): View
    {
        $payouts = AffiliatePayoutRequest::query()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.payouts.index', compact('payouts'));
    }

    public function edit(AffiliatePayoutRequest $payout): View
    {
        $payout->load('user');

        return view('admin.payouts.form', compact('payout'));
    }

    public function update(Request $request, AffiliatePayoutRequest $payout): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,paid'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $payout->status;
        $payout->update([
            'status' => $data['status'],
            'admin_note' => $data['admin_note'],
            'processed_at' => in_array($data['status'], ['paid', 'rejected'], true) ? now() : $payout->processed_at,
        ]);

        if ($data['status'] === PayoutStatus::Paid->value
            && $oldStatus !== PayoutStatus::Paid) {
            $user = $payout->user;
            $user->update([
                'affiliate_balance' => max(0, $user->affiliate_balance - $payout->amount),
            ]);
        }

        return redirect()
            ->route('admin.payouts.index')
            ->with('success', 'Đã cập nhật yêu cầu rút tiền.');
    }
}
