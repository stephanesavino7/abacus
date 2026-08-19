<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WooMax
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <section class="error-404">
            <div class="error-404__visual">
                <svg width="200" height="160" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <!-- Tree trunk -->
                    <rect x="92" y="90" width="16" height="60" rx="4" fill="#8B6914"/>
                    <!-- Tree canopy -->
                    <ellipse cx="100" cy="75" rx="45" ry="50" fill="#4a7c59"/>
                    <ellipse cx="100" cy="65" rx="38" ry="42" fill="#5a9469"/>
                    <!-- 404 text carved in tree -->
                    <text x="100" y="80" text-anchor="middle" font-family="serif" font-size="22" font-weight="bold" fill="rgba(255,255,255,0.7)">404</text>
                    <!-- Ground -->
                    <ellipse cx="100" cy="152" rx="55" ry="8" fill="#c4a882" opacity="0.5"/>
                </svg>
            </div>

            <header class="error-404__header">
                <h1 class="error-404__title"><?php esc_html_e( 'Page introuvable', 'woomax' ); ?></h1>
                <p class="error-404__subtitle"><?php esc_html_e( 'Oups ! On dirait que vous vous êtes perdu dans la forêt.', 'woomax' ); ?></p>
            </header>

            <div class="error-404__content">
                <p><?php esc_html_e( 'La page que vous recherchez a peut-être été déplacée, renommée ou n\'existe plus. Pas de panique — utilisez la recherche ou retournez à l\'accueil.', 'woomax' ); ?></p>

                <?php get_search_form(); ?>

                <div class="error-404__actions">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                        <?php esc_html_e( 'Retour à l\'accueil', 'woomax' ); ?>
                    </a>
                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-outline">
                            <?php esc_html_e( 'Voir la boutique', 'woomax' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <div class="error-404__products">
                    <h2><?php esc_html_e( 'Nos produits populaires', 'woomax' ); ?></h2>
                    <?php
                    $products = wc_get_products( array(
                        'limit'    => 4,
                        'orderby'  => 'popularity',
                        'status'   => 'publish',
                    ) );

                    if ( $products ) : ?>
                        <div class="products-mini-grid">
                            <?php foreach ( $products as $product ) : ?>
                                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="mini-product-card">
                                    <?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
                                    <span class="mini-product-name"><?php echo esc_html( $product->get_name() ); ?></span>
                                    <span class="mini-product-price"><?php echo $product->get_price_html(); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php
get_footer();
