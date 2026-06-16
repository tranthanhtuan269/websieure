<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    ];

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

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => 'Đã thanh toán',
            self::STATUS_DELIVERED => 'Đã giao theme',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Chờ thanh toán',
        };
    }

    public function getRouteKeyName(): string
    {
        return 'order_code';
    }
}
