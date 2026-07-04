<?php

namespace App\Services;

use App\Enums\LandingPageType;
use App\Models\LandingPage;
use App\Models\LandingPageGeneration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use ZipArchive;

class LandingPagePackExportService
{
    public function generationsBase(): string
    {
        return storage_path('app/exports/generations');
    }

    public function sourceImagesDirectory(string $generationId): string
    {
        return $this->packDirectory($generationId).'/_source/images';
    }

    public function packDirectory(string $generationId): string
    {
        return $this->generationsBase().'/'.$generationId;
    }

    public function packZipPath(string $generationId): string
    {
        return $this->generationsBase().'/lamwebre-pack-'.$generationId.'.zip';
    }

    public function pageExportDirectory(string $generationId, string $pageType): ?string
    {
        $allowed = ['standard', 'compare', 'popup'];
        if (! in_array($pageType, $allowed, true)) {
            return null;
        }

        $directory = $this->packDirectory($generationId).'/'.$pageType;

        return File::isDirectory($directory) ? $directory : null;
    }

    public function exportPack(LandingPageGeneration $generation): string
    {
        $directory = $this->packDirectory($generation->id);
        $sourceImagesDir = $this->sourceImagesDirectory($generation->id);
        $zipPath = $this->packZipPath($generation->id);
        $packData = $generation->pack_data ?? [];
        $affiliateUrl = $generation->affiliate_url;

        File::put($directory.'/README.txt', $this->readmeContent($generation));

        $pages = [
            'standard' => [LandingPageType::Standard, $packData['standard'] ?? [], null],
            'compare' => [LandingPageType::Scroll, $packData['compare'] ?? [], $packData['compare']['compare'] ?? []],
            'popup' => [LandingPageType::Popup, $packData['popup'] ?? [], null],
        ];

        foreach ($pages as $folder => [$type, $data, $compare]) {
            if ($data === []) {
                continue;
            }

            $pageDir = $directory.'/'.$folder;
            $this->installPageAssets($pageDir, $data, $sourceImagesDir);

            $page = $this->toLandingPage($data, $type, $affiliateUrl);
            $view = match ($type) {
                LandingPageType::Standard => 'landing-pages.standard',
                LandingPageType::Popup => 'landing-pages.popup',
                LandingPageType::Scroll => 'landing-pages.compare',
            };

            $viewData = [
                'page' => $page,
                'affiliateUrl' => $affiliateUrl,
                'cssApp' => 'assets/css/app.css',
                'cssLanding' => 'assets/css/landing.css',
                'staticExport' => true,
            ];

            if ($compare !== null) {
                $viewData['compare'] = $compare;
            }

            $html = View::make($view, $viewData)->render();

            File::put($pageDir.'/index.html', $html);
        }

        File::deleteDirectory($directory.'/assets');

        $this->createZip($directory, $zipPath);

        return $zipPath;
    }

    private function installPageAssets(string $pageDir, array $data, string $sourceImagesDir): void
    {
        File::ensureDirectoryExists($pageDir.'/assets/css');
        File::ensureDirectoryExists($pageDir.'/assets/images');

        foreach (['app.css', 'landing.css'] as $cssFile) {
            File::copy(public_path('css/'.$cssFile), $pageDir.'/assets/css/'.$cssFile);
        }

        foreach ($this->collectImageFiles($data) as $filename) {
            $source = $sourceImagesDir.'/'.$filename;

            if (File::exists($source)) {
                File::copy($source, $pageDir.'/assets/images/'.$filename);
            }
        }
    }

    /**
     * @return list<string>
     */
    private function collectImageFiles(array $data): array
    {
        $files = [];

        foreach (['hero_image'] as $key) {
            $file = $this->imageFilename($data[$key] ?? null);
            if ($file) {
                $files[] = $file;
            }
        }

        foreach ($data['sections'] ?? [] as $section) {
            $file = $this->imageFilename($section['image'] ?? null);
            if ($file) {
                $files[] = $file;
            }
        }

        // Standard luôn cần 3 ảnh trong bài
        foreach (['image-1.jpg', 'image-2.jpg', 'image-3.jpg'] as $fallback) {
            $files[] = $fallback;
        }

        return array_values(array_unique($files));
    }

    private function imageFilename(?string $path): ?string
    {
        if (! $path || ! str_starts_with($path, 'assets/images/')) {
            return null;
        }

        return basename($path);
    }

    private function createZip(string $directory, string $zipPath): void
    {
        File::ensureDirectoryExists(dirname($zipPath));

        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Không thể tạo file zip.');
        }

        $root = 'lamwebre-landing-pack';

        foreach (File::allFiles($directory) as $file) {
            $relative = $root.'/'.str_replace('\\', '/', $file->getRelativePathname());
            $zip->addFile($file->getPathname(), $relative);
        }

        $zip->close();
    }

    private function readmeContent(LandingPageGeneration $generation): string
    {
        $topic = $generation->topic ?? 'Landing Page';
        $affiliate = $generation->affiliate_url;

        return <<<TXT
LAMWEBRE — Gói 3 landing page
============================
Chủ đề: {$topic}
Link affiliate: {$affiliate}

Cấu trúc (mỗi folder tự chứa asset, upload độc lập được):
  standard/
    index.html
    assets/css/ + assets/images/
  compare/
    index.html
    assets/css/ + assets/images/
  popup/
    index.html
    assets/css/ + assets/images/

Upload lên hosting:
  1. Giải nén zip
  2. Upload 1 folder (standard, compare hoặc popup) lên hosting / subdomain
  3. Trỏ document root vào folder đó
  4. Truy cập: https://domain.com/

TXT;
    }

    /**
     * @return array{view: string, data: array<string, mixed>}|null
     */
    public function buildPreviewViewData(LandingPageGeneration $generation, string $type): ?array
    {
        $allowed = ['standard', 'compare', 'popup'];
        if (! in_array($type, $allowed, true)) {
            return null;
        }

        $packData = $generation->pack_data ?? [];
        $pageData = $packData[$type] ?? [];

        if ($pageData === []) {
            return null;
        }

        $pageData = $this->mapPreviewImages($pageData, $generation);
        $affiliateUrl = $generation->affiliate_url;

        $landingType = match ($type) {
            'standard' => LandingPageType::Standard,
            'compare' => LandingPageType::Scroll,
            'popup' => LandingPageType::Popup,
        };

        $page = $this->toLandingPage($pageData, $landingType, $affiliateUrl);
        $view = match ($landingType) {
            LandingPageType::Standard => 'landing-pages.standard',
            LandingPageType::Popup => 'landing-pages.popup',
            LandingPageType::Scroll => 'landing-pages.compare',
        };

        $labels = [
            'standard' => 'Landing chuẩn ~3000 từ (3 ảnh)',
            'compare' => 'Bảng so sánh đối thủ',
            'popup' => 'Landing popup Cookie Notice',
        ];

        $viewData = [
            'page' => $page,
            'affiliateUrl' => $affiliateUrl,
            'reviewMode' => true,
            'reviewGeneration' => $generation,
            'reviewPageType' => $type,
            'reviewPageLabel' => $labels[$type] ?? $type,
        ];

        if ($type === 'compare') {
            $viewData['compare'] = $pageData['compare'] ?? [];
        }

        return ['view' => $view, 'data' => $viewData];
    }

    public function previewImagePath(LandingPageGeneration $generation, string $filename): ?string
    {
        if (! preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
            return null;
        }

        $path = $this->sourceImagesDirectory($generation->id).'/'.$filename;

        return File::exists($path) ? $path : null;
    }

    /**
     * @return list<array{type: string, label: string, url: string}>
     */
    public function previewLinks(LandingPageGeneration $generation): array
    {
        $packData = $generation->pack_data ?? [];
        $links = [];

        $types = [
            'standard' => 'Landing chuẩn ~3000 từ (3 ảnh)',
            'compare' => 'Bảng so sánh đối thủ',
            'popup' => 'Landing popup Cookie Notice',
        ];

        foreach ($types as $type => $label) {
            if (empty($packData[$type])) {
                continue;
            }

            $links[] = [
                'type' => $type,
                'label' => $label,
                'url' => route('admin.landing-pages.generator.preview', [$generation, $type]),
            ];
        }

        return $links;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function mapPreviewImages(array $data, LandingPageGeneration $generation): array
    {
        if (! empty($data['hero_image'])) {
            $data['hero_image'] = $this->previewImageUrl($generation, $data['hero_image']) ?? $data['hero_image'];
        }

        foreach ($data['sections'] ?? [] as $index => $section) {
            if (! empty($section['image'])) {
                $data['sections'][$index]['image'] = $this->previewImageUrl($generation, $section['image']) ?? $section['image'];
            }
        }

        return $data;
    }

    private function previewImageUrl(LandingPageGeneration $generation, ?string $relativePath): ?string
    {
        if (! $relativePath || ! str_starts_with($relativePath, 'assets/images/')) {
            return null;
        }

        $filename = basename($relativePath);

        if (! $this->previewImagePath($generation, $filename)) {
            return null;
        }

        return route('admin.landing-pages.generator.preview.image', [$generation, $filename]);
    }

    private function toLandingPage(array $data, LandingPageType $type, string $affiliateUrl): LandingPage
    {
        return new LandingPage([
            'type' => $type,
            'title' => $data['title'] ?? 'Landing Page',
            'meta_description' => $data['meta_description'] ?? null,
            'hero_image' => $data['hero_image'] ?? null,
            'affiliate_url' => $affiliateUrl,
            'intro' => $data['intro'] ?? null,
            'sections' => $data['sections'] ?? null,
            'body' => $data['body'] ?? null,
            'popup_settings' => $data['popup_settings'] ?? null,
            'is_active' => true,
        ]);
    }
}
