<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_code', 'theme_id', 'customer_name', 'customer_email',
        'customer_phone', 'amount', 'status', 'note',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
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
