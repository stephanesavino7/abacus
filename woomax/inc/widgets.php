<?php
/**
 * WooMax – Widgets personnalisés
 *
 * @package WooMax
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Widget : Produits récents stylisés
 */
class WooMax_Recent_Products_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct( 'woomax_recent_products', __( 'WooMax : Produits récents', 'woomax' ), [
            'description' => __( 'Affiche les produits les plus récents avec une mise en page WooMax.', 'woomax' ),
        ] );
    }
    public function widget( $args, $instance ) {
        if ( ! class_exists( 'WooCommerce' ) ) return;
        echo $args['before_widget'];
        $title = apply_filters( 'widget_title', $instance['title'] ?? '' );
        if ( $title ) echo $args['before_title'] . $title . $args['after_title'];
        $query = new WP_Query( [
            'post_type'      => 'product',
            'posts_per_page' => absint( $instance['number'] ?? 4 ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ] );
        if ( $query->have_posts() ) {
            echo '<ul class="woomax-widget-products">';
            while ( $query->have_posts() ) {
                $query->the_post();
                global $product;
                $product = wc_get_product( get_the_ID() );
                ?>
                <li style="display:flex;gap:12px;margin-bottom:16px;align-items:center;">
                    <a href="<?php the_permalink(); ?>" style="flex-shrink:0;width:60px;height:60px;border-radius:6px;overflow:hidden;display:block;">
                        <?php echo get_the_post_thumbnail( null, 'thumbnail', [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
                    </a>
                    <div>
                        <a href="<?php the_permalink(); ?>" style="font-size:14px;font-weight:600;color:var(--woomax-text-color);display:block;margin-bottom:4px;"><?php the_title(); ?></a>
                        <span style="color:var(--woomax-primary);font-weight:700;font-size:13px;"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
                    </div>
                </li>
                <?php
            }
            echo '</ul>';
        }
        wp_reset_postdata();
        echo $args['after_widget'];
    }
    public function form( $instance ) {
        $title  = $instance['title']  ?? __( 'Produits récents', 'woomax' );
        $number = $instance['number'] ?? 4;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Titre :', 'woomax' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Nombre de produits :', 'woomax' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" value="<?php echo absint( $number ); ?>" size="3">
        </p>
        <?php
    }
    public function update( $new_instance, $old_instance ) {
        return [
            'title'  => sanitize_text_field( $new_instance['title'] ?? '' ),
            'number' => absint( $new_instance['number'] ?? 4 ),
        ];
    }
}

/**
 * Widget : Newsletter
 */
class WooMax_Newsletter_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct( 'woomax_newsletter', __( 'WooMax : Newsletter', 'woomax' ), [
            'description' => __( 'Formulaire d\'inscription à la newsletter.', 'woomax' ),
        ] );
    }
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        $title = apply_filters( 'widget_title', $instance['title'] ?? '' );
        if ( $title ) echo $args['before_title'] . $title . $args['after_title'];
        $desc  = $instance['description'] ?? __( 'Recevez nos offres en exclusivité.', 'woomax' );
        $btn   = $instance['button']      ?? __( "M'inscrire", 'woomax' );
        $holder= $instance['placeholder'] ?? __( 'Votre e-mail…', 'woomax' );
        ?>
        <div class="woomax-widget-newsletter">
            <?php if ( $desc ) : ?><p style="font-size:14px;margin-bottom:14px;"><?php echo esc_html( $desc ); ?></p><?php endif; ?>
            <form class="woomax-newsletter-form" style="display:flex;flex-direction:column;gap:8px;">
                <input type="email" name="email" placeholder="<?php echo esc_attr( $holder ); ?>" required
                    style="padding:12px 16px;border:1px solid var(--woomax-border-color);border-radius:4px;font-size:14px;width:100%;outline:none;">
                <?php wp_nonce_field( 'woomax-nonce', '_nonce', true, true ); ?>
                <button type="submit" class="woomax-btn woomax-btn--primary" style="width:100%;justify-content:center;"><?php echo esc_html( $btn ); ?></button>
            </form>
        </div>
        <?php
        echo $args['after_widget'];
    }
    public function form( $instance ) {
        $title       = $instance['title']       ?? __( 'Newsletter', 'woomax' );
        $description = $instance['description'] ?? __( 'Recevez nos offres en exclusivité.', 'woomax' );
        $button      = $instance['button']      ?? __( "M'inscrire", 'woomax' );
        ?>
        <p><label><?php esc_html_e( 'Titre :', 'woomax' ); ?> <input class="widefat" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($title); ?>"></label></p>
        <p><label><?php esc_html_e( 'Description :', 'woomax' ); ?> <input class="widefat" name="<?php echo esc_attr( $this->get_field_name('description') ); ?>" type="text" value="<?php echo esc_attr($description); ?>"></label></p>
        <p><label><?php esc_html_e( 'Texte bouton :', 'woomax' ); ?> <input class="widefat" name="<?php echo esc_attr( $this->get_field_name('button') ); ?>" type="text" value="<?php echo esc_attr($button); ?>"></label></p>
        <?php
    }
    public function update( $new_instance, $old_instance ) {
        return [
            'title'       => sanitize_text_field( $new_instance['title']       ?? '' ),
            'description' => sanitize_text_field( $new_instance['description'] ?? '' ),
            'button'      => sanitize_text_field( $new_instance['button']      ?? '' ),
            'placeholder' => sanitize_text_field( $new_instance['placeholder'] ?? '' ),
        ];
    }
}

// ─── Register widgets ──────────────────────────────────────────────────────────
add_action( 'widgets_init', function() {
    register_widget( 'WooMax_Recent_Products_Widget' );
    register_widget( 'WooMax_Newsletter_Widget' );
} );
