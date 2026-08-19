<?php
/**
 * WooMax – Intégration WooCommerce
 *
 * @package WooMax
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ─── Remove default WooCommerce wrappers ──────────────────────────────────────
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar',             'woocommerce_get_sidebar', 10 );

// ─── Custom wrappers ──────────────────────────────────────────────────────────
add_action( 'woocommerce_before_main_content', function() {
    echo '<div class="woomax-wc-wrapper woomax-container">';
}, 10 );
add_action( 'woocommerce_after_main_content', function() {
    echo '</div>';
}, 10 );

// ─── Products per page ────────────────────────────────────────────────────────
add_filter( 'loop_shop_per_page', function() {
    return absint( get_theme_mod( 'woomax_shop_per_page', 12 ) );
}, 20 );

// ─── Shop columns ────────────────────────────────────────────────────────────
add_filter( 'loop_shop_columns', function() {
    return absint( get_theme_mod( 'woomax_shop_columns', 4 ) );
} );

// ─── Remove default WooCommerce styles ───────────────────────────────────────
add_filter( 'woocommerce_enqueue_styles', function( $styles ) {
    unset( $styles['woocommerce-layout'] );
    unset( $styles['woocommerce-smallscreen'] );
    return $styles;
} );

// ─── Breadcrumbs ──────────────────────────────────────────────────────────────
add_filter( 'woocommerce_breadcrumb_defaults', function( $defaults ) {
    $defaults['delimiter']   = '<span class="woomax-breadcrumb__sep">›</span>';
    $defaults['wrap_before'] = '<nav class="woomax-breadcrumb" aria-label="' . esc_attr__( 'Fil d\'Ariane', 'woomax' ) . '">';
    $defaults['wrap_after']  = '</nav>';
    $defaults['before']      = '';
    $defaults['after']       = '';
    return $defaults;
} );

// ─── Related products ─────────────────────────────────────────────────────────
add_filter( 'woocommerce_output_related_products_args', function( $args ) {
    $args['posts_per_page'] = absint( get_theme_mod( 'woomax_related_products_count', 4 ) );
    $args['columns']        = 4;
    return $args;
} );

// ─── Remove default product image from single page ───────────────────────────
// We use our own gallery template

// ─── Cart fragments ───────────────────────────────────────────────────────────
add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
    ob_start();
    ?>
    <span class="woomax-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.woomax-cart-count'] = ob_get_clean();
    return $fragments;
} );

// ─── Mini cart ────────────────────────────────────────────────────────────────
function woomax_render_mini_cart() {
    ?>
    <div class="woomax-cart-overlay" id="woomax-cart-overlay"></div>
    <div class="woomax-cart-sidebar" id="woomax-cart-sidebar" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Panier', 'woomax' ); ?>">
        <div class="woomax-cart-sidebar__header">
            <span class="woomax-cart-sidebar__title"><?php esc_html_e( 'Mon panier', 'woomax' ); ?></span>
            <button class="woomax-cart-sidebar__close" id="woomax-cart-close" aria-label="<?php esc_attr_e( 'Fermer', 'woomax' ); ?>">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="woomax-cart-sidebar__body">
            <div class="woomax-mini-cart-content">
                <?php woocommerce_mini_cart(); ?>
            </div>
        </div>
        <div class="woomax-cart-sidebar__footer">
            <div class="woomax-cart-sidebar__total">
                <span><?php esc_html_e( 'Total', 'woomax' ); ?></span>
                <span class="woomax-cart-total"><?php echo WC()->cart ? WC()->cart->get_cart_total() : ''; ?></span>
            </div>
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="woomax-btn woomax-btn--secondary" style="width:100%;text-align:center;margin-bottom:10px;">
                <?php esc_html_e( 'Voir le panier', 'woomax' ); ?>
            </a>
            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="woomax-btn woomax-btn--primary" style="width:100%;text-align:center;">
                <?php esc_html_e( 'Commander', 'woomax' ); ?>
            </a>
        </div>
    </div>
    <?php
}
add_action( 'wp_footer', 'woomax_render_mini_cart', 5 );

// ─── Quick view AJAX ──────────────────────────────────────────────────────────
function woomax_quick_view() {
    check_ajax_referer( 'woomax-nonce', 'nonce' );
    $product_id = absint( $_POST['product_id'] ?? 0 );
    if ( ! $product_id ) wp_send_json_error();
    $product = wc_get_product( $product_id );
    if ( ! $product ) wp_send_json_error();
    ob_start();
    ?>
    <div class="woomax-quick-view__inner">
        <div class="woomax-quick-view__gallery">
            <?php echo woocommerce_get_product_thumbnail( 'woomax-medium' ); ?>
        </div>
        <div class="woomax-quick-view__info">
            <h2><?php echo esc_html( $product->get_name() ); ?></h2>
            <div class="woomax-single-product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
            <div class="woomax-single-product__short-desc"><?php echo wp_kses_post( $product->get_short_description() ); ?></div>
            <?php if ( $product->is_in_stock() ) : ?>
            <div class="woomax-single-product__add-to-cart">
                <div class="woomax-qty-input">
                    <button type="button" class="woomax-qty-minus">−</button>
                    <input type="number" value="1" min="1" class="woomax-qty">
                    <button type="button" class="woomax-qty-plus">+</button>
                </div>
                <button class="woomax-btn woomax-btn--primary woomax-atc-quick" data-id="<?php echo $product->get_id(); ?>">
                    <?php esc_html_e( 'Ajouter au panier', 'woomax' ); ?>
                </button>
            </div>
            <?php endif; ?>
            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="woomax-btn woomax-btn--secondary" style="margin-top:12px;">
                <?php esc_html_e( 'Voir les détails', 'woomax' ); ?>
            </a>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    wp_send_json_success( [ 'html' => $html ] );
}
add_action( 'wp_ajax_woomax_quick_view',        'woomax_quick_view' );
add_action( 'wp_ajax_nopriv_woomax_quick_view', 'woomax_quick_view' );

// ─── Quick view modal ─────────────────────────────────────────────────────────
add_action( 'wp_footer', function() {
    if ( ! get_theme_mod( 'woomax_show_quickview', true ) ) return;
    ?>
    <div class="woomax-quick-view-modal" id="woomax-quick-view" style="
        display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.7);
        align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;padding:40px;max-width:900px;width:100%;position:relative;max-height:90vh;overflow-y:auto;">
            <button onclick="document.getElementById('woomax-quick-view').style.display='none'"
                style="position:absolute;top:16px;right:16px;background:#f5f5f5;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:18px;">×</button>
            <div class="woomax-quick-view__inner" style="display:grid;grid-template-columns:1fr 1fr;gap:40px;"></div>
        </div>
    </div>
    <?php
}, 10 );

// ─── Product wishlist button ───────────────────────────────────────────────────
function woomax_wishlist_button( $product_id ) {
    if ( ! get_theme_mod( 'woomax_show_wishlist_btn', true ) ) return;
    $wishlist = isset( $_COOKIE['woomax_wishlist'] ) ? json_decode( stripslashes( $_COOKIE['woomax_wishlist'] ), true ) : [];
    $active   = in_array( $product_id, (array) $wishlist ) ? 'active' : '';
    printf(
        '<button class="woomax-product-card__action-btn woomax-wishlist-btn %s" data-id="%d" title="%s" aria-label="%s"><i class="fa-solid fa-heart"></i></button>',
        $active, $product_id,
        esc_attr__( 'Ajouter à la liste de souhaits', 'woomax' ),
        esc_attr__( 'Liste de souhaits', 'woomax' )
    );
}

// ─── Checkout field labels ────────────────────────────────────────────────────
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    $fields['billing']['billing_first_name']['placeholder'] = __( 'Prénom', 'woomax' );
    $fields['billing']['billing_last_name']['placeholder']  = __( 'Nom', 'woomax' );
    $fields['billing']['billing_email']['placeholder']      = __( 'Adresse e-mail', 'woomax' );
    $fields['billing']['billing_phone']['placeholder']      = __( 'Numéro de téléphone', 'woomax' );
    return $fields;
} );

// ─── Remove WC default styles that conflict ───────────────────────────────────
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'woomax-woocommerce', WOOMAX_URI . '/assets/css/woocommerce.css', [], WOOMAX_VERSION );
}, 20 );
