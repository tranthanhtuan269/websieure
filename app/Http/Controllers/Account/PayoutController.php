<?php

namespace App\Http\Controllers\Account;

use App\Enums\PayoutStatus;
use App\Http\Controllers\Controller;
use App\Models\AffiliatePayoutRequest;
use App\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function __construct(private AffiliateService $affiliateService) {}

    public function index(): View
    {
        $user = auth()->user();

        return view('account.payouts.index', [
            'user' => $user,
            'payouts' => AffiliatePayoutRequest::query()
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15),
            'availableBalance' => $this->affiliateService->availableBalance($user),
            'minPayout' => config('affiliate.min_payout'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $available = $this->affiliateService->availableBalance($user);
        $minPayout = config('affiliate.min_payout');

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:' . $minPayout],
            'payment_method' => ['required', 'in:bank,momo,zalopay'],
            'payment_info' => ['required', 'string', 'max:1000'],
        ]);

        if ($data['amount'] > $available) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Số tiền vượt quá số dư khả dụng (' . number_format($available, 0, ',', '.') . ' ₫).']);
        }

        AffiliatePayoutRequest::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'status' => PayoutStatus::Pending,
            'payment_method' => $data['payment_method'],
            'payment_info' => $data['payment_info'],
        ]);

        return redirect()
            ->route('account.payouts.index')
            ->with('success', 'Đã gửi yêu cầu rút tiền. Admin sẽ xử lý trong 1-3 ngày làm việc.');
    }
}
