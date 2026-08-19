</main><!-- #woomax-main -->

<?php do_action( 'woomax_before_footer' ); ?>

<footer class="woomax-footer" role="contentinfo">
    <div class="woomax-footer__main">
        <div class="woomax-container">
            <div class="woomax-footer__grid">

                <!-- Brand column -->
                <div class="woomax-footer__brand">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="woomax-footer__logo">
                        <?php
                        $logo_id = get_theme_mod( 'custom_logo' );
                        if ( $logo_id ) {
                            echo wp_get_attachment_image( $logo_id, 'full', false, [ 'style' => 'height:50px;width:auto;filter:brightness(0) invert(1);' ] );
                        } else {
                            echo esc_html( get_theme_mod( 'woomax_logo_text', get_bloginfo( 'name' ) ) );
                        }
                        ?>
                    </a>
                    <p><?php echo wp_kses_post( get_theme_mod( 'woomax_footer_about', __( 'WooMax est votre boutique en ligne dédiée à des produits artisanaux et naturels de qualité premium.', 'woomax' ) ) ); ?></p>
                    <div class="woomax-footer__social">
                        <?php echo woomax_social_icons(); ?>
                    </div>
                </div>

                <!-- Footer menus / links -->
                <div>
                    <h4 class="woomax-footer__col-title"><?php echo esc_html( get_theme_mod( 'woomax_footer_col2_title', __( 'Boutique', 'woomax' ) ) ); ?></h4>
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'footer_1',
                        'container'      => false,
                        'menu_class'     => 'woomax-footer__links',
                        'depth'          => 1,
                        'fallback_cb'    => function() {
                            if ( ! class_exists( 'WooCommerce' ) ) return;
                            $cats = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 6 ] );
                            echo '<ul class="woomax-footer__links">';
                            foreach ( $cats as $cat ) {
                                echo '<li><a href="' . esc_url( get_term_link( $cat ) ) . '"><i class="fa-solid fa-chevron-right" style="font-size:10px;opacity:0.5;"></i> ' . esc_html( $cat->name ) . '</a></li>';
                            }
                            echo '</ul>';
                        },
                    ] );
                    ?>
                </div>

                <div>
                    <h4 class="woomax-footer__col-title"><?php echo esc_html( get_theme_mod( 'woomax_footer_col3_title', __( 'Informations', 'woomax' ) ) ); ?></h4>
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'footer_2',
                        'container'      => false,
                        'menu_class'     => 'woomax-footer__links',
                        'depth'          => 1,
                        'fallback_cb'    => function() {
                            $pages = [ 'a-propos' => __( 'À propos', 'woomax' ), 'contact' => __( 'Contact', 'woomax' ),
                                       'livraison' => __( 'Livraison', 'woomax' ), 'retours' => __( 'Retours', 'woomax' ),
                                       'faq' => 'FAQ', 'mentions-legales' => __( 'Mentions légales', 'woomax' ) ];
                            echo '<ul class="woomax-footer__links">';
                            foreach ( $pages as $slug => $name ) {
                                $page = get_page_by_path( $slug );
                                $url  = $page ? get_permalink( $page->ID ) : home_url( '/' . $slug );
                                echo '<li><a href="' . esc_url( $url ) . '"><i class="fa-solid fa-chevron-right" style="font-size:10px;opacity:0.5;"></i> ' . esc_html( $name ) . '</a></li>';
                            }
                            echo '</ul>';
                        },
                    ] );
                    ?>
                </div>

                <div>
                    <h4 class="woomax-footer__col-title"><?php echo esc_html( get_theme_mod( 'woomax_footer_col4_title', __( 'Contact', 'woomax' ) ) ); ?></h4>
                    <ul class="woomax-footer__links" style="list-style:none;padding:0;">
                        <?php $phone = get_theme_mod( 'woomax_footer_phone', '' ); if ( $phone ) : ?>
                        <li style="margin-bottom:12px;display:flex;gap:10px;align-items:flex-start;">
                            <i class="fa-solid fa-phone" style="color:var(--woomax-primary);margin-top:3px;flex-shrink:0;"></i>
                            <a href="tel:<?php echo esc_attr( preg_replace( '/\s/', '', $phone ) ); ?>" style="color:var(--woomax-footer-text);"><?php echo esc_html( $phone ); ?></a>
                        </li>
                        <?php endif;
                        $email = get_theme_mod( 'woomax_footer_email', '' ); if ( $email ) : ?>
                        <li style="margin-bottom:12px;display:flex;gap:10px;align-items:flex-start;">
                            <i class="fa-solid fa-envelope" style="color:var(--woomax-primary);margin-top:3px;flex-shrink:0;"></i>
                            <a href="mailto:<?php echo esc_attr( $email ); ?>" style="color:var(--woomax-footer-text);"><?php echo esc_html( $email ); ?></a>
                        </li>
                        <?php endif;
                        $address = get_theme_mod( 'woomax_footer_address', '' ); if ( $address ) : ?>
                        <li style="margin-bottom:12px;display:flex;gap:10px;align-items:flex-start;">
                            <i class="fa-solid fa-location-dot" style="color:var(--woomax-primary);margin-top:3px;flex-shrink:0;"></i>
                            <span style="color:var(--woomax-footer-text);"><?php echo nl2br( esc_html( $address ) ); ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="woomax-footer__bottom">
        <div class="woomax-container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;width:100%;">
            <p style="margin:0;font-size:13px;">
                <?php echo wp_kses_post( get_theme_mod( 'woomax_footer_copyright',
                    sprintf( '© %d %s – Tous droits réservés.', date('Y'), get_bloginfo('name') )
                ) ); ?>
            </p>

            <?php if ( get_theme_mod( 'woomax_show_footer_menu', true ) ) :
                wp_nav_menu( [
                    'theme_location' => 'footer_3',
                    'container'      => 'nav',
                    'menu_class'     => '',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                    'items_wrap'     => '<ul style="display:flex;gap:20px;list-style:none;padding:0;margin:0;">%3$s</ul>',
                ] );
            endif; ?>

            <?php if ( get_theme_mod( 'woomax_show_payment_icons', true ) ) : ?>
            <div class="woomax-footer__payments">
                <span class="woomax-footer__payment-icon">VISA</span>
                <span class="woomax-footer__payment-icon">MC</span>
                <span class="woomax-footer__payment-icon">AMEX</span>
                <span class="woomax-footer__payment-icon">PayPal</span>
                <span class="woomax-footer__payment-icon">Stripe</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</footer>

<!-- Back to top -->
<?php if ( get_theme_mod( 'woomax_show_back_to_top', true ) ) : ?>
<button class="woomax-back-to-top" id="woomax-back-to-top" aria-label="<?php esc_attr_e( 'Retour en haut', 'woomax' ); ?>">
    <i class="fa-solid fa-arrow-up"></i>
</button>
<?php endif; ?>

<?php do_action( 'woomax_after_footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
