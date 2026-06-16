<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayoutRequest;
use App\Models\Order;
use App\Services\AffiliateService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private AffiliateService $affiliateService) {}

    public function index(): View
    {
        $user = auth()->user();
        $this->affiliateService->ensureReferralCode($user);

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->orWhere('customer_email', $user->email)
            ->with('theme')
            ->latest()
            ->limit(5)
            ->get();

        $commissions = AffiliateCommission::query()
            ->where('referrer_id', $user->id)
            ->with('order.theme')
            ->latest()
            ->limit(5)
            ->get();

        $payouts = AffiliatePayoutRequest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('account.dashboard', [
            'user' => $user->fresh(),
            'orders' => $orders,
            'commissions' => $commissions,
            'payouts' => $payouts,
            'availableBalance' => $this->affiliateService->availableBalance($user),
            'referralLink' => $this->affiliateService->referralLink($user),
        ]);
    }
}
