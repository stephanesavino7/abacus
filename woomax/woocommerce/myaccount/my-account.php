<?php
/**
 * My Account page - WooMax override
 *
 * @package WooMax
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="woomax-my-account">

    <?php
    /**
     * My Account navigation.
     */
    do_action( 'woocommerce_account_navigation' );
    ?>

    <div class="woocommerce-MyAccount-content">
        <?php
        /**
         * My Account content.
         */
        do_action( 'woocommerce_account_content' );
        ?>
    </div>

</div>
