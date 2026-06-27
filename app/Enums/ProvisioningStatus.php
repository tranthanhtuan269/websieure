<?php

namespace App\Enums;

enum ProvisioningStatus: string
{
    case Queued = 'queued';
    case ConfiguringDns = 'configuring_dns';
    case InstallingWordpress = 'installing_wordpress';
    case ConfiguringSsl = 'configuring_ssl';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Queued => 'Đang chờ xử lý',
            self::ConfiguringDns => 'Cấu hình DNS (Cloudflare)',
            self::InstallingWordpress => 'Cài đặt WordPress',
            self::ConfiguringSsl => 'Cài đặt HTTPS',
            self::Completed => 'Hoàn tất — website đã sẵn sàng',
            self::Failed => 'Thất bại',
        };
    }

    public function step(): int
    {
        return match ($this) {
            self::Queued => 1,
            self::ConfiguringDns => 2,
            self::InstallingWordpress => 3,
            self::ConfiguringSsl => 4,
            self::Completed => 5,
            self::Failed => 0,
        };
    }
}
