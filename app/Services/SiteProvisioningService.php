<?php

namespace App\Services;

use App\Enums\ProvisioningStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class SiteProvisioningService
{
    public function __construct(private CloudflareService $cloudflare) {}

    public function provision(Order $order): void
    {
        $domain = $this->normalizeDomain((string) $order->domain);

        if ($domain === '') {
            throw new \InvalidArgumentException('Domain is required for full package orders.');
        }

        $order->update([
            'provisioning_status' => ProvisioningStatus::Queued->value,
            'provisioning_started_at' => now(),
            'provisioning_completed_at' => null,
            'provisioning_error' => null,
            'provisioning_log' => [],
        ]);

        $this->log($order, 'Bắt đầu cài đặt website cho '.$domain);

        $zoneId = $this->configureDns($order, $domain);
        $credentials = $this->installWordPress($order->customer_email, $domain, null, function (string $message) use ($order): void {
            if ($order->fresh()?->provisioning_status !== ProvisioningStatus::InstallingWordpress->value) {
                $order->update(['provisioning_status' => ProvisioningStatus::InstallingWordpress->value]);
            }
            $this->log($order, $message);
        });
        $this->configureSsl($order, $domain);

        $order->update([
            'provisioning_status' => ProvisioningStatus::Completed->value,
            'provisioning_completed_at' => now(),
            'site_url' => $credentials['site_url'],
            'wp_admin_user' => $credentials['wp_admin_user'],
            'wp_admin_password' => encrypt($credentials['wp_admin_password']),
            'cloudflare_zone_id' => $zoneId,
            'status' => Order::STATUS_DELIVERED,
        ]);

        $order->theme()->increment('sales_count');
        $this->log($order, 'Hoàn tất! Website đã sẵn sàng tại '.$credentials['site_url']);
    }

    /**
     * @param  callable(string): void|null  $logger
     * @return array{site_url: string, site_dir: string, wp_admin_user: string, wp_admin_password: string}
     */
    public function provisionStandalone(
        string $domain,
        string $adminEmail,
        ?string $siteFolder = null,
        bool $configureDns = true,
        ?callable $logger = null,
    ): array {
        $domain = $this->normalizeDomain($domain);
        $log = $logger ?? static function (): void {};

        if ($domain === '') {
            throw new \InvalidArgumentException('Domain is required.');
        }

        $log('Bắt đầu cài đặt website cho '.$domain);

        if ($configureDns) {
            $this->configureDnsStandalone($domain, $log);
        } else {
            $log('Bỏ qua DNS — hãy trỏ domain về IP '.config('provisioning.server_ip'));
        }

        $credentials = $this->installWordPress($adminEmail, $domain, $siteFolder, $log);
        $log('HTTPS được cấu hình qua Let\'s Encrypt (nếu certbot có sẵn).');
        $log('Hoàn tất! Website đã sẵn sàng tại '.$credentials['site_url']);

        return $credentials;
    }

    private function configureDnsStandalone(string $domain, callable $log): ?string
    {
        $log('Đang cấu hình DNS qua Cloudflare...');

        if (! $this->cloudflare->isEnabled()) {
            $log('Cloudflare chưa bật — bỏ qua DNS tự động. Hãy trỏ domain về IP '.config('provisioning.server_ip'));

            return null;
        }

        $zone = $this->cloudflare->ensureZone($domain);
        $this->cloudflare->pointDomainToServer(
            $zone['zone_id'],
            $domain,
            config('provisioning.server_ip'),
        );

        $nameservers = $this->cloudflare->nameservers($zone['zone_id']);
        if ($zone['created'] && $nameservers !== []) {
            $log('Zone mới — cập nhật nameserver tại nhà đăng ký: '.implode(', ', $nameservers));
        } else {
            $log('DNS A record đã trỏ '.$domain.' về '.config('provisioning.server_ip').' (zone '.$zone['zone_name'].')');
        }

        return $zone['zone_id'];
    }

    private function configureDns(Order $order, string $domain): ?string
    {
        $order->update(['provisioning_status' => ProvisioningStatus::ConfiguringDns->value]);
        $this->log($order, 'Đang cấu hình DNS qua Cloudflare...');

        if (! $this->cloudflare->isEnabled()) {
            $this->log($order, 'Cloudflare chưa bật — bỏ qua DNS tự động. Hãy trỏ domain về IP '.config('provisioning.server_ip'));

            return null;
        }

        $zone = $this->cloudflare->ensureZone($domain);
        $this->cloudflare->pointDomainToServer(
            $zone['zone_id'],
            $domain,
            config('provisioning.server_ip'),
        );

        $nameservers = $this->cloudflare->nameservers($zone['zone_id']);
        if ($zone['created'] && $nameservers !== []) {
            $this->log($order, 'Zone mới — cập nhật nameserver tại nhà đăng ký: '.implode(', ', $nameservers));
        } else {
            $this->log($order, 'DNS A record đã trỏ về '.config('provisioning.server_ip'));
        }

        return $zone['zone_id'];
    }

    /**
     * @return array{site_url: string, site_dir: string, wp_admin_user: string, wp_admin_password: string}
     */
    private function installWordPress(
        string $adminEmail,
        string $domain,
        ?string $siteFolder = null,
        ?callable $logger = null,
    ): array {
        $log = $logger ?? static function (): void {};
        $log('Đang cài đặt WordPress...');

        $script = base_path('scripts/provision-wordpress.sh');
        $sitesPath = config('provisioning.sites_path');
        $serverIp = config('provisioning.server_ip');
        $adminUser = config('provisioning.wordpress.admin_user');
        $folder = $siteFolder ?: $domain;

        $command = sprintf(
            'bash %s %s %s %s %s %s',
            escapeshellarg($script),
            escapeshellarg($domain),
            escapeshellarg($adminEmail),
            escapeshellarg($sitesPath),
            escapeshellarg($serverIp),
            escapeshellarg($folder),
        );

        $env = ['WP_ADMIN_USER' => $adminUser];
        $output = $this->runRemoteCommand($command, $env);

        if (! str_contains($output, 'PROVISION_OK')) {
            throw new \RuntimeException('WordPress install failed: '.Str::limit($output, 500));
        }

        $parsed = $this->parseProvisionOutput($output);
        $parsed['site_dir'] = rtrim($sitesPath, '/').'/'.$folder;
        $log('WordPress đã cài xong tại '.$parsed['site_url']);

        return $parsed;
    }

    private function configureSsl(Order $order, string $domain): void
    {
        $order->update(['provisioning_status' => ProvisioningStatus::ConfiguringSsl->value]);
        $this->log($order, 'HTTPS đã được cấu hình trong quá trình cài WordPress (Let\'s Encrypt).');
    }

    /**
     * @param  array<string, string>  $env
     */
    private function runRemoteCommand(string $command, array $env = []): string
    {
        if (config('provisioning.run_local')) {
            $result = Process::timeout(600)->env($env)->run($command);

            if (! $result->successful()) {
                throw new \RuntimeException($result->errorOutput() ?: $result->output());
            }

            return $result->output();
        }

        $sshCommand = $this->buildSshCommand($command);
        $sshEnv = $env;

        if (! config('provisioning.ssh.key') && ! config('provisioning.ssh.password')) {
            $agentSocket = getenv('SSH_AUTH_SOCK') ?: '/run/host-services/ssh-auth.sock';
            if (is_string($agentSocket) && $agentSocket !== '' && file_exists($agentSocket)) {
                $sshEnv['SSH_AUTH_SOCK'] = $agentSocket;
            }
        }

        $result = Process::timeout(600)->env($sshEnv)->run($sshCommand);

        if (! $result->successful()) {
            Log::error('SSH provisioning failed', [
                'output' => $result->output(),
                'error' => $result->errorOutput(),
            ]);
            throw new \RuntimeException($result->errorOutput() ?: $result->output());
        }

        return $result->output();
    }

    private function buildSshCommand(string $remoteCommand): string
    {
        $ssh = config('provisioning.ssh');
        $user = $ssh['user'];
        $host = $ssh['host'];
        $port = $ssh['port'];

        $batchMode = $ssh['password'] ? '' : ' -o BatchMode=yes';
        $options = sprintf(
            '-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null%s -p %d',
            $batchMode,
            $port,
        );

        if ($ssh['key']) {
            $options .= ' -i '.escapeshellarg($ssh['key']);
        }

        if ($ssh['password']) {
            return sprintf(
                'sshpass -p %s ssh %s %s@%s %s',
                escapeshellarg($ssh['password']),
                $options,
                escapeshellarg($user),
                escapeshellarg($host),
                escapeshellarg($remoteCommand),
            );
        }

        return sprintf(
            'ssh %s %s@%s %s',
            $options,
            escapeshellarg($user),
            escapeshellarg($host),
            escapeshellarg($remoteCommand),
        );
    }

    /**
     * @return array{site_url: string, wp_admin_user: string, wp_admin_password: string}
     */
    private function parseProvisionOutput(string $output): array
    {
        $data = [
            'site_url' => 'https://example.com',
            'wp_admin_user' => config('provisioning.wordpress.admin_user'),
            'wp_admin_password' => '',
        ];

        foreach (explode("\n", $output) as $line) {
            if (str_starts_with($line, 'SITE_URL=')) {
                $data['site_url'] = substr($line, 9);
            }
            if (str_starts_with($line, 'WP_ADMIN_USER=')) {
                $data['wp_admin_user'] = substr($line, 14);
            }
            if (str_starts_with($line, 'WP_ADMIN_PASS=')) {
                $data['wp_admin_password'] = substr($line, 14);
            }
        }

        if ($data['wp_admin_password'] === '') {
            throw new \RuntimeException('Could not parse WordPress credentials from install output.');
        }

        return $data;
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain) ?? $domain;
        $domain = rtrim($domain, '/');

        return $domain;
    }

    private function log(Order $order, string $message): void
    {
        $log = $order->provisioning_log ?? [];
        $log[] = ['at' => now()->toIso8601String(), 'message' => $message];
        $order->update(['provisioning_log' => $log]);
        Log::info('Provisioning', ['order' => $order->order_code, 'message' => $message]);
    }
}
