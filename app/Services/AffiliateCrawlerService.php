<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AffiliateCrawlerService
{
    private function httpClient(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml',
            'Accept-Language' => 'vi,en;q=0.9',
        ])->timeout(25);

        if (config('landing.crawler.verify_ssl') === false) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * @return array{title: string, description: string, images: list<string>}
     */
    public function crawl(string $url): array
    {
        $response = $this->httpClient()->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException('Không thể truy cập link affiliate (HTTP '.$response->status().').');
        }

        $html = $response->body();
        $baseUrl = $this->resolveBaseUrl($url);

        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $title = $this->metaContent($xpath, 'og:title')
            ?: $this->metaContent($xpath, 'twitter:title')
            ?: $this->nodeText($xpath, '//title')
            ?: 'Landing Page';

        $description = $this->metaContent($xpath, 'og:description')
            ?: $this->metaContent($xpath, 'description')
            ?: $this->metaContent($xpath, 'twitter:description')
            ?: '';

        $images = [];

        foreach (['og:image', 'twitter:image'] as $property) {
            $image = $this->metaContent($xpath, $property);
            if ($image) {
                $images[] = $this->absoluteUrl($image, $baseUrl);
            }
        }

        foreach ($xpath->query('//img[@src]') as $img) {
            $src = trim($img->getAttribute('src'));
            if ($src === '' || str_starts_with($src, 'data:')) {
                continue;
            }

            $absolute = $this->absoluteUrl($src, $baseUrl);
            if ($this->isLikelyContentImage($absolute)) {
                $images[] = $absolute;
            }
        }

        $images = array_values(array_unique($images));

        if ($images === []) {
            $images[] = 'https://picsum.photos/seed/'.md5($url).'/1200/800';
        }

        return [
            'title' => Str::limit(html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 200, ''),
            'description' => Str::limit(html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 500, ''),
            'images' => array_slice($images, 0, 10),
        ];
    }

    /**
     * @param  list<string>  $urls
     * @return list<array{url: string, path: string, relative: string}>
     */
    public function downloadImages(array $urls, string $destDir, int $limit = 3): array
    {
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $saved = [];

        foreach (array_slice($urls, 0, $limit) as $index => $url) {
            try {
                $client = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; LamWebReBot/1.0)',
                ])->timeout(20);

                if (config('landing.crawler.verify_ssl') === false) {
                    $client = $client->withoutVerifying();
                }

                $response = $client->get($url);

                if (! $response->successful()) {
                    continue;
                }

                $contentType = $response->header('Content-Type', 'image/jpeg');
                $ext = match (true) {
                    str_contains($contentType, 'png') => 'png',
                    str_contains($contentType, 'webp') => 'webp',
                    str_contains($contentType, 'gif') => 'gif',
                    default => 'jpg',
                };

                $filename = 'image-'.($index + 1).'.'.$ext;
                $path = $destDir.'/'.$filename;
                file_put_contents($path, $response->body());

                $saved[] = [
                    'url' => $url,
                    'path' => $path,
                    'relative' => 'assets/images/'.$filename,
                ];
            } catch (\Throwable) {
                continue;
            }
        }

        if ($saved === [] && $urls !== []) {
            $saved[] = [
                'url' => $urls[0],
                'path' => null,
                'relative' => $urls[0],
            ];
        }

        $saved = $this->ensureImageCount($saved, $destDir, $limit);

        return $saved;
    }

    /**
     * @param  list<array{url: string, path: string|null, relative: string}>  $saved
     * @return list<array{url: string, path: string|null, relative: string}>
     */
    private function ensureImageCount(array $saved, string $destDir, int $limit): array
    {
        $index = count($saved);

        while ($index < $limit) {
            $filename = 'image-'.($index + 1).'.jpg';
            $path = $destDir.'/'.$filename;

            if ($saved !== [] && ($saved[0]['path'] ?? null) && file_exists($saved[0]['path'])) {
                copy($saved[0]['path'], $path);
            } else {
                $seed = md5($destDir.'-'.$index);
                $bytes = @file_get_contents("https://picsum.photos/seed/{$seed}/960/640.jpg");
                if ($bytes === false) {
                    break;
                }
                file_put_contents($path, $bytes);
            }

            $saved[] = [
                'url' => $saved[0]['url'] ?? "https://picsum.photos/seed/{$index}",
                'path' => $path,
                'relative' => 'assets/images/'.$filename,
            ];
            $index++;
        }

        return $saved;
    }

    private function resolveBaseUrl(string $url): string
    {
        $parts = parse_url($url);

        return ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');
    }

    private function absoluteUrl(string $src, string $baseUrl): string
    {
        if (str_starts_with($src, '//')) {
            return 'https:'.$src;
        }

        if (preg_match('#^https?://#i', $src)) {
            return $src;
        }

        if (str_starts_with($src, '/')) {
            return rtrim($baseUrl, '/').$src;
        }

        return rtrim($baseUrl, '/').'/'.ltrim($src, '/');
    }

    private function metaContent(DOMXPath $xpath, string $name): ?string
    {
        $queries = [
            "//meta[@property='{$name}']/@content",
            "//meta[@name='{$name}']/@content",
        ];

        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes && $nodes->length > 0) {
                return trim($nodes->item(0)?->nodeValue ?? '');
            }
        }

        return null;
    }

    private function nodeText(DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);
        if ($nodes && $nodes->length > 0) {
            return trim($nodes->item(0)?->textContent ?? '');
        }

        return null;
    }

    private function isLikelyContentImage(string $url): bool
    {
        $lower = strtolower($url);

        foreach (['icon', 'logo', 'sprite', 'pixel', '1x1', 'spacer', 'avatar', '.svg'] as $needle) {
            if (str_contains($lower, $needle)) {
                return false;
            }
        }

        return (bool) preg_match('/\.(jpe?g|png|webp|gif)(\?|$)/i', $lower) || str_contains($lower, '/image');
    }
}
