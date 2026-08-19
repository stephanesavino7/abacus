<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php do_action( 'woomax_before_header' ); ?>

<?php
$header_style  = get_theme_mod( 'woomax_header_style', 'default' );
$is_transparent = get_theme_mod( 'woomax_header_transparent', false ) && is_front_page();
$show_search   = get_theme_mod( 'woomax_show_search', true );
$show_wishlist = get_theme_mod( 'woomax_show_wishlist_icon', true );
$show_account  = get_theme_mod( 'woomax_show_account_icon', true );
$show_cart     = get_theme_mod( 'woomax_show_cart_icon', true );
$header_classes = 'woomax-header';
if ( $is_transparent ) $header_classes .= ' transparent-header';
if ( $header_style !== 'default' ) $header_classes .= ' woomax-header--' . $header_style;
?>

<header class="<?php echo esc_attr( $header_classes ); ?>" id="woomax-header" role="banner">
    <div class="woomax-container">
        <div class="woomax-header__inner">

            <?php if ( $header_style === 'centered' ) : ?>

                <!-- CENTERED LAYOUT -->
                <button class="woomax-mobile-toggle" id="woomax-mobile-toggle" aria-label="<?php esc_attr_e( 'Menu', 'woomax' ); ?>">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <nav class="woomax-nav" role="navigation" aria-label="<?php esc_attr_e( 'Navigation principale', 'woomax' ); ?>">
                    <?php
                    $menu_items = array_slice( wp_get_nav_menu_items( get_nav_menu_locations()['primary'] ?? 0 ), 0, 3 );
                    wp_nav_menu( [
                        'theme_location' => 'primary',
                        'menu_class'     => 'woomax-nav__list',
                        'container'      => false,
                        'walker'         => new WooMax_Nav_Walker(),
                        'depth'          => 3,
                        'fallback_cb'    => false,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    ] );
                    ?>
                </nav>
                <?php woomax_render_logo( 'header' ); ?>
                <div class="woomax-header-actions">
                    <?php if ( $show_search ) : ?>
                    <button class="woomax-header-action woomax-search-trigger" aria-label="<?php esc_attr_e( 'Rechercher', 'woomax' ); ?>">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <?php endif; ?>
                    <?php if ( $show_cart && class_exists( 'WooCommerce' ) ) : ?>
                    <button class="woomax-header-action woomax-cart-trigger" aria-label="<?php esc_attr_e( 'Panier', 'woomax' ); ?>">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="badge woomax-cart-count"><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></span>
                    </button>
                    <?php endif; ?>
                </div>

            <?php else : ?>

                <!-- DEFAULT LAYOUT -->
                <?php woomax_render_logo( 'header' ); ?>
                <nav class="woomax-nav" role="navigation" aria-label="<?php esc_attr_e( 'Navigation principale', 'woomax' ); ?>">
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'primary',
                        'menu_class'     => 'woomax-nav__list',
                        'container'      => false,
                        'walker'         => new WooMax_Nav_Walker(),
                        'depth'          => 3,
                        'fallback_cb'    => function() {
                            echo '<ul><li><a href="' . esc_url( home_url() ) . '">' . esc_html__( 'Accueil', 'woomax' ) . '</a></li>';
                            if ( class_exists('WooCommerce') ) echo '<li><a href="' . esc_url( wc_get_page_permalink('shop') ) . '">' . esc_html__( 'Boutique', 'woomax' ) . '</a></li>';
                            echo '<li><a href="' . esc_url( home_url('/contact') ) . '">' . esc_html__( 'Contact', 'woomax' ) . '</a></li></ul>';
                        },
                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    ] );
                    ?>
                </nav>
                <div class="woomax-header-actions">
                    <?php if ( $show_search ) : ?>
                    <button class="woomax-header-action woomax-search-trigger" aria-label="<?php esc_attr_e( 'Rechercher', 'woomax' ); ?>">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <?php endif; ?>
                    <?php if ( $show_wishlist ) : ?>
                    <a class="woomax-header-action" href="<?php echo esc_url( home_url( '/wishlist' ) ); ?>" aria-label="<?php esc_attr_e( 'Ma liste de souhaits', 'woomax' ); ?>">
                        <i class="fa-solid fa-heart"></i>
                        <span class="badge woomax-wishlist-count">0</span>
                    </a>
                    <?php endif; ?>
                    <?php if ( $show_account && class_exists( 'WooCommerce' ) ) : ?>
                    <a class="woomax-header-action" href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" aria-label="<?php esc_attr_e( 'Mon compte', 'woomax' ); ?>">
                        <i class="fa-solid fa-circle-user"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ( $show_cart && class_exists( 'WooCommerce' ) ) : ?>
                    <button class="woomax-header-action woomax-cart-trigger" aria-label="<?php esc_attr_e( 'Panier', 'woomax' ); ?>">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="badge woomax-cart-count"><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></span>
                    </button>
                    <?php endif; ?>
                    <button class="woomax-mobile-toggle" id="woomax-mobile-toggle" aria-label="<?php esc_attr_e( 'Menu mobile', 'woomax' ); ?>">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>

            <?php endif; ?>

        </div>
    </div>
</header>

<!-- Search overlay -->
<div class="woomax-search-overlay" id="woomax-search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Recherche', 'woomax' ); ?>">
    <div class="woomax-search-overlay__inner">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="woomax-search-overlay__form">
            <input type="search" name="s" class="woomax-search-overlay__input"
                placeholder="<?php esc_attr_e( 'Rechercher des produits…', 'woomax' ); ?>"
                value="<?php echo get_search_query(); ?>" autocomplete="off" autofocus>
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <input type="hidden" name="post_type" value="product">
            <?php endif; ?>
            <button type="submit" class="woomax-search-overlay__close" style="font-size:1.4rem;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
        <p class="woomax-search-overlay__hint"><?php esc_html_e( 'Appuyez sur Entrée pour rechercher ou Échap pour fermer.', 'woomax' ); ?></p>
    </div>
    <button class="woomax-search-overlay__close" id="woomax-search-close" style="position:absolute;top:24px;right:24px;" aria-label="<?php esc_attr_e( 'Fermer la recherche', 'woomax' ); ?>">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<!-- Mobile navigation -->
<div class="woomax-mobile-nav" id="woomax-mobile-nav" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu mobile', 'woomax' ); ?>">
    <div class="woomax-mobile-nav__overlay" id="woomax-mobile-overlay"></div>
    <div class="woomax-mobile-nav__panel">
        <div class="woomax-mobile-nav__close">
            <?php woomax_render_logo(); ?>
            <button id="woomax-mobile-close" aria-label="<?php esc_attr_e( 'Fermer', 'woomax' ); ?>">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <?php
        wp_nav_menu( [
            'theme_location' => 'primary',
            'container'      => false,
            'fallback_cb'    => function() {
                echo '<ul><li><a href="' . esc_url( home_url() ) . '">' . esc_html__( 'Accueil', 'woomax' ) . '</a></li></ul>';
            },
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        ] );
        ?>
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
        <div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--woomax-border-color);">
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" style="display:flex;align-items:center;gap:10px;padding:12px 0;font-weight:600;color:var(--woomax-text-color);">
                <i class="fa-solid fa-circle-user"></i> <?php esc_html_e( 'Mon compte', 'woomax' ); ?>
            </a>
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" style="display:flex;align-items:center;gap:10px;padding:12px 0;font-weight:600;color:var(--woomax-text-color);">
                <i class="fa-solid fa-bag-shopping"></i> <?php esc_html_e( 'Mon panier', 'woomax' ); ?>
                <span style="margin-left:auto;background:var(--woomax-primary);color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:12px;" class="woomax-cart-count"><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php do_action( 'woomax_after_header' ); ?>

<!-- Preloader -->
<?php if ( get_theme_mod( 'woomax_show_preloader', false ) ) : ?>
<div id="woomax-preloader" style="position:fixed;inset:0;background:<?php echo esc_attr( get_theme_mod( 'woomax_preloader_bg', '#ffffff' ) ); ?>;z-index:99999;display:flex;align-items:center;justify-content:center;">
    <div style="width:48px;height:48px;border:4px solid <?php echo esc_attr( get_theme_mod( 'woomax_border_color', '#e8e0d5' ) ); ?>;border-top-color:<?php echo esc_attr( get_theme_mod( 'woomax_preloader_color', '#8B5E3C' ) ); ?>;border-radius:50%;animation:woomax-spin 0.8s linear infinite;"></div>
</div>
<script>window.addEventListener('load',function(){var p=document.getElementById('woomax-preloader');if(p){p.style.opacity='0';p.style.transition='opacity 0.4s ease';setTimeout(function(){p.remove();},400);}});</script>
<?php endif; ?>

<main id="woomax-main" class="woomax-main" role="main">
