<?php

namespace App\Enums;

enum CommissionStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ thanh toán đơn',
            self::Approved => 'Đã duyệt',
            self::Cancelled => 'Đã hủy',
        };
    }
}
