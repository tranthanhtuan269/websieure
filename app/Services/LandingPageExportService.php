<?php

namespace App\Services;

use App\Enums\LandingPageType;
use App\Models\LandingPage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use ZipArchive;

class LandingPageExportService
{
    public function exportDirectory(): string
    {
        return config('landing.export_directory');
    }

    public function exportZipPath(): string
    {
        return config('landing.export_zip');
    }

    /**
     * @return array{directory: string, zip: string, count: int}
     */
    public function export(): array
    {
        $directory = $this->exportDirectory();
        $zipPath = $this->exportZipPath();

        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }

        File::ensureDirectoryExists($directory);
        File::ensureDirectoryExists($directory.'/assets/css');

        foreach (['app.css', 'landing.css'] as $cssFile) {
            File::copy(
                public_path('css/'.$cssFile),
                $directory.'/assets/css/'.$cssFile
            );
        }

        File::put($directory.'/README.txt', $this->readmeContent());

        $pages = LandingPage::query()->orderBy('slug')->get();
        $count = 0;

        foreach ($pages as $page) {
            $view = match ($page->type) {
                LandingPageType::Standard => 'landing-pages.standard',
                LandingPageType::Popup => 'landing-pages.popup',
                LandingPageType::Scroll => 'landing-pages.scroll',
            };

            $pageDir = $directory.'/'.$page->slug;
            File::ensureDirectoryExists($pageDir);

            $html = View::make($view, array_merge($this->viewData($page), [
                'staticExport' => true,
            ]))->render();

            File::put($pageDir.'/index.html', $html);
            $count++;
        }

        $this->createZip($directory, $zipPath);

        return [
            'directory' => $directory,
            'zip' => $zipPath,
            'count' => $count,
        ];
    }

    /**
     * @return array{page: LandingPage, affiliateUrl: string, cssApp: string, cssLanding: string}
     */
    private function viewData(LandingPage $page): array
    {
        return [
            'page' => $page,
            'affiliateUrl' => $page->resolvedAffiliateUrl(),
            'cssApp' => '../assets/css/app.css',
            'cssLanding' => '../assets/css/landing.css',
        ];
    }

    private function createZip(string $directory, string $zipPath): void
    {
        File::ensureDirectoryExists(dirname($zipPath));

        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Không thể tạo file zip: '.$zipPath);
        }

        $root = basename($directory);

        foreach (File::allFiles($directory) as $file) {
            $relative = $root.'/'.str_replace('\\', '/', $file->getRelativePathname());
            $zip->addFile($file->getPathname(), $relative);
        }

        $zip->close();
    }

    private function readmeContent(): string
    {
        return <<<'TXT'
LAMWEBRE — Gói 30 landing page HTML tĩnh
========================================

Cấu trúc thư mục:
  assets/css/     — File CSS dùng chung
  <slug>/         — Mỗi landing page một thư mục
    index.html    — Trang landing

Cách đưa lên hosting:
  1. Giải nén file zip
  2. Upload toàn bộ thư mục lên hosting (public_html hoặc subdomain)
  3. Truy cập: https://domain.com/<slug>/
     Ví dụ: https://domain.com/website-spa/

Gắn từng landing page vào domain riêng:
  - Upload cả thư mục gói lên hosting
  - Trỏ document root subdomain vào thư mục <slug>/
  - Hoặc copy nội dung <slug>/ vào public_html và sửa đường dẫn CSS thành assets/css/...

Lưu ý:
  - Ảnh hero dùng URL từ picsum.photos (cần internet khi xem)
  - Nút CTA trỏ tới link affiliate đã cấu hình trong admin
  - Có thể chỉnh link affiliate trực tiếp trong file index.html

TXT;
    }
}
