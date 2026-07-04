<?php

namespace App\Console\Commands;

use App\Services\LandingPageExportService;
use Illuminate\Console\Command;

class ExportLandingPagesCommand extends Command
{
    protected $signature = 'landing-pages:export';

    protected $description = 'Export all landing pages to static HTML folder and zip file';

    public function handle(LandingPageExportService $exporter): int
    {
        $result = $exporter->export();

        $this->info("Đã export {$result['count']} landing page.");
        $this->line("Thư mục: {$result['directory']}");
        $this->line("File zip: {$result['zip']}");

        return self::SUCCESS;
    }
}
