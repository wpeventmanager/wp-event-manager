<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
include_once('wp-event-manager-form-submit-event.php');

/**
 * WP_Event_Manager_Form_Edit_Event class.
 */
class WP_Event_Manager_Form_Edit_Event extends WP_Event_Manager_Form_Submit_Event {

	public $form_name           = 'edit-event';
	public $event_id;

	/** @var WP_Event_Manager_Form_Edit_Event The single instance of the class */

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
		$this->event_id = !empty($_REQUEST['event_id']) ? absint(wp_unslash($_REQUEST[ 'event_id' ])) : 0;
		if  (!event_manager_user_can_edit_event($this->event_id)) {
			$this->event_id = 0;
		}
		add_filter('submit_event_form_fields', array($this,'add_event_thumbnail_field'));
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
		$event = get_post($this->event_id);
		if(empty($this->event_id ) || ($event->post_status !== 'publish' && !event_manager_user_can_edit_pending_submissions())) {
			echo wp_kses_post(wpautop(__('Invalid listing', 'wp-event-manager')));
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
					if('event_title' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = esc_attr($event->post_title);
					} elseif('event_description' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_kses_post($event->post_content);
					} elseif('organizer_logo' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = has_post_thumbnail($event->ID) ? get_post_thumbnail_id($event->ID) : esc_url(get_post_meta($event->ID, '_' . $key, true));
					} elseif('event_start_date' === $key) {
						$event_start_date = esc_html(get_post_meta($event->ID, '_' . $key, true));
        				// Convert date and time value into selected datepicker value
						$this->fields[ $group_key ][ $key ]['value'] = gmdate($php_date_format ,strtotime($event_start_date));
					} elseif('event_end_date' === $key) {
						$event_end_date = esc_html(get_post_meta($event->ID, '_' . $key, true));
        				// Convert date and time value into selected datepicker value
						$this->fields[ $group_key ][ $key ]['value'] = gmdate($php_date_format ,strtotime($event_end_date));
					} elseif('event_organizer_ids' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = get_post_meta($event->ID, '_' . $key, true);
					} elseif('event_venue_ids' === $key) {
						$this->fields[ $group_key ][ $key ]['value'] = get_post_meta($event->ID, '_' . $key, true);
					} elseif(!empty($field['taxonomy'])) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms($event->ID, $field['taxonomy'], array('fields' => 'ids'));
					} else {
						$this->fields[ $group_key ][ $key ]['value'] = get_post_meta($event->ID, '_' . stripslashes($key), true);
					}
				}
				if(!empty($field['type']) &&  $field['type'] == 'date'){
					$event_date = esc_html(get_post_meta($event->ID, '_' . stripslashes($key), true));
					$this->fields[ $group_key ][ $key ]['value'] = !empty($event_date) ? gmdate($php_date_format ,strtotime($event_date)) :'';
				}
				if(!empty($field['type']) &&  $field['type'] == 'button'){
					if(isset($this->fields[ $group_key ][ $key ]['value']) && empty($this->fields[ $group_key ][ $key ]['value'])) {
						$this->fields[ $group_key ][ $key ]['value'] = esc_attr($field['placeholder']);
					}
				}
			}
		}
		
		$this->fields = apply_filters('submit_event_form_fields_get_event_data', $this->fields, $event);
		wp_enqueue_script('wp-event-manager-event-submission');
		get_event_manager_template('event-submit.php', array(
			'form'               => esc_attr($this->form_name),
			'event_id'           => esc_attr($this->get_event_id()),
			'action'             => esc_url($this->get_action()),
			'event_fields'       => $this->get_fields('event'),
			'organizer_fields'   => $this->get_fields('organizer'),
			'venue_fields'       => $this->get_fields('venue'),
			'step'               => esc_attr($this->get_step()),
			'submit_button_text' => __('Save changes', 'wp-event-manager')
		));
	}

	/**
	 * Submit Step is posted.
	 */
	public function submit_handler() {
		if(empty($_POST['submit_event'])) {
			return;
		}
		try {
			// Get posted values
			$values = $this->get_posted_fields();

			// Validate required
			if(is_wp_error(($return = $this->validate_fields($values)))) {
				throw new Exception($return->get_error_message());
			}
			$event_title       = html_entity_decode( $values['event']['event_title'] );
			$event_description = html_entity_decode( $values['event']['event_description'] );
			$event_title       = wp_strip_all_tags( $event_title );

			$this->save_event( $event_title, $event_description, $this->event_id ? '' : 'preview', $values );
			$this->update_event_data( $values );

			// Successful
			switch (get_post_status($this->event_id)) {
				case 'publish':
					$custom_message = get_option('wpem_event_updated_message');
					if (empty($custom_message)) {
						$custom_message = __('Your changes have been saved.', 'wp-event-manager') . ' <a href="' . esc_url(get_permalink($this->event_id)) . '">' . __('View &rarr;', 'wp-event-manager') . '</a>';
					} else {
						$custom_message = $custom_message . ' <a href="' . esc_url(get_permalink($this->event_id)) . '">' . __('View &rarr;', 'wp-event-manager') . '</a>';
					}
					echo wp_kses_post('<div class="event-manager-message wpem-alert wpem-alert-success">' . $custom_message . '</div>');
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