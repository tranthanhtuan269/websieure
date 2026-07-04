<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LandingAiService
{
    public function isConfigured(): bool
    {
        return filled(config('landing.ai.api_key'));
    }

    /**
     * @param  array{title: string, description: string, affiliate_url: string, topic?: string}  $context
     * @return array{title: string, meta_description: string, intro: string, sections: list<array{title: string, content: string}>}
     */
    public function generateStandard(array $context, int $wordTarget = 3000): array
    {
        $prompt = <<<PROMPT
Write landing page copy in {$this->languageLabel()} for this product/service:
- Source page title: {$context['title']}
- Description: {$context['description']}
- Affiliate URL: {$context['affiliate_url']}
- Topic: {$context['topic']}

Style requirements (US direct-response marketing):
- American English spelling and idioms (color, optimize, center)
- Speak to the reader with "you" and "your"
- Benefit-led, confident, clear — like a top US SaaS or DTC landing page
- Short, punchy section titles; persuasive but not hypey
- SEO-friendly without keyword stuffing

Content requirements:
- Roughly {$wordTarget} words total (intro + 3 sections)
- 3 sections, each with a title and long-form content (~900-1000 words per section)
- Plain text only — no HTML. Separate paragraphs with \\n\\n

Return JSON:
{
  "title": "...",
  "meta_description": "...",
  "intro": "...",
  "sections": [
    {"title": "...", "content": "..."},
    {"title": "...", "content": "..."},
    {"title": "...", "content": "..."}
  ]
}
PROMPT;

        $result = $this->callAi($prompt);

        return [
            'title' => $result['title'] ?? $context['title'],
            'meta_description' => $result['meta_description'] ?? Str::limit($context['description'], 160),
            'intro' => $this->ensureWordCount($result['intro'] ?? '', (int) round($wordTarget * 0.15), $context),
            'sections' => collect($result['sections'] ?? [])->take(3)->map(function ($section, $i) use ($wordTarget, $context) {
                return [
                    'title' => $section['title'] ?? 'Section '.($i + 1),
                    'content' => $this->ensureWordCount($section['content'] ?? '', (int) round($wordTarget * 0.28), $context),
                ];
            })->values()->all(),
        ];
    }

    public function generatePopupMessage(array $context): array
    {
        return [
            'title' => 'Cookie Notice',
            'message' => 'We use cookies to personalize content and ads, provide social media features, and analyze site traffic. By clicking Accept, you agree to our use of cookies. Learn more in our',
            'button_text' => 'Accept & Continue',
            'policy_url' => $context['affiliate_url'] ?? null,
        ];
    }

    /**
     * @param  array{title: string, description: string, affiliate_url: string, topic?: string}  $context
     */
    public function generateCompare(array $context): array
    {
        $prompt = <<<PROMPT
Create a US-style product comparison landing page in {$this->languageLabel()} for:
- Product: {$context['title']}
- Description: {$context['description']}
- Affiliate URL: {$context['affiliate_url']}

Write like a modern American comparison page (clear headline, confident positioning, conversion-focused CTA).
Use American English spelling and tone.

Return JSON:
{
  "title": "How ... Compares",
  "meta_description": "...",
  "product_name": "short product name",
  "competitor_name": "Traditional alternative / Other options",
  "intro": "1-2 sentence hook",
  "summary": "short closing paragraph",
  "cta_title": "...",
  "cta_text": "...",
  "cta_button": "...",
  "rows": [
    {"feature": "Feature name", "ours": true, "theirs": false},
    ... 8-12 rows
  ]
}
ours/theirs are booleans (true = has feature, false = does not).
PROMPT;

        $result = $this->callAi($prompt);

        if ($result === []) {
            return $this->fallbackCompare($context);
        }

        return [
            'title' => $result['title'] ?? $this->compareTitle($context['title']),
            'meta_description' => $result['meta_description'] ?? Str::limit($context['description'], 160),
            'product_name' => $result['product_name'] ?? $context['title'],
            'competitor_name' => $result['competitor_name'] ?? 'Traditional alternatives',
            'intro' => $result['intro'] ?? '',
            'summary' => $result['summary'] ?? '',
            'cta_title' => $result['cta_title'] ?? 'Ready to make the switch?',
            'cta_text' => $result['cta_text'] ?? 'See why thousands of customers choose us over the competition.',
            'cta_button' => $result['cta_button'] ?? 'Get started today →',
            'rows' => collect($result['rows'] ?? [])->take(12)->map(fn ($row) => [
                'feature' => $row['feature'] ?? '',
                'ours' => (bool) ($row['ours'] ?? false),
                'theirs' => (bool) ($row['theirs'] ?? false),
            ])->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function callAi(string $prompt): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $models = array_values(array_unique(array_filter(array_merge(
            [config('landing.ai.model')],
            config('landing.ai.fallback_models', [])
        ))));

        $lastError = null;

        foreach ($models as $model) {
            try {
                return $this->callGeminiModel($model, $prompt);
            } catch (\RuntimeException $e) {
                $message = $e->getMessage();
                if (str_contains($message, 'NOT_FOUND') || str_contains($message, '"code": 404')) {
                    $lastError = $e;

                    continue;
                }

                throw $e;
            }
        }

        throw $lastError ?? new \RuntimeException('No Gemini model available.');
    }

    /**
     * @return array<string, mixed>
     */
    private function callGeminiModel(string $model, string $prompt): array
    {
        $apiKey = config('landing.ai.api_key');
        $baseUrl = rtrim(config('landing.ai.base_url'), '/');
        $url = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        $system = <<<SYSTEM
You are a senior US direct-response copywriter who writes high-converting landing pages for American audiences.
Always write in American English (en-US): conversational, benefit-driven, confident, and clear.
Use American spelling. Avoid British phrasing. Never use Vietnamese.
Always return valid JSON only — no markdown fences or commentary.
SYSTEM;

        $response = Http::timeout(120)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $system."\n\n".$prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'responseMimeType' => 'application/json',
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Gemini API error ('.$model.'): '.$response->body());
        }

        $content = $response->json('candidates.0.content.parts.0.text', '');

        return $this->parseJsonResponse($content);
    }

    /**
     * @return array<string, mixed>
     */
    private function parseJsonResponse(string $content): array
    {
        $content = trim($content);

        if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  array{title: string, description: string, topic?: string}  $context
     */
    private function ensureWordCount(string $text, int $target, array $context): string
    {
        $text = trim($text);

        if ($text === '' || $this->countWords($text) >= $target) {
            return $text !== '' ? $text : $this->fallbackParagraph($context, $target);
        }

        $content = new LandingPageContentService;
        $topic = $context['topic'] ?? $context['title'];
        $filler = $content->intro($topic, $context['title']);

        while ($this->countWords($text) < $target) {
            $text .= "\n\n".$filler;
        }

        return $text;
    }

    /**
     * @param  array{title: string, description: string, topic?: string}  $context
     */
    private function fallbackParagraph(array $context, int $target): string
    {
        $content = new LandingPageContentService;
        $topic = $context['topic'] ?? 'your business';

        return $content->sectionContent($topic, $context['title'], 0);
    }

    private function countWords(string $text): int
    {
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * @param  array{title: string, description: string, affiliate_url: string, topic?: string}  $context
     * @return array{title: string, meta_description: string, intro: string, sections: list<array{title: string, content: string}>}
     */
    public function fallbackStandard(array $context, int $wordTarget = 3000): array
    {
        $content = new LandingPageContentService;
        $topic = $context['topic'] ?? $context['title'];
        $niche = $context['description'] ?: $topic;

        return [
            'title' => $context['title'],
            'meta_description' => Str::limit($context['description'] ?: "Professional {$topic} solution built to convert", 160),
            'intro' => $content->intro($niche, $topic),
            'sections' => collect($content->standardSections(Str::slug($topic), $niche, $topic))
                ->map(fn ($s) => ['title' => $s['title'], 'content' => $s['content']])
                ->all(),
        ];
    }

    /**
     * @param  array{title: string, description: string, affiliate_url: string, topic?: string}  $context
     */
    public function fallbackCompare(array $context): array
    {
        $product = $context['title'];

        return [
            'title' => $this->compareTitle($product),
            'meta_description' => Str::limit($context['description'] ?: "See how {$product} stacks up against the competition", 160),
            'product_name' => $product,
            'competitor_name' => 'Traditional alternatives',
            'intro' => "Here's a side-by-side look at why {$product} is the smarter pick for most buyers.",
            'summary' => "{$product} delivers more value, faster setup, and lower long-term cost than outdated alternatives.",
            'cta_title' => 'Ready to get started?',
            'cta_text' => 'Tap below to see pricing, details, and today\'s offer.',
            'cta_button' => 'See the offer →',
            'rows' => [
                ['feature' => 'Launch in 24–48 hours', 'ours' => true, 'theirs' => false],
                ['feature' => 'SEO-ready out of the box', 'ours' => true, 'theirs' => false],
                ['feature' => 'Mobile & tablet optimized', 'ours' => true, 'theirs' => true],
                ['feature' => 'Post-launch support included', 'ours' => true, 'theirs' => false],
                ['feature' => 'Custom branding (logo & colors)', 'ours' => true, 'theirs' => false],
                ['feature' => 'Contact forms & live chat ready', 'ours' => true, 'theirs' => false],
                ['feature' => 'One-time purchase — you own it', 'ours' => true, 'theirs' => false],
                ['feature' => 'Easy content updates', 'ours' => true, 'theirs' => false],
                ['feature' => 'Monthly subscription fees', 'ours' => false, 'theirs' => true],
                ['feature' => 'Feature limits by pricing tier', 'ours' => false, 'theirs' => true],
            ],
        ];
    }

    private function compareTitle(string $product): string
    {
        return "{$product} vs. the competition";
    }

    private function languageLabel(): string
    {
        return config('landing.content.language', 'American English');
    }
}
