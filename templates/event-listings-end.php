<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
</div>
<?php if(isset($show_filters)) {
	if($show_filters){
		if (!$show_pagination && $show_more) {
			if($events->found_posts > $per_page){
			echo '<div id="load_more_events_loader" class="load_more_events_loader">';
				echo wp_kses_post('<a class="load_more_events" id="load_more_events" href="#" style="display:none;"><strong>' . __('Load more events', 'wp-event-manager') . '</strong></a>');
			echo '</div>';
			}
		}else{
			
			echo wp_kses_post(wpem_get_event_listing_pagination($events->max_num_pages));
		}
	} else {
		if ($events->found_posts > $per_page && $show_more) :
			if ($show_pagination) :
				echo wp_kses_post(wpem_get_event_listing_pagination($events->max_num_pages));?>
			<?php else :?>
				<div id="load_more_events_loader" class="load_more_events_loader">
					<a class="load_more_events" id="load_more_events" href="#" ><strong><?php esc_html_e('Load more listings', 'wp-event-manager'); ?></strong></a>
				</div>
			<?php endif;
		endif;
	} 
} 
$wpem_html_after_event_list = get_option( 'enable_after_html' );
if( $wpem_html_after_event_list ){
	$wpem_html_content = get_option( 'event_content_after_html' );
	echo wp_kses_post($wpem_html_content);
} ?>
</div>