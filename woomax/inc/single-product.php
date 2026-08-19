<?php
/**
 * WooMax – Personnalisation de la fiche produit (single product)
 *
 * Permet, via le Customizer (Boutique ▸ Infos fiche produit) :
 *  - d'afficher / masquer chaque information du résumé produit
 *    (titre, notation, prix, description, formulaire d'achat, référence,
 *     catégories, étiquettes, disponibilité, partage) ;
 *  - de réordonner les éléments principaux ;
 *  - d'ajouter des badges de réassurance et un bloc d'information additionnel
 *    (livraison, garantie…).
 *
 * Toute la logique est protégée : elle ne s'exécute que si WooCommerce est actif.
 *
 * @package WooMax
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// N'agit que si WooCommerce est disponible.
if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}

/**
 * Reconstruit le résumé de la fiche produit selon les réglages du Customizer.
 * Exécuté sur `wp`, avant le rendu du template, uniquement sur une page produit.
 */
function woomax_customize_single_summary() {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) {
        return;
    }

    $hook = 'woocommerce_single_product_summary';

    // 1. On retire les éléments par défaut de WooCommerce.
    remove_action( $hook, 'woocommerce_template_single_title',       5 );
    remove_action( $hook, 'woocommerce_template_single_rating',      10 );
    remove_action( $hook, 'woocommerce_template_single_price',       10 );
    remove_action( $hook, 'woocommerce_template_single_excerpt',     20 );
    remove_action( $hook, 'woocommerce_template_single_add_to_cart', 30 );
    remove_action( $hook, 'woocommerce_template_single_meta',        40 );
    remove_action( $hook, 'woocommerce_template_single_sharing',     50 );

    // 2. Table de correspondance : clé => [ callback, réglage d'affichage ].
    $map = [
        'title'   => [ 'woocommerce_template_single_title',       'woomax_sp_show_title' ],
        'rating'  => [ 'woocommerce_template_single_rating',      'woomax_sp_show_rating' ],
        'price'   => [ 'woocommerce_template_single_price',       'woomax_sp_show_price' ],
        'excerpt' => [ 'woocommerce_template_single_excerpt',     'woomax_sp_show_excerpt' ],
        'cart'    => [ 'woocommerce_template_single_add_to_cart', 'woomax_sp_show_add_to_cart' ],
        'meta'    => [ 'woomax_single_product_meta',              null ], // méta personnalisée
    ];

    // 3. Lecture de l'ordre défini par l'utilisateur.
    $order_raw = get_theme_mod( 'woomax_sp_order', 'title,rating,price,excerpt,cart,meta' );
    $order     = array_filter( array_map( 'trim', explode( ',', (string) $order_raw ) ) );
    if ( empty( $order ) ) {
        $order = array_keys( $map );
    }
    // On complète avec les clés manquantes (au cas où l'utilisateur en aurait oublié).
    foreach ( array_keys( $map ) as $key ) {
        if ( ! in_array( $key, $order, true ) ) {
            $order[] = $key;
        }
    }

    // 4. Ré-enregistrement dans l'ordre voulu, en respectant les cases à cocher.
    $priority = 10;
    foreach ( $order as $key ) {
        if ( ! isset( $map[ $key ] ) ) {
            continue;
        }
        list( $callback, $mod ) = $map[ $key ];
        if ( $mod && ! get_theme_mod( $mod, true ) ) {
            continue;
        }
        add_action( $hook, $callback, $priority );
        $priority += 10;
    }

    // 5. Masquer la disponibilité (stock) si désactivée.
    if ( ! get_theme_mod( 'woomax_sp_show_stock', true ) ) {
        add_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
    }

    // 6. Boutons de partage (optionnels), après les éléments principaux.
    if ( get_theme_mod( 'woomax_sp_show_sharing', false ) ) {
        add_action( $hook, 'woocommerce_template_single_sharing', 180 );
    }

    // 7. Badges de réassurance et bloc d'information additionnel.
    add_action( $hook, 'woomax_single_product_trust_badges', 190 );
    add_action( $hook, 'woomax_single_product_extra_info',   200 );
}
add_action( 'wp', 'woomax_customize_single_summary' );

/**
 * Méta produit personnalisée : référence (SKU), catégories, étiquettes.
 * Chaque élément est contrôlé indépendamment via le Customizer.
 */
function woomax_single_product_meta() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $show_sku = get_theme_mod( 'woomax_sp_show_sku', true );
    $show_cat = get_theme_mod( 'woomax_sp_show_categories', true );
    $show_tag = get_theme_mod( 'woomax_sp_show_tags', false );

    if ( ! $show_sku && ! $show_cat && ! $show_tag ) {
        return;
    }

    echo '<div class="product_meta woomax-product-meta">';

    if ( $show_sku && wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) {
        echo '<span class="sku_wrapper">' . esc_html__( 'Référence :', 'woomax' ) . ' <span class="sku">'
            . esc_html( $product->get_sku() ? $product->get_sku() : __( 'N/A', 'woomax' ) )
            . '</span></span>';
    }

    if ( $show_cat ) {
        echo wc_get_product_category_list(
            $product->get_id(),
            ', ',
            '<span class="posted_in">' . esc_html__( 'Catégories :', 'woomax' ) . ' ',
            '</span>'
        );
    }

    if ( $show_tag ) {
        echo wc_get_product_tag_list(
            $product->get_id(),
            ', ',
            '<span class="tagged_as">' . esc_html__( 'Étiquettes :', 'woomax' ) . ' ',
            '</span>'
        );
    }

    echo '</div>';
}

/**
 * Affiche les badges de réassurance configurés dans le Customizer.
 */
function woomax_single_product_trust_badges() {
    if ( ! get_theme_mod( 'woomax_sp_show_trust', true ) ) {
        return;
    }

    $raw   = (string) get_theme_mod( 'woomax_sp_trust_text', '' );
    $lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw ) ) );
    if ( empty( $lines ) ) {
        return;
    }

    echo '<ul class="woomax-trust-badges">';
    foreach ( $lines as $line ) {
        $parts = array_map( 'trim', explode( '|', $line, 2 ) );
        if ( count( $parts ) > 1 ) {
            $icon = $parts[0];
            $text = $parts[1];
        } else {
            $icon = '';
            $text = $parts[0];
        }
        echo '<li class="woomax-trust-badge">';
        if ( '' !== $icon ) {
            echo '<span class="woomax-trust-badge__icon">' . esc_html( $icon ) . '</span>';
        }
        echo '<span class="woomax-trust-badge__text">' . esc_html( $text ) . '</span>';
        echo '</li>';
    }
    echo '</ul>';
}

/**
 * Affiche le bloc d'information additionnel (livraison, garantie…).
 */
function woomax_single_product_extra_info() {
    $title = (string) get_theme_mod( 'woomax_sp_extra_title', '' );
    $text  = (string) get_theme_mod( 'woomax_sp_extra_text', '' );

    if ( '' === trim( $text ) ) {
        return;
    }

    echo '<div class="woomax-single-extra">';
    if ( '' !== trim( $title ) ) {
        echo '<h3 class="woomax-single-extra__title">' . esc_html( $title ) . '</h3>';
    }
    echo '<div class="woomax-single-extra__text">' . wp_kses_post( wpautop( $text ) ) . '</div>';
    echo '</div>';
}
