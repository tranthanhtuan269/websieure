<?php

namespace App\Models;

use App\Enums\CommissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    protected $fillable = [
        'order_id',
        'referrer_id',
        'buyer_id',
        'order_amount',
        'commission_rate',
        'commission_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CommissionStatus::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function statusLabel(): string
    {
        return $this->status->label();
    }
}
