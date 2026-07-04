<?php

namespace App\Models;

use App\Enums\LandingPageType;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'type',
        'title',
        'slug',
        'meta_description',
        'hero_image',
        'affiliate_url',
        'intro',
        'sections',
        'body',
        'popup_settings',
        'is_active',
    ];

    protected $casts = [
        'type' => LandingPageType::class,
        'sections' => 'array',
        'popup_settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolvedAffiliateUrl(): string
    {
        return $this->affiliate_url ?: config('landing.default_affiliate_url', url('/'));
    }

    public function wordCount(): int
    {
        $text = strip_tags((string) $this->intro . ' ' . (string) $this->body);

        if (is_array($this->sections)) {
            foreach ($this->sections as $section) {
                $text .= ' ' . strip_tags((string) ($section['content'] ?? ''));
            }
        }

        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
    }

    public function typeLabel(): string
    {
        return $this->type->label();
    }
}
