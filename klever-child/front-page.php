<?php
/**
 * Front page — KLEVER FRUIT desktop layout
 */
defined('ABSPATH') || exit;

$cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$flash_products = klever_products('', 5, true);
$gift_products = klever_products('qua-tang-chuc-mung', 6);
$shop_products = klever_products('', 6);
$vitamin_products = klever_products('qua-tang-cao-cap', 6);
$stories = get_posts(['numberposts' => 4, 'post_status' => 'publish', 'category_name' => 'cau-chuyen']);
if (empty($stories)) {
    $stories = get_posts(['numberposts' => 4, 'post_status' => 'publish']);
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="kf-site">

<!-- ═══ HEADER ═══ -->
<header class="kf-header">
    <div class="kf-wrap kf-header__inner">
        <button class="kf-header__burger" id="kf-menu-btn" aria-label="Menu">☰</button>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="kf-logo">
            <span>KLEVER</span> <em>🍒</em> <span>FRUIT</span>
        </a>
        <form class="kf-search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" placeholder="Bạn đang tìm kiếm gì?" value="<?php echo esc_attr(get_search_query()); ?>">
            <input type="hidden" name="post_type" value="product">
            <button type="submit" aria-label="Tìm kiếm">🔍</button>
        </form>
        <div class="kf-header__actions">
            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="kf-header__auth">Đăng nhập / Đăng ký</a>
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="kf-header__cart">
                🛒 <span class="kf-header__cart-count"><?php echo esc_html($cart_count); ?></span>
            </a>
        </div>
    </div>
    <nav class="kf-nav">
        <div class="kf-wrap kf-nav__inner">
            <a href="#qua-tang">QUÀ TẶNG TRÁI CÂY</a>
            <a href="#san-pham">SẢN PHẨM</a>
            <a href="#tuoi-hang-ngay">TRÁI CÂY TƯƠI HÀNG NGÀY</a>
            <a href="#cau-chuyen">KHÁM PHÁ KLEVER</a>
            <a href="#footer">HỆ THỐNG CỬA HÀNG</a>
        </div>
    </nav>
</header>

<!-- ═══ HERO ═══ -->
<section class="kf-hero" id="kf-hero">
    <div class="kf-hero__slides">
        <div class="kf-hero__slide kf-hero__slide--active" style="background-image:url('https://images.unsplash.com/photo-1528821122594-5ca9533f8560?w=1600')">
            <div class="kf-wrap kf-hero__content">
                <p class="kf-hero__eyebrow">Nhập khẩu trực tiếp, chính ngạch bằng đường bay</p>
                <h1 class="kf-hero__title">Cherry Washington Mỹ</h1>
                <p class="kf-hero__desc">Có mặt tại Việt Nam chỉ sau 48h</p>
            </div>
        </div>
        <div class="kf-hero__slide" style="background-image:url('https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=1600')">
            <div class="kf-wrap kf-hero__content">
                <p class="kf-hero__eyebrow">Trái cây nhập khẩu cao cấp</p>
                <h1 class="kf-hero__title">Việt Quất Mỹ Premium</h1>
                <p class="kf-hero__desc">Tươi ngon mỗi ngày — Giao tận nhà</p>
            </div>
        </div>
        <div class="kf-hero__slide" style="background-image:url('https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=1600')">
            <div class="kf-wrap kf-hero__content">
                <p class="kf-hero__eyebrow">Quà tặng doanh nghiệp</p>
                <h1 class="kf-hero__title">Giỏ Quà Trái Cây Cao Cấp</h1>
                <p class="kf-hero__desc">Sang trọng — Ý nghĩa — Tinh tế</p>
            </div>
        </div>
    </div>
    <div class="kf-hero__dots" id="kf-hero-dots">
        <button class="kf-hero__dot kf-hero__dot--active" data-i="0"></button>
        <button class="kf-hero__dot" data-i="1"></button>
        <button class="kf-hero__dot" data-i="2"></button>
    </div>
</section>

<!-- ═══ FLASH SALE ═══ -->
<section class="kf-section kf-flash">
    <div class="kf-wrap">
        <div class="kf-flash__head">
            <h2 class="kf-flash__title">⚡ FLASH SALE</h2>
            <div class="kf-countdown" id="kf-countdown">
                <div class="kf-countdown__item"><span data-h>00</span><small>Giờ</small></div>
                <div class="kf-countdown__item"><span data-m>00</span><small>Phút</small></div>
                <div class="kf-countdown__item"><span data-s>00</span><small>Giây</small></div>
            </div>
        </div>
        <div class="kf-flash__grid">
            <?php foreach ($flash_products as $p) {
                klever_render_product_card($p, 'flash');
            } ?>
        </div>
    </div>
</section>

<!-- ═══ VOUCHERS ═══ -->
<section class="kf-vouchers">
    <div class="kf-wrap kf-vouchers__row">
        <div class="kf-voucher"><span class="kf-voucher__icon">🎁</span><div><strong>GIẢM NGAY 100k</strong><small>Đơn từ 1.999k</small></div></div>
        <div class="kf-voucher"><span class="kf-voucher__icon">💰</span><div><strong>GIẢM NGAY 150k</strong><small>Đơn từ 2.999k</small></div></div>
        <div class="kf-voucher"><span class="kf-voucher__icon">🚚</span><div><strong>FREESHIP 50k</strong><small>Đơn từ 999k</small></div></div>
        <div class="kf-voucher"><span class="kf-voucher__icon">⭐</span><div><strong>GIẢM NGAY 40k</strong><small>Thành viên mới</small></div></div>
    </div>
</section>

<!-- ═══ QUÀ TẶNG ═══ -->
<section class="kf-section kf-split-section" id="qua-tang">
    <div class="kf-wrap">
        <h2 class="kf-section-title">QUÀ TẶNG TRÁI CÂY</h2>
        <div class="kf-tabs" data-tabs="gift">
            <button class="kf-tabs__btn kf-tabs__btn--active" data-tab="chuc-mung">🎉 Chúc mừng sinh nhật</button>
            <button class="kf-tabs__btn" data-tab="cao-cap">🎁 Quà tặng cao cấp</button>
            <button class="kf-tabs__btn" data-tab="cam-on">💌 Cảm ơn</button>
            <button class="kf-tabs__btn" data-tab="khai-truong">🏢 Khai trương</button>
        </div>
        <?php
        $gift_cats = ['chuc-mung' => 'qua-tang-chuc-mung', 'cao-cap' => 'qua-tang-cao-cap', 'cam-on' => 'qua-tang-cam-on', 'khai-truong' => 'qua-tang-chuc-mung'];
        foreach ($gift_cats as $tab => $cat) :
            $items = klever_products($cat, 6);
        ?>
        <div class="kf-split kf-tab-panel<?php echo $tab === 'chuc-mung' ? ' kf-tab-panel--active' : ''; ?>" data-panel="<?php echo esc_attr($tab); ?>">
            <div class="kf-split__banner" style="background-image:url('https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=600')">
                <span>Quà tặng<br>Trái cây</span>
            </div>
            <div class="kf-split__grid">
                <?php foreach ($items as $p) {
                    klever_render_product_card($p, 'grid');
                } ?>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="kf-section-more"><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Xem tất cả →</a></div>
    </div>
</section>

<!-- ═══ SẢN PHẨM ═══ -->
<section class="kf-section kf-split-section kf-section--gray" id="san-pham">
    <div class="kf-wrap">
        <h2 class="kf-section-title">SẢN PHẨM</h2>
        <div class="kf-tabs" data-tabs="product">
            <button class="kf-tabs__btn kf-tabs__btn--active" data-tab="nhap-khau">🌍 Trái Cây Nhập Khẩu</button>
            <button class="kf-tabs__btn" data-tab="trong-nuoc">🇻🇳 Trái Cây Trong Nước</button>
            <button class="kf-tabs__btn" data-tab="gio-qua">🧺 Giỏ Quà Trái Cây</button>
        </div>
        <div class="kf-split kf-tab-panel kf-tab-panel--active" data-panel="nhap-khau">
            <div class="kf-split__banner" style="background-image:url('https://images.unsplash.com/photo-1619560139134-8e8c4d2c9d0f?w=600')">
                <span>Trái cây<br>theo mùa</span>
            </div>
            <div class="kf-split__grid">
                <?php foreach ($shop_products as $p) {
                    klever_render_product_card($p, 'grid');
                } ?>
            </div>
        </div>
        <div class="kf-section-more"><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Xem tất cả →</a></div>
    </div>
</section>

<!-- ═══ SỐNG LÀNH ═══ -->
<section class="kf-section kf-split-section" id="tuoi-hang-ngay">
    <div class="kf-wrap">
        <h2 class="kf-section-title kf-section-title--long">SỐNG LÀNH MỖI NGÀY — SỐNG SANG CÙNG KLEVER FRUIT</h2>
        <div class="kf-tabs">
            <button class="kf-tabs__btn kf-tabs__btn--active">BST Vitamin cho gia đình 5 người</button>
            <button class="kf-tabs__btn">Hộp Vitamin hàng tuần</button>
            <button class="kf-tabs__btn">Combo detox</button>
        </div>
        <div class="kf-split">
            <div class="kf-split__banner kf-split__banner--lifestyle" style="background-image:url('https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=600')">
                <span>Sống sang<br>sống lành</span>
            </div>
            <div class="kf-split__grid">
                <?php
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                $i = 0;
                foreach (array_slice($vitamin_products, 0, 6) as $p) :
                    $day = $days[$i] ?? 'Daily';
                    $i++;
                ?>
                <div class="kf-card kf-card--vitamin">
                    <a href="<?php echo esc_url(get_permalink($p->get_id())); ?>" class="kf-card__img-link">
                        <div class="kf-card__img">
                            <span class="kf-card__day">For <?php echo esc_html($day); ?></span>
                            <img src="<?php echo esc_url(klever_product_image($p)); ?>" alt="<?php echo esc_attr($p->get_name()); ?>" loading="lazy">
                        </div>
                    </a>
                    <div class="kf-card__body">
                        <h3 class="kf-card__name"><?php echo esc_html($p->get_name()); ?></h3>
                        <div class="kf-card__price"><?php echo $p->get_price_html(); ?></div>
                        <a href="<?php echo esc_url($p->add_to_cart_url()); ?>" class="kf-card__btn add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr($p->get_id()); ?>">CHỌN MUA</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="kf-section-more"><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Xem tất cả →</a></div>
    </div>
</section>

<!-- ═══ CÂU CHUYỆN ═══ -->
<section class="kf-section kf-section--gray" id="cau-chuyen">
    <div class="kf-wrap">
        <h2 class="kf-section-title">KLEVER FRUIT, CÂU CHUYỆN CỦA CHÚNG TÔI</h2>
        <div class="kf-carousel" id="kf-story-carousel">
            <button class="kf-carousel__arrow kf-carousel__arrow--prev" data-target="kf-story-track">‹</button>
            <div class="kf-carousel__track" id="kf-story-track">
                <?php foreach ($stories as $post) :
                    setup_postdata($post);
                    $thumb = get_the_post_thumbnail_url($post, 'medium') ?: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=600';
                ?>
                <article class="kf-article-card">
                    <a href="<?php echo esc_url(get_permalink($post)); ?>">
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>" loading="lazy">
                        <h3><?php echo esc_html(get_the_title($post)); ?></h3>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt($post), 20)); ?></p>
                        <footer>
                            <span>📅 <?php echo esc_html(get_the_date('d/m/Y', $post)); ?></span>
                            <span class="kf-article-card__more">Xem thêm »</span>
                        </footer>
                    </a>
                </article>
                <?php endforeach;
                wp_reset_postdata(); ?>
            </div>
            <button class="kf-carousel__arrow kf-carousel__arrow--next" data-target="kf-story-track">›</button>
        </div>
    </div>
</section>

<!-- ═══ VIDEO ═══ -->
<section class="kf-section">
    <div class="kf-wrap">
        <h2 class="kf-section-title">KLEVER FRUIT... QUA NHỮNG THƯỚC PHIM</h2>
        <div class="kf-carousel" id="kf-video-carousel">
            <button class="kf-carousel__arrow kf-carousel__arrow--prev" data-target="kf-video-track">‹</button>
            <div class="kf-carousel__track" id="kf-video-track">
                <?php
                $videos = [
                    ['title' => 'Hành trình cherry Mỹ đến Việt Nam', 'img' => 'https://images.unsplash.com/photo-1528821122594-5ca9533f8560?w=600'],
                    ['title' => 'Klever Fruit — Nông trại đến bàn ăn', 'img' => 'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=600'],
                    ['title' => 'Quy trình đóng gói quà tặng cao cấp', 'img' => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=600'],
                    ['title' => 'Sống lành mỗi ngày cùng Klever', 'img' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=600'],
                ];
                foreach ($videos as $v) :
                ?>
                <div class="kf-video-card">
                    <div class="kf-video-card__thumb" style="background-image:url('<?php echo esc_url($v['img']); ?>')">
                        <span class="kf-video-card__play">▶</span>
                    </div>
                    <h3><?php echo esc_html($v['title']); ?></h3>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="kf-carousel__arrow kf-carousel__arrow--next" data-target="kf-video-track">›</button>
        </div>
    </div>
</section>

<!-- ═══ FOOTER ═══ -->
<footer class="kf-footer" id="footer">
    <div class="kf-wrap">
        <div class="kf-footer__logo">KLEVER 🍎 FRUIT</div>
        <div class="kf-footer__cols">
            <div class="kf-footer__col">
                <h4>Thông tin công ty</h4>
                <p>123 Nguyễn Huệ, Quận 1, TP.HCM</p>
                <p>Hotline: <a href="tel:0900000000">0900 000 000</a></p>
                <p>Email: <a href="mailto:contact@lamwebre.com">contact@lamwebre.com</a></p>
                <div class="kf-footer__badge">Đã thông báo Bộ Công Thương</div>
            </div>
            <div class="kf-footer__col">
                <h4>Chính sách và Dịch vụ</h4>
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Chính sách giao hàng</a>
                <a href="#">Chính sách đổi trả</a>
                <a href="#">Điều khoản sử dụng</a>
            </div>
            <div class="kf-footer__col">
                <h4>Quà Tặng</h4>
                <a href="#">Quà tặng doanh nghiệp</a>
                <a href="#">Quà tặng cá nhân</a>
                <a href="#">Giỏ quà theo mùa</a>
                <a href="#">Quà tặng cao cấp</a>
            </div>
            <div class="kf-footer__col">
                <h4>Kênh liên hệ</h4>
                <div class="kf-footer__social">
                    <a href="#" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Instagram">📷</a>
                    <a href="#" aria-label="YouTube">▶</a>
                    <a href="#" aria-label="Zalo">Z</a>
                </div>
                <p class="kf-footer__stores"><strong>Cửa hàng:</strong> Hà Nội · TP.HCM · Đà Nẵng · Cần Thơ</p>
            </div>
        </div>
        <div class="kf-footer__bottom">
            <p>© <?php echo esc_html(date('Y')); ?> KLEVER FRUIT. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- ═══ FLOATING ═══ -->
<div class="kf-floats">
    <button class="kf-float kf-float--top" id="kf-scroll-top" aria-label="Lên đầu">↑</button>
    <a href="#" class="kf-float kf-float--zalo" aria-label="Zalo">Zalo</a>
    <a href="#" class="kf-float kf-float--msg" aria-label="Messenger">💬</a>
</div>

<!-- Mobile drawer -->
<div class="kf-drawer" id="kf-drawer">
    <div class="kf-drawer__panel">
        <button class="kf-drawer__close" id="kf-drawer-close">✕</button>
        <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Cửa hàng</a>
        <a href="#qua-tang">Quà tặng</a>
        <a href="<?php echo esc_url(wc_get_cart_url()); ?>">Giỏ hàng</a>
        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">Tài khoản</a>
    </div>
</div>

</div><!-- .kf-site -->
<?php wp_footer(); ?>
</body>
</html>
