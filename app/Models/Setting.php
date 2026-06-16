<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::rememberForever('setting:' . $key, function () use ($key) {
            return static::query()->find($key);
        });

        return $setting?->value ?? $default;
    }

    public static function getInt(string $key, int $default = 0): int
    {
        return (int) static::get($key, $default);
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );

        Cache::forget('setting:' . $key);
    }

    public static function commissionRate(): int
    {
        return static::getInt('affiliate_commission_rate', (int) config('affiliate.commission_rate'));
    }
}
