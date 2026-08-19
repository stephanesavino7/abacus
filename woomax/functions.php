<?php
/**
 * WooMax Theme Functions
 *
 * @package WooMax
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─── Constants ────────────────────────────────────────────────────────────────
define( 'WOOMAX_VERSION',   '1.0.0' );
define( 'WOOMAX_DIR',       get_template_directory() );
define( 'WOOMAX_URI',       get_template_directory_uri() );
define( 'WOOMAX_ASSETS',    WOOMAX_URI . '/assets' );

// ─── Required Files ───────────────────────────────────────────────────────────
require_once WOOMAX_DIR . '/inc/helpers.php';
require_once WOOMAX_DIR . '/inc/customizer.php';
require_once WOOMAX_DIR . '/inc/widgets.php';
if ( class_exists( 'WooCommerce' ) ) {
    require_once WOOMAX_DIR . '/inc/woocommerce.php';
}

// ─── Theme Setup ──────────────────────────────────────────────────────────────
function woomax_setup() {
    load_theme_textdomain( 'woomax', WOOMAX_DIR . '/languages' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form','comment-form','comment-list','gallery','caption','style','script' ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'woocommerce', [
        'thumbnail_image_width' => 600,
        'single_image_width'    => 900,
        'product_grid'          => [
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 20,
            'default_columns' => 4,
            'min_columns'     => 1,
            'max_columns'     => 6,
        ],
    ] );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'custom-logo', [
        'height'      => 120,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    // Custom image sizes
    add_image_size( 'woomax-thumbnail',  600, 600, true );
    add_image_size( 'woomax-medium',     900, 600, true );
    add_image_size( 'woomax-wide',      1440, 600, true );
    add_image_size( 'woomax-hero',      1920, 900, true );

    // Menus
    register_nav_menus( [
        'primary'  => __( 'Menu Principal',     'woomax' ),
        'footer_1' => __( 'Footer – Colonne 1', 'woomax' ),
        'footer_2' => __( 'Footer – Colonne 2', 'woomax' ),
        'footer_3' => __( 'Footer – Colonne 3', 'woomax' ),
    ] );

    $GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'woomax_setup' );

// ─── Enqueue Scripts & Styles ─────────────────────────────────────────────────
function woomax_scripts() {
    // Google Fonts
    $heading_font = get_theme_mod( 'woomax_heading_font', 'Playfair Display' );
    $body_font    = get_theme_mod( 'woomax_body_font',    'Lato' );
    $fonts = array_unique( [ $heading_font, $body_font, 'Lato' ] );
    $font_families = implode( '|', array_map( fn($f) => str_replace( ' ', '+', $f ) . ':300,400,600,700,800', $fonts ) );
    wp_enqueue_style( 'woomax-google-fonts',
        "https://fonts.googleapis.com/css2?family={$font_families}&display=swap",
        [], null
    );

    // Icons
    wp_enqueue_style( 'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        [], '6.5.0'
    );

    // Main stylesheet
    wp_enqueue_style( 'woomax-style', get_stylesheet_uri(), [], WOOMAX_VERSION );
    wp_enqueue_style( 'woomax-pages', WOOMAX_ASSETS . '/css/pages.css', ['woomax-style'], WOOMAX_VERSION );

    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_style( 'woomax-woocommerce', WOOMAX_ASSETS . '/css/woocommerce.css', ['woomax-style'], WOOMAX_VERSION );
    }

    // Inline CSS variables from Customizer
    wp_add_inline_style( 'woomax-style', woomax_generate_css_variables() );

    // Main JS
    wp_enqueue_script( 'woomax-main', WOOMAX_ASSETS . '/js/main.js', ['jquery'], WOOMAX_VERSION, true );

    // WooCommerce-dependent data (only when WooCommerce is active to avoid fatal errors)
    $woomax_cart_url     = function_exists( 'wc_get_cart_url' )               ? wc_get_cart_url()               : '';
    $woomax_checkout_url = function_exists( 'wc_get_checkout_url' )           ? wc_get_checkout_url()           : '';
    $woomax_currency     = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '';

    wp_localize_script( 'woomax-main', 'WooMaxData', [
        'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
        'nonce'      => wp_create_nonce( 'woomax-nonce' ),
        'cartUrl'    => $woomax_cart_url,
        'checkoutUrl'=> $woomax_checkout_url,
        'currency'   => $woomax_currency,
        'strings'    => [
            'addedToCart'   => __( 'Ajouté au panier !', 'woomax' ),
            'addingToCart'  => __( 'Ajout...', 'woomax' ),
            'addToWishlist' => __( 'Ajouté à la liste de souhaits', 'woomax' ),
            'error'         => __( 'Une erreur est survenue.', 'woomax' ),
            'loading'       => __( 'Chargement...', 'woomax' ),
        ],
    ] );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'woomax_scripts' );

// ─── Customizer Preview JS ────────────────────────────────────────────────────
function woomax_customizer_preview_js() {
    wp_enqueue_script( 'woomax-customizer-preview',
        WOOMAX_ASSETS . '/js/customizer-preview.js',
        ['customize-preview'], WOOMAX_VERSION, true
    );
}
add_action( 'customize_preview_init', 'woomax_customizer_preview_js' );

// ─── CSS Variables Generator ──────────────────────────────────────────────────
function woomax_generate_css_variables() {
    $mods = [
        '--woomax-primary'           => get_theme_mod( 'woomax_primary_color',      '#8B5E3C' ),
        '--woomax-primary-dark'      => woomax_darken_color( get_theme_mod( 'woomax_primary_color', '#8B5E3C' ), 20 ),
        '--woomax-primary-light'     => woomax_lighten_color( get_theme_mod( 'woomax_primary_color', '#8B5E3C' ), 30 ),
        '--woomax-secondary'         => get_theme_mod( 'woomax_secondary_color',    '#4A7C59' ),
        '--woomax-accent'            => get_theme_mod( 'woomax_accent_color',        '#D4A853' ),
        '--woomax-text-color'        => get_theme_mod( 'woomax_text_color',          '#2c2c2c' ),
        '--woomax-text-muted'        => get_theme_mod( 'woomax_text_muted_color',    '#777777' ),
        '--woomax-bg-color'          => get_theme_mod( 'woomax_bg_color',            '#ffffff' ),
        '--woomax-bg-secondary'      => get_theme_mod( 'woomax_bg_secondary_color',  '#f9f6f1' ),
        '--woomax-bg-dark'           => get_theme_mod( 'woomax_bg_dark_color',       '#1e1b18' ),
        '--woomax-border-color'      => get_theme_mod( 'woomax_border_color',        '#e8e0d5' ),
        '--woomax-heading-font'      => '"' . get_theme_mod( 'woomax_heading_font',  'Playfair Display' ) . '", serif',
        '--woomax-body-font'         => '"' . get_theme_mod( 'woomax_body_font',     'Lato' ) . '", sans-serif',
        '--woomax-body-font-size'    => get_theme_mod( 'woomax_body_font_size',      '16' ) . 'px',
        '--woomax-heading-weight'    => get_theme_mod( 'woomax_heading_weight',      '700' ),
        '--woomax-body-line-height'  => get_theme_mod( 'woomax_body_line_height',    '1.7' ),
        '--woomax-container-width'   => get_theme_mod( 'woomax_container_width',     '1280' ) . 'px',
        '--woomax-border-radius'     => get_theme_mod( 'woomax_border_radius',       '6' ) . 'px',
        '--woomax-header-bg'         => get_theme_mod( 'woomax_header_bg',           '#ffffff' ),
        '--woomax-header-text'       => get_theme_mod( 'woomax_header_text_color',   '#2c2c2c' ),
        '--woomax-header-height'     => get_theme_mod( 'woomax_header_height',       '80' ) . 'px',
        '--woomax-footer-bg'         => get_theme_mod( 'woomax_footer_bg',           '#1e1b18' ),
        '--woomax-footer-text'       => get_theme_mod( 'woomax_footer_text_color',   '#c8bfb0' ),
        '--woomax-footer-heading'    => get_theme_mod( 'woomax_footer_heading_color','#ffffff' ),
        '--woomax-btn-radius'        => get_theme_mod( 'woomax_btn_border_radius',   '4' ) . 'px',
        '--woomax-section-padding'   => get_theme_mod( 'woomax_section_padding',     '80' ) . 'px',
    ];

    // Button text-transform
    $btn_style = get_theme_mod( 'woomax_btn_style', 'uppercase' );
    if ( $btn_style === 'capitalize' ) {
        $mods['--woomax-btn-text-transform'] = 'capitalize';
        $mods['--woomax-btn-letter-spacing'] = '0.02em';
    } elseif ( $btn_style === 'normal' ) {
        $mods['--woomax-btn-text-transform'] = 'none';
        $mods['--woomax-btn-letter-spacing'] = '0';
    }

    $css = ':root {' . "\n";
    foreach ( $mods as $var => $value ) {
        $css .= "  {$var}: {$value};\n";
    }
    $css .= "}\n";

    // Custom CSS from Customizer
    $custom_css = get_theme_mod( 'woomax_custom_css', '' );
    if ( $custom_css ) {
        $css .= "\n/* Custom CSS */\n" . wp_strip_all_tags( $custom_css );
    }

    return $css;
}

// ─── Widgets ──────────────────────────────────────────────────────────────────
function woomax_register_sidebars() {
    $args_base = [
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ];
    register_sidebar( array_merge( $args_base, [
        'name'        => __( 'Sidebar Principal', 'woomax' ),
        'id'          => 'sidebar-1',
        'description' => __( 'Widgets de la sidebar principale.', 'woomax' ),
    ] ) );
    register_sidebar( array_merge( $args_base, [
        'name'        => __( 'Sidebar Boutique', 'woomax' ),
        'id'          => 'sidebar-shop',
        'description' => __( 'Widgets de filtres pour la boutique.', 'woomax' ),
    ] ) );
    for ( $i = 1; $i <= 4; $i++ ) {
        register_sidebar( array_merge( $args_base, [
            'name'        => sprintf( __( 'Footer – Zone %d', 'woomax' ), $i ),
            'id'          => "footer-{$i}",
            'description' => sprintf( __( 'Zone de widgets du footer, colonne %d.', 'woomax' ), $i ),
        ] ) );
    }
    register_sidebar( array_merge( $args_base, [
        'name'        => __( 'Barre de notification', 'woomax' ),
        'id'          => 'top-bar',
        'description' => __( 'Barre d\'information en haut du site.', 'woomax' ),
    ] ) );
}
add_action( 'widgets_init', 'woomax_register_sidebars' );

// ─── AJAX: Mini-cart fragment ─────────────────────────────────────────────────
function woomax_mini_cart() {
    check_ajax_referer( 'woomax-nonce', 'nonce' );
    $count = WC()->cart->get_cart_contents_count();
    $total = WC()->cart->get_cart_total();
    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();
    wp_send_json_success( [ 'count' => $count, 'total' => $total, 'html' => $mini_cart ] );
}
add_action( 'wp_ajax_woomax_mini_cart',        'woomax_mini_cart' );
add_action( 'wp_ajax_nopriv_woomax_mini_cart', 'woomax_mini_cart' );

// ─── AJAX: Newsletter ─────────────────────────────────────────────────────────
function woomax_newsletter_subscribe() {
    check_ajax_referer( 'woomax-nonce', 'nonce' );
    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => __( 'Adresse e-mail invalide.', 'woomax' ) ] );
    }
    // Here you would integrate your newsletter provider
    do_action( 'woomax_newsletter_subscribe', $email );
    wp_send_json_success( [ 'message' => __( 'Inscription réussie ! Merci.', 'woomax' ) ] );
}
add_action( 'wp_ajax_woomax_newsletter',        'woomax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_woomax_newsletter', 'woomax_newsletter_subscribe' );

// ─── AJAX: Wishlist ───────────────────────────────────────────────────────────
function woomax_toggle_wishlist() {
    check_ajax_referer( 'woomax-nonce', 'nonce' );
    $product_id = absint( $_POST['product_id'] ?? 0 );
    if ( ! $product_id ) wp_send_json_error();
    $wishlist = (array) ( $_COOKIE['woomax_wishlist'] ? json_decode( stripslashes( $_COOKIE['woomax_wishlist'] ), true ) : [] );
    $added = false;
    if ( in_array( $product_id, $wishlist ) ) {
        $wishlist = array_values( array_diff( $wishlist, [ $product_id ] ) );
    } else {
        $wishlist[] = $product_id;
        $added = true;
    }
    setcookie( 'woomax_wishlist', json_encode( $wishlist ), time() + 30 * DAY_IN_SECONDS, '/' );
    wp_send_json_success( [ 'added' => $added, 'count' => count( $wishlist ) ] );
}
add_action( 'wp_ajax_woomax_wishlist',        'woomax_toggle_wishlist' );
add_action( 'wp_ajax_nopriv_woomax_wishlist', 'woomax_toggle_wishlist' );

// ─── Excerpt length ───────────────────────────────────────────────────────────
add_filter( 'excerpt_length', fn() => 25 );
add_filter( 'excerpt_more',   fn() => '…' );

// ─── Body classes ─────────────────────────────────────────────────────────────
function woomax_body_classes( $classes ) {
    $classes[] = 'woomax-theme';
    $header_style = get_theme_mod( 'woomax_header_style', 'default' );
    $classes[] = 'header-style-' . $header_style;
    if ( is_singular() ) $classes[] = 'singular';
    if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
        $classes[] = 'woocommerce-active';
    }
    return $classes;
}
add_filter( 'body_class', 'woomax_body_classes' );

// ─── Menus walker ─────────────────────────────────────────────────────────────
class WooMax_Nav_Walker extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = str_repeat( "\t", $depth );
        $classes = empty( $item->classes ) ? [] : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $class_names = implode( ' ', array_filter( apply_filters( 'nav_menu_css_class', $classes, $item, $args ) ) );
        $has_children = in_array( 'menu-item-has-children', $classes );
        $output .= $indent . '<li class="' . esc_attr( $class_names ) . '">';
        $atts = [];
        $atts['href']   = ! empty( $item->url ) ? $item->url : '#';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( $value ) $attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
        }
        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
        $output .= '<a' . $attributes . '>';
        $output .= esc_html( $title );
        if ( $has_children ) {
            $output .= ' <i class="fa-solid fa-chevron-down woomax-nav-arrow" style="font-size:10px;opacity:0.7;"></i>';
        }
        $output .= '</a>';
    }
}

// ─── Helper: hex color manipulation ──────────────────────────────────────────
function woomax_darken_color( $hex, $percent ) {
    $hex = ltrim( $hex, '#' );
    if ( strlen( $hex ) === 3 ) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    $r = max( 0, hexdec( substr( $hex, 0, 2 ) ) - round( 255 * $percent / 100 ) );
    $g = max( 0, hexdec( substr( $hex, 2, 2 ) ) - round( 255 * $percent / 100 ) );
    $b = max( 0, hexdec( substr( $hex, 4, 2 ) ) - round( 255 * $percent / 100 ) );
    return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
}
function woomax_lighten_color( $hex, $percent ) {
    $hex = ltrim( $hex, '#' );
    if ( strlen( $hex ) === 3 ) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    $r = min( 255, hexdec( substr( $hex, 0, 2 ) ) + round( 255 * $percent / 100 ) );
    $g = min( 255, hexdec( substr( $hex, 2, 2 ) ) + round( 255 * $percent / 100 ) );
    $b = min( 255, hexdec( substr( $hex, 4, 2 ) ) + round( 255 * $percent / 100 ) );
    return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
}

// ─── Page title ───────────────────────────────────────────────────────────────
function woomax_page_title() {
    if ( is_home() && ! is_front_page() ) {
        return get_the_title( get_option( 'page_for_posts' ) );
    } elseif ( is_archive() ) {
        return get_the_archive_title();
    } elseif ( is_search() ) {
        return sprintf( __( 'Résultats pour : %s', 'woomax' ), get_search_query() );
    } elseif ( is_404() ) {
        return __( 'Page introuvable', 'woomax' );
    } else {
        return get_the_title();
    }
}

// ─── Breadcrumbs ──────────────────────────────────────────────────────────────
function woomax_breadcrumbs() {
    $home = __( 'Accueil', 'woomax' );
    $sep  = '<span class="woomax-breadcrumb__sep">›</span>';
    echo '<nav class="woomax-breadcrumb" aria-label="Fil d\'Ariane">';
    echo '<a href="' . esc_url( home_url() ) . '">' . esc_html( $home ) . '</a>';
    echo $sep;
    if ( is_woocommerce() ) {
        woocommerce_breadcrumb( [ 'delimiter' => $sep, 'wrap_before' => '', 'wrap_after' => '', 'before' => '', 'after' => '' ] );
    } elseif ( is_single() ) {
        $cats = get_the_category();
        if ( $cats ) { echo '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>' . $sep; }
        echo '<span>' . get_the_title() . '</span>';
    } elseif ( is_page() ) {
        echo '<span>' . get_the_title() . '</span>';
    } elseif ( is_archive() ) {
        echo '<span>' . get_the_archive_title() . '</span>';
    } elseif ( is_search() ) {
        echo '<span>' . sprintf( __( 'Recherche : %s', 'woomax' ), get_search_query() ) . '</span>';
    } elseif ( is_404() ) {
        echo '<span>404</span>';
    }
    echo '</nav>';
}

// ─── Top bar ──────────────────────────────────────────────────────────────────
function woomax_render_top_bar() {
    if ( ! get_theme_mod( 'woomax_show_top_bar', false ) ) return;
    $bg    = get_theme_mod( 'woomax_top_bar_bg',   '#1e1b18' );
    $color = get_theme_mod( 'woomax_top_bar_color','#ffffff' );
    $text  = get_theme_mod( 'woomax_top_bar_text', __( '🚚 Livraison gratuite dès 50€ d\'achat — Code promo : BIENVENUE10', 'woomax' ) );
    echo '<div class="woomax-top-bar" style="background:' . esc_attr( $bg ) . ';color:' . esc_attr( $color ) . ';padding:10px 0;text-align:center;font-size:13px;font-weight:500;">';
    echo '<div class="woomax-container">' . wp_kses_post( $text ) . '</div>';
    echo '</div>';
}
add_action( 'woomax_before_header', 'woomax_render_top_bar' );

// ─── Favicon ──────────────────────────────────────────────────────────────────
add_action( 'wp_head', function() {
    if ( has_site_icon() ) return;
    echo '<link rel="icon" href="' . esc_url( WOOMAX_URI . '/assets/images/favicon.svg' ) . '" />';
} );

// ─── WooCommerce: declare HPOS compatibility ──────────────────────────────────
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );
