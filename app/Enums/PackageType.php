<?php

namespace App\Enums;

enum PackageType: string
{
    case Theme = 'theme';
    case Full = 'full';

    public function label(): string
    {
        return match ($this) {
            self::Theme => 'Chỉ mua theme',
            self::Full => 'Gói Full (domain + host + cài đặt)',
        };
    }
}
