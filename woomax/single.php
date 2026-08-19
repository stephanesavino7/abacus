<?php
/**
 * The template for displaying all single posts
 *
 * @package WooMax
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="single-post__hero">
                            <?php the_post_thumbnail( 'full', array( 'class' => 'single-post__hero-img' ) ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="single-post__inner">
                        <header class="entry-header">
                            <div class="post-meta">
                                <span class="post-date"><?php echo get_the_date(); ?></span>
                                <?php if ( has_category() ) : ?>
                                    <span class="separator">·</span>
                                    <span class="post-categories"><?php the_category( ', ' ); ?></span>
                                <?php endif; ?>
                                <span class="separator">·</span>
                                <span class="post-author"><?php echo get_the_author(); ?></span>
                            </div>
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="entry-content">
                            <?php
                            the_content( sprintf(
                                wp_kses(
                                    __( 'Continuer la lecture de %s <span class="meta-nav">&rarr;</span>', 'woomax' ),
                                    array( 'span' => array( 'class' => array() ) )
                                ),
                                the_title( '<span class="screen-reader-text">"', '"</span>', false )
                            ) );

                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Pages :', 'woomax' ),
                                'after'  => '</div>',
                            ) );
                            ?>
                        </div>

                        <?php if ( has_tag() ) : ?>
                            <footer class="entry-footer">
                                <div class="post-tags">
                                    <span class="tags-label"><?php esc_html_e( 'Tags :', 'woomax' ); ?></span>
                                    <?php the_tags( '', ', ' ); ?>
                                </div>
                            </footer>
                        <?php endif; ?>
                    </div>
                </article>

                <nav class="post-navigation">
                    <div class="nav-previous">
                        <?php previous_post_link( '<span class="nav-label">&larr; ' . esc_html__( 'Article précédent', 'woomax' ) . '</span><br>%link' ); ?>
                    </div>
                    <div class="nav-next">
                        <?php next_post_link( '<span class="nav-label">' . esc_html__( 'Article suivant', 'woomax' ) . ' &rarr;</span><br>%link' ); ?>
                    </div>
                </nav>

                <?php
                $author_id = get_the_author_meta( 'ID' );
                $author_bio = get_the_author_meta( 'description' );
                if ( $author_bio ) :
                ?>
                <div class="author-box">
                    <div class="author-avatar">
                        <?php echo get_avatar( $author_id, 80 ); ?>
                    </div>
                    <div class="author-info">
                        <h3 class="author-name"><?php the_author(); ?></h3>
                        <p class="author-bio"><?php echo esc_html( $author_bio ); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>

            <?php endwhile; ?>
        </div>

        <?php get_sidebar(); ?>
    </div>
</main>

<?php
get_footer();
