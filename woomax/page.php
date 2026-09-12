<?php
/**
 * The template for displaying all pages
 *
 * @package WooMax
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area content-area--full">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php if ( has_post_thumbnail() && ! is_front_page() ) : ?>
                        <div class="page-hero">
                            <?php the_post_thumbnail( 'full' ); ?>
                            <div class="page-hero-overlay">
                                <h1 class="page-title"><?php the_title(); ?></h1>
                            </div>
                        </div>
                    <?php else : ?>
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>
                    <?php endif; ?>

                    <div class="entry-content page-content">
                        <?php
                        the_content();
                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages :', 'woomax' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>
                </article>

                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>

            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php
get_footer();
