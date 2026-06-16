<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Theme;
use App\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private AffiliateService $affiliateService) {}

    public function create(Theme $theme): View
    {
        abort_unless($theme->is_active, 404);
        $theme->load('category');

        $user = Auth::user();

        return view('checkout.create', compact('theme', 'user'));
    }

    public function store(Request $request, Theme $theme): RedirectResponse
    {
        abort_unless($theme->is_active, 404);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $referrer = $this->affiliateService->resolveReferrer($request);
        $user = Auth::user();

        if ($referrer && $user && $referrer->id === $user->id) {
            $referrer = null;
        }

        if ($referrer && strcasecmp($data['customer_email'], $referrer->email) === 0) {
            $referrer = null;
        }

        $order = Order::create([
            'order_code' => $this->generateOrderCode(),
            'theme_id' => $theme->id,
            'user_id' => $user?->id,
            'referrer_id' => $referrer?->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'amount' => $theme->currentPrice(),
            'status' => Order::STATUS_PENDING,
            'note' => $data['note'] ?? null,
        ]);

        $this->affiliateService->syncCommissionForOrder($order);

        return redirect()
            ->route('checkout.success', $order)
            ->with('success', 'Đặt mua thành công! Chúng tôi sẽ liên hệ bạn sớm.');
    }

    public function success(Order $order): View
    {
        $order->load('theme.category', 'referrer');

        return view('checkout.success', compact('order'));
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'WSR-' . strtoupper(Str::random(8));
        } while (Order::query()->where('order_code', $code)->exists());

        return $code;
    }
}
