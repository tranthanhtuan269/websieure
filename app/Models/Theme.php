<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'tagline', 'description', 'features',
        'price', 'sale_price', 'preview_url', 'thumbnail_image', 'thumbnail_color', 'thumbnail_label',
        'is_featured', 'is_active', 'sales_count',
    ];

    protected $casts = [
        'features' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currentPrice(): int
    {
        return $this->sale_price && $this->sale_price < $this->price
            ? $this->sale_price
            : $this->price;
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function discountPercent(): ?int
    {
        if (! $this->isOnSale() || $this->price <= 0) {
            return null;
        }

        return (int) round((1 - $this->sale_price / $this->price) * 100);
    }

    public function formattedPrice(): string
    {
        return number_format($this->currentPrice(), 0, ',', '.') . ' ₫';
    }

    public function thumbnailUrl(): ?string
    {
        if (! $this->thumbnail_image) {
            return null;
        }

        return asset('storage/' . ltrim($this->thumbnail_image, '/'));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
