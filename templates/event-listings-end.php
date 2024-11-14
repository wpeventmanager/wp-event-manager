</div>
<?php if(isset($show_filters)) {
	if($show_filters){
		if (!$show_pagination && $show_more) {
			echo '<div id="load_more_events_loader">';
				echo wp_kses_post('<a class="load_more_events" id="load_more_events" href="#" style="display:none;"><strong>' . __('Load more events', 'wp-event-manager') . '</strong></a>');
			echo '</div>';
		}else{
			
			echo wp_kses_post(get_event_listing_pagination($events->max_num_pages));
		}
	}else{
		//error_log("show filter is not set");
		if ($events->found_posts > $per_page && $show_more) :
			if ($show_pagination) :
				echo wp_kses_post(get_event_listing_pagination($events->max_num_pages));?>
			<?php else :?>
				<div id="load_more_events_loader">
					<a class="load_more_events" id="load_more_events" href="#" ><strong><?php esc_html_e('Load more listings', 'wp-event-manager'); ?></strong></a>
				</div>
			<?php endif;
		endif;
	} 
} 
$html_after_event_list = get_option( 'enable_after_html' );
if( $html_after_event_list ){
	$html_content = get_option( 'event_content_after_html' );
	echo wp_kses_post($html_content);
}
?>
</div>
