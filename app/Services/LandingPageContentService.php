<?php

namespace App\Services;

class LandingPageContentService
{
    /** @var list<array{slug: string, title: string, niche: string, keyword: string}> */
    private array $topics = [
        ['slug' => 'spa-website', 'title' => 'Professional Spa Website — Affordable & Fast', 'niche' => 'spa and beauty', 'keyword' => 'spa website'],
        ['slug' => 'restaurant-website', 'title' => 'Restaurant Website with Online Reservations', 'niche' => 'restaurants and food service', 'keyword' => 'restaurant website'],
        ['slug' => 'real-estate-website', 'title' => 'Real Estate Website That Closes More Deals', 'niche' => 'real estate', 'keyword' => 'real estate website'],
        ['slug' => 'ecommerce-website', 'title' => 'Complete Online Store Website', 'niche' => 'e-commerce', 'keyword' => 'online store website'],
        ['slug' => 'education-website', 'title' => 'Modern Education & Training Center Website', 'niche' => 'education and training', 'keyword' => 'education website'],
        ['slug' => 'marketing-landing-page', 'title' => 'High-Converting Marketing Landing Page', 'niche' => 'digital marketing', 'keyword' => 'marketing landing page'],
        ['slug' => 'dental-website', 'title' => 'Dental Practice Website That Builds Trust', 'niche' => 'dental and healthcare', 'keyword' => 'dental website'],
        ['slug' => 'gym-website', 'title' => 'Professional Gym & Fitness Website', 'niche' => 'gym and fitness', 'keyword' => 'gym website'],
        ['slug' => 'law-firm-website', 'title' => 'Law Firm Website That Wins Clients', 'niche' => 'legal services', 'keyword' => 'law firm website'],
        ['slug' => 'hotel-website', 'title' => 'Hotel & Resort Website with Online Booking', 'niche' => 'hospitality and travel', 'keyword' => 'hotel website'],
    ];

    public function topics(): array
    {
        return $this->topics;
    }

    public function heroImage(string $slug, int $width = 1200, int $height = 800): string
    {
        return "https://picsum.photos/seed/{$slug}/{$width}/{$height}";
    }

    public function sectionImage(string $slug, int $index, int $width = 960, int $height = 640): string
    {
        return "https://picsum.photos/seed/{$slug}-section-{$index}/{$width}/{$height}";
    }

    public function metaDescription(string $title, string $niche, string $keyword): string
    {
        return "{$title} — A {$keyword} built for {$niche}. Clean design, SEO-ready, fast launch, and pricing that makes sense.";
    }

    public function intro(string $niche, string $keyword): string
    {
        return implode("\n\n", [
            "In a crowded market, a professional {$keyword} isn't a nice-to-have — it's how customers find you, trust you, and choose you over the competition. If you're in {$niche}, your website is working for you 24/7, even when you're off the clock.",
            "A well-built site helps you look legit, answer questions before prospects call, and turn traffic into leads and sales. Instead of dropping tens of thousands on a custom agency build, you can launch a proven theme fast, customize it to your brand, and start getting results this week.",
            "In this guide, we'll break down why a {$keyword} is one of the smartest investments you can make, what every high-performing page needs, and how to pick the right package for your goals and budget in {$niche}.",
        ]);
    }

    public function sectionContent(string $niche, string $keyword, int $sectionIndex): string
    {
        $topics = [
            [
                'heading' => 'Why your website matters more than ever',
                'points' => [
                    "{$niche} is moving online fast. Your customers compare options, read reviews, and book appointments from their phones. If you don't have a solid {$keyword}, you're leaving money on the table every single day.",
                    "Think of your website as a storefront that never closes. Visitors can browse services, check pricing, fill out a form, or start a chat anytime — nights, weekends, holidays included. That's huge if you're a small team without a full-time sales staff.",
                    'Google rewards sites that load fast, read clearly, and deliver real value. Nail SEO early and your cost to acquire customers drops over time — instead of relying only on paid ads that stop the moment you pause spend.',
                    'Your brand shows up in every detail: colors, typography, photography, messaging. A polished {$keyword} signals quality — and in '.$niche.', that trust factor drives the decision to buy.',
                    "Analytics tell you what's working: top pages, best-converting forms, traffic sources. Those insights fuel smarter marketing and better service — not guesswork.",
                ],
            ],
            [
                'heading' => 'What a high-converting website includes',
                'points' => [
                    "Your homepage needs a clear headline with a core benefit and a bold call-to-action. You've got seconds to earn attention — lead with value, not fluff.",
                    "Service and product pages should showcase what you offer with strong visuals, specific benefits, and transparent pricing. In {$niche}, galleries, menus, rate cards, or catalogs help buyers compare and commit faster.",
                    "An About page builds credibility: your story, team, credentials, awards. People buy from people — especially when they're choosing a provider for the first time.",
                    "Contact forms and chat (Messenger, WhatsApp, SMS) remove friction. Today's buyers expect fast replies — a simple form or chat button can lift conversions noticeably.",
                    'A blog or news section keeps content fresh, boosts SEO, and positions you as the expert. Every helpful article is another chance to rank for keywords tied to your '.$keyword.'.',
                    "Mobile-first isn't optional — most traffic is on phones. Responsive layout, thumb-friendly buttons, and easy forms on small screens directly impact revenue.",
                ],
            ],
            [
                'heading' => 'Pick the right solution and launch fast',
                'points' => [
                    'Building from scratch is slow and expensive. Industry-specific themes give you layouts that are already tested for conversions across devices — so you skip months of trial and error.',
                    'A smart rollout looks like this: pick your theme → add logo, photos, and copy → we configure and customize → you get keys to the kingdom with a simple admin guide. Done in 24–48 hours, not weeks.',
                    'A one-time theme purchase beats endless monthly platform fees with locked features. You own the site. You control the stack. You scale on your timeline.',
                    'After launch, plug in Google Analytics, ad pixels, email tools, and CRM as you grow. A flexible site grows with your business — not against it.',
                    "Investing in a {$keyword} today puts your {$niche} business in the game: more visibility, more trust, more customers. Choose the package that fits your budget and show up like a pro online.",
                ],
            ],
        ];

        $section = $topics[$sectionIndex] ?? $topics[0];
        $paragraphs = [$section['heading'].'.'];

        foreach ($section['points'] as $point) {
            $paragraphs[] = $point;
            $paragraphs[] = $this->fillerParagraph($niche, $keyword);
            $paragraphs[] = $this->fillerParagraph($niche, $keyword);
        }

        $content = implode("\n\n", $paragraphs);

        return $this->expandToWordCount($content, 950, $niche, $keyword);
    }

    public function scrollBody(string $niche, string $keyword): string
    {
        $parts = [
            $this->intro($niche, $keyword),
            $this->sectionContent($niche, $keyword, 0),
        ];

        $content = implode("\n\n", $parts);

        return $this->expandToWordCount($content, 1000, $niche, $keyword);
    }

    public function standardSections(string $slug, string $niche, string $keyword): array
    {
        $sections = [];

        for ($i = 0; $i < 3; $i++) {
            $sections[] = [
                'title' => match ($i) {
                    0 => 'Why you need a '.$keyword.' now',
                    1 => 'What a pro '.$niche.' website includes',
                    2 => 'Launch fast — without breaking the bank',
                    default => 'Section '.($i + 1),
                },
                'content' => $this->sectionContent($niche, $keyword, $i),
                'image' => $this->sectionImage($slug, $i + 1),
            ];
        }

        return $sections;
    }

    public function popupSettings(string $niche): array
    {
        return [
            'title' => 'Cookie Notice',
            'message' => "We use cookies to improve your experience and show relevant offers for {$niche}. By continuing, you agree to our cookie policy and personalized content.",
            'button_text' => 'Accept & Continue',
        ];
    }

    private function fillerParagraph(string $niche, string $keyword): string
    {
        $templates = [
            "A lot of {$niche} owners worry about cost upfront — but a solid {$keyword} often pays for itself after the first few bookings or sales because it keeps bringing in new customers.",
            "Your competitors in {$niche} are already investing online. Wait too long and you'll get harder to find — especially when buyers search Google for {$keyword} near them.",
            'Speed matters: fast load times, clear navigation, and obvious contact info keep visitors engaged and nudge them to act — call, book, or buy.',
            "Today's {$niche} customers want transparency, real reviews, and easy ways to reach you. Your website is where all of that comes together professionally.",
            "When you launch a {$keyword}, start with the basics: services, pricing, real photos, and contact details. A proven theme presents it all beautifully — no coding required.",
        ];

        return $templates[array_rand($templates)];
    }

    private function expandToWordCount(string $content, int $target, string $niche, string $keyword): string
    {
        $paragraphs = array_filter(array_map('trim', explode("\n\n", $content)));

        while ($this->countWords(implode(' ', $paragraphs)) < $target) {
            $paragraphs[] = $this->fillerParagraph($niche, $keyword);
        }

        return implode("\n\n", $paragraphs);
    }

    private function countWords(string $text): int
    {
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
    }
}
