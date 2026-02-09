<?php
if(!defined('ABSPATH')) {
}

/**
 * WP_Event_Manager_Setup class.
 * From admin panel, setuping post event page, event dashboard page and event listings page.
 * 
 */
class WP_Event_Manager_Setup {

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('admin_menu', array($this, 'admin_menu'), 12);
		add_action('admin_head', array($this, 'admin_head'));
		add_action('admin_init', array($this, 'redirect'));
		if(isset($_GET['page']) && 'event-manager-setup' === sanitize_text_field( wp_unslash($_GET['page']))) {
			// Only verify nonce for actions that modify data
			if (isset($_GET['skip-event-manager-setup']) && sanitize_text_field(wp_unslash($_GET['skip-event-manager-setup'])) === 1) {
				if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'wpem_skip_setup_nonce')) {
					wp_die('Security check failed');
				}
			}
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 12);
		}
		add_action('wp_ajax_wpem_save_installation_settings', array($this,'wpem_save_installation_settings'));
	}

	/**
	 * Admin menu.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_dashboard_page(__('Setup', 'wp-event-manager'), __('Setup', 'wp-event-manager'), 'manage_options', 'event-manager-setup', array($this, 'output'));
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page('index.php', 'event-manager-setup');
	}

	/**
	 * Sends user to the setup page on first activation.
	 */
	public function redirect() {
		global $pagenow;

		if(isset($_GET['page']) && sanitize_text_field( wp_unslash($_GET['page'])) === 'event-manager-setup') {		// Verify nonce for actions that modify data
		if (isset($_GET['skip-event-manager-setup']) && sanitize_text_field(wp_unslash($_GET['skip-event-manager-setup'])) === 1) {
			if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'wpem_skip_setup_nonce')) {
				wp_die('Security check failed');
			}
		}
			if(get_option('wpem_installation', false)) {
				wp_safe_redirect( admin_url( 'index.php' ) );
			}
		}
		// Bail if no activation redirect transient is set
		if(!get_transient('_event_manager_activation_redirect')) {
			return;
		}
		if(!current_user_can('manage_options')) {
			return;
		}
		// Delete the redirect transient
		delete_transient('_event_manager_activation_redirect');
		// Bail if activating from network, or bulk, or within an iFrame
		if(is_network_admin() || isset($_GET['activate-multi']) || defined('IFRAME_REQUEST')) {
			return;
		}
		if((isset($_GET['action']) && 'upgrade-plugin' == sanitize_text_field( wp_unslash($_GET['action']))) && (isset($_GET['plugin']) && strstr(sanitize_text_field( wp_unslash($_GET['plugin'])), 'wp-event-manager.php'))) {
			return;
		}
		wp_safe_redirect(admin_url('index.php?page=event-manager-setup'));
	}

	/**
	 * Enqueue scripts for setup page.
	 */
	public function admin_enqueue_scripts()	{
		wp_enqueue_style('event_manager_setup_css', EVENT_MANAGER_PLUGIN_URL . '/assets/css/setup.min.css', array('dashicons'), '1.0.0');
		wp_enqueue_script('event_manager_setup_js', EVENT_MANAGER_PLUGIN_URL . '/assets//js/setup.min.js', array('jquery'), '1.0.0', true);

		wp_localize_script('event_manager_setup_js', 'wpem_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'   => wp_create_nonce('wpem_save_installation_settings_nonce')
		));
	}

	/**
	 * Create a page.
	 *
	 * @param  string $title
	 * @param  string $content
	 * @param  string $option
	 */
	public function create_page($title, $content, $option) {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => sanitize_title($title),
			'post_title'     => $title,
			'post_content'   => $content,
			'post_parent'    => 0,
			'comment_status' => 'closed',
		);
		$page_id = wp_insert_post($page_data);
		if($option) {
			update_option($option, $page_id);
		}
	}

	/**
	 * Output addons page.
	 */
	public function output() {

		$step     = ! empty( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
		$wpem_url = esc_url( get_option( 'wp_event_manager_store_url' ) );

		/*
		* STEP 3: Create pages
		*/
		if ( 3 === $step && ! empty( $_POST ) ) {

			if (
				! isset( $_REQUEST['setup_wizard'] ) ||
				! wp_verify_nonce(
					sanitize_text_field( wp_unslash( $_REQUEST['setup_wizard'] ) ),
					'step_3'
				)
			) {
				wp_die( esc_attr__( 'Error in nonce. Try again.', 'wp-event-manager' ) );
			}

			$create_pages = array();
			$page_titles  = array();

			// Sanitize create page checkboxes (keys only)
			if ( isset( $_POST['wp-event-manager-create-page'] ) && is_array( $_POST['wp-event-manager-create-page'] ) ) {
				foreach ( wp_unslash( $_POST['wp-event-manager-create-page'] ) as $key => $value ) {
					$create_pages[ sanitize_key( $key ) ] = true;
				}
			}

			// Sanitize page titles
			if ( isset( $_POST['wp-event-manager-page-title'] ) && is_array( $_POST['wp-event-manager-page-title'] ) ) {
				foreach ( wp_unslash( $_POST['wp-event-manager-page-title'] ) as $key => $value ) {
					$page_titles[ sanitize_key( $key ) ] = sanitize_text_field( $value );
				}
			}

			$pages_to_create = array(
				'submit_event_form'     => '[submit_event_form]',
				'event_dashboard'       => '[event_dashboard]',
				'events'                => '[events]',
				'submit_organizer_form' => '[submit_organizer_form]',
				'organizer_dashboard'   => '[organizer_dashboard]',
				'event_organizers'      => '[event_organizers]',
				'submit_venue_form'     => '[submit_venue_form]',
				'venue_dashboard'       => '[venue_dashboard]',
				'event_venues'          => '[event_venues]',
			);

			foreach ( $pages_to_create as $page => $content ) {
				if ( empty( $create_pages[ $page ] ) || empty( $page_titles[ $page ] ) ) {
					continue;
				}

				$this->create_page(
					$page_titles[ $page ],
					$content,
					'event_manager_' . $page . '_page_id'
				);
			}
		}
		?>

		<div class="wp_event_manager wp_event_manager_addons_wrap">
			<h2><?php esc_attr_e( 'WP Event Manager Setup', 'wp-event-manager' ); ?></h2>

			<div class="wpem-setup-wrapper">
				<ul class="wp-event-manager-setup-steps">
					<li class="<?php echo 1 === $step ? 'wp-event-manager-setup-active-step' : ''; ?>">
						<?php esc_attr_e( '1. Introduction', 'wp-event-manager' ); ?>
					</li>
					<li class="<?php echo in_array( $step, array( 2, 3 ), true ) ? 'wp-event-manager-setup-active-step' : ''; ?>">
						<?php esc_attr_e( '2. Page Setup', 'wp-event-manager' ); ?>
					</li>
					<li class="<?php echo 3 === $step ? 'wp-event-manager-setup-active-step' : ''; ?>">
						<?php esc_attr_e( '3. Done', 'wp-event-manager' ); ?>
					</li>
				</ul>

				<?php if ( 1 === $step ) : ?>
					<div class="wpem-step-window">
						<img src="<?php echo esc_url( EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-setup-welcome-image.png' ); ?>" alt="" />
						<h3><?php esc_attr_e( 'Welcome to WP Event Manager!', 'wp-event-manager' ); ?></h3>
						<p><?php esc_attr_e( 'Thank you for trusting WP Event Manager to manage your upcoming events.', 'wp-event-manager' ); ?></p>
					</div>

					<p class="submit">
						<a href="<?php echo esc_url( add_query_arg( 'step', 2 ) ); ?>" class="button button-primary">
							<?php esc_attr_e( 'Continue to page setup', 'wp-event-manager' ); ?>
						</a>
					</p>
				<?php endif; ?>

				<?php if ( 2 === $step ) : ?>
					<h3><?php esc_attr_e( 'Page Setup', 'wp-event-manager' ); ?></h3>

					<form action="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>" method="post">
						<?php wp_nonce_field( 'step_3', 'setup_wizard' ); ?>

						<table class="wp-event-manager-shortcodes widefat">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th><?php esc_attr_e( 'Page Title', 'wp-event-manager' ); ?></th>
									<th><?php esc_attr_e( 'Content Shortcode', 'wp-event-manager' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="checkbox" checked name="wp-event-manager-create-page[submit_event_form]" /></td>
									<td><input type="text" name="wp-event-manager-page-title[submit_event_form]" value="<?php echo esc_attr__( 'Post an Event', 'wp-event-manager' ); ?>" /></td>
									<td><code>[submit_event_form]</code></td>
								</tr>
							</tbody>
						</table>

						<p class="submit">
							<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Create selected pages', 'wp-event-manager' ); ?>" />
						</p>
					</form>
				<?php endif; ?>

				<?php if ( 3 === $step ) : ?>
					<h3><?php esc_attr_e( 'Setup Complete', 'wp-event-manager' ); ?></h3>
					<p><?php esc_attr_e( 'Your event manager is ready to use.', 'wp-event-manager' ); ?></p>
				<?php endif; ?>

			</div>
		</div>
	<?php
	}

	/**
	 * Sanitize a 2d array.
	 *
	 * @param  array $array
	 * @return array
	 */
	private function sanitize_array($input)	{
		if(is_array($input)) {
			foreach ($input as $k => $v) {
				$input[$k] = $this->sanitize_array($v);
			}
			return $input;
		} else {
			return sanitize_text_field($input);
		}
	}

	/**
	 * Saving settings.
	 *
	 * @param  array $array
	 * @return array
	 */
	public function wpem_save_installation_settings() {
		if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'wpem_save_installation_settings_nonce' ) ) {
			wp_send_json_error(array('message' => 'Invalid nonce'));
		}

		$date_format = isset( $_POST['date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['date_format'] ) ) : '';
		$time_format = isset( $_POST['time_format'] ) ? sanitize_text_field( wp_unslash( $_POST['time_format'] ) ) : '';

		if ( ! empty( $date_format ) ) {
			update_option('event_manager_datepicker_format', $date_format);
		}
		if ( ! empty( $time_format ) ) {
			update_option('event_manager_timepicker_format', $time_format);
		}
		wp_send_json_success(array(
			'message' => 'Settings saved successfully!',
			'redirect_url' => admin_url('index.php')
		));
	}

}
new WP_Event_Manager_Setup();
