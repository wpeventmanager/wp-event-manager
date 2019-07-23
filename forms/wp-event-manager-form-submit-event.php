<?php
/**
 * WP_Event_Manager_Form_Submit_Event class.
 */

class WP_Event_Manager_Form_Submit_Event extends WP_Event_Manager_Form {
    
	public    $form_name = 'submit-event';
	protected $event_id;
	protected $preview_event;
	/** @var WP_Event_Manager_Form_Submit_Event The single instance of the class */
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
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'process' ) );
		$this->steps  = (array) apply_filters( 'submit_event_steps', array(
			'submit' => array(
				'name'     => __( 'Submit Details', 'wp-event-manager' ),
				'view'     => array( $this, 'submit' ),
				'handler'  => array( $this, 'submit_handler' ),
				'priority' => 10
				),

			'preview' => array(
				'name'     => __( 'Preview', 'wp-event-manager' ),
				'view'     => array( $this, 'preview' ),
				'handler'  => array( $this, 'preview_handler' ),
				'priority' => 20
			),

			'done' => array(
				'name'     => __( 'Done', 'wp-event-manager' ),
				'view'     => array( $this, 'done' ),
				'priority' => 30
			)
		) );

		uasort( $this->steps, array( $this, 'sort_by_priority' ) );
		// Get step/event
		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $this->steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $this->steps ) );
		}

		$this->event_id = ! empty( $_REQUEST['event_id'] ) ? absint( $_REQUEST[ 'event_id' ] ) : 0;
		if ( ! event_manager_user_can_edit_event( $this->event_id ) ) {
			$this->event_id = 0;
		}
		
		// Allow resuming from cookie.
		$this->resume_edit = false;
		if ( ! isset( $_GET[ 'new' ] ) && ( 'before' === get_option( 'event_manager_paid_listings_flow' ) || !$this->event_id  ) && ! empty( $_COOKIE['wp-event-manager-submitting-event-id'] ) && ! empty( $_COOKIE['wp-event-manager-submitting-event-key'] ) ){
			$event_id     = absint( $_COOKIE['wp-event-manager-submitting-event-id'] );
			$event_status = get_post_status( $event_id );
			if ( 'preview' === $event_status && get_post_meta( $event_id, '_submitting_key', true ) === $_COOKIE['wp-event-manager-submitting-event-key'] ) {
				$this->event_id = $event_id;
			}
		}
		// Load event details
		if ( $this->event_id ) {
			$event_status = get_post_status( $this->event_id );
			if ( 'expired' === $event_status ) {
				if ( ! event_manager_user_can_edit_event( $this->event_id ) ) {
					$this->event_id = 0;
					$this->step   = 0;
				}
			} elseif ( ! in_array( $event_status, apply_filters( 'event_manager_valid_submit_event_statuses', array( 'preview' ) ) ) ) {
				$this->event_id = 0;
				$this->step   = 0;
			}
		}
	}
	
	/**
	 * Get the submitted event ID
	 * @return int
	*/
	public function get_event_id() {
		return absint( $this->event_id );
	}
	/**
	 * init_fields function.
	 */
	public function init_fields() {
		if ( $this->fields ) {
			return;
		}
		
		$allowed_registration_method = get_option( 'event_manager_allowed_registration_method', '' );
		switch ( $allowed_registration_method ) {
			case 'email' :
				$registration_method_label       = __( 'Registration email', 'wp-event-manager' );
				$registration_method_placeholder = __( 'you@yourdomain.com', 'wp-event-manager' );
			break;
			case 'url' :
				$registration_method_label       = __( 'Registration URL', 'wp-event-manager' );
				$registration_method_placeholder = __( 'http://', 'wp-event-manager' );
			break;
			default :
				$registration_method_label       = __( 'Registration email/URL', 'wp-event-manager' );
				$registration_method_placeholder = __( 'Enter an email address or website URL', 'wp-event-manager' );
			break;
		}
		
		$this->fields = apply_filters( 'submit_event_form_fields', array(
			'event' => array(
				'event_title' => array(
					'label'       => __( 'Event Title', 'wp-event-manager' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => __('Event title','wp-event-manager'),
					'priority'    => 1
				),

				'event_type' => array(
					'label'       => __( 'Event Type', 'wp-event-manager' ),
					'type'        =>  get_option('event_manager_multiselect_event_type') ?  'term-multiselect' : 'term-select',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 2,
					'default'     => 'meeting-or-networking-event',
					'taxonomy'    => 'event_listing_type'
				),

				'event_category' => array(
					'label'       => __( 'Event Category', 'wp-event-manager' ),
					'type'        => get_option('event_manager_multiselect_event_category') ?  'term-multiselect' : 'term-select',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 3,
					'default'     => '',
					'taxonomy'    => 'event_listing_category'
				),

				'event_online' => array(
							        'label'=> __('Online Event','wp-event-manager'),							      	
							        'type'  => 'radio',
								    'default'  => 'no',
								    'options'  => array(
											    'yes' => __( 'Yes', 'wp-event-manager' ),
											    'no' => __( 'No', 'wp-event-manager' )
								 		    ),
								    'priority'    => 4,
							        'required'=>true
		 		 ),		
		 		 
		 		 'event_venue_name' => array(
					'label'       => __( 'Venue Name', 'wp-event-manager' ),
					'type'        => 'text',
					'required'    => 'true',					
					'placeholder' => __( 'Please enter the venue name', 'wp-event-manager' ),
					'priority'    => 5
				),
					
				'event_address' => array(
						'label'       => __( 'Address', 'wp-event-manager' ),
						'type'        => 'text',
						'required'    => 'true',
						'placeholder' => __( 'Please enter street name and number', 'wp-event-manager' ),
						'priority'    => 6
				),
					
				'event_pincode' => array(
					'label'       => __( 'Zip Code', 'wp-event-manager' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => __( 'Please enter zip code (Area code)', 'wp-event-manager' ),
					'priority'    => 8
				),
					
				'event_location' => array(
						'label'       => __( 'Location', 'wp-event-manager' ),
						'type'        => 'text',
						'required'    => true,
						'placeholder' => __( 'e.g. "Berlin","London"', 'wp-event-manager' ),
						'priority'    => 7
				),
					
				'event_banner' => array(
					'label'       => __( 'Event Banner', 'wp-event-manager' ),
					'type'        => 'file',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 9,
					'ajax'        => true,
					'multiple'    => get_option( 'event_manager_user_can_add_multiple_banner' ) == 1 ? true : false,
					'allowed_mime_types' => array(
						'jpg'  => 'image/jpeg',
						'jpeg' => 'image/jpeg',
						'gif'  => 'image/gif',
						'png'  => 'image/png'
					)
				),

				'event_description' => array(
					'label'       => __( 'Description', 'wp-event-manager' ),
					'type'        => 'wp-editor',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 10
				),
					
				'registration' => array(
					'label'       => $registration_method_label,
					'type'        => 'text',
					'required'    => true,
					'placeholder' => $registration_method_placeholder,
					'priority'    => 11
				),
					
				'event_start_date' => array(  
								'label'=> __( 'Start Date', 'wp-event-manager' ),
								'placeholder'  => __( 'Please enter event start date', 'wp-event-manager' ),								
								'type'  => 'date',
								'priority'    => 12,
								'required'=>true	  
							  ),
				'event_start_time' => array(  
								'label'=> __( 'Start Time', 'wp-event-manager' ),
								'placeholder'  => __( 'Please enter event start time', 'wp-event-manager' ),								
								'type'  => 'time',
								'priority'    => 13,
								'required'=>true	  
								),

				'event_end_date' => array(
							        'label'=> __( 'End Date', 'wp-event-manager' ),
							        'placeholder'  => __( 'Please enter event end date', 'wp-event-manager' ),							        
							        'type'  => 'date',
								    'priority'    => 14,
							        'required'=>true
							  ),
							  
				'event_end_time' => array(  
								'label'=> __( 'End Time', 'wp-event-manager' ),
								'placeholder'  => __( 'Please enter event end time', 'wp-event-manager' ),								
								'type'  => 'time',
								'priority'    => 15,
								'required'=>true	  
							  ),
				'event_timezone' => array(
						'label'=> __( 'Event timezone', 'wp-event-manager' ),
						'placeholder'  	=> __( 'Please select timezone for event', 'wp-event-manager' ),
						'type'  		=> 'timezone',
						'priority'    	=> 15,
						'required'	=> true,
						'class'		=> 'event-manager-category-dropdown',
						'default'	=> '+5:00'
						//'value'		=> ''
				),

				'event_ticket_options' => array(
							        'label'=> __( 'Ticket Options', 'wp-event-manager' ),							      
							        'type'  => 'radio',
								    'default'  => 'free',
								    'options'  => array(
											    'paid' => __( 'Paid', 'wp-event-manager' ),
											    'free' => __( 'Free', 'wp-event-manager' )
								 		    ),
								    'priority'    => 16,
							        'required'=>true
		 		),
                'event_ticket_price' => array(
							        'label'=> __( 'Ticket Price', 'wp-event-manager' ),                              
							        'placeholder'  => __( 'Please enter ticket price', 'wp-event-manager' ),							        
							        'type'  => 'text',
									'priority'    => 17,
							        'required'=>true
							  		),

				'event_link_to_eventpage' => array(
									'label'       => __( 'Link To Event Page', 'wp-event-manager' ),									
									'type'        => 'text',
									'required'    => false,					
									'placeholder' => __( 'e.g http://www.example.com', 'wp-event-manager' ),
									'priority'    => 19
									),

				'event_registration_deadline' => array(
									'label'       => __( 'Registration Deadline', 'wp-event-manager' ),	
									'type'        => 'date',
									'required'    => false,					
									'placeholder' => __( 'Please enter registration deadline', 'wp-event-manager' ),
									'priority'    => 20
									),						 
			),
			
			'organizer' => array(
				'organizer_name' => array(
								'label'       => __( 'Organization name', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => __( 'Enter the name of the organization', 'wp-event-manager' ),
								'priority'    => 1
				),
				'organizer_logo' => array(
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

				'organizer_description' => array(
					'label'       => __( 'Organizer Description', 'wp-event-manager' ),
					'type'        => 'wp-editor',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 3
				),	

				'organizer_email' => array(
								'label'       => __( 'Organization Email', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => __( 'Enter your email address', 'wp-event-manager' ),
								'priority'    => 5
				),

				'organizer_website' => array(
								'label'       => __( 'Website', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Website URL e.g http://www.yourorganization.com', 'wp-event-manager' ),
								'priority'    => 6
				),

				'organizer_twitter' => array(
								'label'       => __( 'Twitter', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Twitter URL e.g http://twitter.com/yourorganizer', 'wp-event-manager' ),
								'priority'    => 7
				),

				'organizer_youtube' => array(
								'label'       => __( 'Youtube', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Youtube Channel URL e.g http://www.youtube.com/channel/yourcompany', 'wp-event-manager' ),
								'priority'    => 8
				),
				'organizer_facebook' => array(
								'label'       => __( 'Facebook', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Facebook URL e.g http://www.facebook.com/yourcompany', 'wp-event-manager' ),
								
								'priority'    => 10
				),
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
	 * Validate the posted fields
	 *
	 * @return bool on success, WP_ERROR on failure
	 */
	protected function validate_fields( $values ) {
		$this->fields =  apply_filters( 'before_submit_event_form_validate_fields', $this->fields , $values );
	      foreach ( $this->fields as $group_key => $group_fields )
    	  {     	      
    	       //this filter need to apply for remove required attributes when option online event selected and ticket price.
    	       if(isset($group_fields['event_online'] ) )
				 {
    				if($group_fields['event_online']['value']=='yes')
    				{	  
    				    $group_fields['event_venue_name']['required']=false;
    					$group_fields['event_address']['required']=false;
    					$group_fields['event_pincode']['required']=false;
    					$group_fields['event_location']['required']=false;
    				}
				 }
				 
				 if(isset($group_fields['event_ticket_options']) )
				{
    				if($group_fields['event_ticket_options']['value']=='free')
    				{	
    					$group_fields['event_ticket_price']['required']=false;
    				} 			
				}
		        foreach ( $group_fields as $key => $field ) 
              	{
    				if ( $field['required'] && empty( $values[ $group_key ][ $key ] ) ) {	    
    					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'wp-event-manager' ), $field['label'] ) );
    				}

				    if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], array( 'term-checklist', 'term-select', 'term-multiselect' ) ) ) {
    					if ( is_array( $values[ $group_key ][ $key ] ) ) {
    						$check_value = $values[ $group_key ][ $key ];
    					} else {
    						$check_value = empty( $values[ $group_key ][ $key ] ) ? array() : array( $values[ $group_key ][ $key ] );
    					}
    					foreach ( $check_value as $term ) {    
    						if ( ! term_exists( $term, $field['taxonomy'] ) ) {
    							return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'wp-event-manager' ), $field['label'] ) );    
    						}
    					}
    				}

				if ( 'file' === $field['type'] && ! empty( $field['allowed_mime_types'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = array_filter( $values[ $group_key ][ $key ] );
					} else {
						$check_value = array_filter( array( $values[ $group_key ][ $key ] ) );
					}
					if ( ! empty( $check_value ) ) {
						foreach ( $check_value as $file_url ) {
							$file_url = current( explode( '?', $file_url ) );
							$file_info = wp_check_filetype( $file_url );
							if ( ! is_numeric( $file_url ) && $file_info && ! in_array( $file_info['type'], $field['allowed_mime_types'] ) ) {
								throw new Exception( sprintf( __( '"%s" (filetype %s) needs to be one of the following file types: %s', 'wp-event-manager' ), $field['label'], $info['ext'], implode( ', ', array_keys( $field['allowed_mime_types'] ) ) ) );
							}
						}
					}
				}
			}
		}
		
		// Registration method
		if ( isset( $values['event']['registration'] ) && ! empty( $values['event']['registration'] ) ) {
			$allowed_registration_method = get_option( 'event_manager_allowed_registration_method', '' );
			$values['event']['registration'] = str_replace( ' ', '+', $values['event']['registration'] );
			switch ( $allowed_registration_method ) {
				case 'email' :
					if ( ! is_email( $values['event']['registration'] ) ) {
						throw new Exception( __( 'Please enter a valid registration email address', 'wp-event-manager' ) );
					}
				break;
				case 'url' :
					// Prefix http if needed
					if ( ! strstr( $values['event']['registration'], 'http:' ) && ! strstr( $values['event']['registration'], 'https:' ) ) {
						$values['event']['registration'] = 'http://' . $values['event']['registration'];
					}
					if ( ! filter_var( $values['event']['registration'], FILTER_VALIDATE_URL ) ) {
						throw new Exception( __( 'Please enter a valid registration URL', 'wp-event-manager' ) );
					}
				break;
				default :
					if ( ! is_email( $values['event']['registration'] ) ) {
						// Prefix http if needed
						if ( ! strstr( $values['event']['registration'], 'http:' ) && ! strstr( $values['event']['registration'], 'https:' ) ) {
							$values['event']['registration'] = 'http://' . $values['event']['registration'];
						}
						if ( ! filter_var( $values['event']['registration'], FILTER_VALIDATE_URL ) ) {
							throw new Exception( __( 'Please enter a valid registration email address or URL', 'wp-event-manager' ) );
						}
					}
				break;
			}
		}
		
		//organizer email validation
		if (isset( $values['organizer']['organizer_email'] ) && !empty( $values['organizer']['organizer_email'] ) ) {
			if ( ! is_email( $values['organizer']['organizer_email'] ) ) {
				throw new Exception( __( 'Please enter a valid organizer email address', 'wp-event-manager' ) );
			}
				
		}
		
		return apply_filters( 'submit_event_form_validate_fields', true, $this->fields, $values );
	}

	/**
	 * event_types function.
	 */

	private function event_types() {
		$options = array();
		$terms   = get_event_listing_types();
		foreach ( $terms as $term ) {
			$options[ $term->slug ] = $term->name;
		}
		return $options;
	}

	/**
	 * Submit Step
	 */
	public function submit() {
			// Init fields
			//$this->init_fields(); We dont need to initialize with this function because of field edior
			// Now field editor function will return all the fields 
			//Get merged fields from db and default fields.
			$this->merge_with_custom_fields('frontend' );
			
			//get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
			$datepicker_date_format 	= WP_Event_Manager_Date_Time::get_datepicker_format();
						
			//covert datepicker format  into php date() function date format
			$php_date_format 		= WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format( $datepicker_date_format );
			
		// Load data if neccessary
		if ( $this->event_id ) {
			$event = get_post( $this->event_id );
			foreach ( $this->fields as $group_key => $group_fields ) {
				foreach ( $group_fields as $key => $field ) {
					switch ( $key ) {
						case 'event_title' :
							$this->fields[ $group_key ][ $key ]['value'] = $event->post_title;
						break;
						case 'event_description' :
							$this->fields[ $group_key ][ $key ]['value'] = $event->post_content;
						break;
						case  'organizer_logo':
							$this->fields[ $group_key ][ $key ]['value'] = has_post_thumbnail( $event->ID ) ? get_post_thumbnail_id( $event->ID ) : get_post_meta( $event->ID, '_' . $key, true );
						break;
						
						case ($key ==  'event_start_date' ||  $key == 'event_end_date' ) :
							$event_date = get_post_meta( $event->ID, '_' . $key, true );
							$default_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
							$default_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format( $default_date_format );
						
							$this->fields[ $group_key ][ $key ]['value'] = date($default_date_format ,strtotime($event_date) );
						break;
							
						case 'event_type' :
							$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $event->ID, 'event_listing_type', array( 'fields' => 'ids' ) );
							if ( ! event_manager_multiselect_event_type() ) {
								$this->fields[ $group_key ][ $key ]['value'] = current( $this->fields[ $group_key ][ $key ]['value'] );
							}
						break;
						case 'event_category' :
							$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $event->ID, 'event_listing_category', array( 'fields' => 'ids' ) );
						break;
						default:
							$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $event->ID, '_' . $key, true );
						break;
					}
					if ( ! empty( $field['taxonomy'] ) ) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $event->ID, $field['taxonomy'], array( 'fields' => 'ids' ) );
					}
					
					if(! empty( $field['type'] ) &&  $field['type'] == 'date' ){
						$event_date = get_post_meta( $event->ID, '_' . $key, true );
						$this->fields[ $group_key ][ $key ]['value'] = date($php_date_format ,strtotime($event_date) );
					}
				}
			}

			$this->fields = apply_filters( 'submit_event_form_fields_get_event_data', $this->fields, $event );
		// Get user meta
		} elseif ( is_user_logged_in() && empty( $_POST['submit_event'] ) ) {
			if ( ! empty( $this->fields['organizer'] ) ) {
				foreach ( $this->fields['organizer'] as $key => $field ) {
					$this->fields['organizer'][ $key ]['value'] = get_user_meta( get_current_user_id(), '_' . $key, true );
				}
			}
			
			if ( ! empty( $this->fields['event']['registration'] ) ) {
				$allowed_registration_method = get_option( 'event_manager_allowed_registration_method', '' );
				if ( $allowed_registration_method !== 'url' ) {
					$current_user = wp_get_current_user();
					$this->fields['event']['registration']['value'] = $current_user->user_email;
				}
			}
			
			
			$this->fields = apply_filters( 'submit_event_form_fields_get_user_data', $this->fields, get_current_user_id() );
		}

		wp_enqueue_script( 'wp-event-manager-event-submission' );
		get_event_manager_template( 'event-submit.php', array(
			'form'               => $this->form_name,
			'event_id'             => $this->get_event_id(),
			'resume_edit'        => $this->resume_edit,
			'action'             => $this->get_action(),
			'event_fields'         => $this->get_fields( 'event' ),
			'organizer_fields'     => $this->get_fields( 'organizer' ),
			'step'               => $this->get_step(),
			'submit_button_text' => apply_filters( 'submit_event_form_submit_button_text', __( 'Preview', 'wp-event-manager' ) )
		) );
	}

	/**
	 * Submit Step is posted
	 */
	public function submit_handler() {
		try {
			// Init fields
			//$this->init_fields(); We dont need to initialize with this function because of field edior
			// Now field editor function will return all the fields 
			//Get merged fields from db and default fields.
			$this->merge_with_custom_fields('frontend' );
			
			// Get posted values
			$values = $this->get_posted_fields();
			if ( empty( $_POST['submit_event'] ) ) {
				return;
			}
			// Validate required
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new Exception( $return->get_error_message() );
			}
			// Account creation
			if ( ! is_user_logged_in() ) {
				$create_account = false;
				if ( event_manager_enable_registration() ) {
					if ( event_manager_user_requires_account() ) {
						if ( ! event_manager_generate_username_from_email() && empty( $_POST['create_account_username'] ) ) {
							throw new Exception( __( 'Please enter a username.', 'wp-event-manager' ) );
						}
						if ( empty( $_POST['create_account_email'] ) ) {
							throw new Exception( __( 'Please enter your email address.', 'wp-event-manager' ) );
						}
						if ( empty( $_POST['create_account_email'] ) ) {
							throw new Exception( __( 'Please enter your email address.', 'wp-event-manager' ) );
						}
					}
					if ( ! event_manager_use_standard_password_setup_email() && ! empty( $_POST['create_account_password'] ) ) {
						if ( empty( $_POST['create_account_password_verify'] ) || $_POST['create_account_password_verify'] !== $_POST['create_account_password'] ) {
							throw new Exception( __( 'Passwords must match.', 'wp-event-manager' ) );
						}
						if ( ! event_manager_validate_new_password( $_POST['create_account_password'] ) ) {
							$password_hint = event_manager_get_password_rules_hint();
							if ( $password_hint ) {
								throw new Exception( sprintf( __( 'Invalid Password: %s', 'wp-event-manager' ), $password_hint ) );
							} else {
								throw new Exception( __( 'Password is not valid.', 'wp-event-manager' ) );
							}
						}
					}

					if ( ! empty( $_POST['create_account_email'] ) ) {
						$create_account = wp_event_manager_create_account( array(
							'username' => ( event_manager_generate_username_from_email() || empty( $_POST['create_account_username'] ) ) ? '' : $_POST['create_account_username'],
							'password' => ( event_manager_use_standard_password_setup_email() || empty( $_POST['create_account_password'] ) ) ? '' : $_POST['create_account_password'],
							'email'    => $_POST['create_account_email'],
							'role'     => get_option( 'event_manager_registration_role' )
						) );
					}
				}

				if ( is_wp_error( $create_account ) ) {
					throw new Exception( $create_account->get_error_message() );
				}
			}
			if ( event_manager_user_requires_account() && ! is_user_logged_in() ) {
				throw new Exception( __( 'You must be signed in to post a new listing.','wp-event-manager' ) );
			}

			// Update the event
			$this->save_event( $values['event']['event_title'], $values['event']['event_description'], $this->event_id ? '' : 'preview', $values );
			$this->update_event_data( $values );
			// Successful, show next step
			$this->step ++;
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Update or create a event listing from posted data
	 *
	 * @param  string $post_title
	 * @param  string $post_content
	 * @param  string $status
	 * @param  array $values
	 * @param  bool $update_slug
	 */
	protected function save_event( $post_title, $post_content, $status = 'preview', $values = array(), $update_slug = true ) {
		$event_data = array(
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'post_type'      => 'event_listing',
			'comment_status' => 'closed'
		);

	if ( $update_slug ) {
			$event_slug   = array();
			// Prepend with organizer name
			/* if ( apply_filters( 'submit_event_form_prefix_post_name_with_organizer', true ) && ! empty( $values['organizer']['organizer_name'] ) ) {
				$event_slug[] = $values['organizer']['organizer_name'];
			}
			// Prepend location
			if ( apply_filters( 'submit_event_form_prefix_post_name_with_location', true ) && ! empty( $values['event']['event_location'] ) ) {
				$event_slug[] = $values['event']['event_location'];
			} */
			// Prepend with event type
			if ( apply_filters( 'submit_event_form_prefix_post_name_with_event_type', true ) && ! empty( $values['event']['event_type'] ) ) {
				if ( event_manager_multiselect_event_type() && is_array($values['event']['event_type']) ) {
					
					$event_type = array_values($values['event']['event_type'])[0];
					if( is_int ($event_type) ){
						$event_type_taxonomy = get_term( $values['event']['event_type'][0]);
						$event_type = $event_type_taxonomy->name;
					}
					$event_slug[] = $event_type;
				}
				else{

					$event_type = $values['event']['event_type'];
					
					if( is_int ($event_type) ){
						$event_type_taxonomy = get_term( $values['event']['event_type']);
						$event_type = $event_type_taxonomy->name;
					}
					$event_slug[] = $event_type;
				}
			}
			$event_slug[]            	= $post_title;
			$event_slugs				= implode( '-', $event_slug ) ;
			$event_data['post_name'] 	= apply_filters('submit_event_form_save_slug_data', $event_slugs);
		}
		if ( $status ) {
			$event_data['post_status'] = $status;
		}
		$event_data = apply_filters( 'submit_event_form_save_event_data', $event_data, $post_title, $post_content, $status, $values );
		if ( $this->event_id ) {
			$event_data['ID'] = $this->event_id;
			wp_update_post( $event_data );
		} else {
			$this->event_id = wp_insert_post( $event_data );
			if ( ! headers_sent() ) {
				$submitting_key = uniqid();
				setcookie( 'wp-event-manager-submitting-event-id', $this->event_id, 0, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( 'wp-event-manager-submitting-event-key', $submitting_key, 0, COOKIEPATH, COOKIE_DOMAIN, false );
				update_post_meta( $this->event_id, '_submitting_key', $submitting_key );
			}
		}
	}
	/**
	 * Create an attachment
	 * @param  string $attachment_url
	 * @return int attachment id
	 */
	protected function create_attachment( $attachment_url ) {
		include_once( ABSPATH . 'wp-admin/includes/image.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );
	
		$upload_dir     = wp_upload_dir();
		$attachment_url = str_replace( array( $upload_dir['baseurl'], WP_CONTENT_URL, site_url( '/' ) ), array( $upload_dir['basedir'], WP_CONTENT_DIR, ABSPATH ), $attachment_url );
	
		if ( empty( $attachment_url ) || ! is_string( $attachment_url ) ) {
			return 0;
		}
	
		$attachment     = array(
				'post_title'   => get_the_title( $this->event_id ),
				'post_content' => '',
				'post_status'  => 'inherit',
				'post_parent'  => $this->event_id,
				'guid'         => $attachment_url
		);
	
		if ( $info = wp_check_filetype( $attachment_url ) ) {
			$attachment['post_mime_type'] = $info['type'];
		}
	
		$attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->event_id );
	
		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
			return $attachment_id;
		}
	
		return 0;
	}
	/**
	 * Set event meta + terms based on posted values
	 *
	 * @param  array $values
	 */
	protected function update_event_data( $values ) {
		// Set defaults
		add_post_meta( $this->event_id, '_cancelled', 0, true );
		add_post_meta( $this->event_id, '_featured', 0, true );
		$maybe_attach = array();
		
		//get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format 	= WP_Event_Manager_Date_Time::get_datepicker_format();
		
		//covert datepicker format  into php date() function date format
		$php_date_format 		= WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format( $datepicker_date_format );

		$ticket_type='';
		$recurre_event='';
		// Loop fields and save meta and term data
		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				// Save taxonomies
				if ( ! empty( $field['taxonomy'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						wp_set_object_terms( $this->event_id, $values[ $group_key ][ $key ], $field['taxonomy'], false );
					} else {
						wp_set_object_terms( $this->event_id, array( $values[ $group_key ][ $key ] ), $field['taxonomy'], false );
					}				
				// oragnizer logo is a featured image
				}
				elseif ( 'organizer_logo' === $key ) {
					$attachment_id = is_numeric( $values[ $group_key ][ $key ] ) ? absint( $values[ $group_key ][ $key ] ) : $this->create_attachment( $values[ $group_key ][ $key ] );
					if ( empty( $attachment_id ) ) {
						delete_post_thumbnail( $this->event_id );
					} else {
						set_post_thumbnail( $this->event_id, $attachment_id );
					}
					update_user_meta( get_current_user_id(), '_organizer_logo', $attachment_id );
					
					// Save meta data
				}
				
			//save event start date according to mysql date format with event start time
				elseif( $key === 'event_start_date'  ){

					if(isset( $values[ $group_key ][ $key ] ) && isset( $values[ $group_key ][ 'event_start_time' ] )){

						$start_time = WP_Event_Manager_Date_Time::get_db_formatted_time( $values[ $group_key ][ 'event_start_time' ] );
						//combine event start date value with event start time 
						$date =  $values[ $group_key ][ $key ].' '.$start_time ;
						
        				 //Convert date and time value into DB formatted format and save eg. 1970-01-01 00:00:00
						$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format . ' H:i:s'  , $date);
						$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;

						update_post_meta( $this->event_id, '_' . $key,$date_dbformatted);
					}
					else
						update_post_meta( $this->event_id, '_' . $key, $values[ $group_key ][ $key ] );

				}
				elseif( $key ==='event_end_date' ){
						//save event end date according to mysql date format with event end time
						if(isset( $values[ $group_key ][ $key ] ) && isset( $values[ $group_key ][ 'event_end_time' ] )){
							$end_time = WP_Event_Manager_Date_Time::get_db_formatted_time( $values[ $group_key ][ 'event_end_time' ] );
							//combine event start date value with event start time 
							$date =  $values[ $group_key ][ $key ].' '.$end_time ;

	        				 //Convert date and time value into DB formatted format and save eg. 1970-01-01 00:00:00
							$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format . ' H:i:s'  , $date);
							$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;

							update_post_meta( $this->event_id, '_' . $key, $date_dbformatted );
						}
						else
							update_post_meta( $this->event_id, '_' . $key, $values[ $group_key ][ $key ] );
				}
				elseif ( $field['type'] == 'date' ) {
					$date = $values[ $group_key ][ $key ];					
					//Convert date and time value into DB formatted format and save eg. 1970-01-01
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format  , $date );
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;
					update_post_meta( $this->event_id, '_' . $key, $date_dbformatted );
				}
				 else { 
					update_post_meta( $this->event_id, '_' . $key, $values[ $group_key ][ $key ] );
					if('_' .$key=='_event_ticket_options' && $values[ $group_key ][ $key ]=='free'){
					    $ticket_type=$values[ $group_key ][ $key ];
					}
					if('_' .$key=='_event_recurrence' && $values[ $group_key ][ $key ]=='no'){
					    $recurre_event='no';
					}
				}

				// Handle attachments.
					if ( 'file' === $field['type'] ) {
						if ( is_array( $values[ $group_key ][ $key ] ) ) {
							foreach ( $values[ $group_key ][ $key ] as $file_url ) {
								$maybe_attach[] = $file_url;
							}
						} else {
							$maybe_attach[] = $values[ $group_key ][ $key ];
						}
					}
			}
		}
		$maybe_attach = array_filter( $maybe_attach );
		// Handle attachments
		if ( sizeof( $maybe_attach ) && apply_filters( 'event_manager_attach_uploaded_files', true ) ) {
			
			// Get attachments
			$attachments     = get_posts( 'post_parent=' . $this->event_id . '&post_type=attachment&fields=ids&numberposts=-1' );
			$attachment_urls = array();
			// Loop attachments already attached to the event
			foreach ( $attachments as $attachment_key => $attachment ) {
				$attachment_urls[] = wp_get_attachment_url( $attachment_id );
			}
			foreach ( $maybe_attach as $attachment_url ) {
				if ( ! in_array( $attachment_url, $attachment_urls ) ) {
					$this->create_attachment( $attachment_url );
				}
			}
		}
		// reset meta value if ticket type is free
		if($ticket_type=='free'){
		    update_post_meta( $this->event_id, '_event_ticket_price', '');
		}
		if($recurre_event=='no'){
		    update_post_meta( $this->event_id, '_recure_every', '');
		    update_post_meta( $this->event_id, '_recure_month_day', '');
		    update_post_meta( $this->event_id, '_recure_weekday', '');
		    update_post_meta( $this->event_id, '_recure_untill', '');
		}
		// And user meta to save time in future
		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), '_organizer_name', isset( $values['organizer']['organizer_name'] ) ? $values['organizer']['organizer_name'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_website', isset( $values['organizer']['organizer_website'] ) ? $values['organizer']['organizer_website'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_tagline', isset( $values['organizer']['organizer_tagline'] ) ? $values['organizer']['organizer_tagline'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_twitter', isset( $values['organizer']['organizer_twitter'] ) ? $values['organizer']['organizer_twitter'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_logo', isset( $values['organizer']['organizer_logo'] ) ? $values['organizer']['organizer_logo'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_video', isset( $values['organizer']['organizer_video'] ) ? $values['organizer']['organizer_video'] : '' );
		}
		do_action( 'event_manager_update_event_data', $this->event_id, $values );
	}

	/**
	 * Preview Step
	 */

	public function preview() {
		global $post, $event_preview;
		if ( $this->event_id ) {
			$event_preview       = true;
			$action            = $this->get_action();
			$post              = get_post( $this->event_id );
			setup_postdata( $post );
			$post->post_status = 'preview';
				get_event_manager_template( 'event-preview.php',  array( 'form' => $this ) );
			wp_reset_postdata();
		}
	}
	
	/**
	 * Preview Step Form handler
	 */
	public function preview_handler() {
		if ( ! $_POST ) {
			return;
		}
		// Edit = show submit form again
		if ( ! empty( $_POST['edit_event'] ) ) {
			$this->step --;
		}
		// Continue = change event status then show next screen
		if ( ! empty( $_POST['continue'] ) ) {
			$event = get_post( $this->event_id );
			if ( in_array( $event->post_status, array( 'preview', 'expired' ) ) ) {
				// Reset expiry
				delete_post_meta( $event->ID, '_event_expiry_date' );
				// Update event listing
				$update_event                  = array();
				$update_event['ID']            = $event->ID;
				$update_event['post_status']   = apply_filters( 'submit_event_post_status', get_option( 'event_manager_submission_requires_approval' ) ? 'pending' : 'publish',$event);
				$update_event['post_date']     = current_time( 'mysql' );
				$update_event['post_date_gmt'] = current_time( 'mysql', 1 );
				wp_update_post( $update_event );
			}			
			$this->step ++;
		}
	}
	
	/**
	 * Done Step
	 */
	public function done() {
		do_action( 'event_manager_event_submitted', $this->event_id );
		get_event_manager_template( 'event-submitted.php', array( 'event' => get_post( $this->event_id ) ) );
	}
	
	/**
	 * get user selected fields from the field editor
	 *
	 * @return fields Array
	 */
	public function get_event_manager_fieldeditor_fields(){
		return apply_filters('get_event_manager_fieldeditor_fields', get_option( 'event_manager_form_fields', false ) );
	}
	
	/**
	 * This function will initilize default fields and return as array
	 * @return fields Array
	 **/
	public function get_default_fields( ) {
		if(empty($this->fields)){
			// Make sure fields are initialized and set
			$this->init_fields();
		}
	
		return $this->fields;
	}
	
	/**
	 * Merge and replace $default_fields with custom fields
	 *
	 * @return array Returns merged and replaced fields
	 */
	public function merge_with_custom_fields( $field_view = 'frontend' ) {
	
		$custom_fields  = $this->get_event_manager_fieldeditor_fields();
		$default_fields = $this->get_default_fields( );
		
		if(!get_option('event_manager_enable_event_ticket_prices', false)){
		    if(isset($custom_fields['event']['event_ticket_options']))
		        $custom_fields['event']['event_ticket_options']['visibility']=false;
		    if(isset($custom_fields['event']['event_ticket_price']))
		        $custom_fields['event']['event_ticket_price']['visibility']=false;
		            
		    if(isset($default_fields['event']['event_ticket_options']))
		        unset($default_fields['event']['event_ticket_options']);
		    if(isset($default_fields['event']['event_ticket_price']))
		        unset($default_fields['event']['event_ticket_price']);
		}
		if ( !get_option( 'event_manager_enable_categories') || (wp_count_terms( 'event_listing_category' ) == 0 && isset($custom_fields['event']['event_category'])) ) {
		    $custom_fields['event']['event_category']['visibility']=false;
		    unset($default_fields['event']['event_category']);
		}
		
		if ( ! get_option( 'event_manager_enable_event_types' ) || (wp_count_terms( 'event_listing_type' ) == 0 && isset($custom_fields['event']['event_type'])) ) {
		    $custom_fields['event']['event_type']['visibility']=false;
		    unset($default_fields['event']['event_type']);
		}
		
		if(!is_array($custom_fields )){
		    $this->fields = apply_filters('merge_with_custom_fields',$default_fields,$default_fields) ;
		    return $this->fields;
		}
	
		$updated_fields = ! empty( $custom_fields ) ? array_replace_recursive( $default_fields, $custom_fields ) : $default_fields;
		
		/**
		 * Above array_replace_recursive function will replace the default fields by custom fields.
		 * If array key is not same then it will merge array. This is only case for the Radio and Select Field(In case of array if key is not same).
		 * For eg. options key it has any value or option as per user requested or overrided but array_replace_recursive will merge both 		options of default field and custom fields.
		 User change the default value of the event_online (radio button) from Yes --> Y and No--> N then array_replace_recursive will merge both valus of the options array for event_online like options('yes'=>'yes', 'no'=>'no','y'=>'y','n'=>'n') but  we need to keep only updated options value of the event_online so we have to remove old default options values and for that we have to do the following procedure.
		 * In short: To remove default options need to replace the options array with custom options which is added by user.
		 **/
		foreach($default_fields as $default_group_key => $default_group){
			foreach ($default_group as $field_key => $field_value) {
				foreach($field_value as $key => $value ){
					if( isset( $custom_fields[$default_group_key][$field_key][$key]) && ( $key == 'options' || is_array($value) ) )
						$updated_fields[$default_group_key][$field_key][$key] = $custom_fields[$default_group_key][$field_key][$key];
				}
			}
		}
		
		/**
		 * If default field is removed via field editor then we can not removed this field from the code because it is hardcode in the file so we need to set flag to identify to keep the record which perticular field is removed by the user.
		 * Using visibility flag we can identify those fields need to remove or keep in the Field Editor based on visibility flag value. if visibility true then we will keep the field and if visibility flag false then we will not show this default field in the field editor. (As action of user removed this field from the field editor but not removed from the code so we have to set this flag)
		 * We are getting several default fields from the addons and using theme side customization via 'submit_event_form_fields' filter.
		 * Now, Not easy to manage filter fields and default fields of plugin in this case so we need to set this flag for identify wheather field show  or not in the field editor.
		 *
		 * If user selected admin only fields then we need to unset that fields from the frontend user.
		 **/
		if(!empty($updated_fields))
		foreach ( $updated_fields as $group_key => $group_fields ) {
			foreach ($group_fields as $key => $field) {
			    
			    $updated_fields[$group_key][$key]=array_map('stripslashes_deep',$updated_fields[$group_key][$key]);				
			    
				//remove if visiblity is false
				if(isset($field['visibility']) && $field['visibility'] == false )
					unset($updated_fields[$group_key][$key]);
					
				//remove admin fields if view type is frontend
				if( isset($field['admin_only']) &&  $field_view == 'frontend' &&  $field['admin_only'] == true )
					unset($updated_fields[$group_key][$key]);
			}
			uasort( $updated_fields[$group_key], array( $this, 'sort_by_priority' ) );
		}
		
		$this->fields = apply_filters('merge_with_custom_fields',$updated_fields,$default_fields) ;
	
		return $this->fields;
	}
}