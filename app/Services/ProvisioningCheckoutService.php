<?php

namespace App\Services;

use App\Jobs\ProvisionSiteJob;
use App\Models\Order;
use Illuminate\Support\Str;

class ProvisioningCheckoutService
{
    public function generateOrderCode(): string
    {
        do {
            $code = 'WSR-'.strtoupper(Str::random(8));
        } while (Order::query()->where('order_code', $code)->exists());

        return $code;
    }

    public function generateProvisioningToken(): string
    {
        do {
            $token = Str::random(48);
        } while (Order::query()->where('provisioning_token', $token)->exists());

        return $token;
    }

    public function startProvisioning(Order $order): void
    {
        if (! $order->isFullPackage()) {
            return;
        }

        if ($order->provisioning_status === \App\Enums\ProvisioningStatus::Completed->value) {
            return;
        }

        if (in_array($order->provisioning_status, [
            \App\Enums\ProvisioningStatus::Queued->value,
            \App\Enums\ProvisioningStatus::ConfiguringDns->value,
            \App\Enums\ProvisioningStatus::InstallingWordpress->value,
            \App\Enums\ProvisioningStatus::ConfiguringSsl->value,
        ], true)) {
            return;
        }

        $order->update([
            'status' => Order::STATUS_PAID,
            'provisioning_status' => \App\Enums\ProvisioningStatus::Queued->value,
        ]);

        ProvisionSiteJob::dispatch($order->fresh());
    }
}
