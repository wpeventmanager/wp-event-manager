<?php
global $wp_post_types;

switch ( $event->post_status ) :

	case 'publish' :
		printf('<p class="post-submitted-success-green-message wpem-alert wpem-alert-success">'.__( '%s listed successfully. To view your listing <a href="%s">click here</a>.', 'wp-event-manager' ).'</p>', esc_attr($wp_post_types['event_listing']->labels->singular_name), esc_url(get_permalink( $event->ID ) ));
	break;
	
	case 'pending' :
		printf( '<p class="post-submitted-success-green-message wpem-alert wpem-alert-success">'.__( '%s submitted successfully. Your listing will be visible once approved.', 'wp-event-manager' ).'</p>', esc_attr($wp_post_types['event_listing']->labels->singular_name), esc_url(get_permalink( $event->ID ) ));
	break;

	default :
		do_action( 'event_manager_event_submitted_content_' . str_replace( '-', '_', sanitize_title( $event->post_status ) ), $event );
	break;

endswitch;

do_action( 'event_manager_event_submitted_content_after', sanitize_title( $event->post_status ), $event );
