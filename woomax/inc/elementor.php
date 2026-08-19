<?php
/**
 * WooMax – Compatibilité Elementor & Elementor Pro (Theme Builder)
 *
 * Ce fichier rend le thème pleinement compatible avec Elementor :
 *  - Déclaration du support Elementor + largeur de contenu
 *  - Enregistrement des emplacements du Theme Builder (header, footer, single, archive)
 *  - Modèle de page "Elementor pleine largeur" (sans header/footer/sidebar)
 *  - Réglages de la boutique compatibles Elementor
 *
 * @package WooMax
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Vérifie si Elementor est actif.
 */
function woomax_is_elementor_active() {
    return did_action( 'elementor/loaded' ) || defined( 'ELEMENTOR_VERSION' );
}

/**
 * Vérifie si Elementor Pro est actif.
 */
function woomax_is_elementor_pro_active() {
    return defined( 'ELEMENTOR_PRO_VERSION' );
}

/**
 * Déclare le support du thème pour Elementor.
 */
add_action( 'after_setup_theme', function() {
    // Largeur de contenu par défaut d'Elementor
    add_theme_support( 'elementor', [
        'content_width' => (int) get_theme_mod( 'woomax_container_width', 1280 ),
    ] );

    // Compatibilité avec le système de couleurs/typo global d'Elementor
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
}, 20 );

/**
 * Enregistre les emplacements du Theme Builder d'Elementor Pro.
 * Cela permet aux utilisateurs de concevoir header/footer/single/archive
 * entièrement dans Elementor, avec repli sur les templates du thème.
 */
add_action( 'elementor/theme/register_locations', function( $elementor_theme_manager ) {
    $elementor_theme_manager->register_all_core_location();
} );

/**
 * Affiche un emplacement Elementor s'il existe, sinon exécute le repli du thème.
 *
 * @param string   $location Nom de l'emplacement (header, footer, single, archive…).
 * @param callable $fallback Fonction de repli à exécuter si aucun template Elementor.
 * @return bool True si un template Elementor a été affiché.
 */
function woomax_elementor_location( $location, $fallback = null ) {
    if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location ) ) {
        return true;
    }
    if ( is_callable( $fallback ) ) {
        call_user_func( $fallback );
    }
    return false;
}

/**
 * Modèle de page « Elementor – Pleine largeur (WooMax) ».
 * Charge un canevas sans en-tête/pied de page du thème pour laisser Elementor gérer.
 */
add_filter( 'theme_page_templates', function( $templates ) {
    $templates['template-elementor-full.php'] = __( 'Elementor – Pleine largeur (WooMax)', 'woomax' );
    $templates['template-elementor-canvas.php'] = __( 'Elementor – Canevas (WooMax)', 'woomax' );
    return $templates;
} );

/**
 * Résout le fichier de template pour nos modèles Elementor virtuels.
 */
add_filter( 'template_include', function( $template ) {
    if ( is_singular() ) {
        $page_template = get_page_template_slug( get_queried_object_id() );

        if ( 'template-elementor-full.php' === $page_template ) {
            $custom = WOOMAX_DIR . '/page-templates/template-elementor-full.php';
            if ( file_exists( $custom ) ) return $custom;
        }
        if ( 'template-elementor-canvas.php' === $page_template ) {
            $custom = WOOMAX_DIR . '/page-templates/template-elementor-canvas.php';
            if ( file_exists( $custom ) ) return $custom;
        }
    }
    return $template;
}, 99 );

/**
 * Ajoute des classes utiles au body quand Elementor est actif.
 */
add_filter( 'body_class', function( $classes ) {
    if ( woomax_is_elementor_active() ) {
        $classes[] = 'woomax-elementor-active';
    }
    if ( woomax_is_elementor_pro_active() ) {
        $classes[] = 'woomax-elementor-pro-active';
    }
    return $classes;
} );

/**
 * Enregistre une catégorie de widgets Elementor dédiée au thème
 * (utile si l'on ajoute des widgets personnalisés plus tard).
 */
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
    $elements_manager->add_category(
        'woomax',
        [
            'title' => __( 'WooMax', 'woomax' ),
            'icon'  => 'fa fa-plug',
        ]
    );
} );

/**
 * Synchronise la largeur de contenu Elementor avec le réglage du Customizer.
 */
add_action( 'elementor/frontend/after_enqueue_styles', function() {
    $width = (int) get_theme_mod( 'woomax_container_width', 1280 );
    $css = ".elementor-section.elementor-section-boxed > .elementor-container{max-width:{$width}px;}";
    wp_add_inline_style( 'elementor-frontend', $css );
} );

/**
 * Astuce admin : si Elementor n'est pas installé, on n'affiche rien de bloquant.
 * La compatibilité est passive et sans effet tant qu'Elementor est absent.
 */
