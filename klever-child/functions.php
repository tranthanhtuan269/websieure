<?php

defined('ABSPATH') || exit;

add_action('after_setup_theme', function (): void {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
});

add_action('wp_enqueue_scripts', function (): void {
    $parent = 'twentytwentyfive-style';
    wp_enqueue_style($parent, get_template_directory_uri().'/style.css', [], wp_get_theme(get_template())->get('Version'));
    wp_enqueue_style(
        'klever-child',
        get_stylesheet_directory_uri().'/assets/css/klever.css',
        [$parent],
        '1.0.0'
    );
    wp_enqueue_script(
        'klever-child',
        get_stylesheet_directory_uri().'/assets/js/klever.js',
        [],
        '1.0.0',
        true
    );
}, 20);

add_filter('woocommerce_currency_symbol', function (string $symbol, string $currency): string {
    return $currency === 'VND' ? 'đ' : $symbol;
}, 10, 2);

add_filter('woocommerce_get_price_html', function (string $price, $product): string {
    if (! $product->is_on_sale()) {
        return $price;
    }

    $regular = wc_price($product->get_regular_price());
    $sale = wc_price($product->get_sale_price());

    return '<span class="klever-price-sale">'.$sale.'</span><span class="klever-price-regular">'.$regular.'</span>';
}, 10, 2);

add_action('init', function (): void {
    register_block_pattern_category('klever', [
        'label' => __('Klever Fruit', 'klever-child'),
    ]);
});

add_filter('body_class', function (array $classes): array {
    $classes[] = 'klever-fruit';

    return $classes;
});

// Ẩn tiêu đề trang mặc định trên front page
add_filter('render_block', function (string $content, array $block): string {
    if (is_front_page() && ($block['blockName'] ?? '') === 'core/post-title') {
        return '';
    }

    return $content;
}, 10, 2);
