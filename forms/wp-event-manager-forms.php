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
		if(!class_exists('WP_Event_Manager_Form')) {
			include 'wp-event-manager-form-abstract.php';
		}

		// Now try to load the form_name
		$form_class  = 'WP_Event_Manager_Form_' . str_replace('-', '_', $form_name);
		$form_file   = EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-' . $form_name . '.php';			

		if(class_exists($form_class)) {
			return call_user_func(array($form_class, 'instance'));
		}
		if(!file_exists($form_file)) {
			return false;
		}
		if(!class_exists($form_class)) {
			include $form_file;
		}
		// Init the form
		return call_user_func(array($form_class, 'instance'));
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