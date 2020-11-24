<?php

include_once( 'wp-event-manager-form-submit-event.php' );

/**
 * WP_Event_Manager_Form_Edit_Event class.
 */

class WP_Event_Manager_Form_Edit_Event extends WP_Event_Manager_Form_Submit_Event {

	public $form_name           = 'edit-event';

	/** @var WP_Event_Manager_Form_Edit_Event The single instance of the class */

	protected static $_instance = null;

	/**
	 * Main Instance
	 */

	public static function instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}
		
		return self::$_instance;
	}

	/**
	 * Constructor
	*/
	
	public function __construct() {

		$this->event_id = ! empty( $_REQUEST['event_id'] ) ? absint( $_REQUEST[ 'event_id' ] ) : 0;

		if  ( ! event_manager_user_can_edit_event( $this->event_id ) ) {

			$this->event_id = 0;
		}
	}

	/**
	 * output function.
	*/

	public function output( $atts = array() ) {

		$this->submit_handler();

		$this->submit();
	}

	/**
	 * Submit Step
	 */

	public function submit() {

		$event = get_post( $this->event_id );

		if ( empty( $this->event_id  ) || ( $event->post_status !== 'publish' && ! event_manager_user_can_edit_pending_submissions() ) ) {

			echo wpautop( __( 'Invalid listing', 'wp-event-manager' ) );

			return;
		}

		// Init fields
		//$this->init_fields(); We dont need to initialize with this function because of field edior
		// Now field editor function will return all the fields 
		//Get merged fields from db and default fields.
		$this->merge_with_custom_fields( 'frontend' );
		
		//get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format 	= WP_Event_Manager_Date_Time::get_datepicker_format();
		
		//covert datepicker format  into php date() function date format
		$php_date_format 		= WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format( $datepicker_date_format );
		
		foreach ( $this->fields as $group_key => $group_fields ) {

			foreach ( $group_fields as $key => $field ) {

				if ( ! isset( $this->fields[ $group_key ][ $key ]['value'] ) ) {

					if ( 'event_title' === $key ) {

						$this->fields[ $group_key ][ $key ]['value'] = $event->post_title;

					} elseif ( 'event_description' === $key ) {

						$this->fields[ $group_key ][ $key ]['value'] = $event->post_content;

					} elseif ( 'organizer_logo' === $key ) {
						$this->fields[ $group_key ][ $key ]['value'] = has_post_thumbnail( $event->ID ) ? get_post_thumbnail_id( $event->ID ) : get_post_meta( $event->ID, '_' . $key, true );
						
					} elseif ( 'event_start_date' === $key ) {
						$event_start_date = get_post_meta( $event->ID, '_' . $key, true );
        				//Convert date and time value into selected datepicker value
						$this->fields[ $group_key ][ $key ]['value'] = date($php_date_format ,strtotime($event_start_date));
					} elseif('event_end_date' === $key) {
						$event_end_date = get_post_meta( $event->ID, '_' . $key, true );
        				//Convert date and time value into selected datepicker value
						$this->fields[ $group_key ][ $key ]['value'] = date($php_date_format ,strtotime($event_end_date));
					} elseif ( ! empty( $field['taxonomy'] ) ) {

						$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $event->ID, $field['taxonomy'], array( 'fields' => 'ids' ) );

					} else {

						$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $event->ID, '_' . $key, true );
					}
				}
				if(! empty( $field['type'] ) &&  $field['type'] == 'date'){
					$event_date = get_post_meta( $event->ID, '_' . $key, true );
					$this->fields[ $group_key ][ $key ]['value'] = !empty($event_date) ? date($php_date_format ,strtotime($event_date) ) :'';
				}
				if(! empty( $field['type'] ) &&  $field['type'] == 'button'){
					if(isset($this->fields[ $group_key ][ $key ]['value']) && empty($this->fields[ $group_key ][ $key ]['value']))
					{
						$this->fields[ $group_key ][ $key ]['value'] = $field['placeholder'];
					}
				}
			}
		}

		$this->fields = apply_filters( 'submit_event_form_fields_get_event_data', $this->fields, $event );

		

		wp_enqueue_script( 'wp-event-manager-event-submission' );

		get_event_manager_template( 'event-submit.php', array(

			'form'               => $this->form_name,

			'event_id'             => $this->get_event_id(),

			'action'             => $this->get_action(),

			'event_fields'         => $this->get_fields( 'event' ),

			'organizer_fields'     => $this->get_fields( 'organizer' ),

			'venue_fields'     => $this->get_fields( 'venue' ),

			'step'               => $this->get_step(),

			'submit_button_text' => __( 'Save changes', 'wp-event-manager' )

			) );
	}

	/**
	 * Submit Step is posted
	 */

	public function submit_handler() {

		if ( empty( $_POST['submit_event'] ) ) {

			return;
		}

		try {

			// Get posted values

			$values = $this->get_posted_fields();

			// Validate required

			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {

				throw new Exception( $return->get_error_message() );
			}
			
			// Update the event

			$this->save_event( $values['event']['event_title'], $values['event']['event_description'], '', $values, false );

			$this->update_event_data( $values );

			// Successful

			switch ( get_post_status( $this->event_id ) ) {

				case 'publish' :

					echo '<div class="event-manager-message wpem-alert wpem-alert-success">' . __( 'Your changes have been saved.', 'wp-event-manager' ) . ' <a href="' . get_permalink( $this->event_id ) . '">' . __( 'View &rarr;', 'wp-event-manager' ) . '</a>' . '</div>';

				break;

				default :

					echo '<div class="event-manager-message wpem-alert wpem-alert-success">' . __( 'Your changes have been saved.', 'wp-event-manager' ) . '</div>';

				break;
			}

		} catch ( Exception $e ) {

			echo '<div class="event-manager-error wpem-alert wpem-alert-danger">' . $e->getMessage() . '</div>';

			return;
		}
	}
}