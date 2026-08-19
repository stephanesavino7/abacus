<?php
/**
 * WooMax – Page d'accueil
 *
 * @package WooMax
 */
get_header();
$hero_style = get_theme_mod( 'woomax_hero_style', 'full-screen' );
$hero_image = get_theme_mod( 'woomax_hero_image', '' );
$hero_tag   = get_theme_mod( 'woomax_hero_tag', __( 'Nouveau · Collection 2025', 'woomax' ) );
$hero_title = get_theme_mod( 'woomax_hero_title', __( 'Des produits naturels,<br>pour une vie meilleure', 'woomax' ) );
$hero_sub   = get_theme_mod( 'woomax_hero_subtitle', __( 'Découvrez notre sélection de produits artisanaux, issus de matières premières durables et certifiées.', 'woomax' ) );
$btn1_text  = get_theme_mod( 'woomax_hero_btn1_text', __( 'Découvrir la boutique', 'woomax' ) );
$btn1_url   = get_theme_mod( 'woomax_hero_btn1_url', '/boutique' );
$btn2_text  = get_theme_mod( 'woomax_hero_btn2_text', __( 'Notre histoire', 'woomax' ) );
$btn2_url   = get_theme_mod( 'woomax_hero_btn2_url', '/a-propos' );
$overlay_color   = get_theme_mod( 'woomax_hero_overlay', '#000000' );
$overlay_opacity = get_theme_mod( 'woomax_hero_overlay_opacity', 50 ) / 100;
?>

<!-- ══════════ HERO ══════════ -->
<section class="woomax-hero<?php echo $hero_style === 'full-screen' ? '' : ' woomax-hero--' . esc_attr( $hero_style ); ?>"
    <?php if ( $hero_image ) : ?>
    style="background-image:url('<?php echo esc_url( $hero_image ); ?>');background-size:cover;background-position:center;"
    <?php else : ?>
    style="background:linear-gradient(135deg, var(--woomax-primary-dark) 0%, var(--woomax-primary) 50%, var(--woomax-accent) 100%);"
    <?php endif; ?>>
    <?php if ( $hero_image ) : ?>
    <div style="position:absolute;inset:0;background:<?php echo esc_attr( $overlay_color ); ?>;opacity:<?php echo $overlay_opacity; ?>;"></div>
    <?php endif; ?>
    <div class="woomax-container" style="position:relative;z-index:1;">
        <div class="woomax-hero__content woomax-fade-in">
            <?php if ( $hero_tag ) : ?>
            <div class="woomax-hero__tag"><?php echo esc_html( $hero_tag ); ?></div>
            <?php endif; ?>
            <h1><?php echo wp_kses_post( $hero_title ); ?></h1>
            <?php if ( $hero_sub ) : ?>
            <p><?php echo wp_kses_post( $hero_sub ); ?></p>
            <?php endif; ?>
            <div class="woomax-hero__actions">
                <?php if ( $btn1_text && $btn1_url ) : ?>
                <a href="<?php echo esc_url( $btn1_url ); ?>" class="woomax-btn woomax-btn--primary woomax-btn--lg"><?php echo esc_html( $btn1_text ); ?></a>
                <?php endif; ?>
                <?php if ( $btn2_text && $btn2_url ) : ?>
                <a href="<?php echo esc_url( $btn2_url ); ?>" class="woomax-btn woomax-btn--white woomax-btn--lg"><?php echo esc_html( $btn2_text ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Hero decorative stats -->
    <div style="position:absolute;bottom:40px;right:40px;z-index:2;display:flex;gap:32px;" class="d-none-mobile">
        <div style="text-align:center;color:#fff;">
            <div style="font-size:2rem;font-family:var(--woomax-heading-font);font-weight:700;">2 500+</div>
            <div style="font-size:12px;opacity:.8;letter-spacing:.1em;text-transform:uppercase;"><?php esc_html_e( 'Clients satisfaits', 'woomax' ); ?></div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,.3);"></div>
        <div style="text-align:center;color:#fff;">
            <div style="font-size:2rem;font-family:var(--woomax-heading-font);font-weight:700;">4.9★</div>
            <div style="font-size:12px;opacity:.8;letter-spacing:.1em;text-transform:uppercase;"><?php esc_html_e( 'Note moyenne', 'woomax' ); ?></div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,.3);"></div>
        <div style="text-align:center;color:#fff;">
            <div style="font-size:2rem;font-family:var(--woomax-heading-font);font-weight:700;">100%</div>
            <div style="font-size:12px;opacity:.8;letter-spacing:.1em;text-transform:uppercase;"><?php esc_html_e( 'Naturel', 'woomax' ); ?></div>
        </div>
    </div>
</section>

<!-- ══════════ FEATURES STRIP ══════════ -->
<?php if ( get_theme_mod( 'woomax_show_features', true ) ) : ?>
<section class="woomax-features" style="background:<?php echo esc_attr( get_theme_mod( 'woomax_features_bg', '#8B5E3C' ) ); ?>;color:<?php echo esc_attr( get_theme_mod( 'woomax_features_text', '#ffffff' ) ); ?>;">
    <div class="woomax-container">
        <div class="woomax-features__grid">
            <?php for ( $i = 1; $i <= 4; $i++ ) :
                $icon  = get_theme_mod( "woomax_feature_{$i}_icon",  [ 'fa-truck', 'fa-shield-halved', 'fa-rotate-left', 'fa-headset' ][$i-1] );
                $title = get_theme_mod( "woomax_feature_{$i}_title", [ 'Livraison gratuite', 'Paiement sécurisé', 'Retours 30 jours', 'Support 7j/7' ][$i-1] );
                $text  = get_theme_mod( "woomax_feature_{$i}_text",  [ 'Dès 50€ d\'achat', 'SSL · Stripe · PayPal', 'Satisfait ou remboursé', 'Par chat et téléphone' ][$i-1] );
            ?>
            <div class="woomax-feature">
                <div class="woomax-feature__icon"><i class="fa-solid <?php echo esc_attr( $icon ); ?>"></i></div>
                <div>
                    <div class="woomax-feature__title"><?php echo esc_html( $title ); ?></div>
                    <div class="woomax-feature__text"><?php echo esc_html( $text ); ?></div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════ FEATURED PRODUCTS ══════════ -->
<?php if ( get_theme_mod( 'woomax_show_home_products', true ) && class_exists( 'WooCommerce' ) ) :
    $prod_title = get_theme_mod( 'woomax_home_products_title', __( 'Nos Meilleures Ventes', 'woomax' ) );
    $prod_type  = get_theme_mod( 'woomax_home_products_type', 'best_selling' );
    $prod_count = absint( get_theme_mod( 'woomax_home_products_count', 8 ) );
    $prod_cols  = absint( get_theme_mod( 'woomax_home_products_cols', 4 ) );
    $products   = woomax_get_products( $prod_type, $prod_count );
?>
<section class="woomax-section woomax-section--bg">
    <div class="woomax-container">
        <?php woomax_section_title( __( 'Notre sélection', 'woomax' ), $prod_title, '' ); ?>
        <?php if ( $products->have_posts() ) : ?>
        <!-- Tabs -->
        <div style="display:flex;justify-content:center;gap:8px;margin-bottom:40px;flex-wrap:wrap;" id="woomax-product-tabs">
            <?php
            $tab_types = [ 'best_selling' => __( 'Meilleures ventes', 'woomax' ), 'newest' => __( 'Nouveautés', 'woomax' ), 'featured' => __( 'Vedettes', 'woomax' ), 'on_sale' => __( 'Promotions', 'woomax' ) ];
            foreach ( $tab_types as $type => $label ) :
            ?>
            <button class="woomax-tab-filter <?php echo $type === $prod_type ? 'woomax-btn woomax-btn--primary woomax-btn--sm' : 'woomax-btn woomax-btn--secondary woomax-btn--sm'; ?>"
                data-type="<?php echo esc_attr( $type ); ?>" style="margin:0;">
                <?php echo esc_html( $label ); ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="woomax-products-grid woomax-products-grid--<?php echo absint( $prod_cols ); ?>" id="woomax-products-container">
            <?php while ( $products->have_posts() ) : $products->the_post(); global $product; $product = wc_get_product( get_the_ID() ); if ( ! $product ) continue; ?>
            <?php get_template_part( 'template-parts/product-card' ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <div style="text-align:center;margin-top:48px;">
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="woomax-btn woomax-btn--primary woomax-btn--lg">
                <?php esc_html_e( 'Voir tous les produits', 'woomax' ); ?> <i class="fa-solid fa-arrow-right" style="margin-left:8px;"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ══════════ CATEGORIES GRID ══════════ -->
<?php if ( get_theme_mod( 'woomax_show_home_cats', true ) && class_exists( 'WooCommerce' ) ) :
    $cats = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 6, 'exclude' => [ get_option( 'default_product_cat' ) ] ] );
    if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) :
?>
<section class="woomax-section">
    <div class="woomax-container">
        <?php woomax_section_title( __( 'Collections', 'woomax' ), get_theme_mod( 'woomax_home_cats_title', __( 'Explorez nos univers', 'woomax' ) ), '' ); ?>
        <div class="woomax-categories-grid">
            <?php foreach ( $cats as $i => $cat ) :
                $thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'woomax-medium' ) : '';
                $is_tall   = $i === 0 || $i === 3;
            ?>
            <div class="woomax-category-card<?php echo $is_tall ? ' woomax-category-card--tall' : ''; ?>">
                <?php if ( $thumb_url ) : ?>
                <img class="woomax-category-card__image" src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" loading="lazy">
                <?php else : ?>
                <div class="woomax-category-card__image" style="background:linear-gradient(135deg,var(--woomax-primary),var(--woomax-accent));min-height:260px;"></div>
                <?php endif; ?>
                <div class="woomax-category-card__content">
                    <div class="woomax-category-card__count"><?php echo sprintf( _n( '%d produit', '%d produits', $cat->count, 'woomax' ), $cat->count ); ?></div>
                    <div class="woomax-category-card__name"><?php echo esc_html( $cat->name ); ?></div>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="woomax-category-card__link">
                        <?php esc_html_e( 'Explorer', 'woomax' ); ?> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" style="position:absolute;inset:0;" aria-label="<?php echo esc_attr( $cat->name ); ?>"></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; endif; ?>

<!-- ══════════ PROMO BANNER ══════════ -->
<?php if ( get_theme_mod( 'woomax_show_home_banner', true ) ) :
    $banner_image = get_theme_mod( 'woomax_banner_image', '' );
    $banner_tag   = get_theme_mod( 'woomax_banner_tag',   __( 'Offre spéciale', 'woomax' ) );
    $banner_title = get_theme_mod( 'woomax_banner_title', __( 'Jusqu\'à <span style="color:var(--woomax-accent)">-40%</span><br>sur nos essentiels', 'woomax' ) );
    $banner_text  = get_theme_mod( 'woomax_banner_text',  __( 'Profitez de nos offres exclusives sur une sélection de produits.', 'woomax' ) );
    $banner_btn   = get_theme_mod( 'woomax_banner_btn',   __( 'Voir les promotions', 'woomax' ) );
    $banner_url   = get_theme_mod( 'woomax_banner_url',   '/boutique' );
?>
<section class="woomax-section">
    <div class="woomax-container">
        <div class="woomax-banner" style="<?php if ( $banner_image ) echo 'background-image:url(' . esc_url( $banner_image ) . ');background-size:cover;background-position:center;'; else echo 'background:linear-gradient(135deg, var(--woomax-bg-dark) 0%, var(--woomax-primary-dark) 100%);'; ?>">
            <div class="woomax-banner__content">
                <?php if ( $banner_tag ) : ?><span class="woomax-banner__tag"><?php echo esc_html( $banner_tag ); ?></span><?php endif; ?>
                <h2><?php echo wp_kses_post( $banner_title ); ?></h2>
                <?php if ( $banner_text ) : ?><p><?php echo wp_kses_post( $banner_text ); ?></p><?php endif; ?>
                <?php if ( $banner_btn && $banner_url ) : ?>
                <a href="<?php echo esc_url( $banner_url ); ?>" class="woomax-btn woomax-btn--white woomax-btn--lg">
                    <?php echo esc_html( $banner_btn ); ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════ TESTIMONIALS ══════════ -->
<?php if ( get_theme_mod( 'woomax_show_testimonials', true ) ) :
    $testimonials = [
        [ 'name' => 'Sophie M.',    'role' => 'Cliente fidèle',   'rating' => 5, 'text' => 'Des produits d\'une qualité exceptionnelle. Je commande régulièrement et je suis toujours satisfaite de mes achats. Livraison rapide et emballage soigné.' ],
        [ 'name' => 'Pierre L.',    'role' => 'Amateur de nature', 'rating' => 5, 'text' => 'Je cherchais des produits naturels et certifiés depuis longtemps. WooMax répond parfaitement à mes attentes. Je recommande vivement !' ],
        [ 'name' => 'Marie-Claire', 'role' => 'Blogueuse lifestyle', 'rating' => 5, 'text' => 'Le service client est exceptionnel. J\'ai eu une petite question sur ma commande et ils ont répondu en moins d\'une heure. Bravo !' ],
    ];
?>
<section class="woomax-section woomax-section--bg">
    <div class="woomax-container">
        <?php woomax_section_title( __( 'Avis clients', 'woomax' ), get_theme_mod( 'woomax_testimonials_title', __( 'Ce que disent nos clients', 'woomax' ) ), '' ); ?>
        <div class="woomax-testimonials-grid">
            <?php foreach ( $testimonials as $t ) : ?>
            <div class="woomax-testimonial woomax-fade-in">
                <div class="woomax-testimonial__quote">"</div>
                <p class="woomax-testimonial__text"><?php echo esc_html( $t['text'] ); ?></p>
                <div class="woomax-testimonial__author">
                    <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--woomax-primary),var(--woomax-accent));display:flex;align-items:center;justify-content:center;color:#fff;font-family:var(--woomax-heading-font);font-weight:700;font-size:1.1rem;">
                        <?php echo esc_html( substr( $t['name'], 0, 1 ) ); ?>
                    </div>
                    <div>
                        <div class="woomax-testimonial__name"><?php echo esc_html( $t['name'] ); ?></div>
                        <div class="woomax-testimonial__role"><?php echo esc_html( $t['role'] ); ?></div>
                        <div class="woomax-testimonial__rating"><?php echo str_repeat( '★', $t['rating'] ); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════ LATEST BLOG POSTS ══════════ -->
<?php
$blog_posts = new WP_Query( [ 'post_type' => 'post', 'posts_per_page' => 3, 'post_status' => 'publish' ] );
if ( $blog_posts->have_posts() ) :
?>
<section class="woomax-section">
    <div class="woomax-container">
        <?php woomax_section_title( __( 'Blog', 'woomax' ), __( 'Nos derniers articles', 'woomax' ), '' ); ?>
        <div class="woomax-blog-grid">
            <?php while ( $blog_posts->have_posts() ) : $blog_posts->the_post(); ?>
            <article class="woomax-post-card woomax-fade-in">
                <?php if ( has_post_thumbnail() ) : ?>
                <div class="woomax-post-card__image">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'woomax-medium' ); ?></a>
                </div>
                <?php endif; ?>
                <div class="woomax-post-card__body">
                    <div class="woomax-post-card__meta">
                        <span class="woomax-post-card__category"><?php the_category( ', ' ); ?></span>
                        <span><?php echo get_the_date( 'd M Y' ); ?></span>
                    </div>
                    <h3 class="woomax-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="woomax-post-card__excerpt"><?php the_excerpt(); ?></p>
                    <a href="<?php the_permalink(); ?>" class="woomax-post-card__link">
                        <?php esc_html_e( 'Lire la suite', 'woomax' ); ?> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════ NEWSLETTER ══════════ -->
<?php if ( get_theme_mod( 'woomax_show_newsletter', true ) ) :
    $nl_title  = get_theme_mod( 'woomax_newsletter_title', __( 'Restez informé', 'woomax' ) );
    $nl_sub    = get_theme_mod( 'woomax_newsletter_subtitle', __( 'Inscrivez-vous pour recevoir nos offres exclusives, nouveautés et conseils directement dans votre boîte mail.', 'woomax' ) );
    $nl_ph     = get_theme_mod( 'woomax_newsletter_placeholder', __( 'Votre adresse e-mail…', 'woomax' ) );
    $nl_btn    = get_theme_mod( 'woomax_newsletter_btn', __( "M'inscrire", 'woomax' ) );
?>
<section class="woomax-newsletter">
    <div class="woomax-container">
        <h2><?php echo esc_html( $nl_title ); ?></h2>
        <p><?php echo wp_kses_post( $nl_sub ); ?></p>
        <form class="woomax-newsletter__form" id="woomax-newsletter-form" novalidate>
            <input type="email" name="email" placeholder="<?php echo esc_attr( $nl_ph ); ?>" class="woomax-newsletter__input" required>
            <button type="submit" class="woomax-btn woomax-btn--white">
                <?php echo esc_html( $nl_btn ); ?>
            </button>
        </form>
        <p id="woomax-newsletter-msg" style="margin-top:16px;font-size:14px;opacity:0.9;display:none;"></p>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
