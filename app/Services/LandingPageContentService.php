<?php

namespace App\Services;

class LandingPageContentService
{
    /** @var list<array{slug: string, title: string, niche: string, keyword: string}> */
    private array $topics = [
        ['slug' => 'website-spa', 'title' => 'Website Spa Chuyên Nghiệp Giá Rẻ', 'niche' => 'spa và làm đẹp', 'keyword' => 'website spa'],
        ['slug' => 'website-nha-hang', 'title' => 'Website Nhà Hàng Đặt Bàn Online', 'niche' => 'nhà hàng và ẩm thực', 'keyword' => 'website nhà hàng'],
        ['slug' => 'website-bat-dong-san', 'title' => 'Website Bất Động Sản Bán Hàng Hiệu Quả', 'niche' => 'bất động sản', 'keyword' => 'website bất động sản'],
        ['slug' => 'website-ban-hang-online', 'title' => 'Website Bán Hàng Online Toàn Diện', 'niche' => 'thương mại điện tử', 'keyword' => 'website bán hàng'],
        ['slug' => 'website-giao-duc', 'title' => 'Website Trung Tâm Giáo Dục Hiện Đại', 'niche' => 'giáo dục và đào tạo', 'keyword' => 'website giáo dục'],
        ['slug' => 'landing-page-marketing', 'title' => 'Landing Page Marketing Chuyển Đổi Cao', 'niche' => 'digital marketing', 'keyword' => 'landing page marketing'],
        ['slug' => 'website-nha-khoa', 'title' => 'Website Nha Khoa Uy Tín Thu Hút Khách', 'niche' => 'nha khoa và y tế', 'keyword' => 'website nha khoa'],
        ['slug' => 'website-gym-fitness', 'title' => 'Website Phòng Gym Fitness Chuyên Nghiệp', 'niche' => 'gym và fitness', 'keyword' => 'website gym'],
        ['slug' => 'website-luat-su', 'title' => 'Website Văn Phòng Luật Sư Chuyên Nghiệp', 'niche' => 'dịch vụ pháp lý', 'keyword' => 'website luật sư'],
        ['slug' => 'website-khach-san', 'title' => 'Website Khách Sạn Resort Đặt Phòng Online', 'niche' => 'khách sạn và du lịch', 'keyword' => 'website khách sạn'],
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
        return "{$title} — Giải pháp {$keyword} cho ngành {$niche}. Thiết kế đẹp, tối ưu SEO, triển khai nhanh, giá cạnh tranh.";
    }

    public function intro(string $niche, string $keyword): string
    {
        return implode("\n\n", [
            "Trong bối cảnh cạnh tranh ngày càng gay gắt, việc sở hữu một {$keyword} chuyên nghiệp không còn là lựa chọn mà đã trở thành yêu cầu bắt buộc đối với mọi doanh nghiệp trong lĩnh vực {$niche}. Khách hàng hiện đại tìm kiếm thông tin trực tuyến trước khi quyết định mua dịch vụ, và doanh nghiệp nào xuất hiện đầu tiên với hình ảnh uy tín sẽ giành được lợi thế lớn.",
            "Một website được thiết kế bài bản giúp bạn xây dựng thương hiệu, tăng độ tin cậy và tạo kênh bán hàng hoạt động 24/7. Thay vì đầu tư hàng chục triệu cho agency truyền thống, bạn có thể chọn giải pháp theme website chất lượng cao với chi phí hợp lý, triển khai trong thời gian ngắn và dễ dàng tùy chỉnh theo nhu cầu riêng.",
            "Bài viết này phân tích chi tiết lý do vì sao {$keyword} là khoản đầu tư thông minh, các thành phần cần có để trang web hiệu quả, và cách lựa chọn gói giải pháp phù hợp ngân sách cũng như mục tiêu kinh doanh của bạn trong ngành {$niche}.",
        ]);
    }

    public function sectionContent(string $niche, string $keyword, int $sectionIndex): string
    {
        $topics = [
            [
                'heading' => 'Tầm quan trọng của website trong kỷ nguyên số',
                'points' => [
                    "Ngành {$niche} đang chứng kiến sự chuyển dịch mạnh mẽ sang kênh trực tuyến. Khách hàng so sánh giá, đọc đánh giá và đặt lịch hẹn ngay trên điện thoại. Nếu doanh nghiệp của bạn chưa có {$keyword}, bạn đang mất đi một lượng khách hàng tiềm năng đáng kể mỗi ngày.",
                    'Website hoạt động như văn phòng ảo mở cửa 24 giờ. Khách có thể tìm hiểu dịch vụ, xem bảng giá, liên hệ qua form hoặc chat vào bất kỳ lúc nào — kể cả ngoài giờ làm việc. Điều này đặc biệt quan trọng với các doanh nghiệp nhỏ không có đội ngũ chăm sóc khách hàng lớn.',
                    'Google và các công cụ tìm kiếm ưu tiên website có cấu trúc rõ ràng, tốc độ tải nhanh và nội dung chất lượng. Khi bạn đầu tư đúng cách cho SEO ngay từ đầu, chi phí thu hút khách hàng mới sẽ giảm dần theo thời gian so với việc chỉ chạy quảng cáo trả phí.',
                    'Thương hiệu được nhận diện qua giao diện nhất quán: màu sắc, typography, hình ảnh chuyên nghiệp. Một {$keyword} được thiết kế tốt truyền tải thông điệp rằng bạn coi trọng chất lượng — điều khách hàng ngành '.$niche.' rất quan tâm khi ra quyết định.',
                    'Dữ liệu hành vi người dùng từ website giúp bạn hiểu khách hàng hơn: trang nào được xem nhiều, form nào có tỷ lệ chuyển đổi cao, nguồn traffic đến từ đâu. Những insight này là nền tảng để tối ưu marketing và cải thiện dịch vụ.',
                ],
            ],
            [
                'heading' => 'Các thành phần website hiệu quả cần có',
                'points' => [
                    'Trang chủ cần headline rõ ràng nêu lợi ích cốt lõi, kèm call-to-action nổi bật. Khách truy cập chỉ dành vài giây để quyết định ở lại hay rời đi — thiết kế phải truyền tải giá trị ngay lập tức.',
                    "Trang dịch vụ/sản phẩm trình bày chi tiết từng hạng mục với hình ảnh chất lượng cao, mô tả lợi ích cụ thể và giá minh bạch. Ngành {$niche} thường cần gallery, bảng giá hoặc catalog để khách dễ so sánh.",
                    'Trang giới thiệu kể câu chuyện thương hiệu: lịch sử, đội ngũ, chứng chỉ, giải thưởng. Yếu tố con người và uy tín là đòn bẩy quan trọng để chuyển đổi khách hàng mới thành khách hàng trung thành.',
                    'Form liên hệ và tích hợp chat (Zalo, Messenger, WhatsApp) giảm rào cản liên lạc. Khách hàng ngày nay muốn phản hồi nhanh — một form đơn giản hoặc nút chat có thể tăng tỷ lệ chuyển đổi đáng kể.',
                    'Blog hoặc mục tin tức giúp cập nhật nội dung thường xuyên, cải thiện SEO và thể hiện chuyên môn. Mỗi bài viết hữu ích là cơ hội xuất hiện trên Google với từ khóa liên quan đến '.$keyword.'.',
                    'Tối ưu mobile-first là bắt buộc: hơn 70% lượt truy cập từ điện thoại. Giao diện responsive, nút bấm đủ lớn và form dễ điền trên màn hình nhỏ sẽ quyết định trải nghiệm khách hàng.',
                ],
            ],
            [
                'heading' => 'Lựa chọn giải pháp và triển khai nhanh',
                'points' => [
                    'Thay vì xây dựng từ đầu với chi phí cao và thời gian dài, theme website chuyên biệt cho từng ngành là lựa chọn thông minh. Bạn có sẵn layout đã được thiết kế, test trên nhiều thiết bị và tối ưu cho chuyển đổi.',
                    'Quy trình triển khai gọn: chọn theme phù hợp → cung cấp logo, hình ảnh, nội dung → đội ngũ cài đặt và tùy chỉnh → bàn giao kèm hướng dẫn quản trị. Toàn bộ có thể hoàn thành trong 24–48 giờ thay vì vài tuần.',
                    'Chi phí theme một lần thấp hơn nhiều so với thuê agency thiết kế riêng. Bạn vẫn sở hữu website, không phụ thuộc nền tảng thuê bao hàng tháng với tính năng hạn chế như các dịch vụ landing page đơn giản.',
                    'Sau khi ra mắt, bạn có thể tích hợp thêm: Google Analytics, pixel quảng cáo, email marketing, CRM. Nền tảng website linh hoạt cho phép mở rộng theo giai đoạn phát triển kinh doanh.',
                    "Đầu tư {$keyword} ngay hôm nay là bước đi chiến lược để doanh nghiệp {$niche} của bạn cạnh tranh hiệu quả, xây dựng niềm tin và tạo nguồn khách hàng bền vững. Hãy chọn giải pháp phù hợp ngân sách và bắt đầu xuất hiện chuyên nghiệp trên không gian số.",
                ],
            ],
        ];

        $section = $topics[$sectionIndex] ?? $topics[0];
        $paragraphs = [$section['heading'] . '.'];

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
                    0 => 'Vì sao cần ' . $keyword . ' ngay bây giờ?',
                    1 => 'Thành phần website ' . $niche . ' chuẩn chuyên nghiệp',
                    2 => 'Triển khai nhanh — chi phí hợp lý',
                    default => 'Phần ' . ($i + 1),
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
            'title' => 'Cookie Settings',
            'message' => "We use cookies to improve your experience and show relevant offers for {$niche} services. By continuing, you agree to our cookie policy and personalized content.",
            'button_text' => 'Yes, I accept',
            'decline_text' => 'Manage preferences',
        ];
    }

    private function fillerParagraph(string $niche, string $keyword): string
    {
        $templates = [
            "Nhiều chủ doanh nghiệp {$niche} ban đầu e ngại chi phí, nhưng thực tế một {$keyword} chất lượng thường hoàn vốn chỉ sau vài đơn hàng hoặc hợp đồng dịch vụ đầu tiên nhờ khả năng tiếp cận khách hàng mới liên tục.",
            "Đối thủ cạnh tranh trực tiếp trong ngành {$niche} đã và đang đầu tư mạnh vào kênh online. Việc chậm trễ có thể khiến thương hiệu của bạn bị lu mờ, đặc biệt khi khách hàng tìm kiếm trên Google với các từ khóa liên quan đến {$keyword}.",
            'Trải nghiệm người dùng mượt mà — tốc độ tải nhanh, menu rõ ràng, thông tin liên hệ dễ tìm — là yếu tố then chốt giữ chân khách và khuyến khích họ hành động: gọi điện, đặt lịch, hoặc mua hàng.',
            "Xu hướng tiêu dùng ngành {$niche} cho thấy khách hàng ưu tiên thương hiệu minh bạch, có đánh giá thực tế và kênh liên lạc đa dạng. Website là nơi tập trung tất cả yếu tố đó một cách chuyên nghiệp.",
            "Khi triển khai {$keyword}, bạn nên chuẩn bị bộ nội dung cơ bản: giới thiệu dịch vụ, bảng giá, hình ảnh thực tế và thông tin liên hệ. Nền tảng theme giúp bạn trình bày những nội dung này một cách hấp dẫn mà không cần kỹ năng lập trình.",
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
