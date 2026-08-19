<?php
/**
 * Cart Page - WooMax theme override
 *
 * @package WooMax
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woomax-cart-page">

    <?php do_action( 'woocommerce_before_cart' ); ?>

    <div class="cart-page-header">
        <div class="container">
            <h1 class="cart-page-title"><?php esc_html_e( 'Mon Panier', 'woomax' ); ?></h1>
            <?php if ( ! WC()->cart->is_empty() ) : ?>
                <p class="cart-item-count">
                    <?php
                    printf(
                        esc_html( _n( '%d article', '%d articles', WC()->cart->get_cart_contents_count(), 'woomax' ) ),
                        WC()->cart->get_cart_contents_count()
                    );
                    ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <div class="cart-layout">
                <div class="cart-items-column">
                    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="product-thumbnail">&nbsp;</th>
                                <th class="product-name"><?php esc_html_e( 'Produit', 'woomax' ); ?></th>
                                <th class="product-price"><?php esc_html_e( 'Prix', 'woomax' ); ?></th>
                                <th class="product-quantity"><?php esc_html_e( 'Quantité', 'woomax' ); ?></th>
                                <th class="product-subtotal"><?php esc_html_e( 'Sous-total', 'woomax' ); ?></th>
                                <th class="product-remove">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                            <?php
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                                $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                    ?>
                                    <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                        <td class="product-thumbnail">
                                            <?php
                                            $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                                            if ( ! $product_permalink ) {
                                                echo $thumbnail;
                                            } else {
                                                printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                            }
                                            ?>
                                        </td>

                                        <td class="product-name" data-title="<?php esc_attr_e( 'Produit', 'woomax' ); ?>">
                                            <?php
                                            if ( ! $product_permalink ) {
                                                echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                            } else {
                                                echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                            }
                                            do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
                                            echo wc_get_formatted_cart_item_data( $cart_item );
                                            echo apply_filters( 'woocommerce_cart_item_backorder_notification', null, $product_id );
                                            ?>
                                        </td>

                                        <td class="product-price" data-title="<?php esc_attr_e( 'Prix', 'woomax' ); ?>">
                                            <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                                        </td>

                                        <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantité', 'woomax' ); ?>">
                                            <?php
                                            if ( $_product->is_sold_individually() ) {
                                                $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                            } else {
                                                $product_quantity = woocommerce_quantity_input(
                                                    array(
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => $cart_item['quantity'],
                                                        'max_value'    => $_product->get_max_purchase_quantity(),
                                                        'min_value'    => '0',
                                                        'product_name' => $_product->get_name(),
                                                    ),
                                                    $_product,
                                                    false
                                                );
                                            }
                                            echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                                            ?>
                                        </td>

                                        <td class="product-subtotal" data-title="<?php esc_attr_e( 'Sous-total', 'woomax' ); ?>">
                                            <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                                        </td>

                                        <td class="product-remove">
                                            <?php
                                            echo apply_filters(
                                                'woocommerce_cart_item_remove_link',
                                                sprintf(
                                                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                                    esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                    /* translators: %s is the product name */
                                                    esc_attr( sprintf( __( 'Supprimer %s du panier', 'woomax' ), $_product->get_name() ) ),
                                                    esc_attr( $product_id ),
                                                    esc_attr( $_product->get_sku() )
                                                ),
                                                $cart_item_key
                                            );
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                            <?php do_action( 'woocommerce_cart_contents' ); ?>

                            <tr>
                                <td colspan="6" class="actions">
                                    <?php if ( wc_coupons_enabled() ) { ?>
                                        <div class="coupon">
                                            <label for="coupon_code"><?php esc_html_e( 'Code promo', 'woomax' ); ?></label>
                                            <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Code promo', 'woomax' ); ?>" />
                                            <button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Appliquer', 'woomax' ); ?>"><?php esc_html_e( 'Appliquer', 'woomax' ); ?></button>
                                            <?php do_action( 'woocommerce_cart_coupon' ); ?>
                                        </div>
                                    <?php } ?>

                                    <button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="update_cart" value="<?php esc_attr_e( 'Mettre à jour le panier', 'woomax' ); ?>"><?php esc_html_e( 'Mettre à jour', 'woomax' ); ?></button>

                                    <?php do_action( 'woocommerce_cart_actions' ); ?>
                                    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                                </td>
                            </tr>

                            <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-totals-column">
                    <?php
                    /**
                     * Hook: woocommerce_cart_collaterals
                     */
                    do_action( 'woocommerce_cart_collaterals' );
                    ?>
                </div>
            </div>

            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </form>

        <?php do_action( 'woocommerce_after_cart' ); ?>
    </div>
</div>
