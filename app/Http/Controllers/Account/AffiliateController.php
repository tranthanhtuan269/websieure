<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Order;
use App\Services\AffiliateService;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function __construct(private AffiliateService $affiliateService) {}

    public function index(): View
    {
        $user = auth()->user();
        $this->affiliateService->ensureReferralCode($user);

        $commissions = AffiliateCommission::query()
            ->where('referrer_id', $user->id)
            ->with(['order.theme', 'buyer'])
            ->latest()
            ->paginate(15);

        $referredOrders = Order::query()
            ->where('referrer_id', $user->id)
            ->with(['theme', 'commission'])
            ->latest()
            ->paginate(15, ['*'], 'orders_page');

        return view('account.affiliate.index', [
            'user' => $user->fresh(),
            'commissions' => $commissions,
            'referredOrders' => $referredOrders,
            'availableBalance' => $this->affiliateService->availableBalance($user),
            'referralLink' => $this->affiliateService->referralLink($user),
            'commissionRate' => \App\Models\Setting::commissionRate(),
        ]);
    }
}
