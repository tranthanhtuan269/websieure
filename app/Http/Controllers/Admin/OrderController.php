<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionSiteJob;
use App\Models\Order;
use App\Services\AffiliateService;
use App\Services\ProvisioningCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private AffiliateService $affiliateService,
        private ProvisioningCheckoutService $provisioningCheckout,
    ) {}

    public function index(): View
    {
        $orders = Order::with(['theme', 'user', 'referrer', 'commission'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function edit(Order $order): View
    {
        $order->load(['theme', 'user', 'referrer', 'commission']);

        return view('admin.orders.form', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,paid,delivered,cancelled'],
            'note' => ['nullable', 'string'],
        ]);

        $oldStatus = $order->status;
        $order->update($data);

        $becamePaid = in_array($data['status'], [Order::STATUS_PAID, Order::STATUS_DELIVERED], true)
            && ! in_array($oldStatus, [Order::STATUS_PAID, Order::STATUS_DELIVERED], true);

        if ($becamePaid) {
            if (! $order->isFullPackage()) {
                $order->theme()->increment('sales_count');
            }

            if ($order->isFullPackage() && ! $order->provisioning_completed_at) {
                $this->provisioningCheckout->startProvisioning($order->fresh());
            }
        }

        $this->affiliateService->syncCommissionForOrder($order->fresh());

        return redirect()->route('admin.orders.index')->with('success', 'Đã cập nhật đơn hàng.');
    }

    public function provision(Order $order): RedirectResponse
    {
        abort_unless($order->isFullPackage(), 422);

        $this->provisioningCheckout->startProvisioning($order->fresh());

        return back()->with('success', 'Đã gửi lệnh cài đặt website.');
    }
}
