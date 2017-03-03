<?php  if ( $register = get_event_registration_method() ) :
	wp_enqueue_script( 'wp-event-manager-event-registration' );
	
	?>
	<div class="event_registration registration">
		<?php do_action( 'event_registration_start', $register ); ?>
		
		<input type="button" class="registration_button button" value="<?php _e( 'Register for event', 'wp-event-manager' ); ?>" />
		
		<div class="registration_details">
			<?php
				/**
				 * event_manager_registration_details_email or event_manager_registration_details_url hook
				 */
				do_action( 'event_manager_registration_details_' . $register->type, $register );
			?>
		</div>
		<?php do_action( 'event_registration_end', $register ); ?>
	</div>
<?php endif; ?>
