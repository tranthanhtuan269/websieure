<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => config('site.admin_email')],
            [
                'name' => 'Admin',
                'password' => config('site.admin_password'),
                'role' => UserRole::Admin,
            ]
        );

        $categories = [
            ['name' => 'Bán hàng Online', 'slug' => 'ban-hang', 'icon' => '🛒', 'color' => '#7c3aed', 'description' => 'Theme shop, thời trang, mỹ phẩm, điện tử', 'sort_order' => 1],
            ['name' => 'Nhà hàng & Cafe', 'slug' => 'nha-hang', 'icon' => '🍽️', 'color' => '#ea580c', 'description' => 'Menu, đặt bàn, giao hàng F&B', 'sort_order' => 2],
            ['name' => 'Spa & Làm đẹp', 'slug' => 'spa', 'icon' => '💆', 'color' => '#db2777', 'description' => 'Salon, spa, nail, clinic thẩm mỹ', 'sort_order' => 3],
            ['name' => 'Bất động sản', 'slug' => 'bat-dong-san', 'icon' => '🏠', 'color' => '#0284c7', 'description' => 'Dự án, môi giới, cho thuê', 'sort_order' => 4],
            ['name' => 'Giáo dục', 'slug' => 'giao-duc', 'icon' => '🎓', 'color' => '#059669', 'description' => 'Trung tâm, khóa học, e-learning', 'sort_order' => 5],
            ['name' => 'Landing Page', 'slug' => 'landing-page', 'icon' => '🚀', 'color' => '#4f46e5', 'description' => 'Trang bán hàng, lead, sự kiện', 'sort_order' => 6],
            ['name' => 'Blog & Tin tức', 'slug' => 'blog', 'icon' => '📰', 'color' => '#0f766e', 'description' => 'Tạp chí, review, tin tức', 'sort_order' => 7],
            ['name' => 'Doanh nghiệp', 'slug' => 'doanh-nghiep', 'icon' => '🏢', 'color' => '#334155', 'description' => 'Giới thiệu công ty, dịch vụ B2B', 'sort_order' => 8],
        ];

        foreach ($categories as $data) {
            Category::query()->updateOrCreate(['slug' => $data['slug']], $data + ['is_active' => true]);
        }

        $themes = [
            ['cat' => 'ban-hang', 'name' => 'ShopMart Pro', 'tagline' => 'Theme bán hàng đa danh mục, giỏ hàng, thanh toán COD', 'price' => 1290000, 'sale' => 990000, 'featured' => true, 'color' => '#7c3aed', 'label' => 'SHOP'],
            ['cat' => 'ban-hang', 'name' => 'Fashion Nova', 'tagline' => 'Lookbook thời trang, flash sale, Instagram feed', 'price' => 1190000, 'sale' => null, 'featured' => true, 'color' => '#be185d', 'label' => 'FASHION'],
            ['cat' => 'ban-hang', 'name' => 'TechStore', 'tagline' => 'Điện tử & phụ kiện, so sánh sản phẩm', 'price' => 1090000, 'sale' => 890000, 'featured' => false, 'color' => '#1d4ed8', 'label' => 'TECH'],
            ['cat' => 'nha-hang', 'name' => 'Foodie Bistro', 'tagline' => 'Menu đẹp, đặt bàn online, gallery món ăn', 'price' => 990000, 'sale' => 790000, 'featured' => true, 'color' => '#c2410c', 'label' => 'FOOD'],
            ['cat' => 'nha-hang', 'name' => 'Coffee House', 'tagline' => 'Cafe specialty, franchise, giao hàng', 'price' => 890000, 'sale' => null, 'featured' => false, 'color' => '#78350f', 'label' => 'CAFE'],
            ['cat' => 'spa', 'name' => 'Glow Spa', 'tagline' => 'Đặt lịch spa, bảng giá dịch vụ, gallery', 'price' => 950000, 'sale' => 750000, 'featured' => true, 'color' => '#db2777', 'label' => 'SPA'],
            ['cat' => 'bat-dong-san', 'name' => 'HomeCity', 'tagline' => 'Listing BĐS, bản đồ, lọc theo quận', 'price' => 1490000, 'sale' => 1190000, 'featured' => true, 'color' => '#0369a1', 'label' => 'HOME'],
            ['cat' => 'bat-dong-san', 'name' => 'Villa Estate', 'tagline' => 'Biệt thự cao cấp, virtual tour', 'price' => 1690000, 'sale' => null, 'featured' => false, 'color' => '#0e7490', 'label' => 'VILLA'],
            ['cat' => 'giao-duc', 'name' => 'EduMaster', 'tagline' => 'Khóa học online, lịch học, đăng ký', 'price' => 1190000, 'sale' => 990000, 'featured' => true, 'color' => '#047857', 'label' => 'EDU'],
            ['cat' => 'landing-page', 'name' => 'LaunchPad', 'tagline' => 'Landing bán khóa học / sản phẩm số', 'price' => 690000, 'sale' => 490000, 'featured' => true, 'color' => '#4338ca', 'label' => 'LAUNCH'],
            ['cat' => 'landing-page', 'name' => 'Event Spark', 'tagline' => 'Sự kiện, countdown, form đăng ký', 'price' => 590000, 'sale' => null, 'featured' => false, 'color' => '#6d28d9', 'label' => 'EVENT'],
            ['cat' => 'blog', 'name' => 'NewsDaily', 'tagline' => 'Tin tức, chuyên mục, newsletter', 'price' => 790000, 'sale' => 650000, 'featured' => false, 'color' => '#0f766e', 'label' => 'NEWS'],
            ['cat' => 'doanh-nghiep', 'name' => 'CorpPlus', 'tagline' => 'Giới thiệu công ty, team, dự án', 'price' => 990000, 'sale' => 850000, 'featured' => true, 'color' => '#334155', 'label' => 'CORP'],
            ['cat' => 'doanh-nghiep', 'name' => 'Agency X', 'tagline' => 'Agency marketing, portfolio case study', 'price' => 1090000, 'sale' => null, 'featured' => false, 'color' => '#1e293b', 'label' => 'AGENCY'],
            ['cat' => 'blog', 'name' => 'Thuoc360', 'tagline' => 'Website coupon & affiliate — store, mã giảm giá, blog', 'price' => 500000, 'sale' => null, 'featured' => true, 'color' => '#e63946', 'label' => '360', 'preview' => 'http://thuoc360.test', 'product' => true],
        ];

        $features = [
            'Responsive mobile/tablet/desktop',
            'Tối ưu SEO cơ bản',
            'Form liên hệ tích hợp',
            'Hỗ trợ cài đặt lên hosting',
            'Tùy chỉnh logo & màu thương hiệu',
        ];

        $thuoc360Features = [
            'Nền tảng coupon & affiliate đầy đủ (Laravel 10)',
            'Trang store, coupon, blog, danh mục, tìm kiếm',
            'Dashboard admin & member, import affiliate link',
            'Keyword generator, API tích hợp crawl coupon',
            'SEO, sitemap.xml, giao diện responsive',
            'Kèm hướng dẫn cài đặt XAMPP/hosting',
        ];

        foreach ($themes as $t) {
            $category = Category::where('slug', $t['cat'])->first();
            if (! $category) {
                continue;
            }

            $slug = Str::slug($t['name']);
            $themeFeatures = ! empty($t['product']) ? $thuoc360Features : $features;
            $description = ! empty($t['product'])
                ? 'Thuoc360 là website mã giảm giá & affiliate hoàn chỉnh, xây bằng Laravel. Gồm trang chủ, danh sách store/coupon, blog, dashboard quản trị và công cụ import store từ link affiliate. Phù hợp triển khai nhanh mô hình coupon site tại Việt Nam hoặc thị trường US.'
                : $t['tagline'] . ' Phù hợp triển khai nhanh cho doanh nghiệp vừa và nhỏ tại Việt Nam.';

            Theme::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'name' => $t['name'],
                    'tagline' => $t['tagline'],
                    'description' => $description,
                    'features' => $themeFeatures,
                    'price' => $t['price'],
                    'sale_price' => $t['sale'],
                    'preview_url' => $t['preview'] ?? ('https://demo.websieure.test/' . $slug),
            'thumbnail_image' => ! empty($t['product']) ? 'themes/thuoc360-home.png' : null,
                    'thumbnail_color' => $t['color'],
                    'thumbnail_label' => $t['label'],
                    'is_featured' => $t['featured'],
                    'is_active' => true,
                    'sales_count' => ! empty($t['product']) ? 28 : random_int(5, 120),
                ]
            );
        }

        $this->call(LandingPageSeeder::class);
    }
}
