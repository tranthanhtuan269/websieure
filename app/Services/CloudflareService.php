<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    private string $baseUrl = 'https://api.cloudflare.com/client/v4';

    public function isEnabled(): bool
    {
        return config('provisioning.cloudflare.enabled')
            && config('provisioning.cloudflare.api_token')
            && config('provisioning.cloudflare.account_id');
    }

    /**
     * @return array{zone_id: string, created: bool}
     */
    public function ensureZone(string $domain): array
    {
        $existing = $this->findZone($domain);
        if ($existing) {
            return ['zone_id' => $existing, 'created' => false];
        }

        $response = $this->request('POST', '/zones', [
            'name' => $domain,
            'account' => ['id' => config('provisioning.cloudflare.account_id')],
            'type' => 'full',
        ]);

        return [
            'zone_id' => $response['result']['id'],
            'created' => true,
        ];
    }

    public function pointDomainToServer(string $zoneId, string $domain, string $ip): void
    {
        $this->upsertDnsRecord($zoneId, 'A', $domain, $ip, proxied: true);
        $this->upsertDnsRecord($zoneId, 'A', 'www.'.$domain, $ip, proxied: true);
    }

    /**
     * @return array<int, string>
     */
    public function nameservers(string $zoneId): array
    {
        $response = $this->request('GET', "/zones/{$zoneId}");

        return $response['result']['name_servers'] ?? [];
    }

    private function findZone(string $domain): ?string
    {
        $response = $this->request('GET', '/zones', [
            'name' => $domain,
            'status' => 'active,pending,initializing',
        ]);

        foreach ($response['result'] ?? [] as $zone) {
            if (strcasecmp($zone['name'], $domain) === 0) {
                return $zone['id'];
            }
        }

        return null;
    }

    private function upsertDnsRecord(
        string $zoneId,
        string $type,
        string $name,
        string $content,
        bool $proxied = false,
    ): void {
        $existing = $this->request('GET', "/zones/{$zoneId}/dns_records", [
            'type' => $type,
            'name' => $name,
        ]);

        $payload = [
            'type' => $type,
            'name' => $name,
            'content' => $content,
            'ttl' => 1,
            'proxied' => $proxied,
        ];

        if (! empty($existing['result'][0]['id'])) {
            $recordId = $existing['result'][0]['id'];
            $this->request('PUT', "/zones/{$zoneId}/dns_records/{$recordId}", $payload);

            return;
        }

        $this->request('POST', "/zones/{$zoneId}/dns_records", $payload);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $data = []): array
    {
        $url = $this->baseUrl.$path;
        $pending = Http::withToken(config('provisioning.cloudflare.api_token'))
            ->acceptJson()
            ->timeout(30);

        $response = match (strtoupper($method)) {
            'GET' => $pending->get($url, $data),
            'POST' => $pending->post($url, $data),
            'PUT' => $pending->put($url, $data),
            'DELETE' => $pending->delete($url, $data),
            default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };

        $body = $response->json();

        if (! ($body['success'] ?? false)) {
            $errors = collect($body['errors'] ?? [])->pluck('message')->implode('; ');
            Log::error('Cloudflare API error', ['path' => $path, 'errors' => $body['errors'] ?? []]);
            throw new \RuntimeException($errors ?: 'Cloudflare API request failed.');
        }

        return $body;
    }
}
