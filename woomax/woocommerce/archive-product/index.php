<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * @package WooMax
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$columns           = woocommerce_get_default_products_per_row();
$total             = woocommerce_get_default_product_rows_per_page() * $columns;
$orderby           = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );

?>

<main id="primary" class="site-main woocommerce-shop-page">

    <?php
    /**
     * Hook: woocommerce_before_main_content
     */
    do_action( 'woocommerce_before_main_content' );
    ?>

    <header class="woocommerce-products-header shop-header">
        <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
            <h1 class="woocommerce-products-header__title page-title">
                <?php woocommerce_page_title(); ?>
            </h1>
        <?php endif; ?>

        <?php
        /**
         * Hook: woocommerce_archive_description
         */
        do_action( 'woocommerce_archive_description' );
        ?>
    </header>

    <?php if ( woocommerce_product_loop() ) : ?>

        <div class="shop-toolbar">
            <?php
            /**
             * Hook: woocommerce_before_shop_loop
             */
            do_action( 'woocommerce_before_shop_loop' );
            ?>
        </div>

        <?php woocommerce_product_loop_start(); ?>

        <?php if ( wc_get_loop_prop( 'total' ) ) : ?>
            <?php while ( have_posts() ) : ?>
                <?php the_post(); ?>
                <?php
                /**
                 * Hook: woocommerce_shop_loop
                 */
                do_action( 'woocommerce_shop_loop' );
                ?>
                <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php endwhile; ?>
        <?php endif; ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
        /**
         * Hook: woocommerce_after_shop_loop
         */
        do_action( 'woocommerce_after_shop_loop' );
        ?>

    <?php else : ?>
        <?php
        /**
         * Hook: woocommerce_no_products_found
         */
        do_action( 'woocommerce_no_products_found' );
        ?>
    <?php endif; ?>

    <?php
    /**
     * Hook: woocommerce_after_main_content
     */
    do_action( 'woocommerce_after_main_content' );
    ?>

</main>

<?php
get_footer( 'shop' );
