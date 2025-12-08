<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'wpem-updater-functions.php';

// Only load admin functionality when in dashboard
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'wpem-updater-admin.php';
}

/**
 * WPEM_Updater.
 *
 * @version 1.0
 * @author  WPEM Team
 */
class WPEM_Updater {
	private $plugin_name = '';
	private $plugin_file = '';
	private $plugin_slug = '';
	private $errors      = array();
	private $plugin_data = array();
	// Runtime cache to prevent duplicate HTTP requests within a single page load
	private static $did_update_check = false;
	private static $update_response_cache = null;
	// Flag to ensure the update check filter is registered only once globally
	private static $filter_registered = false;

	/**
	 * Constructor, used if called directly.
	 */
	public function __construct( $file ) {
		$this->plugin_data = get_wpem_plugins_info();
		$this->init_updates( $file );
	}

	//Init the updater.
	public function init_updates( $file ) {
		$this->plugin_data = get_wpem_plugins_info();
		foreach($this->plugin_data as $plugin_info){
			register_activation_hook( $plugin_info['TextDomain'], array( $this, 'plugin_activation' ), 10 );
			register_deactivation_hook( $plugin_info['TextDomain'], array( $this, 'plugin_deactivation' ), 10 );
		}
		add_filter( 'block_local_requests', '__return_false' );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action('wpem_check_for_licence_expire', array($this, 'wpem_check_for_licence_expire'));
		add_action('wpem_set_suscription_expire_message', array($this, 'wpem_suscription_expire_message'));

		include_once( 'wpem-updater-api.php' );
		include_once( 'wpem-updater-key-api.php' );
	}

	//Ran on WP admin_init hook.
	public function admin_init() {
		global $wp_version;
		$this->load_errors();

		add_action( 'shutdown', array( $this, 'store_errors' ) );
		if ( is_admin() && current_user_can( 'update_plugins' ) && ! self::$filter_registered ) {
			add_filter('site_transient_update_plugins', array($this, 'check_for_updates'));
			self::$filter_registered = true;
		}

		if ( current_user_can( 'update_plugins' ) ) {
			$this->admin_requests();
			$this->init_key_ui();
		}
	}
	
	//Process admin requests.
	private function admin_requests() {
		foreach($this->plugin_data as $plugin_info){
			if ( !empty( $_POST[ $plugin_info['TextDomain'] . '_licence_key' ] ) ) {
				$this->activate_licence_request($plugin_info);
			} elseif ( !empty( $_GET[ 'dismiss-' . sanitize_title( $plugin_info['TextDomain'] ) ] ) ) {
				update_option( $plugin_info['TextDomain'] . '_hide_key_notice', 1 );
			} elseif ( !empty( $_GET[ 'dismiss-key-expire-' . sanitize_title( $plugin_info['TextDomain'] ) ] ) ) {
				update_option( $plugin_info['TextDomain'] . '_hide_key_expire_notice', 1 );
			} elseif ( !empty( $_GET['activated_licence'] ) && $_GET['activated_licence'] === $plugin_info['TextDomain'] ) {
				$this->add_notice( array( $this, 'activated_key_notice' ) );
			} elseif ( !empty( $_GET['deactivated_licence'] ) && $_GET['deactivated_licence'] === $plugin_info['TextDomain'] ) {
				$this->add_notice( array( $this, 'deactivated_key_notice' ) );
			} elseif ( !empty( $_GET[ $plugin_info['TextDomain'] . '_deactivate_licence' ] ) ) {
				$this->deactivate_licence_request($plugin_info);
			}
		}
	}

	//Deactivate a licence request.
	private function deactivate_licence_request($plugin_info) {
		$this->deactivate_licence($plugin_info);
		wp_redirect( remove_query_arg( array( 'activated_licence', $plugin_info['TextDomain'] . '_deactivate_licence' ), add_query_arg( 'deactivated_licence', $plugin_info['TextDomain'] ) ) );
		exit;
	}
	
	//Activate a licence request.
	private function activate_licence_request($plugin_info) {
		if ( $this->activate_licence( $plugin_info ) ) {
			wp_redirect( remove_query_arg( array( 'deactivated_licence', $plugin_info['TextDomain'] . '_deactivate_licence' ), add_query_arg( 'activated_licence', $plugin_info['TextDomain'] ) ) );
			exit;
		} else {
			wp_redirect( remove_query_arg( array( 'activated_licence', 'deactivated_licence', $plugin_info['TextDomain'] . '_deactivate_licence' ) ) );
			exit;
		}
	}
	
	//Init keys UI.
	private function init_key_ui() {
		foreach($this->plugin_data as $plugin_info){
			$licence_key = get_option( $plugin_info['TextDomain'] . '_licence_key' );
			$email       = get_option( $plugin_info['TextDomain'] . '_email' );
			if ( ! $licence_key ) {
				add_filter( 'plugin_action_links_' . $plugin_info['TextDomain'].'/'.$plugin_info['TextDomain'].'.php', array( $this, 'activation_links' ) );
				$this->add_notice( array( $this, 'key_notice' ) );
			} else {
				add_action( 'after_plugin_row_' . $plugin_info['TextDomain'], array( $this, 'multisite_updates' ), 10, 2 );
				add_filter( 'plugin_action_links_' . $plugin_info['TextDomain'].'/'.$plugin_info['TextDomain'].'.php', array( $this, 'deactivation_links' ) );
			}
		}
		$this->add_notice( array( $this, 'error_notices' ) );
		
	}

    //Add notices.
	private function add_notice( $callback ) {
		add_action( 'admin_notices', $callback );
		add_action( 'network_admin_notices', $callback );
	}

	/**
	 * Add an error message.
	 *
	 * @param string $message Your error message
	 * @param string $type    Type of error message
	 */
	public function add_error( $message, $type = '' ) {
		if ( $type ) {
			$this->errors[ $type ] = $message;
		} else {
			$this->errors[] = $message;
		}
	}

	//Load errors from option.
	public function load_errors() {
		foreach($this->plugin_data as $plugin_info){
			$this->errors = get_option( $plugin_info['TextDomain'] . '_errors', array() );
		}
	}

	//Store errors in option.
	public function store_errors() {
		foreach($this->plugin_data as $plugin_info){
			if ( sizeof( $this->errors ) > 0 ) {
				update_option( $plugin_info['TextDomain'] . '_errors', $this->errors );
			} else {
				delete_option( $plugin_info['TextDomain'] . '_errors' );
			}
		}
	}

	//Output errors.
	public function error_notices() {
		if ( !empty( $this->errors ) ) {
			foreach ( $this->errors as $key => $error ) {
				include plugin_dir_path( __FILE__ ) . 'templates/error-notice.php';
				if ( $key !== 'invalid_key' ) {
					unset( $this->errors[ $key ] );
				}
			}
		}
		foreach($this->plugin_data as $plugin_info){
			if  (! get_option( $plugin_info['TextDomain'] . '_hide_key_expire_notice' ) && ( get_option( $plugin_info['TextDomain'] . '_key_expire') || get_option( $plugin_info['TextDomain'] . '_key_expire_pre' ))) {
				$plugin_name = $plugin_info['Name'];
				$plugin_slug = $plugin_info['TextDomain'];
				include plugin_dir_path( __FILE__ ) . 'templates/key-expire-notice.php';
			}
		}
	}

	//Ran on plugin-activation.
	public function plugin_activation() {
		$plugin_slug = dirname( plugin_basename( __FILE__ ) );

        // Log or use the plugin slug
		delete_option( $this->plugin_slug . '_hide_key_notice' );
		delete_option( $this->plugin_slug . '_hide_key_expire_notice' );
	}

	//Ran on plugin-deactivation.
	public function plugin_deactivation() {
		$plugin_slug = dirname( plugin_basename( __FILE__ ) );

        // Log or use the plugin slug
		$this->deactivate_licence();
	}

	//Try to activate a licence.
	public function activate_licence( $plugin_info ) {
		$licence_key = sanitize_text_field( $_POST[ $plugin_info['TextDomain'] . '_licence_key' ] );
		$email       = sanitize_text_field( $_POST[ $plugin_info['TextDomain'] . '_email' ] );

		try {
			if ( empty( $licence_key ) ) {
				throw new Exception( 'Please enter your licence key' );
			}

			if ( empty( $email ) ) {
				throw new Exception( 'Please enter the email address associated with your licence' );
			}

			$activate_results = json_decode( WPEM_Updater_Key_API::activate( array(
				'email'          => $email,
				'licence_key'    => $licence_key,
				'api_product_id' => $plugin_info['TextDomain']
			) ), true );

			if ( !empty( $activate_results['activated'] ) ) {
				$this->errors           = array();

				update_option( $plugin_info['TextDomain'] . '_licence_key', $licence_key );
				update_option( $plugin_info['TextDomain'] . '_email', $email );
				update_option( $plugin_info['TextDomain'] . '_licence_key_activate', 1 );
				update_option( $plugin_info['TextDomain'] . '_licence_expired', $activate_results['activations']['date_expires'] );
				delete_option( $plugin_info['TextDomain'] . '_errors' );
				delete_option( $plugin_info['TextDomain'] . '_key_expire_pre' );
				delete_option( $plugin_info['TextDomain'] . '_key_expire' );
				
				// Clear update check cache when license is activated
				if ( function_exists( 'delete_transient' ) ) {
					delete_transient( 'wpem_bulk_plugin_update_check' );
				}

				$subscription_expire_date = $activate_results['activations']['date_expires'];
				// Hook for registering cron job
				$activation_detail['expire_date'] = $subscription_expire_date;
				$activation_detail['licence_key'] = $licence_key;
				$activation_detail['email'] = $email;
				$activation_detail['api_product_ids'] = array( $activate_results['activations']['api_product_id'] );
				wp_schedule_single_event(strtotime($subscription_expire_date) - (48 * 60 * 60), 'wpem_set_suscription_expire_message', array($activation_detail));

				$this->update_cron_if_expire_date_exists($subscription_expire_date, $activate_results['activations']['api_product_id'], $activation_detail);
				
				return true;
			} elseif ( $activate_results === false ) {
				throw new Exception( 'Connection failed to the Licence Key API server. Try again later.' );
			} elseif ( isset( $activate_results['error_code'] ) ) {
				throw new Exception( $activate_results['error'] );
			}

		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return false;
		}
	}

	//Deactivate a licence.
	public function deactivate_licence($plugin_info) {
		$licence_key = get_option(  $plugin_info['TextDomain'] . '_licence_key', true );
		$reset = WPEM_Updater_Key_API::deactivate( array(
				'api_product_id' => $plugin_info['TextDomain'],
				'licence_key'    => $licence_key,
		) );                                                                    

		delete_option( $plugin_info['TextDomain'] . '_licence_key' );
		delete_option( $plugin_info['TextDomain'] . '_email' );
		delete_option( $plugin_info['TextDomain'] . '_licence_key_activate' );
		delete_option( $plugin_info['TextDomain'] . '_errors' );
		delete_option( $plugin_info['TextDomain'] . '_key_expire' );
		delete_option( $plugin_info['TextDomain'] . '_key_expire_pre' );
		delete_site_transient( 'update_plugins' );
		
		// Clear update check cache when license is deactivated
		if ( function_exists( 'delete_transient' ) ) {
			delete_transient( 'wpem_bulk_plugin_update_check' );
		}
		
		$this->errors           = array();
		$api_key          = '';
		$activation_email = '';
		//redirect on license page
		return true;
	}

	//Activation links.
	public function activation_links( $links ) {
		$links[] = '<a href="' . add_query_arg( array('post_type' => 'event_listing', 'page' => 'wpem_license'), admin_url( 'edit.php' ) ) . '">' . __('Activate licence', 'wp-event-manager') . '</a>';
		return $links;
	}

	//Deactivation links.
	public function deactivation_links( $links ) {
		// Check if the 'Deactivate' link exists
		if (isset($links['deactivate'])) {
			// Extract the URL from the existing 'Deactivate' link
			$deactivate_link = $links['deactivate'];
	
			// Use a regular expression to capture the plugin slug from the 'plugin' parameter
			if (preg_match('/plugin=([^%]+)%2F/', $deactivate_link, $matches)) {
				// The plugin slug is in $matches[1]
				$plugin_slug = $matches[1];
	
				// Create the dynamic 'Deactivate Licence' link based on the plugin slug
				$links[] = '<a href="' . admin_url('plugins.php?' . $plugin_slug . '_deactivate_licence=1') . '">Deactivate Licence</a>';
			}
		}
		return $links;
	}

	//Show a notice prompting the user to update.
	public function key_notice() {
		foreach ($this->plugin_data as $plugin) {
			if (  sizeof( $this->errors ) === 0 && (! get_option( $plugin['TextDomain'] . '_hide_key_notice' ) && ! get_option( $plugin['TextDomain'] . '_licence_key' ))) {
				include plugin_dir_path( __FILE__ ) . 'templates/key-notice.php';
			}
		}
	}

	//Activation success notice.
	public function activated_key_notice() {
		$plugin_name = '';
		$plugin_slug = $_GET['activated_licence'];
		foreach ($this->plugin_data as $plugin) {
			if ($plugin['TextDomain'] === $plugin_slug) {
				$plugin_name = $plugin['Name'];
				break;
			}
		}
		include plugin_dir_path( __FILE__ ) . 'templates/activated-key.php';
	}

	//Dectivation success notice.
	public function deactivated_key_notice() {
		$plugin_name = '';
		$plugin_slug = $_GET['deactivated_licence'];
		foreach ($this->plugin_data as $plugin) {
			if ($plugin['TextDomain'] === $plugin_slug) {
				$plugin_name = $plugin['Name'];
				break;
			}
		}
		include plugin_dir_path( __FILE__ ) . 'templates/deactivated-key.php';
	}

	//Check for plugin updates.
	public function check_for_updates( $check_for_updates_data ) {
		if ( empty( $check_for_updates_data->checked ) ) {
			return $check_for_updates_data;
		}
		// If we've already performed an update check during this request, reuse cached response
		if ( self::$did_update_check ) {

			if ( is_object( self::$update_response_cache ) ) {
				$response = self::$update_response_cache;
				foreach ($this->plugin_data as $plugin_info) {
					$plugin_slug = $plugin_info['TextDomain'];
					// Check if property exists and is an array or object
		    		if (isset($response->$plugin_slug) && isset($response->$plugin_slug['new_version'])) {
						$new_version = $response->$plugin_slug['new_version'];
						if(isset($new_version)){
							if (isset($check_for_updates_data->checked[$plugin_info['plugin_files']]) && version_compare( $new_version, $plugin_info['Version'], '>' ) ) {
								$response->$plugin_slug['plugin'] = $plugin_info['plugin_files'];
								$check_for_updates_data->response[ $plugin_info['plugin_files'] ] = (object)$response->$plugin_slug;
							}
						}
					}
				}
			}
			return $check_for_updates_data;
		}
		
		$plugin_names = array();
		$plugin_slugs = array();
		$plugin_licenses = array();
		$plugin_files = array();
		$plugin_emails = array();
		$plugin_versions = array();
		if(!empty($this->plugin_data)){
			foreach($this->plugin_data as $plugin_info){
				$licence_key = get_option(  $plugin_info['TextDomain'] . '_licence_key', true );
				$email       = get_option(  $plugin_info['TextDomain'] . '_email', true );
				if ( !empty($licence_key) && !empty($email) ) {
					array_push($plugin_names,  $plugin_info['Name']);
					array_push($plugin_slugs,  $plugin_info['TextDomain']);
					array_push($plugin_files,  $plugin_info['plugin_files']);
					array_push($plugin_versions,  $plugin_info['Version']);
					array_push($plugin_emails,  $email);
					array_push($plugin_licenses,  $licence_key);
				}
			}
			// Set version variables.
			$response = $this->get_plugin_version($plugin_names, $plugin_slugs, $plugin_licenses, $plugin_emails, $plugin_versions);
			// Cache the response for subsequent calls in this request
			if ( isset($response) && is_object( $response ) ) {
				self::$update_response_cache = $response;
			}
		}
		// Mark that we've performed the update check
		self::$did_update_check = true;
		
		if(isset($response) && !empty($response) && is_object($response)){
			foreach ($this->plugin_data as $plugin_info) {
				$plugin_slug = $plugin_info['TextDomain'];
				// Check if property exists and is an array or object
    			if (isset($response->$plugin_slug) && isset($response->$plugin_slug['new_version'])) {
					$new_version = $response->$plugin_slug['new_version'];
					if(isset($new_version)){
						if (isset($check_for_updates_data->checked[$plugin_info['plugin_files']]) && version_compare( $new_version, $plugin_info['Version'], '>' ) ) {
							$response->$plugin_slug['plugin'] = $plugin_info['plugin_files'];
							$check_for_updates_data->response[ $plugin_info['plugin_files'] ] = (object)$response->$plugin_slug;
						}
					}
				}
			}
		}
		
		// Reset the flag to allow future update checks
		$update_check_running = false;
		
		return $check_for_updates_data;	
	}

	//Take over the Plugin info screen.
	public function plugins_api( $default, $action, $args ) {
		global $wp_version;
		if(!empty($this->plugin_data)){
			foreach($this->plugin_data as $plugin_info){
				$licence_key = get_option(  $plugin_info['TextDomain'] . '_licence_key', true );
				$email       = get_option(  $plugin_info['TextDomain'] . '_email', true );
				if ( ! $licence_key ) {
					return $default;
				}

				if ( ! isset( $args->slug ) || ( $args->slug !== $plugin_info['TextDomain'] ) ) {
					return $default;
				}

				if ( $response = $this->get_plugin_info() ) {
					return $response;
				}
			}
		} else {
			return $default;
		}		
	}

	/**
	 * Get plugin version info from API.
	 * @return array|bool
	 */
	public function get_plugin_version($plugin_names, $plugin_slugs, $plugin_licenses, $plugin_emails, $plugin_versions) {
		$response = WPEM_Updater_API::plugin_update_check( array(
			'plugin_name'    => $plugin_names,
			'version'        => $plugin_versions,
			'api_product_id' => $plugin_slugs,
			'licence_key'    => $plugin_licenses,
			'email'          => $plugin_emails
		) );
		if ( isset( $response->errors ) ) {
			delete_option( $this->plugin_slug . '_licence_key_activate' );
			$this->handle_errors( $response->errors );
		}

		// Set version variables.
		if ( isset( $response ) && is_object( $response ) && $response !== false ) {
			return $response;
		}

		return false;
	}

	/**
	 * Get plugin info from API.
	 * @return array|bool
	 */
	public function get_plugin_info() {
		$response = WPEM_Updater_API::plugin_information( array(
			'plugin_name'    => $this->plugin_name,
			'version'        => $this->plugin_data['Version'],
			'api_product_id' => $this->plugin_slug,
			'licence_key'    => $this->api_key,
			'email'          => $this->activation_email
		) );

		if ( isset( $response->errors ) ) {
			$this->handle_errors( $response->errors );
		}

		// If everything is okay return the $response.
		if ( isset( $response ) && is_object( $response ) && $response !== false ) {
			return $response;
		}

		return false;
	}

	/**
	 * Handle errors from the API.
	 * @param  array $errors
	 */
	public function handle_errors( $errors ) {
		if ( !empty( $errors['no_key'] ) ) {
			// translators: 1: plugin name, 2: plugin name (repeated for setup)
			$this->add_error( sprintf( __('A licence key for could not be found. Maybe you forgot to enter a licence key when setting up.', 'wp-event-manager')) );
		} elseif ( !empty( $errors['invalid_request'] ) ) {
			$this->add_error( 'Invalid update request' );
		} elseif ( !empty( $errors['invalid_key'] ) ) {
			$this->add_error( $errors['invalid_key'], 'invalid_key' );
		} elseif ( !empty( $errors['no_activation'] ) ) {
			// $this->deactivate_licence();
			$this->add_error( $errors['no_activation'] );
		}
	}

 	/**
     * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
     *
     * Based on code by Pippin Williamson.
     *
     * @param string  $file
     * @param array   $plugin
     */
    public function multisite_updates( $file, $plugin ) {
        if ( ! is_multisite() || is_network_admin() ) {
            return;
		}

		// Remove our filter on the site transient.
		remove_filter( 'site_transient_update_plugins', array( $this, 'check_for_updates' ) );

		$update_cache = get_site_transient( 'update_plugins' );

		// Check if we have no version info, or every hour.
		if ( empty( $update_cache->response ) || empty( $update_cache->response[ $this->plugin_name ] ) || empty( $update_cache->last_checked ) || $update_cache->last_checked < strtotime( '-1 hour' ) ) {
			// Get plugin version info.
			if ( $version_info = $this->get_plugin_version() ) {
				//if ( version_compare( $this->plugin_data['Version'], $version_info->new_version, '<' ) ) {
				$update_cache->response[ $this->plugin_name ] = $version_info;
				//}
				$update_cache->last_checked                  = time();
				$update_cache->checked[ $this->plugin_name ] = $this->plugin_data['Version'];

				set_site_transient( 'update_plugins', $update_cache );
			}
		} else {
			$version_info = $update_cache->response[ $this->plugin_name ];
		}

		// Restore our filter.
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );

        if ( !empty( $version_info->new_version ) && version_compare( $this->plugin_data['Version'], $version_info->new_version, '<' ) ) {

			$wp_list_table  = _get_list_table( 'WP_Plugins_List_Table' );
			$changelog_link = network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $this->plugin_name . '&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=597' );
			include plugin_dir_path( __FILE__ ) . 'templates/ms-update.php';
        }
    }

	/**
	 * This function is used to check license key is expired or not
	 */
	public function wpem_check_for_licence_expire($activation_detail){
		$args = array();
		$endpoint = esc_url( get_option( 'wp_event_manager_store_url' ) .'?wc-api=wpemstore_licensing_update_api');
		$activation_detail = apply_filters('wpem_cron_default_activation_args', $activation_detail);
		$defaults = array(
			'request'  => 'checklicenceexpire',
			'instance' => site_url(),
			'activation_detail' => $activation_detail,
		);
		$args    = wp_parse_args( $args, $defaults );
		$request = wp_remote_get( $endpoint . '&' . http_build_query( $args, '', '&' ) );
		
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return false;
		} else {
			$response_data =  wp_remote_retrieve_body( $request );
			$response_data = json_decode($response_data, true);
			if(isset($response_data['errors'])){
				foreach($activation_detail['api_product_ids'] as $plugin){
					update_option( $plugin . '_key_expire', 'key_expire' );
					delete_option( $plugin . '_licence_key_activate' );
					delete_option( $plugin . '_licence_expired' );
					delete_option( $plugin . '_key_expire_pre' );
				}
			} else {
				$subscription_expire_date = $response_data['date_expires'];
				$activation_detail['expire_date'] = $subscription_expire_date;
				wp_schedule_single_event(strtotime($subscription_expire_date), 'wpem_check_for_licence_expire', array($activation_detail));
			}
			return $response_data;
		}
	}
	/**
	 * This function is used to show subscription expire notice
	 */
	public function wpem_suscription_expire_message($activation_detail){
		foreach($activation_detail['api_product_ids'] as $plugin){
			update_option( $plugin . '_key_expire_pre', $plugin );
		}
	}

	/**
	 * This function is used to set/update cron based on plugin subscription expire date
	 * @since 1.0.7
	 */
	public function update_cron_if_expire_date_exists($new_expire_date, $new_api_product_ids, $new_args) {
    
		// Check if the cron job with the same expire_date exists
		$scheduled_jobs = _get_cron_array();
		
		// Iterate through scheduled jobs to check for already existing 
		foreach ($scheduled_jobs as $timestamp => $cron) {
			foreach ($cron as $hook => $details) {
				
				// Look for the specific hook 'wpem_check_for_licence_expire'
				if ($hook === 'wpem_check_for_licence_expire') {
					
					// Iterate through all the jobs with this hook
					foreach ($details as $key => $job) {
						// Check if the expire_date matches
						if (isset($job['args'][0]['expire_date']) && $job['args'][0]['expire_date'] === $new_expire_date) {
							
							$product_ids = array();
							
							if(!is_array($job['args'][0]['api_product_ids'])) {
								array_push($product_ids, $job['args'][0]['api_product_ids']);
							} else {
								foreach($job['args'][0]['api_product_ids'] as $product_id){
									array_push($product_ids, $product_id);
								}
							}
							// Remove the existing cron job with the matching expire_date
							wp_unschedule_event($timestamp, $hook, $job['args']);
							array_push($product_ids, $new_api_product_ids);
							// Update the api_product_ids
							$updated_args = $job['args'][0]; // Copy existing arguments
							$updated_args['api_product_ids'] = $product_ids; // Update the product IDs
							// Schedule a new cron job with the updated parameters
							wp_schedule_single_event(time(), 'wpem_check_for_licence_expire', array($updated_args));
							return;
						}
					}
				}
			}
		}
		// If no matching cron job is found, schedule a new one
		wp_schedule_single_event(time(), 'wpem_check_for_licence_expire', array($new_args));
		return;
	}
}
