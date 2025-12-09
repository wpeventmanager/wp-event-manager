<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<div id="event-manager-event-dashboard">
	<p class="account-sign-in wpem-alert wpem-alert-info">
    <?php esc_html_e('You need to be signed in to manage your listings.', 'wp-event-manager'); ?> 
		<a href="<?php  echo !empty(get_option('event_manager_login_page_url')) ? esc_url(apply_filters('submit_event_form_login_url', get_option('event_manager_login_page_url'))) : 	esc_url(home_url() . '/wp-login.php'); ?>">
			<?php esc_html_e('Sign in', 'wp-event-manager'); ?>
		</a>
	</p>
</div>
