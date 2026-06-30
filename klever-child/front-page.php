<?php
/**
 * Front page template — Klever Fruit
 */
defined('ABSPATH') || exit;

$cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

$flash_products = wc_get_products([
    'limit'    => 6,
    'status'   => 'publish',
    'orderby'  => 'date',
    'order'    => 'DESC',
    'on_sale'  => true,
]);

if (empty($flash_products)) {
    $flash_products = wc_get_products([
        'limit'   => 6,
        'status'  => 'publish',
        'orderby' => 'date',
        'order'   => 'DESC',
    ]);
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

<div class="wp-site-blocks">

<!-- HEADER -->
<header class="klever-header">
    <div class="klever-header__top">
        <button class="klever-header__menu" id="klever-menu-btn" aria-label="Menu">☰</button>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="klever-logo">
            <span class="klever-logo__text">KLEVER</span>
            <span class="klever-logo__fruit">🍒</span>
            <span class="klever-logo__text">FRUIT</span>
        </a>
        <div class="klever-header__icons">
            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="klever-header__icon" aria-label="Tài khoản">👤</a>
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="klever-header__icon klever-cart-badge" aria-label="Giỏ hàng">
                🛍️
                <span class="cart-count"><?php echo esc_html($cart_count); ?></span>
            </a>
        </div>
    </div>
    <form class="klever-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="search" name="s" placeholder="Tìm kiếm sản phẩm..." value="<?php echo get_search_query(); ?>">
        <input type="hidden" name="post_type" value="product">
        <span class="klever-search__icon">🔍</span>
    </form>
</header>

<!-- HERO -->
<section class="klever-hero">
    <div class="klever-hero__slide">
        <p class="klever-hero__tag">KLEVER FRUIT ĐỒNG HÀNH CÙNG LỄ KỶ NIỆM QUỐC KHÁNH HOA KỲ LẦN THỨ 250</p>
        <h1 class="klever-hero__title">AMERICA IN RED — THE SEASON OF AMERICAN CHERRIES</h1>
        <p class="klever-hero__subtitle">SẮC ĐỎ CỦA MÙA CHERRY MỸ</p>
        <div class="klever-hero__flags">
            <span class="klever-hero__flag">🇻🇳</span>
            <span class="klever-hero__flag">🇺🇸</span>
        </div>
        <span class="klever-hero__cherries">🍒</span>
    </div>
    <div class="klever-hero__slide" style="display:none">
        <p class="klever-hero__tag">KLEVER FRUIT — TRÁI CÂY NHẬP KHẨU CAO CẤP</p>
        <h1 class="klever-hero__title">TƯƠI NGON MỖI NGÀY — GIAO TẬN NHÀ</h1>
        <p class="klever-hero__subtitle">CHẤT LƯỢNG HÀNG ĐẦU THẾ GIỚI</p>
        <div class="klever-hero__flags">
            <span class="klever-hero__flag">🫐</span>
            <span class="klever-hero__flag">🍈</span>
        </div>
        <span class="klever-hero__cherries">🍇</span>
    </div>
    <div class="klever-hero__dots">
        <span class="klever-hero__dot klever-hero__dot--active"></span>
        <span class="klever-hero__dot"></span>
    </div>
</section>

<!-- FLASH SALE -->
<section class="klever-flash">
    <div class="klever-flash__header">
        <h2 class="klever-flash__title">
            <span class="klever-flash__bag">🛍️⚡</span>
            FLASH SALE
        </h2>
        <div class="klever-countdown" id="klever-countdown">
            <div class="klever-countdown__box">
                <span class="klever-countdown__num" data-days>5</span>
                <span class="klever-countdown__label">Ngày</span>
            </div>
            <div class="klever-countdown__box">
                <span class="klever-countdown__num" data-hours>00</span>
                <span class="klever-countdown__label">Giờ</span>
            </div>
            <div class="klever-countdown__box">
                <span class="klever-countdown__num" data-mins>00</span>
                <span class="klever-countdown__label">Phút</span>
            </div>
            <div class="klever-countdown__box">
                <span class="klever-countdown__num" data-secs>00</span>
                <span class="klever-countdown__label">Giây</span>
            </div>
        </div>
    </div>
    <div class="klever-products">
        <?php foreach ($flash_products as $product) :
            $id = $product->get_id();
            $permalink = get_permalink($id);
            $image = wp_get_attachment_image_url($product->get_image_id(), 'medium') ?: 'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=400';
            $origin = get_post_meta($id, '_klever_origin', true) ?: 'NHẬP KHẨU';
            $tag1 = get_post_meta($id, '_klever_tag1', true);
            $tag2 = get_post_meta($id, '_klever_tag2', true);
            $pct = '';
            if ($product->is_on_sale() && $product->get_regular_price() > 0) {
                $pct = '-' . round(100 - ($product->get_sale_price() / $product->get_regular_price() * 100)) . '%';
            }
        ?>
        <a href="<?php echo esc_url($permalink); ?>" class="klever-product-card">
            <div class="klever-product-card__img-wrap">
                <?php if ($pct) : ?><span class="klever-product-card__badge"><?php echo esc_html($pct); ?></span><?php endif; ?>
                <span class="klever-product-card__wishlist">❤️</span>
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy">
                <?php if ($tag1 || $tag2) : ?>
                <div class="klever-product-card__tags">
                    <?php if ($tag1) : ?><span class="klever-product-card__tag"><?php echo esc_html($tag1); ?></span><?php endif; ?>
                    <?php if ($tag2) : ?><span class="klever-product-card__tag"><?php echo esc_html($tag2); ?></span><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="klever-product-card__body">
                <div class="klever-product-card__origin"><?php echo esc_html($origin); ?></div>
                <h3 class="klever-product-card__name"><?php echo esc_html($product->get_name()); ?></h3>
                <?php echo $product->get_price_html(); ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- ALL PRODUCTS -->
<section class="klever-featured">
    <h2 class="klever-section-title">Sản phẩm nổi bật</h2>
    <div class="klever-all-products">
        <?php
        $all = wc_get_products(['limit' => 4, 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC']);
        foreach ($all as $product) :
            $id = $product->get_id();
            $image = wp_get_attachment_image_url($product->get_image_id(), 'medium') ?: 'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=400';
            $origin = get_post_meta($id, '_klever_origin', true) ?: 'NHẬP KHẨU';
            $pct = '';
            if ($product->is_on_sale() && $product->get_regular_price() > 0) {
                $pct = '-' . round(100 - ($product->get_sale_price() / $product->get_regular_price() * 100)) . '%';
            }
        ?>
        <a href="<?php echo esc_url(get_permalink($id)); ?>" class="klever-product-card">
            <div class="klever-product-card__img-wrap">
                <?php if ($pct) : ?><span class="klever-product-card__badge"><?php echo esc_html($pct); ?></span><?php endif; ?>
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy">
            </div>
            <div class="klever-product-card__body">
                <div class="klever-product-card__origin"><?php echo esc_html($origin); ?></div>
                <h3 class="klever-product-card__name"><?php echo esc_html($product->get_name()); ?></h3>
                <?php echo $product->get_price_html(); ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- PROMOTIONS -->
<section class="klever-promos">
    <h2 class="klever-promos__title">Khuyến mãi dành cho bạn</h2>
    <div class="klever-promos__scroll">
        <div class="klever-coupon">
            <button class="klever-coupon__info" aria-label="Thông tin">ⓘ</button>
            <div class="klever-coupon__amount">Giảm Ngay <strong>150k</strong></div>
            <div class="klever-coupon__desc">Khi mua đơn từ 2.999k</div>
            <div class="klever-coupon__footer">
                <div class="klever-coupon__meta">
                    <span>Mã: <strong>KM150K-T6</strong></span>
                    <span>HSD: 30/06/2026</span>
                </div>
                <button class="klever-coupon__copy" data-code="KM150K-T6">SAO CHÉP MÃ</button>
            </div>
        </div>
        <div class="klever-coupon klever-coupon--gold">
            <button class="klever-coupon__info" aria-label="Thông tin">ⓘ</button>
            <div class="klever-coupon__coin">🪙</div>
            <div class="klever-coupon__amount">Hoàn tiền <strong>5%</strong></div>
            <div class="klever-coupon__desc">Khi thanh toán online</div>
            <div class="klever-coupon__footer">
                <div class="klever-coupon__meta">
                    <span>Mã: <strong>HOAN5-T6</strong></span>
                    <span>HSD: 30/06/2026</span>
                </div>
                <button class="klever-coupon__copy" data-code="HOAN5-T6">SAO CHÉP MÃ</button>
            </div>
        </div>
        <div class="klever-coupon">
            <button class="klever-coupon__info" aria-label="Thông tin">ⓘ</button>
            <div class="klever-coupon__amount">Freeship <strong>50k</strong></div>
            <div class="klever-coupon__desc">Đơn hàng từ 999k</div>
            <div class="klever-coupon__footer">
                <div class="klever-coupon__meta">
                    <span>Mã: <strong>FREE50-T6</strong></span>
                    <span>HSD: 15/07/2026</span>
                </div>
                <button class="klever-coupon__copy" data-code="FREE50-T6">SAO CHÉP MÃ</button>
            </div>
        </div>
    </div>
</section>

<!-- GIFT SECTION -->
<section class="klever-gifts">
    <div class="klever-gifts__heading">
        <h2>QUÀ TẶNG TRÁI CÂY</h2>
    </div>
    <div class="klever-gifts__tabs" id="klever-gift-tabs">
        <button class="klever-gifts__tab klever-gifts__tab--active" data-tab="chuc-mung">
            <span class="klever-gifts__tab-icon">🎉</span>
            <span>Chúc mừng nhân dịp</span>
        </button>
        <button class="klever-gifts__tab" data-tab="cao-cap">
            <span class="klever-gifts__tab-icon">🎁</span>
            <span>Quà Tặng Cao Cấp</span>
        </button>
        <button class="klever-gifts__tab" data-tab="cam-on">
            <span class="klever-gifts__tab-icon">💌</span>
            <span>Cảm ơn</span>
        </button>
    </div>
    <?php
    $gift_tabs = [
        'chuc-mung' => 'qua-tang-chuc-mung',
        'cao-cap'   => 'qua-tang-cao-cap',
        'cam-on'    => 'qua-tang-cam-on',
    ];
    foreach ($gift_tabs as $tab_id => $cat_slug) :
        $gift_products = wc_get_products([
            'limit'    => 4,
            'status'   => 'publish',
            'category' => [$cat_slug],
        ]);
        if (empty($gift_products)) {
            $gift_products = wc_get_products(['limit' => 2, 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC']);
        }
    ?>
    <div class="klever-gifts__panel<?php echo $tab_id === 'chuc-mung' ? ' klever-gifts__panel--active' : ''; ?>" data-panel="<?php echo esc_attr($tab_id); ?>">
        <div class="klever-gift-grid">
            <?php foreach ($gift_products as $product) :
                $id = $product->get_id();
                $image = wp_get_attachment_image_url($product->get_image_id(), 'medium') ?: 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=400';
                $from_price = get_post_meta($id, '_klever_from_price', true);
            ?>
            <div class="klever-gift-card">
                <a href="<?php echo esc_url(get_permalink($id)); ?>" class="klever-gift-card__img-link">
                    <div class="klever-gift-card__img-wrap">
                        <?php if ($from_price) : ?>
                        <span class="klever-gift-card__from">Chỉ từ <?php echo esc_html($from_price); ?> CHƯA VAT</span>
                        <?php endif; ?>
                        <span class="klever-product-card__wishlist">❤️</span>
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy">
                    </div>
                </a>
                <h3 class="klever-gift-card__name"><?php echo esc_html($product->get_name()); ?></h3>
                <div class="klever-gift-card__price"><?php echo $product->get_price_html(); ?></div>
                <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="klever-gift-card__btn ajax_add_to_cart add_to_cart_button" data-product_id="<?php echo esc_attr($id); ?>">
                    🛍️ CHỌN MUA
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</section>

<!-- OUR STORY -->
<section class="klever-story">
    <div class="klever-story__header">
        <h2 class="klever-story__title">KLEVER FRUIT, CÂU CHUYỆN CỦA CHÚNG TÔI QUA NHỮNG THƯỚC PHIM</h2>
        <div class="klever-story__nav">
            <button class="klever-story__arrow" id="klever-story-prev" aria-label="Trước">‹</button>
            <button class="klever-story__arrow" id="klever-story-next" aria-label="Sau">›</button>
        </div>
    </div>
    <div class="klever-story__track" id="klever-story-track">
        <?php
        $stories = get_posts(['numberposts' => 6, 'post_status' => 'publish', 'category_name' => 'cau-chuyen']);
        if (empty($stories)) {
            $stories = get_posts(['numberposts' => 4, 'post_status' => 'publish']);
        }
        foreach ($stories as $post) :
            setup_postdata($post);
            $thumb = get_the_post_thumbnail_url($post, 'medium') ?: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=600';
        ?>
        <article class="klever-story-card">
            <a href="<?php echo esc_url(get_permalink($post)); ?>">
                <img class="klever-story-card__img" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>" loading="lazy">
                <h3 class="klever-story-card__title"><?php echo esc_html(get_the_title($post)); ?></h3>
                <p class="klever-story-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt($post), 18)); ?></p>
                <div class="klever-story-card__footer">
                    <span class="klever-story-card__date">📅 <?php echo esc_html(get_the_date('d \T\h\á\n\g m, Y', $post)); ?></span>
                    <span class="klever-story-card__more">Xem thêm »</span>
                </div>
            </a>
        </article>
        <?php endforeach;
        wp_reset_postdata();
        ?>
    </div>
</section>

<!-- FOOTER -->
<footer class="klever-footer">
    <div class="klever-footer__logo">
        <span>KLEVER</span> 🍎 <span>FRUIT</span>
    </div>
    <div class="klever-footer__accordion" id="klever-footer-accordion">
        <details class="klever-footer__item">
            <summary>Thông tin công ty <span class="klever-footer__chevron">›</span></summary>
            <div class="klever-footer__content">
                <a href="#">Giới thiệu Klever Fruit</a>
                <a href="#">Tuyển dụng</a>
                <a href="#">Hệ thống cửa hàng</a>
            </div>
        </details>
        <details class="klever-footer__item">
            <summary>Chính sách và Dịch vụ <span class="klever-footer__chevron">›</span></summary>
            <div class="klever-footer__content">
                <a href="#">Chính sách đổi trả</a>
                <a href="#">Chính sách giao hàng</a>
                <a href="#">Bảo mật thông tin</a>
            </div>
        </details>
        <details class="klever-footer__item" open>
            <summary>Quà tặng <span class="klever-footer__chevron">›</span></summary>
            <div class="klever-footer__content">
                <a href="#">Quà tặng doanh nghiệp</a>
                <a href="#">Quà tặng cá nhân</a>
                <a href="#">Giỏ quà theo mùa</a>
            </div>
        </details>
        <details class="klever-footer__item" open>
            <summary>Kênh liên hệ <span class="klever-footer__chevron">›</span></summary>
            <div class="klever-footer__content">
                <a href="tel:0900000000">Hotline: 0900 000 000</a>
                <a href="mailto:contact@lamwebre.com">contact@lamwebre.com</a>
            </div>
        </details>
    </div>
    <div class="klever-footer__social">
        <a href="#" class="klever-footer__social-link" aria-label="Facebook">f</a>
        <a href="#" class="klever-footer__social-link" aria-label="Instagram">📷</a>
    </div>
    <p class="klever-footer__copy">© <?php echo esc_html(date('Y')); ?> KLEVER FRUIT. All rights reserved.</p>
</footer>

<!-- FLOATING BUTTONS -->
<div class="klever-fabs">
    <button class="klever-fab klever-fab--top" id="klever-scroll-top" aria-label="Lên đầu trang">↑</button>
    <a href="tel:0900000000" class="klever-fab klever-fab--contact">
        <span class="klever-fab__icon">💬</span>
        Liên hệ
    </a>
</div>

<!-- MOBILE DRAWER -->
<div class="klever-drawer" id="klever-drawer">
    <div class="klever-drawer__panel">
        <button class="klever-drawer__close" id="klever-drawer-close">✕</button>
        <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Cửa hàng</a>
        <a href="<?php echo esc_url(wc_get_cart_url()); ?>">Giỏ hàng</a>
        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">Tài khoản</a>
    </div>
</div>

</div><!-- .wp-site-blocks -->

<?php wp_footer(); ?>
</body>
</html>
