<?php
/**
 * The Template for displaying all single products
 *
 * @package WooMax
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

?>

<main id="primary" class="site-main woocommerce-product-page">

    <?php
    while ( have_posts() ) :
        the_post();

        /**
         * Hook: woocommerce_before_single_product
         */
        do_action( 'woocommerce_before_single_product' );

        if ( post_password_required() ) {
            echo get_the_password_form(); // WPCS: XSS ok.
            return;
        }
    ?>

    <article id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

        <div class="single-product-inner container">

            <?php
            /**
             * Hook: woocommerce_before_single_product_summary
             * Outputs gallery, sale flash, etc.
             */
            do_action( 'woocommerce_before_single_product_summary' );
            ?>

            <div class="summary entry-summary">
                <?php
                /**
                 * Hook: woocommerce_single_product_summary
                 * Outputs title, rating, price, excerpt, add-to-cart, meta, sharing
                 */
                do_action( 'woocommerce_single_product_summary' );
                ?>
            </div>

        </div>

        <div class="container">
            <?php
            /**
             * Hook: woocommerce_after_single_product_summary
             * Outputs tabs, up-sells, related products
             */
            do_action( 'woocommerce_after_single_product_summary' );
            ?>
        </div>

    </article>

    <?php
        /**
         * Hook: woocommerce_after_single_product
         */
        do_action( 'woocommerce_after_single_product' );

    endwhile;
    ?>

</main>

<?php
get_footer( 'shop' );
