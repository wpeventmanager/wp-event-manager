<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * WP_Event_Manager_Forms class.
 */
class WP_Event_Manager_Forms {

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  2.5
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  2.5
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('init', array($this, 'load_posted_form'));
	}

	/**
	 * If a form was posted, load its class so that it can be processed before display.
	 */
	public function load_posted_form() {
		if ( ! empty( $_POST['event_manager_form'] ) && ! empty( $_POST['_wpnonce'] ) ) {
			$form_name = sanitize_text_field( wp_unslash( $_POST['event_manager_form'] ) );
			$nonce_verified = false;
			
			// Verify nonce based on form type
			switch ( $form_name ) {
				case 'submit-event':
				case 'edit-event':
					$event_id = ! empty( $_POST['event_id'] ) ? absint( wp_unslash( $_POST['event_id'] ) ) : 0;
					$nonce_verified = wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'edit-event_' . $event_id );
					break;
				case 'submit-organizer':
				case 'edit-organizer':
					$organizer_id = ! empty( $_POST['organizer_id'] ) ? absint( wp_unslash( $_POST['organizer_id'] ) ) : 0;
					$nonce_verified = wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'edit-organizer_' . $organizer_id );
					break;
				case 'submit-venue':
				case 'edit-venue':
					$venue_id = ! empty( $_POST['venue_id'] ) ? absint( wp_unslash( $_POST['venue_id'] ) ) : 0;
					$nonce_verified = wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'edit-venue_' . $venue_id );
					break;
			}
			
			if ( $nonce_verified ) {
				$this->load_form_class( $form_name );
			}
		}
	}

	/**
	 * Load a form's class.
	 *
	 * @param  string $form_name
	 * @return string class name on success, false on failure
	 */
	private function load_form_class( $form_name ) {
		// Ensure abstract base is loaded from the same directory as this file
		if ( ! class_exists( 'WP_Event_Manager_Form' ) ) {
			include __DIR__ . '/wp-event-manager-form-abstract.php';
		}

		// Normalize and strictly validate the requested form key
		$form_name = strtolower( sanitize_key( $form_name ) );

		// Strict allowlist: logical key => fixed filename (no user-controlled concatenation)
		$allowed_forms = array(
			'submit-event'     => 'wp-event-manager-form-submit-event.php',
			'edit-event'       => 'wp-event-manager-form-edit-event.php',
			'submit-venue'     => 'wp-event-manager-form-submit-venue.php',
			'edit-venue'       => 'wp-event-manager-form-edit-venue.php',
			'submit-organizer' => 'wp-event-manager-form-submit-organizer.php',
			'edit-organizer'   => 'wp-event-manager-form-edit-organizer.php',
		);

		if ( ! isset( $allowed_forms[ $form_name ] ) ) {
			return false; // Not whitelisted
		}

		// Build class and target file strictly from the map above
		$form_class = 'WP_Event_Manager_Form_' . str_replace( '-', '_', $form_name );

		$forms_dir      = EVENT_MANAGER_PLUGIN_DIR . '/forms/';
		$real_forms_dir = realpath( $forms_dir );
		$target_file    = $forms_dir . $allowed_forms[ $form_name ];
		$real_file_path = realpath( $target_file );

		// Verify the resolved file exists and is inside the /forms directory
		if ( false === $real_file_path || 0 !== strpos( $real_file_path, $real_forms_dir ) ) {
			return false;
		}

		// Load class file if needed
		if ( ! class_exists( $form_class ) ) {
			include_once $real_file_path;
		}

		// Safety: ensure class exists and exposes instance()
		if ( ! class_exists( $form_class ) || ! is_callable( array( $form_class, 'instance' ) ) ) {
			return false;
		}

		// Instantiate
		return call_user_func( array( $form_class, 'instance' ) );
	}

	/**
	 * Gets form.
	 *
	 * @param string $form_name
	 * @param  array $atts Optional passed attributes
	 * @return string
	 */
	public function get_form($form_name, $atts = array()) {
		if($form = $this->load_form_class($form_name)) {
			ob_start();
			$form->output($atts);
			return ob_get_clean();
		}
	}

	/**
	 * Gets the fields event.
	 *
	 * @param string $key
	 * @return array
	 */
	public function get_fields($form_name) {
		if($form = $this->load_form_class($form_name)) {
			 $fields = $form->wpem_merge_with_custom_fields('frontend');
		}
		return $fields;
	}
}