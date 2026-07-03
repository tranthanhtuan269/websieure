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
            self::Standard => 'Chuẩn 3 phần (~3000 từ)',
            self::Popup => 'Popup chấp nhận',
            self::Scroll => 'Cuộn đơn giản (~1000 từ)',
        };
    }
}
