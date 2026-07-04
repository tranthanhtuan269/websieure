<?php

namespace App\Services;

use App\Models\LandingPageGeneration;
use Illuminate\Support\Str;

class LandingPagePackService
{
    public function __construct(
        private AffiliateCrawlerService $crawler,
        private LandingAiService $ai,
        private LandingPagePackExportService $exporter,
    ) {}

    public function runStep(LandingPageGeneration $generation, string $step): LandingPageGeneration
    {
        try {
            match ($step) {
                'crawl' => $this->stepCrawl($generation),
                'standard' => $this->stepStandard($generation),
                'compare' => $this->stepCompare($generation),
                'popup' => $this->stepPopup($generation),
                'zip' => $this->stepZip($generation),
                default => throw new \InvalidArgumentException('Bước không hợp lệ: '.$step),
            };
        } catch (\Throwable $e) {
            $generation->fail($e->getMessage());
        }

        return $generation->fresh();
    }

    private function stepCrawl(LandingPageGeneration $generation): void
    {
        $generation->updateProgress('crawl', 5, 'processing');

        $crawl = $this->crawler->crawl($generation->affiliate_url);
        $images = $this->crawler->downloadImages(
            $crawl['images'],
            $this->exporter->sourceImagesDirectory($generation->id),
            3
        );

        $generation->update([
            'topic' => $crawl['title'],
            'crawl_data' => [
                'title' => $crawl['title'],
                'description' => $crawl['description'],
                'images' => $crawl['images'],
                'local_images' => $images,
            ],
            'pack_data' => [],
        ]);

        $generation->updateProgress('crawl', 20, 'processing');
    }

    private function stepStandard(LandingPageGeneration $generation): void
    {
        $generation->updateProgress('standard', 25, 'processing');

        $context = $this->aiContext($generation);
        $images = $generation->crawl_data['local_images'] ?? [];

        $data = $this->ai->isConfigured()
            ? $this->ai->generateStandard($context, 3000)
            : $this->ai->fallbackStandard($context, 3000);

        $sections = [];
        foreach ($data['sections'] as $i => $section) {
            $sections[] = [
                'title' => $section['title'],
                'content' => $section['content'],
                'image' => $images[$i]['relative'] ?? 'assets/images/image-'.($i + 1).'.jpg',
            ];
        }

        $packData = $generation->pack_data ?? [];
        $packData['standard'] = [
            'title' => $data['title'],
            'meta_description' => $data['meta_description'],
            'intro' => $data['intro'],
            'hero_image' => null,
            'sections' => $sections,
        ];

        $generation->update(['pack_data' => $packData]);
        $generation->updateProgress('standard', 55, 'processing');
    }

    private function stepCompare(LandingPageGeneration $generation): void
    {
        $generation->updateProgress('compare', 60, 'processing');

        $context = $this->aiContext($generation);
        $images = $generation->crawl_data['local_images'] ?? [];

        $data = $this->ai->isConfigured()
            ? $this->ai->generateCompare($context)
            : $this->ai->fallbackCompare($context);

        $packData = $generation->pack_data ?? [];
        $packData['compare'] = [
            'title' => $data['title'],
            'meta_description' => $data['meta_description'],
            'hero_image' => $images[0]['relative'] ?? null,
            'compare' => [
                'product_name' => $data['product_name'],
                'competitor_name' => $data['competitor_name'],
                'intro' => $data['intro'],
                'summary' => $data['summary'],
                'cta_title' => $data['cta_title'],
                'cta_text' => $data['cta_text'],
                'cta_button' => $data['cta_button'],
                'rows' => $data['rows'],
            ],
        ];

        $generation->update(['pack_data' => $packData]);
        $generation->updateProgress('compare', 75, 'processing');
    }

    private function stepPopup(LandingPageGeneration $generation): void
    {
        $generation->updateProgress('popup', 80, 'processing');

        $context = $this->aiContext($generation);
        $images = $generation->crawl_data['local_images'] ?? [];

        $packData = $generation->pack_data ?? [];
        $packData['popup'] = [
            'title' => $context['title'].' — Special Offer',
            'meta_description' => Str::limit($context['description'], 160),
            'hero_image' => $images[0]['relative'] ?? null,
            'popup_settings' => $this->ai->generatePopupMessage($context),
        ];

        $generation->update(['pack_data' => $packData]);
        $generation->updateProgress('popup', 88, 'processing');
    }

    private function stepZip(LandingPageGeneration $generation): void
    {
        $generation->updateProgress('zip', 92, 'processing');

        $zipPath = $this->exporter->exportPack($generation);

        $generation->update([
            'zip_path' => $zipPath,
        ]);
        $generation->updateProgress('done', 100, 'completed');
    }

    /**
     * @return array{title: string, description: string, affiliate_url: string, topic: string}
     */
    private function aiContext(LandingPageGeneration $generation): array
    {
        $crawl = $generation->crawl_data ?? [];

        return [
            'title' => $crawl['title'] ?? $generation->topic ?? 'Landing Page',
            'description' => $crawl['description'] ?? '',
            'affiliate_url' => $generation->affiliate_url,
            'topic' => $generation->topic ?? ($crawl['title'] ?? 'dịch vụ'),
        ];
    }
}
