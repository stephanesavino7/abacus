<?php
/**
 * The template for displaying search results pages
 *
 * @package WooMax
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">

        <header class="page-header search-header">
            <h1 class="page-title">
                <?php
                printf(
                    esc_html__( 'Résultats pour : %s', 'woomax' ),
                    '<span class="search-query">' . get_search_query() . '</span>'
                );
                ?>
            </h1>
            <p class="search-count">
                <?php
                global $wp_query;
                printf(
                    esc_html( _n( '%s résultat trouvé', '%s résultats trouvés', $wp_query->found_posts, 'woomax' ) ),
                    number_format_i18n( $wp_query->found_posts )
                );
                ?>
            </p>
            <?php get_search_form(); ?>
        </header>

        <div class="content-area">
            <?php if ( have_posts() ) : ?>

                <div class="posts-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'medium_large' ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="post-content-wrap">
                                <div class="post-meta">
                                    <span class="post-type-badge"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
                                    <span class="post-date"><?php echo get_the_date(); ?></span>
                                </div>

                                <h2 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                                <div class="post-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="btn btn-outline read-more">
                                    <?php esc_html_e( 'Voir', 'woomax' ); ?>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => '&larr; ' . esc_html__( 'Précédent', 'woomax' ),
                    'next_text' => esc_html__( 'Suivant', 'woomax' ) . ' &rarr;',
                ) ); ?>

            <?php else : ?>
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h2><?php esc_html_e( 'Aucun résultat', 'woomax' ); ?></h2>
                    <p><?php esc_html_e( 'Votre recherche ne correspond à aucun contenu. Essayez des termes différents.', 'woomax' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php get_sidebar(); ?>
    </div>
</main>

<?php
get_footer();
