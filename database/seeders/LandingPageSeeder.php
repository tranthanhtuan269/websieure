<?php

namespace Database\Seeders;

use App\Enums\LandingPageType;
use App\Models\LandingPage;
use App\Services\LandingPageContentService;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        $content = new LandingPageContentService;
        $affiliateUrl = config('landing.default_affiliate_url');

        foreach ($content->topics() as $topic) {
            LandingPage::updateOrCreate(
                ['slug' => $topic['slug']],
                [
                    'type' => LandingPageType::Standard,
                    'title' => $topic['title'],
                    'meta_description' => $content->metaDescription($topic['title'], $topic['niche'], $topic['keyword']),
                    'hero_image' => $content->heroImage($topic['slug']),
                    'affiliate_url' => $affiliateUrl,
                    'intro' => $content->intro($topic['niche'], $topic['keyword']),
                    'sections' => $content->standardSections($topic['slug'], $topic['niche'], $topic['keyword']),
                    'is_active' => true,
                ]
            );
        }

        foreach ($content->topics() as $topic) {
            $slug = $topic['slug'] . '-popup';

            LandingPage::updateOrCreate(
                ['slug' => $slug],
                [
                    'type' => LandingPageType::Popup,
                    'title' => $topic['title'] . ' — Special Offer',
                    'meta_description' => $content->metaDescription($topic['title'], $topic['niche'], $topic['keyword']),
                    'hero_image' => $content->heroImage($slug, 1600, 900),
                    'affiliate_url' => $affiliateUrl,
                    'popup_settings' => $content->popupSettings($topic['niche']),
                    'is_active' => true,
                ]
            );
        }

        foreach ($content->topics() as $topic) {
            $slug = $topic['slug'] . '-scroll';

            LandingPage::updateOrCreate(
                ['slug' => $slug],
                [
                    'type' => LandingPageType::Scroll,
                    'title' => $topic['title'],
                    'meta_description' => $content->metaDescription($topic['title'], $topic['niche'], $topic['keyword']),
                    'hero_image' => $content->heroImage($slug, 1200, 500),
                    'affiliate_url' => $affiliateUrl,
                    'body' => $content->scrollBody($topic['niche'], $topic['keyword']),
                    'is_active' => true,
                ]
            );
        }
    }
}
