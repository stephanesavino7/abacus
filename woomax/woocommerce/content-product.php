<?php
/**
 * The template for displaying product content within loops.
 *
 * @package WooMax
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<li <?php wc_product_class( 'woomax-product-card', $product ); ?>>
    <div class="product-card-inner">
        <?php
        /**
         * Hook: woocommerce_before_shop_loop_item
         */
        do_action( 'woocommerce_before_shop_loop_item' );
        ?>

        <a href="<?php the_permalink(); ?>" class="product-card-link">
            <div class="product-card-media">
                <?php
                /**
                 * Hook: woocommerce_before_shop_loop_item_title
                 * - woocommerce_show_product_loop_sale_flash
                 * - woocommerce_template_loop_product_thumbnail
                 */
                do_action( 'woocommerce_before_shop_loop_item_title' );
                ?>

                <?php if ( $product->is_on_sale() ) : ?>
                    <span class="product-badge product-badge--sale"><?php esc_html_e( 'Promo', 'woomax' ); ?></span>
                <?php elseif ( $product->is_featured() ) : ?>
                    <span class="product-badge product-badge--featured"><?php esc_html_e( 'Vedette', 'woomax' ); ?></span>
                <?php endif; ?>

                <div class="product-card-overlay">
                    <span class="quick-view-label"><?php esc_html_e( 'Voir le produit', 'woomax' ); ?></span>
                </div>
            </div>
        </a>

        <div class="product-card-info">
            <?php
            /**
             * Hook: woocommerce_shop_loop_item_title
             * - woocommerce_template_loop_product_title
             */
            do_action( 'woocommerce_shop_loop_item_title' );
            ?>

            <div class="product-card-meta">
                <?php
                /**
                 * Hook: woocommerce_after_shop_loop_item_title
                 * - woocommerce_template_loop_rating
                 * - woocommerce_template_loop_price
                 */
                do_action( 'woocommerce_after_shop_loop_item_title' );
                ?>
            </div>

            <div class="product-card-actions">
                <?php
                /**
                 * Hook: woocommerce_after_shop_loop_item
                 * - woocommerce_template_loop_add_to_cart
                 */
                do_action( 'woocommerce_after_shop_loop_item' );
                ?>
            </div>
        </div>
    </div>
</li>
