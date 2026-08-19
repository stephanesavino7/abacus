<?php
/**
 * The main template file
 *
 * @package WooMax
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            <?php if ( have_posts() ) : ?>

                <header class="page-header">
                    <?php
                    the_archive_title( '<h1 class="page-title">', '</h1>' );
                    the_archive_description( '<div class="archive-description">', '</div>' );
                    ?>
                </header>

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
                                    <span class="post-date"><?php echo get_the_date(); ?></span>
                                    <?php if ( has_category() ) : ?>
                                        <span class="post-categories"><?php the_category( ', ' ); ?></span>
                                    <?php endif; ?>
                                </div>

                                <h2 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                                <div class="post-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="btn btn-outline read-more">
                                    <?php esc_html_e( 'Lire la suite', 'woomax' ); ?>
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
                    <h1><?php esc_html_e( 'Aucun résultat trouvé', 'woomax' ); ?></h1>
                    <p><?php esc_html_e( 'Désolé, aucun contenu ne correspond à vos critères.', 'woomax' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php get_sidebar(); ?>
    </div>
</main>

<?php
get_footer();
