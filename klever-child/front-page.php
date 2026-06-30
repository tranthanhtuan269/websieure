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
<section>
    <h2 class="klever-section-title">Sản phẩm nổi bật</h2>
    <div class="klever-all-products">
        <?php
        $all = wc_get_products(['limit' => 8, 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC']);
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

<!-- FLOATING CONTACT -->
<a href="tel:0900000000" class="klever-fab">
    <span class="klever-fab__icon">💬</span>
    Liên hệ
</a>

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
