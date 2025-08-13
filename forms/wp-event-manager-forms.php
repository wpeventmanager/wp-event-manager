<?php
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
		if(!empty($_POST['event_manager_form'])) {
			$this->load_form_class(esc_attr($_POST['event_manager_form']));
		}
	}

	/**
	 * Load a form's class.
	 *
	 * @param  string $form_name
	 * @return string class name on success, false on failure
	 */
	private function load_form_class($form_name) {
		if ( ! class_exists( 'WP_Event_Manager_Form' ) ) {
			include __DIR__ . '/wp-event-manager-form-abstract.php';
		}
		 // Normalize and strictly validate form name
    	$form_name = strtolower( sanitize_key( $form_name ) );

		// Allowed form names and their corresponding file names
		$allowed_forms = array(
			'submit-event'    => 'wp-event-manager-form-submit-event.php',
			'edit-event'      => 'wp-event-manager-form-edit-event.php',
			'submit-venue'    => 'wp-event-manager-form-submit-venue.php',
			'edit-venue'      => 'wp-event-manager-form-edit-venue.php',
			'submit-organizer'=> 'wp-event-manager-form-submit-organizer.php',
			'edit-organizer'  => 'wp-event-manager-form-edit-organizer.php',
		);

		if ( ! isset( $allowed_forms[ $form_name ] ) ) {
			return false; // Not in whitelist
		}

		$form_class = 'WP_Event_Manager_Form_' . str_replace( '-', '_', $form_name );
		$form_file  = EVENT_MANAGER_PLUGIN_DIR . '/forms/' . $allowed_forms[ $form_name ];

		// Resolve and verify the real path is inside the forms directory
		$real_forms_dir = realpath( EVENT_MANAGER_PLUGIN_DIR . '/forms/' );
		$real_file_path = realpath( $form_file );

		if ( $real_file_path === false || strpos( $real_file_path, $real_forms_dir ) !== 0 ) {
			return false; // Path traversal or file not found
		}

		if ( class_exists( $form_class ) ) {
			return call_user_func( array( $form_class, 'instance' ) );
		}

		include_once $real_file_path;

		if ( ! class_exists( $form_class ) ) {
			return false; // Class still doesn't exist after include
		}

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
			 $fields = $form->merge_with_custom_fields('frontend');
		}
		return $fields;
	}
}