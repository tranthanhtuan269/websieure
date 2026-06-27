<?php

namespace App\Models;

use App\Enums\PackageType;
use App\Enums\ProvisioningStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Crypt;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_code', 'theme_id', 'user_id', 'referrer_id',
        'customer_name', 'customer_email', 'customer_phone',
        'amount', 'status', 'note',
        'package_type', 'domain', 'provisioning_status', 'provisioning_token',
        'site_url', 'wp_admin_user', 'wp_admin_password',
        'cloudflare_zone_id', 'provisioning_log', 'provisioning_error',
        'provisioning_started_at', 'provisioning_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'provisioning_log' => 'array',
            'provisioning_started_at' => 'datetime',
            'provisioning_completed_at' => 'datetime',
        ];
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function commission(): HasOne
    {
        return $this->hasOne(AffiliateCommission::class);
    }

    public function isFullPackage(): bool
    {
        return $this->package_type === PackageType::Full->value;
    }

    public function provisioningStatusEnum(): ?ProvisioningStatus
    {
        return $this->provisioning_status
            ? ProvisioningStatus::from($this->provisioning_status)
            : null;
    }

    public function decryptedWpPassword(): ?string
    {
        if (! $this->wp_admin_password) {
            return null;
        }

        try {
            return Crypt::decryptString($this->wp_admin_password);
        } catch (\Throwable) {
            return null;
        }
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => 'Đã thanh toán',
            self::STATUS_DELIVERED => 'Đã giao theme',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Chờ thanh toán',
        };
    }

    public function packageLabel(): string
    {
        return PackageType::tryFrom($this->package_type)?->label() ?? $this->package_type;
    }

    public function getRouteKeyName(): string
    {
        return 'order_code';
    }
}
