<?php

namespace App\Services;

use App\Enums\CommissionStatus;
use App\Enums\PayoutStatus;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayoutRequest;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class AffiliateService
{
    public function generateReferralCode(User $user): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::query()->where('referral_code', $code)->exists());

        return $code;
    }

    public function ensureReferralCode(User $user): User
    {
        if ($user->referral_code) {
            return $user;
        }

        $user->update([
            'referral_code' => $this->generateReferralCode($user),
        ]);

        return $user->fresh();
    }

    public function referralLink(User $user): string
    {
        $user = $this->ensureReferralCode($user);

        return url('/?ref=' . $user->referral_code);
    }

    public function trackReferral(Request $request): void
    {
        $code = strtoupper(trim((string) $request->query('ref', '')));

        if ($code === '') {
            return;
        }

        $referrer = User::query()->where('referral_code', $code)->first();

        if (! $referrer) {
            return;
        }

        if (auth()->id() === $referrer->id) {
            return;
        }

        Cookie::queue(
            config('affiliate.cookie_name'),
            $code,
            config('affiliate.cookie_days') * 24 * 60
        );
    }

    public function resolveReferrer(?Request $request = null): ?User
    {
        $request ??= request();
        $code = strtoupper(trim((string) $request->cookie(config('affiliate.cookie_name'), '')));

        if ($code === '') {
            return null;
        }

        $referrer = User::query()->where('referral_code', $code)->first();

        if (! $referrer) {
            return null;
        }

        if (auth()->id() === $referrer->id) {
            return null;
        }

        return $referrer;
    }

    public function attachReferrerToOrder(Order $order): void
    {
        if ($order->referrer_id) {
            return;
        }

        $referrer = $this->resolveReferrer();

        if (! $referrer) {
            return;
        }

        if ($order->user_id && $order->user_id === $referrer->id) {
            return;
        }

        if (strcasecmp($order->customer_email, $referrer->email) === 0) {
            return;
        }

        $order->update(['referrer_id' => $referrer->id]);
    }

    public function syncCommissionForOrder(Order $order): void
    {
        if (! $order->referrer_id) {
            return;
        }

        $commission = AffiliateCommission::query()->firstOrNew(['order_id' => $order->id]);
        $paidStatuses = [Order::STATUS_PAID, Order::STATUS_DELIVERED];

        if ($order->status === Order::STATUS_CANCELLED) {
            if ($commission->exists && $commission->status === CommissionStatus::Approved) {
                $this->reverseCommission($commission);
            } elseif ($commission->exists) {
                $commission->update(['status' => CommissionStatus::Cancelled]);
            }

            return;
        }

        if (! in_array($order->status, $paidStatuses, true)) {
            $commission->fill([
                'referrer_id' => $order->referrer_id,
                'buyer_id' => $order->user_id,
                'order_amount' => $order->amount,
                'commission_rate' => $this->commissionRate(),
                'commission_amount' => $this->calculateCommission($order->amount),
                'status' => CommissionStatus::Pending,
            ])->save();

            return;
        }

        if ($commission->exists && $commission->status === CommissionStatus::Approved) {
            return;
        }

        $commission->fill([
            'referrer_id' => $order->referrer_id,
            'buyer_id' => $order->user_id,
            'order_amount' => $order->amount,
            'commission_rate' => $this->commissionRate(),
            'commission_amount' => $this->calculateCommission($order->amount),
            'status' => CommissionStatus::Approved,
        ])->save();

        $referrer = $commission->referrer()->first();

        if ($referrer) {
            $referrer->increment('affiliate_balance', $commission->commission_amount);
            $referrer->increment('affiliate_total_earned', $commission->commission_amount);
        }
    }

    public function availableBalance(User $user): int
    {
        $pending = AffiliatePayoutRequest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [PayoutStatus::Pending, PayoutStatus::Approved])
            ->sum('amount');

        return max(0, $user->affiliate_balance - $pending);
    }

    public function calculateCommission(int $amount): int
    {
        return (int) round($amount * $this->commissionRate() / 100);
    }

    public function commissionRate(): int
    {
        return Setting::commissionRate();
    }

    public function updateCommissionRate(int $rate): void
    {
        Setting::set('affiliate_commission_rate', $rate);

        AffiliateCommission::query()
            ->where('status', CommissionStatus::Pending)
            ->each(function (AffiliateCommission $commission): void {
                $commission->update([
                    'commission_rate' => $rate,
                    'commission_amount' => (int) round($commission->order_amount * $rate / 100),
                ]);
            });
    }

    private function reverseCommission(AffiliateCommission $commission): void
    {
        $referrer = $commission->referrer;

        if ($referrer) {
            $referrer->update([
                'affiliate_balance' => max(0, $referrer->affiliate_balance - $commission->commission_amount),
                'affiliate_total_earned' => max(0, $referrer->affiliate_total_earned - $commission->commission_amount),
            ]);
        }

        $commission->update(['status' => CommissionStatus::Cancelled]);
    }
}
