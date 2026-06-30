<?php

namespace App\Http\Controllers;

use App\Enums\PackageType;
use App\Enums\ProvisioningStatus;
use App\Jobs\ProvisionSiteJob;
use App\Models\Order;
use App\Models\Theme;
use App\Services\AffiliateService;
use App\Services\ProvisioningCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private AffiliateService $affiliateService,
        private ProvisioningCheckoutService $provisioningCheckout,
    ) {}

    public function create(Theme $theme): View
    {
        abort_unless($theme->is_active, 404);
        $theme->load('category');

        $user = Auth::user();
        $hostingFee = config('provisioning.hosting_fee');

        return view('checkout.create', compact('theme', 'user', 'hostingFee'));
    }

    public function store(Request $request, Theme $theme): RedirectResponse
    {
        abort_unless($theme->is_active, 404);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'package_type' => ['required', 'in:theme,full'],
            'domain' => ['required_if:package_type,full', 'nullable', 'string', 'max:255', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'domain.required_if' => 'Vui lòng nhập tên miền cho gói Full.',
            'domain.regex' => 'Tên miền không hợp lệ (vd: example.com).',
        ]);

        $referrer = $this->affiliateService->resolveReferrer($request);
        $user = Auth::user();

        if ($referrer && $user && $referrer->id === $user->id) {
            $referrer = null;
        }

        if ($referrer && strcasecmp($data['customer_email'], $referrer->email) === 0) {
            $referrer = null;
        }

        $packageType = PackageType::from($data['package_type']);
        $amount = $theme->currentPrice();
        if ($packageType === PackageType::Full) {
            $amount += config('provisioning.hosting_fee');
        }

        $order = Order::create([
            'order_code' => $this->provisioningCheckout->generateOrderCode(),
            'theme_id' => $theme->id,
            'user_id' => $user?->id,
            'referrer_id' => $referrer?->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'amount' => $amount,
            'status' => Order::STATUS_PENDING,
            'note' => $data['note'] ?? null,
            'package_type' => $packageType->value,
            'domain' => $packageType === PackageType::Full ? strtolower($data['domain']) : null,
            'provisioning_token' => $packageType === PackageType::Full
                ? $this->provisioningCheckout->generateProvisioningToken()
                : null,
        ]);

        $this->affiliateService->syncCommissionForOrder($order);

        if ($packageType === PackageType::Full && config('provisioning.auto_start')) {
            $this->provisioningCheckout->startProvisioning($order);

            return redirect()->route('provisioning.show', [
                'order' => $order,
                'token' => $order->provisioning_token,
            ]);
        }

        return redirect()
            ->route('checkout.success', $order)
            ->with('success', $packageType === PackageType::Full
                ? 'Đặt mua thành công! Chúng tôi sẽ kích hoạt website sau khi xác nhận thanh toán.'
                : 'Đặt mua thành công! Chúng tôi sẽ liên hệ bạn sớm.');
    }

    public function success(Order $order): View
    {
        $order->load('theme.category', 'referrer');

        return view('checkout.success', compact('order'));
    }
}
