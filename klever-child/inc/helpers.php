<?php

defined('ABSPATH') || exit;

function klever_products(string $category = '', int $limit = 6, bool $on_sale = false): array
{
    $args = [
        'limit'   => $limit,
        'status'  => 'publish',
        'orderby' => 'date',
        'order'   => 'DESC',
    ];

    if ($category !== '') {
        $args['category'] = [$category];
    }

    if ($on_sale) {
        $args['on_sale'] = true;
    }

    $products = wc_get_products($args);

    if (empty($products) && $category !== '') {
        unset($args['category']);

        return wc_get_products($args);
    }

    return $products;
}

function klever_product_image(WC_Product $product, string $fallback = ''): string
{
    $url = wp_get_attachment_image_url($product->get_image_id(), 'medium');

    return $url ?: ($fallback ?: 'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=600');
}

function klever_discount_pct(WC_Product $product): string
{
    if (! $product->is_on_sale() || ! $product->get_regular_price()) {
        return '';
    }

    return '-'.round(100 - ($product->get_sale_price() / $product->get_regular_price() * 100)).'%';
}

function klever_render_product_card(WC_Product $product, string $style = 'flash'): void
{
    $id = $product->get_id();
    $image = klever_product_image($product);
    $pct = klever_discount_pct($product);
    $origin = get_post_meta($id, '_klever_origin', true) ?: '';
    $from = get_post_meta($id, '_klever_from_price', true) ?: '';
    ?>
    <div class="kf-card kf-card--<?php echo esc_attr($style); ?>">
        <a href="<?php echo esc_url(get_permalink($id)); ?>" class="kf-card__img-link">
            <div class="kf-card__img">
                <?php if ($pct) : ?><span class="kf-card__badge"><?php echo esc_html($pct); ?></span><?php endif; ?>
                <?php if ($from) : ?><span class="kf-card__from">Chỉ từ <?php echo esc_html($from); ?> CHƯA VAT</span><?php endif; ?>
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy">
            </div>
        </a>
        <div class="kf-card__body">
            <?php if ($origin) : ?><div class="kf-card__origin"><?php echo esc_html($origin); ?></div><?php endif; ?>
            <h3 class="kf-card__name"><a href="<?php echo esc_url(get_permalink($id)); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
            <div class="kf-card__price"><?php echo $product->get_price_html(); ?></div>
            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="kf-card__btn add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr($id); ?>">CHỌN MUA</a>
        </div>
    </div>
    <?php
}

function klever_format_price(int|float $amount): string
{
    return number_format($amount, 0, ',', '.').'đ';
}
