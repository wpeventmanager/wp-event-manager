<?php
global $wp_post_types;
//print appropriate message accroding to post status or post description
switch ($venue->post_status) :
	case 'publish' :
		printf('<p class="post-submitted-success-green-message wpem-alert wpem-alert-success">'.__('%s listed successfully. To view your venue list <a href="%s">click here</a>.', 'wp-event-manager').'</p>', esc_attr($wp_post_types['event_venue']->labels->singular_name), esc_url(get_permalink($venue->ID)));
		break;
	case 'pending' :
		printf('<p class="post-submitted-success-green-message wpem-alert wpem-alert-success">'.__('%s submitted successfully. Your venue will be visible once approved.', 'wp-event-manager').'</p>', esc_attr($wp_post_types['event_organizer']->labels->singular_name), esc_url(get_permalink($venue->ID)));
		break;
	default :
		do_action('event_manager_venue_submitted_content_' . str_replace('-', '_', sanitize_title($venue->post_status)), $venue);
		break;
endswitch;
do_action('event_manager_venue_submitted_content_after', sanitize_title($venue->post_status), $venue);