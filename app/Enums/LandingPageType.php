<?php

namespace App\Enums;

enum LandingPageType: string
{
    case Standard = 'standard';
    case Popup = 'popup';
    case Scroll = 'scroll';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Chuẩn ~3000 từ (3 ảnh + CTA)',
            self::Popup => 'Popup Cookie Notice (1 ảnh)',
            self::Scroll => 'So sánh đối thủ + CTA',
        };
    }
}
