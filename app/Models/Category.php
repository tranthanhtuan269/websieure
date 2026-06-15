<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'color', 'description', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class);
    }

    public function activeThemes(): HasMany
    {
        return $this->themes()->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
