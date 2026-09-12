<?php
/**
 * WooMax – Carte produit réutilisable
 *
 * @package WooMax
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! isset( $product ) || ! $product ) {
    global $product;
    $product = wc_get_product( get_the_ID() );
}
if ( ! $product ) return;

$product_id = $product->get_id();
$cats       = wc_get_product_category_list( $product_id, ', ', '', '' );
$permalink  = get_permalink( $product_id );
?>
<div class="woomax-product-card" data-product-id="<?php echo $product_id; ?>">

    <!-- Badges -->
    <?php woomax_product_badges( $product ); ?>

    <!-- Image -->
    <div class="woomax-product-card__image">
        <a href="<?php echo esc_url( $permalink ); ?>">
            <?php
            $ratio_map = [ 'square' => '1 / 1', 'portrait' => '3 / 4', 'landscape' => '4 / 3', 'wide' => '16 / 9' ];
            $ratio = $ratio_map[ get_theme_mod( 'woomax_card_image_ratio', 'square' ) ] ?? '1 / 1';
            $img_id = $product->get_image_id();
            $src    = $img_id ? wp_get_attachment_image_url( $img_id, 'woomax-thumbnail' ) : wc_placeholder_img_src( 'woomax-thumbnail' );
            echo '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $product->get_name() ) . '" loading="lazy" style="width:100%;height:100%;object-fit:cover;">';
            ?>
        </a>

        <!-- Hover actions -->
        <div class="woomax-product-card__actions">
            <?php if ( get_theme_mod( 'woomax_show_wishlist_btn', true ) ) :
                woomax_wishlist_button( $product_id );
            endif; ?>
            <?php if ( get_theme_mod( 'woomax_show_quickview', true ) ) : ?>
            <button class="woomax-product-card__action-btn woomax-quickview-btn" data-id="<?php echo $product_id; ?>" title="<?php esc_attr_e( 'Vue rapide', 'woomax' ); ?>">
                <i class="fa-solid fa-eye"></i>
            </button>
            <?php endif; ?>
            <?php if ( get_theme_mod( 'woomax_show_compare_btn', false ) ) : ?>
            <button class="woomax-product-card__action-btn" title="<?php esc_attr_e( 'Comparer', 'woomax' ); ?>">
                <i class="fa-solid fa-right-left"></i>
            </button>
            <?php endif; ?>
        </div>

        <!-- Add to cart overlay -->
        <div class="woomax-product-card__add-to-cart">
            <?php if ( $product->is_in_stock() ) : ?>
            <?php if ( $product->is_type( 'simple' ) ) : ?>
            <button class="woomax-atc-btn button" data-product-id="<?php echo $product_id; ?>">
                <i class="fa-solid fa-bag-shopping" style="margin-right:8px;"></i>
                <?php esc_html_e( 'Ajouter au panier', 'woomax' ); ?>
            </button>
            <?php else : ?>
            <a href="<?php echo esc_url( $permalink ); ?>" class="button">
                <i class="fa-solid fa-eye" style="margin-right:8px;"></i>
                <?php esc_html_e( 'Voir les options', 'woomax' ); ?>
            </a>
            <?php endif; ?>
            <?php else : ?>
            <span class="button" style="background:#999;cursor:default;"><?php esc_html_e( 'Épuisé', 'woomax' ); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Body -->
    <div class="woomax-product-card__body">
        <?php if ( $cats ) : ?>
        <span class="woomax-product-card__category"><?php echo wp_kses_post( $cats ); ?></span>
        <?php endif; ?>
        <h3 class="woomax-product-card__title">
            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
        </h3>
        <?php if ( get_theme_mod( 'woomax_show_rating', true ) ) :
            woomax_product_stars( $product );
        endif; ?>
        <?php woomax_product_price_html( $product ); ?>
    </div>

</div>
