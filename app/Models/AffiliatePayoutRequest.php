<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliatePayoutRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'payment_method',
        'payment_info',
        'admin_note',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PayoutStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return $this->status->label();
    }
}
