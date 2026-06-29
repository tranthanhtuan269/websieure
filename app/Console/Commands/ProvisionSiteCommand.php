<?php

namespace App\Console\Commands;

use App\Services\SiteProvisioningService;
use Illuminate\Console\Command;

class ProvisionSiteCommand extends Command
{
    protected $signature = 'site:provision
        {domain : Tên miền, ví dụ banhang01.lamwebre.com}
        {--email=admin@lamwebre.com : Email quản trị WordPress}
        {--folder= : Thư mục site trên server (mặc định = tên miền)}
        {--skip-dns : Bỏ qua cấu hình DNS Cloudflare}';

    protected $description = 'Cài WordPress lên server và trỏ tên miền (DNS + nginx + SSL)';

    public function handle(SiteProvisioningService $provisioning): int
    {
        $domain = (string) $this->argument('domain');
        $folder = (string) ($this->option('folder') ?: '');
        $email = (string) $this->option('email');

        $this->info("Đang cài WordPress cho {$domain}...");

        try {
            $result = $provisioning->provisionStandalone(
                domain: $domain,
                adminEmail: $email,
                siteFolder: $folder !== '' ? $folder : null,
                configureDns: ! $this->option('skip-dns'),
                logger: fn (string $message) => $this->line($message),
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Hoàn tất!');
        $this->table(
            ['Thông tin', 'Giá trị'],
            [
                ['URL', $result['site_url']],
                ['Thư mục', $result['site_dir']],
                ['WP Admin', $result['wp_admin_user']],
                ['WP Password', $result['wp_admin_password']],
            ],
        );

        return self::SUCCESS;
    }
}
