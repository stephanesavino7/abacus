<?php
/**
 * WooMax – Fonctions utilitaires
 *
 * @package WooMax
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Rendu du logo
 */
function woomax_render_logo( $context = 'default' ) {
    $logo_url   = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : '';
    $logo_light = get_theme_mod( 'woomax_logo_light', '' );
    $max_h      = get_theme_mod( 'woomax_logo_max_height', 50 );
    $text       = get_theme_mod( 'woomax_logo_text', get_bloginfo( 'name' ) );
    $is_trans   = get_theme_mod( 'woomax_header_transparent', false ) && $context === 'header';
    $use_url    = $is_trans && $logo_light ? $logo_light : $logo_url;
    echo '<div class="woomax-logo">';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
    if ( $use_url ) {
        echo '<img src="' . esc_url( $use_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="height:' . absint( $max_h ) . 'px;width:auto;">';
    } else {
        echo '<span class="woomax-logo__text">' . esc_html( $text ) . '</span>';
    }
    echo '</a>';
    echo '</div>';
}

/**
 * Afficher les icônes sociales du footer
 */
function woomax_social_icons() {
    $networks = [
        'facebook'  => [ 'icon' => 'fab fa-facebook-f',  'label' => 'Facebook' ],
        'instagram' => [ 'icon' => 'fab fa-instagram',   'label' => 'Instagram' ],
        'twitter'   => [ 'icon' => 'fab fa-x-twitter',   'label' => 'Twitter/X' ],
        'youtube'   => [ 'icon' => 'fab fa-youtube',     'label' => 'YouTube' ],
        'pinterest' => [ 'icon' => 'fab fa-pinterest-p', 'label' => 'Pinterest' ],
        'linkedin'  => [ 'icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn' ],
        'tiktok'    => [ 'icon' => 'fab fa-tiktok',      'label' => 'TikTok' ],
    ];
    $output = '';
    foreach ( $networks as $key => $data ) {
        $url = get_theme_mod( "woomax_social_{$key}", '' );
        if ( $url ) {
            $output .= sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><i class="%s"></i></a>',
                esc_url( $url ), esc_attr( $data['label'] ), esc_attr( $data['icon'] )
            );
        }
    }
    return $output;
}

/**
 * Nombre de posts de la catégorie d'un produit
 */
function woomax_get_category_count( $term_id ) {
    $term = get_term( $term_id );
    return $term && ! is_wp_error( $term ) ? $term->count : 0;
}

/**
 * Icône FontAwesome en HTML
 */
function woomax_icon( $name, $class = '' ) {
    return sprintf( '<i class="fa-solid fa-%s %s"></i>', esc_attr( $name ), esc_attr( $class ) );
}

/**
 * Section title helper
 */
function woomax_section_title( $subtitle = '', $title = '', $description = '' ) {
    echo '<div class="woomax-section-title">';
    if ( $subtitle ) echo '<span class="subtitle">' . esc_html( $subtitle ) . '</span>';
    if ( $title )    echo '<h2>' . wp_kses_post( $title ) . '</h2>';
    if ( $description ) echo '<p>' . wp_kses_post( $description ) . '</p>';
    echo '</div>';
}

/**
 * Récupère les produits WooCommerce par type
 */
function woomax_get_products( $type = 'featured', $count = 8 ) {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return new WP_Query( [ 'post__in' => [ 0 ] ] );
    }
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'meta_query'     => [ 'relation' => 'AND' ],
    ];
    switch ( $type ) {
        case 'featured':
            $args['tax_query'] = [ [ 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured' ] ];
            break;
        case 'best_selling':
            $args['meta_key'] = 'total_sales';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        case 'newest':
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
        case 'on_sale':
            $args['post__in'] = array_slice( wc_get_product_ids_on_sale(), 0, $count * 2 );
            break;
    }
    return new WP_Query( $args );
}

/**
 * Miniature produit avec ratio
 */
function woomax_product_thumbnail( $product, $size = 'woomax-thumbnail' ) {
    $ratio_map = [
        'square'    => '1 / 1',
        'portrait'  => '3 / 4',
        'landscape' => '4 / 3',
        'wide'      => '16 / 9',
    ];
    $ratio = $ratio_map[ get_theme_mod( 'woomax_card_image_ratio', 'square' ) ] ?? '1 / 1';
    $img_id = $product->get_image_id();
    $src    = $img_id ? wp_get_attachment_image_url( $img_id, $size ) : wc_placeholder_img_src( $size );
    printf(
        '<div class="woomax-product-card__image" style="aspect-ratio:%s;"><img src="%s" alt="%s" loading="lazy"></div>',
        esc_attr( $ratio ),
        esc_url( $src ),
        esc_attr( $product->get_name() )
    );
}

/**
 * Étoiles produit
 */
function woomax_product_stars( $product ) {
    $rating = (float) $product->get_average_rating();
    $count  = (int) $product->get_review_count();
    if ( ! $count ) return;
    $filled = round( $rating );
    $stars  = str_repeat( '★', $filled ) . str_repeat( '☆', 5 - $filled );
    printf(
        '<div class="woomax-product-card__rating"><span class="woomax-stars">%s</span><span class="woomax-rating-count">(%d)</span></div>',
        esc_html( $stars ), $count
    );
}

/**
 * Prix formaté produit
 */
function woomax_product_price_html( $product ) {
    echo '<div class="woomax-product-card__price">';
    echo '<span class="woomax-price">' . wp_kses_post( $product->get_price_html() ) . '</span>';
    echo '</div>';
}

/**
 * Badges produit
 */
function woomax_product_badges( $product ) {
    echo '<div class="woomax-product-card__badges">';
    if ( $product->is_on_sale() ) {
        $reg = (float) $product->get_regular_price();
        $sale= (float) $product->get_sale_price();
        $pct = $reg > 0 ? round( ( $reg - $sale ) / $reg * 100 ) : 0;
        echo '<span class="woomax-badge woomax-badge--sale">-' . $pct . '%</span>';
    }
    if ( $product->is_featured() ) {
        echo '<span class="woomax-badge woomax-badge--hot">' . esc_html__( 'Top', 'woomax' ) . '</span>';
    }
    $created = strtotime( $product->get_date_created() );
    if ( $created && ( time() - $created ) < 30 * DAY_IN_SECONDS ) {
        echo '<span class="woomax-badge woomax-badge--new">' . esc_html__( 'Nouveau', 'woomax' ) . '</span>';
    }
    if ( ! $product->is_in_stock() ) {
        echo '<span class="woomax-badge woomax-badge--out">' . esc_html__( 'Épuisé', 'woomax' ) . '</span>';
    }
    echo '</div>';
}
