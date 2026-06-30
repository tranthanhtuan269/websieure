<?php

namespace App\Http\Controllers;

use App\Enums\PackageType;
use App\Enums\ProvisioningStatus;
use App\Jobs\ProvisionSiteJob;
use App\Models\Order;
use Illuminate\View\View;

class ProvisioningController extends Controller
{
    public function show(Order $order, string $token): View
    {
        abort_unless($order->provisioning_token && hash_equals($order->provisioning_token, $token), 404);
        abort_unless($order->isFullPackage(), 404);

        $order->load('theme.category');

        return view('provisioning.status', [
            'order' => $order,
            'token' => $token,
            'status' => $order->provisioningStatusEnum(),
            'steps' => [
                ProvisioningStatus::Queued,
                ProvisioningStatus::ConfiguringDns,
                ProvisioningStatus::InstallingWordpress,
                ProvisioningStatus::ConfiguringSsl,
                ProvisioningStatus::Completed,
            ],
        ]);
    }

    public function poll(Order $order, string $token)
    {
        abort_unless($order->provisioning_token && hash_equals($order->provisioning_token, $token), 404);

        $order->refresh();

        return response()->json([
            'status' => $order->provisioning_status,
            'status_label' => $order->provisioningStatusEnum()?->label(),
            'step' => $order->provisioningStatusEnum()?->step() ?? 0,
            'site_url' => $order->site_url,
            'wp_admin_user' => $order->provisioning_status === ProvisioningStatus::Completed->value
                ? $order->wp_admin_user
                : null,
            'wp_admin_pass' => $order->provisioning_status === ProvisioningStatus::Completed->value
                ? $order->decryptedWpPassword()
                : null,
            'error' => $order->provisioning_error,
            'log' => $order->provisioning_log ?? [],
            'completed' => $order->provisioning_status === ProvisioningStatus::Completed->value,
            'failed' => $order->provisioning_status === ProvisioningStatus::Failed->value,
        ]);
    }

    public function retry(Order $order, string $token)
    {
        abort_unless($order->provisioning_token && hash_equals($order->provisioning_token, $token), 404);
        abort_unless($order->provisioning_status === ProvisioningStatus::Failed->value, 422);

        $order->update([
            'provisioning_status' => ProvisioningStatus::Queued->value,
            'provisioning_error' => null,
        ]);

        ProvisionSiteJob::dispatch($order);

        return redirect()
            ->route('provisioning.show', ['order' => $order, 'token' => $token])
            ->with('success', 'Đã gửi lại yêu cầu cài đặt.');
    }
}
