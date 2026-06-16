<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Setting;
use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function __construct(private AffiliateService $affiliateService) {}

    public function index(): View
    {
        $affiliates = User::query()
            ->whereNotNull('referral_code')
            ->withCount([
                'referredOrders',
                'commissions as approved_commissions_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->withSum(['commissions as approved_commissions_sum' => fn ($q) => $q->where('status', 'approved')], 'commission_amount')
            ->orderByDesc('affiliate_total_earned')
            ->paginate(20);

        return view('admin.affiliates.index', [
            'affiliates' => $affiliates,
            'commissionRate' => Setting::commissionRate(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'commission_rate' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $this->affiliateService->updateCommissionRate($data['commission_rate']);

        return redirect()
            ->route('admin.affiliates.index')
            ->with('success', 'Đã cập nhật tỷ lệ hoa hồng thành ' . $data['commission_rate'] . '%.');
    }

    public function commissions(): View
    {
        $commissions = AffiliateCommission::query()
            ->with(['order.theme', 'referrer', 'buyer'])
            ->latest()
            ->paginate(20);

        return view('admin.commissions.index', compact('commissions'));
    }
}
