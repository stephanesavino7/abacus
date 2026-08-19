<?php
/**
 * Breadcrumb - WooMax override
 *
 * @package WooMax
 */

defined( 'ABSPATH' ) || exit;

if ( ! woomax_get_option( 'show_breadcrumbs', true ) ) {
    return;
}
?>

<nav class="woocommerce-breadcrumb breadcrumb-nav" aria-label="<?php esc_attr_e( 'Fil d\'Ariane', 'woomax' ); ?>">
    <?php
    $breadcrumbs = new WC_Breadcrumb();
    $crumbs = $breadcrumbs->generate();

    if ( ! empty( $crumbs ) ) {
        $total = count( $crumbs );
        $i = 0;
        foreach ( $crumbs as $key => $crumb ) {
            $i++;
            echo '<span>';
            if ( isset( $crumb[1] ) && $i < $total ) {
                echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
            } else {
                echo esc_html( $crumb[0] );
            }
            echo '</span>';
            if ( $i < $total ) {
                echo '<span class="breadcrumb-sep" aria-hidden="true">›</span>';
            }
        }
    }
    ?>
</nav>
