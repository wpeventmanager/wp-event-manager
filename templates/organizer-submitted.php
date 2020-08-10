<?php
global $wp_post_types;

switch ( $organizer->post_status ) :

	case 'publish' :
		printf('<p class="post-submitted-success-green-message wpem-alert wpem-alert-success">'.__( '%s listed successfully. To view your organizer list <a href="%s">click here</a>.', 'wp-event-manager' ).'</p>', $wp_post_types['event_organizer']->labels->singular_name, get_permalink( $organizer->ID ) );
	break;
	
	case 'pending' :
		printf( '<p class="post-submitted-success-green-message wpem-alert wpem-alert-success">'.__( '%s submitted successfully. Your organizer will be visible once approved.', 'wp-event-manager' ).'</p>', $wp_post_types['event_organizer']->labels->singular_name, get_permalink( $organizer->ID ) );
	break;

	default :
		do_action( 'event_manager_organizer_submitted_content_' . str_replace( '-', '_', sanitize_title( $organizer->post_status ) ), $organizer );
	break;

endswitch;

do_action( 'event_manager_organizer_submitted_content_after', sanitize_title( $organizer->post_status ), $organizer );
