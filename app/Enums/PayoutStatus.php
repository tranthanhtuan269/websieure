<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ duyệt',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Từ chối',
            self::Paid => 'Đã thanh toán',
        };
    }
}
