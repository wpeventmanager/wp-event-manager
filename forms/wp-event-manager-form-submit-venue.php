<?php
/**
 * WP_Event_Manager_Form_Submit_Organizer class.
 */

class WP_Event_Manager_Form_Submit_Venue extends WP_Event_Manager_Form {
	public    $form_name = 'submit-venue';
	/** @var 
	* WP_Event_Manager_Form_Submit_Organizer The single instance of the class 
	*/
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
	 * init_fields function.
	 */
	public function init_fields() {
		if ( $this->fields ) {
			return;
		}
		
		$this->fields = apply_filters( 'submit_venue_form_fields', array(
			'venue' => array(
				'venue_name' => array(
								'label'       => __( 'Venue name', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => __( 'Enter the name of the venue', 'wp-event-manager' ),
								'priority'    => 1
				),
				'venue_logo' => array(
								'label'       => __( 'Logo', 'wp-event-manager' ),
								'type'        => 'file',
								'required'    => false,
								'placeholder' => '',
								'priority'    => 2,
								'ajax'        => true,
								'multiple'    => false,
								'allowed_mime_types' => array(
									'jpg'  => 'image/jpeg',
									'jpeg' => 'image/jpeg',
									'gif'  => 'image/gif',
									'png'  => 'image/png'
								)
				),

				'venue_description' => array(
					'label'       => __( 'Venue Description', 'wp-event-manager' ),
					'type'        => 'wp-editor',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 3
				),	

			

				'venue_website' => array(
								'label'       => __( 'Website', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Website URL e.g http://www.yourorganization.com', 'wp-event-manager' ),
								'priority'    => 6
				)
				
			)
		) );

		//unset timezone field if setting is site wise timezone
		$timezone_setting = get_option( 'event_manager_timezone_setting' ,'site_timezone' );
		if ( $timezone_setting != 'each_event' ) {
			unset( $this->fields['event']['event_timezone'] );
		}
	
		return $this->fields;
	}

	/**
	 * get user selected fields from the field editor
	 *
	 * @return fields Array
	 */
	public  function get_event_manager_fieldeditor_fields(){
		return apply_filters('get_event_manager_fieldeditor_fields', get_option( 'event_manager_form_fields', false ) );
	}

	/**
	 * This function will initilize default fields and return as array
	 * @return fields Array
	 **/
	public  function get_default_fields( ) {
		if(empty($this->fields)){
			// Make sure fields are initialized and set
			$this->init_fields();
		}
	
		return $this->fields;
	}
	
}