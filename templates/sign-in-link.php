<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<div id="event-manager-event-login">
	<p class="account-sign-in wpem-alert wpem-alert-info">
		<?php 
		echo wp_kses_post(
			apply_filters(
				'event_manager_event_login_required_message',
				sprintf(
					// Translators: %s is the URL for the login page
					__('You must <a href="%s">sign in</a> to view more details.', 'wp-event-manager'),
					esc_url(get_option('event_manager_login_page_url'))
				)
			)
		); ?>
	</p>
</div>