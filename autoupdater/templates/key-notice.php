<div class="notice notice-error">
	<?php $license_page_url = get_admin_url() . 'edit.php?post_type=event_listing&page=wpem_license'; ?>
	<p class="wpem-updater-dismiss" style="float:right;"><a href="<?php echo esc_url( add_query_arg( 'dismiss-' . sanitize_title( $plugin['TextDomain'] ), '1' ) ); ?>"><?php esc_html_e( 'Hide notice' ); ?></a></p>
	<?php // translators: 1: plugin list URL, 2: plugin name ?>
	<p><?php printf(__('Please enter your licence key in <a href="%1$s">the plugin list</a> below to get activate all features of "%2$s".', 'wp-event-manager'), esc_url($license_page_url) , esc_html( $plugin['Name'] ) ); ?></p>
	<?php // translators: %s: URL to retrieve lost license key ?>
	<p><small class="description"><?php printf(__('Lost your key? <a href="%s">Retrieve it here</a>.', 'wp-event-manager'), esc_url( get_option( 'wp_event_manager_store_url' ) .'lost-license-key/' ) ); ?></small></p>
</div>