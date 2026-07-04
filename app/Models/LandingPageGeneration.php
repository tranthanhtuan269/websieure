<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageGeneration extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'affiliate_url',
        'topic',
        'status',
        'step',
        'progress',
        'crawl_data',
        'pack_data',
        'zip_path',
        'error_message',
    ];

    protected $casts = [
        'crawl_data' => 'array',
        'pack_data' => 'array',
        'progress' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function updateProgress(string $step, int $progress, ?string $status = 'processing'): void
    {
        $this->update([
            'step' => $step,
            'progress' => min(100, max(0, $progress)),
            'status' => $status,
        ]);
    }

    public function fail(string $message): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $message,
        ]);
    }
}
