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
Viết nội dung landing page tiếng Việt cho sản phẩm/dịch vụ sau:
- Tiêu đề trang đích: {$context['title']}
- Mô tả: {$context['description']}
- Link affiliate: {$context['affiliate_url']}
- Chủ đề: {$context['topic']}

Yêu cầu:
- Tổng cộng khoảng {$wordTarget} từ (intro + 3 section)
- 3 section, mỗi section có title và content dài (~900-1000 từ/section)
- Giọng văn thuyết phục, SEO, có lợi ích rõ ràng
- Không dùng HTML, chỉ plain text, xuống dòng bằng \\n\\n giữa đoạn

Trả về JSON:
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
                    'title' => $section['title'] ?? 'Phần '.($i + 1),
                    'content' => $this->ensureWordCount($section['content'] ?? '', (int) round($wordTarget * 0.28), $context),
                ];
            })->values()->all(),
        ];
    }

    public function generatePopupMessage(array $context): array
    {
        return [
            'title' => 'Cookie Notice',
            'message' => 'This website uses cookies to personalize content and ads, provide social media features, and analyze our traffic. By clicking Accept, you agree to the use of cookies. For more information, visit our',
            'button_text' => 'Accept and Continue',
            'policy_url' => $context['affiliate_url'] ?? null,
        ];
    }

    /**
     * @param  array{title: string, description: string, affiliate_url: string, topic?: string}  $context
     */
    public function generateCompare(array $context): array
    {
        $prompt = <<<PROMPT
Tạo bảng so sánh sản phẩm vs đối thủ (tiếng Việt) cho:
- Sản phẩm: {$context['title']}
- Mô tả: {$context['description']}
- Link: {$context['affiliate_url']}

Trả JSON:
{
  "title": "So sánh ...",
  "meta_description": "...",
  "product_name": "tên sản phẩm ngắn",
  "competitor_name": "Đối thủ / Giải pháp truyền thống",
  "intro": "1-2 câu giới thiệu",
  "summary": "1 đoạn kết luận ngắn",
  "cta_title": "...",
  "cta_text": "...",
  "cta_button": "...",
  "rows": [
    {"feature": "Tính năng 1", "ours": true, "theirs": false},
    ... 8-12 dòng
  ]
}
ours/theirs là boolean (true= có, false= không).
PROMPT;

        $result = $this->callAi($prompt);

        if ($result === []) {
            return $this->fallbackCompare($context);
        }

        return [
            'title' => $result['title'] ?? 'So sánh '.$context['title'],
            'meta_description' => $result['meta_description'] ?? Str::limit($context['description'], 160),
            'product_name' => $result['product_name'] ?? $context['title'],
            'competitor_name' => $result['competitor_name'] ?? 'Giải pháp khác',
            'intro' => $result['intro'] ?? '',
            'summary' => $result['summary'] ?? '',
            'cta_title' => $result['cta_title'] ?? 'Chọn giải pháp tốt hơn',
            'cta_text' => $result['cta_text'] ?? 'Đăng ký ngay để nhận ưu đãi.',
            'cta_button' => $result['cta_button'] ?? 'Nhận ưu đãi ngay →',
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

        throw $lastError ?? new \RuntimeException('Không có model Gemini khả dụng.');
    }

    /**
     * @return array<string, mixed>
     */
    private function callGeminiModel(string $model, string $prompt): array
    {
        $apiKey = config('landing.ai.api_key');
        $baseUrl = rtrim(config('landing.ai.base_url'), '/');
        $url = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout(120)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => "Bạn là copywriter landing page chuyên nghiệp. Luôn trả về JSON hợp lệ, không bọc markdown.\n\n".$prompt,
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
            throw new \RuntimeException('Gemini API lỗi ('.$model.'): '.$response->body());
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
        $topic = $context['topic'] ?? 'dịch vụ';

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
            'meta_description' => Str::limit($context['description'] ?: "Giải pháp {$topic} chuyên nghiệp", 160),
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
            'title' => 'So sánh '.$product.' với giải pháp khác',
            'meta_description' => Str::limit($context['description'] ?: "So sánh tính năng {$product}", 160),
            'product_name' => $product,
            'competitor_name' => 'Giải pháp truyền thống',
            'intro' => 'Xem nhanh vì sao '.$product.' vượt trội so với các lựa chọn phổ biến trên thị trường.',
            'summary' => $product.' mang lại trải nghiệm toàn diện hơn, tiết kiệm chi phí và thời gian triển khai.',
            'cta_title' => 'Sẵn sàng trải nghiệm?',
            'cta_text' => 'Nhấn nút bên dưới để xem chi tiết và nhận ưu đãi.',
            'cta_button' => 'Xem ưu đãi ngay →',
            'rows' => [
                ['feature' => 'Triển khai nhanh trong 24–48h', 'ours' => true, 'theirs' => false],
                ['feature' => 'Tối ưu SEO sẵn có', 'ours' => true, 'theirs' => false],
                ['feature' => 'Responsive mobile/tablet', 'ours' => true, 'theirs' => true],
                ['feature' => 'Hỗ trợ kỹ thuật sau bàn giao', 'ours' => true, 'theirs' => false],
                ['feature' => 'Tùy chỉnh logo & màu thương hiệu', 'ours' => true, 'theirs' => false],
                ['feature' => 'Tích hợp form liên hệ / chat', 'ours' => true, 'theirs' => false],
                ['feature' => 'Chi phí một lần, sở hữu website', 'ours' => true, 'theirs' => false],
                ['feature' => 'Cập nhật nội dung dễ dàng', 'ours' => true, 'theirs' => false],
                ['feature' => 'Phí thuê bao hàng tháng', 'ours' => false, 'theirs' => true],
                ['feature' => 'Giới hạn tính năng theo gói', 'ours' => false, 'theirs' => true],
            ],
        ];
    }
}
