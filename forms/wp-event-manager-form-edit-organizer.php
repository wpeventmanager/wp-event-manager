<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
include_once('wp-event-manager-form-submit-organizer.php');

/**
 * WPEM_Event_Manager_Form_Edit_Event class.
 */
class WPEM_Event_Manager_Form_Edit_Organizer extends WPEM_Event_Manager_Form_Submit_Organizer {

	public $form_name = 'edit-organizer';
	public $organizer_id;

	/** @var WPEM_Event_Manager_Form_Edit_Organizer The single instance of the class */

	protected static $_instance = null;

	/**
	 * Main Instance.
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
		// IMPORTANT: call parent constructor
		parent::__construct();

		// Override form name
		$this->form_name = 'edit-organizer';

		// Get organizer ID
		$this->organizer_id = ! empty($_REQUEST['organizer_id']) ? absint(wp_unslash($_REQUEST['organizer_id'])) : 0;

		// Permission check
		if (!event_manager_user_can_edit_organizer($this->organizer_id)) {
			$this->organizer_id = 0;
		}
	}

	/**
	 * output function.
	*/
	public function output($atts = array()) {
		$this->submit_handler();
		$this->submit();
	}

	/**
	 * Submit Step.
	 */
	public function submit() {
		$organizer = get_post($this->organizer_id);

		if(empty($this->organizer_id ) || ($organizer->post_status !== 'publish')) {
			echo wp_kses_post(wpautop(__('Invalid listing', 'wp-event-manager')));
			return;
		}

		// Init fields
		// $this->init_fields(); We dont need to initialize with this function because of field edior
		// Now field editor function will return all the fields 
		// Get merged fields from db and default fields.
		$this->wpem_merge_with_custom_fields('frontend');
		
		// Get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format 	= WP_Event_Manager_Date_Time::get_datepicker_format();
		
		// Covert datepicker format  into php date() function date format
		$php_date_format 		= WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
		
		foreach ($this->fields as $group_key => $group_fields) {
			foreach ($group_fields as $key => $field) {
				if(!isset($this->fields[ $group_key ][ $key ]['value'])) {
					if('organizer_name' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = esc_attr($organizer->post_title);
					} elseif('organizer_description' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_kses_post($organizer->post_content);
					} elseif('organizer_logo' === $key) {
						/*$this->fields[ $group_key ][ $key ]['value'] = has_post_thumbnail($organizer->ID) ? get_post_thumbnail_id($organizer->ID) : esc_url(get_post_meta($organizer->ID, '_' . $key, true));*/
						$this->fields[ $group_key ][ $key ]['value'] = get_post_meta($organizer->ID, '_' . $key, true);
						
					} elseif(!empty($field['taxonomy'])) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms($organizer->ID, esc_attr($field['taxonomy']), array('fields' => 'ids'));
					} else {
						$this->fields[ $group_key ][ $key ]['value'] = esc_attr(get_post_meta($organizer->ID, '_' . esc_attr($key), true));
					}
				}
				if(!empty($field['type']) &&  $field['type'] == 'date'){
					$organizer_date = esc_html(get_post_meta($organizer->ID, '_' . $key, true));
					$this->fields[ $group_key ][ $key ]['value'] = !empty($organizer_date) ? gmdate($php_date_format ,strtotime($organizer_date)) :'';
				}
			}
		}
		$this->fields = apply_filters('submit_organizer_form_fields_get_organizer_data', $this->fields, $organizer);
		wp_enqueue_script('wp-event-manager-event-submission');
		
		// Show success message if the form was just submitted
		if (isset($_GET['updated']) && $_GET['updated'] === 'true') {
			$this->add_message(__('Your changes have been saved.', 'wp-event-manager'));
		}
		
		wpem_get_event_manager_template('organizer-submit.php', 
			array(
				'form'               	=> esc_attr($this->form_name),
				'organizer_id'          => esc_attr($this->get_organizer_id()),
				'action'             	=> esc_url($this->get_action()),
				'organizer_fields'     	=> $this->get_fields('organizer'),
				'step'               	=> esc_attr($this->get_step()),
				'submit_button_text' 	=> __('Save changes', 'wp-event-manager')
			),
			'wp-event-manager/organizer', 
            EVENT_MANAGER_PLUGIN_DIR . '/templates/organizer'
		);
	}

	/**
	 * Submit Step is posted.
	 */
	public function submit_handler() {
		// Check if form was submitted
		if(empty($_POST['submit_organizer'])) {
			return;
		}
		
		// Verify nonce before processing form data
		if (empty($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['_wpnonce'])), 'edit-organizer_' . $this->organizer_id)) {
			$this->add_error(__('Security check failed. Please try again.', 'wp-event-manager'));
			return;
		}
		
		try {
			// Get posted values
			$values = $this->get_posted_fields();

			// Validate required
			if(is_wp_error(($return = $this->validate_fields($values)))) {
				throw new Exception($return->get_error_message());
			}
			
			// Update the organizer
			$organizer_name        = html_entity_decode($values['organizer']['organizer_name']);
			$organizer_description = html_entity_decode($values['organizer']['organizer_description']);

			$organizer_name = wp_strip_all_tags($organizer_name);

			$this->save_organizer($organizer_name, $organizer_description, '', $values, false);

			$this->update_organizer_data($values);

			// Clear any previous messages to avoid duplicates
			if (!empty($this->errors)) {
				$this->errors = array();
			}
			
			// Add success message
			// $this->add_message(__('Your changes have been saved.', 'wp-event-manager'));
			
			// Redirect to prevent form resubmission
			$redirect_url = remove_query_arg('updated', $this->get_action());
			$redirect_url = add_query_arg('updated', 'true', $redirect_url);
			
			if (!headers_sent()) {
				wp_safe_redirect($redirect_url);
				exit;
			}
		} catch (Exception $e) {
			$this->add_error($e->getMessage());
		}
	}
}