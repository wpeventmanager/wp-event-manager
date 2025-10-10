<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

if(!function_exists('license_enqueue_scripts')){
    /**
     * license_enqueue_scripts function.
     *
     * @access public
     * @return void
     * @since 1.2
     */
    function license_enqueue_scripts($hook) {
		if ($hook === 'event_listing_page_wpem_license' && ! wp_style_is( 'wpem-updater-styles', 'enqueued' )) {
            wp_register_style( 'wpem-updater-styles', plugin_dir_url(__DIR__) . 'autoupdater/assets/css/backend.css' );
        }
    }
}
/**
 * Hook admin-only functions
 */
if ( is_admin() ) {
	add_action('admin_enqueue_scripts', 'license_enqueue_scripts');
	add_action('admin_menu', 'wpem_addon_license_manage_menu', 10);
}

if (!function_exists('wpem_addon_license_manage_menu')) {
	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 * @since 1.2
	 */
	function wpem_addon_license_manage_menu() {
		if(!empty(get_wpem_plugins_info())){
			add_submenu_page(
				'edit.php?post_type=event_listing',
				__('License', 'wp-event-manager'),
				__('License', 'wp-event-manager'),
				'manage_options',
				'wpem_license',
				'wpem_manage_license'
			);
		}
	}
}

/**
 * wpem_manage_license function.
 *
 * @access public
 * @return void
 * @since 1.2
 */
if (!function_exists('wpem_manage_license')) {
	function wpem_manage_license() {
		wp_enqueue_style('wpem-updater-styles');

		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_wpem_plugins_info(); ?>

		<div class="wrap wpem-updater-licence-wrap">
			<h2><?php esc_html_e('License', 'wp-event-manager'); ?></h2>

			<div class="wpem-updater-licence">
				<?php
				foreach ($plugins as $filename => $plugin) {
					$licence_key = get_option($plugin['TextDomain'] . '_licence_key');
					$email = get_option($plugin['TextDomain'] . '_email');

					$disabled = '';
					if (!empty($licence_key)) {
						$disabled = 'disabled';
					}
					require plugin_dir_path( __FILE__ ) . 'templates/addon-licence.php';
				} ?>
			</div>
			<div class="notice notice-info inline">
				<p><?php esc_html_e('Lost your license key?', 'wpem-restaurant-manager'); ?> <a target="_blank" href="wp-eventmanager.com/lost-license-key/"><?php esc_html_e('Retrieve it here', 'wp-event-manager'); ?></a>.</p>
			</div>
		</div>
		<?php
	}
}