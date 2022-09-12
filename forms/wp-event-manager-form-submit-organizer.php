<?php
/**
 * WP_Event_Manager_Form_Submit_Organizer class.
 */

class WP_Event_Manager_Form_Submit_Organizer extends WP_Event_Manager_Form {
	public    $form_name = 'submit-organizer';
	protected $organizer_id;
	protected $preview_organizer;
	
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
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'process' ) );

		
		$this->steps  = (array) apply_filters( 'submit_organizer_steps', array(
			'submit' => array(
				'name'     => __( 'Submit Details', 'wp-event-manager' ),
				'view'     => array( $this, 'submit' ),
				'handler'  => array( $this, 'submit_handler' ),
				'priority' => 10
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

		$this->organizer_id = ! empty( $_REQUEST['organizer_id'] ) ? absint( $_REQUEST[ 'organizer_id' ] ) : 0;
		if ( ! event_manager_user_can_edit_event(sanitize_text_field( $this->organizer_id ) ) ){
			$this->organizer_id = 0;
		}
		
		// Allow resuming from cookie.
		$this->resume_edit = false;
		if ( ! isset( $_GET[ 'new' ] ) && ( !$this->organizer_id  ) && ! empty( $_COOKIE['wp-event-manager-submitting-organizer-id'] ) && ! empty( $_COOKIE['wp-event-manager-submitting-organizer-key'] ) ){
			$organizer_id     = absint( $_COOKIE['wp-event-manager-submitting-organizer-id'] );
			$organizer_status = get_post_status( $organizer_id );
			if ( 'preview' === $organizer_status && get_post_meta( $organizer_id, '_submitting_key', true ) === $_COOKIE['wp-event-manager-submitting-organizer-key'] ) {
				$this->organizer_id = $organizer_id;
			}
		}
		// Load event details
		if ( $this->organizer_id ) {
			$organizer_status = get_post_status( $this->organizer_id );
			if ( 'expired' === $organizer_status ) {
				if ( ! event_manager_user_can_edit_event( $this->organizer_id ) ) {
					$this->organizer_id = 0;
					$this->step   = 0;
				}
			} elseif ( ! in_array( $organizer_status, apply_filters( 'event_manager_valid_submit_organizer_statuses', array( 'publish' ) ) ) ) {
				$this->organizer_id = 0;
				$this->step   = 0;
			}
		}

	}

	/**
	 * Get the submitted event ID
	 * @return int
	*/
	public function get_organizer_id() {
		return absint( $this->organizer_id );
	}

	/**
	 * init_fields function.
	 */
	public function init_fields() {
            
		/*if ( $this->fields ) {
			return;
		}*/
		
		$this->fields = apply_filters( 'submit_organizer_form_fields', array(
			'organizer' => array(
				'organizer_name' => array(
								'label'       => __( 'Organizer name', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => __( 'Enter the name of the organization', 'wp-event-manager' ),
								'priority'    => 1,
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
								'label'       => __( 'Organizer Email', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => __( 'Enter your email address', 'wp-event-manager' ),
								'priority'    => 4,
				),

				'organizer_website' => array(
								'label'       => __( 'Website', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Website URL e.g http://www.yourorganization.com', 'wp-event-manager' ),
								'priority'    => 5
				),

				'organizer_facebook' => array(
								'label'       => __( 'Facebook', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Facebook URL e.g http://www.facebook.com/yourorganizer', 'wp-event-manager' ),
								'priority'    => 6
				),

				'organizer_instagram' => array(
								'label'       => __( 'Instagram', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Instagram URL e.g http://www.instagram.com/yourorganizer', 'wp-event-manager' ),
								'priority'    => 7
				),

				'organizer_youtube' => array(
								'label'       => __( 'Youtube', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Youtube Channel URL e.g http://www.youtube.com/channel/yourorganizer', 'wp-event-manager' ),
								'priority'    => 8
				),

				'organizer_twitter' => array(
								'label'       => __( 'Twitter', 'wp-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Twitter URL e.g http://twitter.com/yourorganizer', 'wp-event-manager' ),
								'priority'    => 9
				),
			)
		) );

		return $this->fields;
	}

		/**
	 * get user selected fields from the field editor
	 *
	 * @return fields Array
	 */
	public  function get_event_manager_fieldeditor_fields(){
		return apply_filters('event_manager_submit_organizer_form_fields', get_option( 'event_manager_submit_organizer_form_fields', false ) );
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
		if ( $this->organizer_id ) {
			$organizer = get_post( $this->organizer_id );
			foreach ( $this->fields as $group_key => $group_fields ) {
				foreach ( $group_fields as $key => $field ) {
					switch ( $key ) {
						case 'organizer_name' :
							$this->fields[ $group_key ][ $key ]['value'] = $organizer->post_title;
						break;
						case 'organizer_description' :
							$this->fields[ $group_key ][ $key ]['value'] = $organizer->post_content;
						break;
						case  'organizer_logo':
							$this->fields[ $group_key ][ $key ]['value'] = has_post_thumbnail( $organizer->ID ) ? get_post_thumbnail_id( $organizer->ID ) : get_post_meta( $organizer->ID, '_' . $key, true );
						break;
						default:
							$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $organizer->ID, '_' . $key, true );
						break;
					}
					if ( ! empty( $field['taxonomy'] ) ) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $organizer->ID, $field['taxonomy'], array( 'fields' => 'ids' ) );
					}
					
					if(! empty( $field['type'] ) &&  $field['type'] == 'date' ){
						$event_date = get_post_meta( $organizer->ID, '_' . $key, true );
						$this->fields[ $group_key ][ $key ]['value'] = date($php_date_format ,strtotime($event_date) );
					}
				}
			}

			$this->fields = apply_filters( 'submit_event_form_fields_get_organizer_data', $this->fields, $organizer );
                        
		}
		

		wp_enqueue_script( 'wp-event-manager-event-submission' );
		get_event_manager_template( 'organizer-submit.php', 
			array(
				'form'               => $this->form_name,
				'organizer_id'       => $this->get_organizer_id(),
				'resume_edit'        => $this->resume_edit,
				'action'             => $this->get_action(),
				'organizer_fields'     => $this->get_fields( 'organizer' ),
				'step'               => $this->get_step(),
				'submit_button_text' => apply_filters( 'submit_organizer_form_submit_button_text',  __( 'Submit', 'wp-event-manager' ) )
			),
			'wp-event-manager/organizer', 
            EVENT_MANAGER_PLUGIN_DIR . '/templates/organizer'
		);

		
	}

	/**
	 * Validate the posted fields
	 *
	 * @return bool on success, WP_ERROR on failure
	 */
	protected function validate_fields( $values ) {
		$this->fields =  apply_filters( 'before_submit_organizer_form_validate_fields', $this->fields , $values );
	      foreach ( $this->fields as $group_key => $group_fields )
    	  {     	      
				 
		        foreach ( $group_fields as $key => $field ) 
              	{
    				if ( $field['required'] && empty( $values[ $group_key ][ $key ] ) ) {	    
    					return new WP_Error( 'validation-error', sprintf(wp_kses('%s is a required field.', 'wp-event-manager' ), $field['label'] )) ;
    				}

				    if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], array( 'term-checklist', 'term-select', 'term-multiselect' ) ) ) {
    					if ( is_array( $values[ $group_key ][ $key ] ) ) {
    						$check_value = $values[ $group_key ][ $key ];
    					} else {
    						$check_value = empty( $values[ $group_key ][ $key ] ) ? array() : array( $values[ $group_key ][ $key ] );
    					}
    					foreach ( $check_value as $term ) {    
    						if ( ! term_exists( $term, $field['taxonomy'] ) ) {
    							return new WP_Error( 'validation-error', sprintf(wp_kses('%s is invalid', 'wp-event-manager' ), $field['label'] )) ;    
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
								throw new Exception( sprintf(wp_kses('"%s" (filetype %s) needs to be one of the following file types: %s', 'wp-event-manager' ), $field['label'], $info['ext'], implode( ', ', array_keys( $field['allowed_mime_types'] ) ) ) ) ;
							}
						}
					}
				}
			}
		}
		
		//organizer email validation
		if (isset( $values['organizer']['organizer_email'] ) && !empty( $values['organizer']['organizer_email'] ) ) {
			if ( ! is_email( $values['organizer']['organizer_email'] ) ) {
				throw new Exception( __( 'Please enter a valid organizer email address', 'wp-event-manager' ) );
			}				
		}
		
		return apply_filters( 'submit_organizer_form_validate_fields', true, $this->fields, $values );
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
			//if ( empty( $_POST['submit_organizer'] ) || !is_user_logged_in() ) {
			if ( empty( $_POST['submit_organizer'] ) ) {
				return;
			}
			// Validate required
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new Exception( $return->get_error_message() );
			}

			$status = is_user_logged_in() ? 'publish' : 'pending';
			
			// Update the event
			$this->save_organizer( $values['organizer']['organizer_name'], $values['organizer']['organizer_description'], $this->organizer_id ? '' : $status, $values );
			$this->update_organizer_data( $values );
			// Successful, show next step
			$this->step ++;
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Update or create a organizer from posted data
	 *
	 * @param  string $post_title
	 * @param  string $post_content
	 * @param  string $status
	 * @param  array $values
	 * @param  bool $update_slug
	 */
	protected function save_organizer( $post_title, $post_content, $status = 'publish', $values = array(), $update_slug = true ) {
		$organizer_data = array(
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'post_type'      => 'event_organizer',
			'comment_status' => 'closed'
		);


		if ( $status ) {
			$organizer_data['post_status'] = $status;
		}
		$organizer_data = apply_filters( 'submit_organizer_form_save_organizer_data', $organizer_data, $post_title, $post_content, $status, $values );
		if ( $this->organizer_id ) {
			$organizer_data['ID'] = $this->organizer_id;
			wp_update_post( $organizer_data );
		} else {
			$this->organizer_id = wp_insert_post( $organizer_data );
			if ( ! headers_sent() ) {
				$submitting_key = uniqid();
				setcookie( 'wp-event-manager-submitting-organizer-id', $this->organizer_id, 0, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( 'wp-event-manager-submitting-organizer-key', $submitting_key, 0, COOKIEPATH, COOKIE_DOMAIN, false );
				update_post_meta( $this->organizer_id, '_submitting_key', $submitting_key );
			}
		}
	}

	/**
	 * Set event meta + terms based on posted values
	 *
	 * @param  array $values
	 */
	protected function update_organizer_data( $values ) {
		$maybe_attach = array();
		
		//get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format 	= WP_Event_Manager_Date_Time::get_datepicker_format();
		
		//covert datepicker format  into php date() function date format
		$php_date_format 		= WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format( $datepicker_date_format );


		// Loop fields and save meta and term data
		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				// Save taxonomies
				if ( ! empty( $field['taxonomy'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						wp_set_object_terms( $this->organizer_id, $values[ $group_key ][ $key ], $field['taxonomy'], false );
					} else {
						wp_set_object_terms( $this->organizer_id, array( $values[ $group_key ][ $key ] ), $field['taxonomy'], false );
					}				
				// oragnizer logo is a featured image
				}
				// elseif ( 'organizer_logo' === $key ) {
				// 	$attachment_id = is_numeric( $values[ $group_key ][ $key ] ) ? absint( $values[ $group_key ][ $key ] ) : $this->create_attachment( $values[ $group_key ][ $key ] );
				// 	if ( empty( $attachment_id ) ) {
				// 		delete_post_thumbnail( $this->organizer_id );
				// 	} else {
				// 		set_post_thumbnail( $this->organizer_id, $attachment_id );
				// 	}
				// 	update_user_meta( get_current_user_id(), '_organizer_logo', $attachment_id );
				// }
				elseif ( $field['type'] == 'date' ) {
					$date = $values[ $group_key ][ $key ];	
					if(!empty($date)) {
						//Convert date and time value into DB formatted format and save eg. 1970-01-01
						$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format  , $date );
						$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;
						update_post_meta( $this->organizer_id, '_' . $key, $date_dbformatted );
					}
					else
						update_post_meta( $this->organizer_id, '_' . $key, '' );
					
				}
				 else { 
					update_post_meta( $this->organizer_id, '_' . $key, $values[ $group_key ][ $key ] );
					// Handle attachments.
					if ( 'file' === $field['type']  ) {
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
		}
		$maybe_attach = array_filter( $maybe_attach );
		// Handle attachments
		if ( sizeof( $maybe_attach ) && apply_filters( 'event_manager_attach_uploaded_files', true ) ) {
			
			// Get attachments
			$attachments     = get_posts( 'post_parent=' . $this->organizer_id . '&post_type=attachment&fields=ids&numberposts=-1' );
			$attachment_urls = array();
			// Loop attachments already attached to the event
			foreach ( $attachments as $attachment_key => $attachment ) {
				$attachment_urls[] = wp_get_attachment_url( $attachment );
			}
			foreach ( $maybe_attach as $attachment_url ) {
				if ( ! in_array( $attachment_url, $attachment_urls ) && !is_numeric($attachment_url) ) {
					$this->create_attachment( $attachment_url );
				}
			}
		}
	
		
		do_action( 'event_manager_update_organizer_data', $this->organizer_id, $values );
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
		$attachment_url = esc_url( $attachment_url, array( 'http', 'https' ) );
		if ( empty( $attachment_url ) ) {
			return 0;
		}
		
		$attachment_url_parts = wp_parse_url( $attachment_url );
		if ( false !== strpos( $attachment_url_parts['path'], '../' ) ) {
			return 0;
		}
		$attachment_url = sprintf( '%s://%s%s', $attachment_url_parts['scheme'], $attachment_url_parts['host'], $attachment_url_parts['path'] );
		$attachment_url = str_replace( array( $upload_dir['baseurl'], WP_CONTENT_URL, site_url( '/' ) ), array( $upload_dir['basedir'], WP_CONTENT_DIR, ABSPATH ), $attachment_url );
		if ( empty( $attachment_url ) || ! is_string( $attachment_url ) ) {
			return 0;
		}
		
		$attachment     = array(
							'post_title'   => get_the_title( $this->organizer_id ),
							'post_content' => '',
							'post_status'  => 'inherit',
							'post_parent'  => $this->organizer_id,
							'guid'         => $attachment_url
						);
	
		if ( $info = wp_check_filetype( $attachment_url ) ) {
			$attachment['post_mime_type'] = $info['type'];
		}
	
		$attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->organizer_id );
	
		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
			return $attachment_id;
		}
	
		return 0;
	}

	/**
	 * Done Step
	 */
	public function done() {
		do_action( 'event_manager_organizer_submitted', $this->organizer_id );
		get_event_manager_template( 
			'organizer-submitted.php', 
			array( 
				'organizer' => get_post( $this->organizer_id ),
			),
			'wp-event-manager/organizer', 
            EVENT_MANAGER_PLUGIN_DIR . '/templates/organizer'
		);
	}
}
