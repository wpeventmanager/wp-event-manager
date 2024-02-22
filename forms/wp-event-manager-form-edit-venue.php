<?php
include_once('wp-event-manager-form-submit-venue.php');

/**
 * WP_Event_Manager_Form_Edit_Venue class.
 */
class WP_Event_Manager_Form_Edit_Venue extends WP_Event_Manager_Form_Submit_Venue {

	public $form_name           = 'edit-venue';

	/** @var WP_Event_Manager_Form_Edit_Venue The single instance of the class */

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
		$this->venue_id = !empty($_REQUEST['venue_id']) ? absint($_REQUEST[ 'venue_id' ]) : 0;
		if  (!event_manager_user_can_edit_event($this->venue_id)) {
			$this->venue_id = 0;
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
		$venue = get_post($this->venue_id);
		if(empty($this->venue_id ) || ($venue->post_status !== 'publish')) {
			echo  wp_kses_post(wpautop(__('Invalid listing', 'wp-event-manager')));
			return;
		}

		// Init fields
		// $this->init_fields(); We dont need to initialize with this function because of field edior
		// Now field editor function will return all the fields 
		// Get merged fields from db and default fields.
		$this->merge_with_custom_fields('frontend');
		
		// Get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format 	= WP_Event_Manager_Date_Time::get_datepicker_format();
		
		// Covert datepicker format  into php date() function date format
		$php_date_format 		= WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
		
		foreach ($this->fields as $group_key => $group_fields) {
			foreach ($group_fields as $key => $field) {
				if(!isset($this->fields[ $group_key ][ $key ]['value'])) {
					if('venue_name' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = esc_attr($venue->post_title);
					} elseif('venue_description' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_kses_post($venue->post_content);
					} elseif('venue_logo' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = has_post_thumbnail($venue->ID) ? get_post_thumbnail_id($venue->ID) : esc_url(get_post_meta($venue->ID, '_' . $key, true));
					} elseif(!empty($field['taxonomy'])) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms($venue->ID, esc_attr($field['taxonomy']), array('fields' => 'ids'));
					} else {
						$this->fields[ $group_key ][ $key ]['value'] = esc_attr(get_post_meta($venue->ID, '_' . esc_attr($key), true));
					}
				}
				if(!empty($field['type']) &&  $field['type'] == 'date'){
					$venue_date = esc_html(get_post_meta($venue->ID, '_' . $key, true));
					$this->fields[ $group_key ][ $key ]['value'] = !empty($venue_date) ? date($php_date_format ,strtotime($venue_date)) :'';
				}
			}
		}
		$this->fields = apply_filters('submit_venue_form_fields_get_venue_data', $this->fields, $venue);
		wp_enqueue_script('wp-event-manager-event-submission');

		get_event_manager_template('venue-submit.php', 
			array(
				'form'              => esc_attr($this->form_name),
				'venue_id'          => esc_attr($this->get_venue_id()),
				'action'            => esc_url($this->get_action()),
				'venue_fields'     	=> $this->get_fields('venue'),
				'step'              => esc_attr($this->get_step()),
				'submit_button_text'=> __('Save changes', 'wp-event-manager')
			),
			'wp-event-manager/venue', 
            EVENT_MANAGER_PLUGIN_DIR . '/templates/venue'
		);
	}

	/**
	 * Submit Step is posted.
	 */
	public function submit_handler() {

		if(empty($_POST['submit_venue'])) {
			return;
		}
		try {
			// Get posted values
			$values = $this->get_posted_fields();
			// Validate required
			if(is_wp_error(($return = $this->validate_fields($values)))) {
				throw new Exception($return->get_error_message());
			}
			// Update the event
			$this->save_venue($values['venue']['venue_name'], $values['venue']['venue_description'], '', $values, false);
			$this->update_venue_data($values);
			// Successful
			switch (get_post_status($this->venue_id)) {
				case 'publish' :
					echo wp_kses_post('<div class="event-manager-message wpem-alert wpem-alert-success">' . __('Your changes have been saved.', 'wp-event-manager') . ' <a href="' . get_permalink($this->venue_id) . '">' . __('View &rarr;', 'wp-event-manager') . '</a>' . '</div>');
					break;
				default :
					echo wp_kses_post('<div class="event-manager-message wpem-alert wpem-alert-success">' . __('Your changes have been saved.', 'wp-event-manager') . '</div>');
					break;
			}
		} catch (Exception $e) {
			echo wp_kses_post('<div class="event-manager-error wpem-alert wpem-alert-danger">' .  esc_html($e->getMessage()) . '</div>');
			return;
		}
	}
}