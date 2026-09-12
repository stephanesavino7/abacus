<?php
/**
 * Template Name: Elementor – Pleine largeur (WooMax)
 *
 * En-tête et pied de page du thème conservés, contenu en pleine largeur
 * (sans sidebar ni conteneur limité) — idéal pour construire avec Elementor.
 *
 * @package WooMax
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main woomax-elementor-full">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php
get_footer();
