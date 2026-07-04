<?php

namespace App\Services;

use App\Models\LandingPageGeneration;
use Illuminate\Support\Facades\File;
use phpseclib3\Net\SFTP;

class LandingPagePackDeployService
{
    public function __construct(
        private LandingPagePackExportService $exportService,
    ) {}

    /**
     * @param  array{
     *     page_type: string,
     *     method: string,
     *     host?: string,
     *     port?: int,
     *     username?: string,
     *     password?: string,
     *     remote_path: string,
     *     site_url?: string|null
     * }  $input
     * @return array{success: bool, message: string, site_url?: string|null, files_uploaded: int}
     */
    public function deploy(LandingPageGeneration $generation, array $input): array
    {
        if (! $generation->isCompleted()) {
            throw new \RuntimeException('Gói landing chưa hoàn tất — không thể deploy.');
        }

        $pageType = $input['page_type'];
        $sourceDir = $this->exportService->pageExportDirectory($generation->id, $pageType);

        if ($sourceDir === null || ! File::isDirectory($sourceDir)) {
            throw new \RuntimeException('Không tìm thấy thư mục export cho landing page này.');
        }

        $fileCount = count(File::allFiles($sourceDir));

        if ($fileCount === 0) {
            throw new \RuntimeException('Thư mục landing trống — không có file để upload.');
        }

        $method = $input['method'];

        if ($method === 'local') {
            $targetDir = $this->resolveLocalTarget($input['remote_path']);
            $this->deployToLocal($sourceDir, $targetDir);

            return [
                'success' => true,
                'message' => 'Đã đẩy '.$fileCount.' file lên server (local).',
                'site_url' => $input['site_url'] ?? null,
                'files_uploaded' => $fileCount,
                'deployed_path' => $targetDir,
            ];
        }

        $this->deployViaSftp(
            $sourceDir,
            $input['host'] ?? '',
            (int) ($input['port'] ?? 22),
            $input['username'] ?? '',
            $input['password'] ?? '',
            $input['remote_path'],
        );

        return [
            'success' => true,
            'message' => 'Đã đẩy '.$fileCount.' file lên hosting qua SFTP.',
            'site_url' => $input['site_url'] ?? null,
            'files_uploaded' => $fileCount,
        ];
    }

    private function deployToLocal(string $sourceDir, string $targetDir): void
    {
        if (File::exists($targetDir)) {
            File::deleteDirectory($targetDir);
        }

        File::copyDirectory($sourceDir, $targetDir);
    }

    private function deployViaSftp(
        string $sourceDir,
        string $host,
        int $port,
        string $username,
        string $password,
        string $remotePath,
    ): void {
        if ($host === '' || $username === '') {
            throw new \RuntimeException('Thiếu host hoặc username SFTP.');
        }

        $remotePath = $this->normalizeRemotePath($remotePath);

        $sftp = new SFTP($host, $port, 30);

        if (! $sftp->login($username, $password)) {
            throw new \RuntimeException('Đăng nhập SFTP thất bại — kiểm tra host, user và mật khẩu.');
        }

        $this->uploadDirectory($sftp, $sourceDir, $remotePath);
    }

    private function uploadDirectory(SFTP $sftp, string $localDir, string $remotePath): void
    {
        $remotePath = rtrim($remotePath, '/');

        if (! $sftp->mkdir($remotePath, -1, true) && ! $sftp->is_dir($remotePath)) {
            throw new \RuntimeException('Không tạo được thư mục đích trên hosting: '.$remotePath);
        }

        foreach (File::allFiles($localDir) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());
            $remoteFile = $remotePath.'/'.$relative;
            $remoteDir = dirname($remoteFile);

            if ($remoteDir !== '.' && $remoteDir !== '/') {
                $sftp->mkdir($remoteDir, -1, true);
            }

            if (! $sftp->put($remoteFile, $file->getPathname(), SFTP::SOURCE_LOCAL_FILE)) {
                throw new \RuntimeException('Upload thất bại: '.$relative);
            }
        }
    }

    private function resolveLocalTarget(string $folder): string
    {
        if (! config('landing.deploy.local_enabled')) {
            throw new \RuntimeException('Deploy local chưa được bật trên server này.');
        }

        $folder = trim($folder, '/');

        if ($folder === '' || str_contains($folder, '..') || str_contains($folder, '\\')) {
            throw new \RuntimeException('Tên thư mục deploy không hợp lệ.');
        }

        if (! preg_match('/^[a-zA-Z0-9._-]+$/', $folder)) {
            throw new \RuntimeException('Tên thư mục chỉ được dùng chữ, số, dấu chấm, gạch ngang.');
        }

        $base = rtrim(config('landing.deploy.local_base_path'), '/');
        $target = $base.'/'.$folder;

        if (! str_starts_with($target, $base.'/')) {
            throw new \RuntimeException('Đường dẫn deploy local không hợp lệ.');
        }

        return $target;
    }

    private function normalizeRemotePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));

        if ($path === '' || ! str_starts_with($path, '/') || str_contains($path, '..')) {
            throw new \RuntimeException('Đường dẫn remote phải là absolute path (bắt đầu bằng /), không chứa ..');
        }

        return $path;
    }

    /**
     * @return array<string, string>
     */
    public function pageTypeLabels(): array
    {
        return [
            'standard' => 'Landing chuẩn ~3000 từ (3 ảnh)',
            'compare' => 'Bảng so sánh đối thủ',
            'popup' => 'Landing popup Cookie Notice',
        ];
    }
}
