<?php
/**
 * Template Name: Elementor – Canevas (WooMax)
 *
 * Canevas vierge : aucun en-tête, pied de page ou sidebar du thème.
 * Elementor prend en charge la totalité de la page (landing pages, etc.).
 *
 * @package WooMax
 */

defined( 'ABSPATH' ) || exit;

// En-tête minimal (uniquement wp_head pour charger les styles/scripts).
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'woomax-elementor-canvas' ); ?>>
<?php wp_body_open(); ?>

<div class="woomax-canvas-content">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
