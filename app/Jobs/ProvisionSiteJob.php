<?php

namespace App\Jobs;

use App\Enums\PackageType;
use App\Enums\ProvisioningStatus;
use App\Models\Order;
use App\Services\SiteProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProvisionSiteJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 900;

    public function __construct(public Order $order) {}

    public function handle(SiteProvisioningService $provisioning): void
    {
        $order = $this->order->fresh();

        if (! $order || $order->package_type !== PackageType::Full->value) {
            return;
        }

        if ($order->provisioning_status === ProvisioningStatus::Completed->value) {
            return;
        }

        try {
            $provisioning->provision($order);
        } catch (\Throwable $e) {
            Log::error('ProvisionSiteJob failed', [
                'order' => $order->order_code,
                'error' => $e->getMessage(),
            ]);

            $order->update([
                'provisioning_status' => ProvisioningStatus::Failed->value,
                'provisioning_error' => $e->getMessage(),
                'provisioning_completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
