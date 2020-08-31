<?php
/**
 * The template for displaying archive.
 */

get_header();

global $wp_query;
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <header class="archive-header has-text-align-center header-footer-group">
            <div class="archive-header-inner section-inner medium">
                <h1 class="archive-title"><?php echo get_the_archive_title(); ?></h1>
            </div><!-- .archive-header-inner -->
        </header><!-- .archive-header -->

        <div class="entry-content wpem-mb-3">
            <div class="event_listings">
                <?php if ( have_posts() ) : ?>

                    <?php get_event_manager_template( 'event-listings-start.php' ,array('layout_type'=>'all')); ?>           

                    <?php while ( have_posts() ) : the_post(); ?>

                        <?php  get_event_manager_template_part( 'content', 'event_listing' ); ?>
                        
                    <?php endwhile; ?>

                    <?php get_event_manager_template( 'event-listings-end.php' ); ?>

                    <?php get_event_manager_template( 'pagination.php', array( 'max_num_pages' => $wp_query->max_num_pages ) ); ?>

                <?php else :

                    do_action( 'event_manager_output_events_no_results' );

                endif;

                wp_reset_postdata(); ?>
            </div>
        </div>
        
    </main>
</div>

<?php get_footer(); ?>