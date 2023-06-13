<?php
/**
 * The template for displaying archive.
 */

get_header();

global $wp_query; ?>

<div class="wpem-container">
    <div class="wpem-main wpem-event-listing-type-page">
        <div class="wpem-row">
            <div class="wpem-col-12 wpem-event-listing-type-page-wrapper">
                <div class="wpem-my-5 wpem-event-listing-type-page-title">
                    <h1 class="wpem-heading-text">
                        <?php echo  wp_kses_post(get_the_archive_title()); ?>
                    </h1>
                </div>
                <?php
                // remove calender view
                remove_action('end_event_listing_layout_icon', 'add_event_listing_calendar_layout_icon'); ?>
                <div class="event_listings">
                    <?php if ( have_posts() ) :
                        get_event_manager_template( 'event-listings-start.php' ,array('layout_type'=>'all')); 
                        while ( have_posts() ) : the_post();
                            get_event_manager_template_part( 'content', 'event_listing' );
                        endwhile; 
                        get_event_manager_template( 'event-listings-end.php' ); 
                        get_event_manager_template( 'pagination.php', array( 'max_num_pages' => $wp_query->max_num_pages ) ); ?>
                    <?php else :
                        do_action( 'event_manager_output_events_no_results' );
                    endif;
                    wp_reset_postdata(); ?>                
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>