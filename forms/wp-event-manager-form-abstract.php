<?php
/**
 * Abstract WP_Event_Manager_Form class.
 *
 * @abstract
 */
abstract class WP_Event_Manager_Form {

	protected $fields    = array();
	protected $action    = '';
	protected $errors    = array();
	protected $steps     = array();
	protected $step      = 0;
	public    $form_name = '';
	
	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong(__FUNCTION__);
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong(__FUNCTION__);
	}

	/**
     * Process function. all processing code if needed - can also change view if step is complete
     */
    public function process() {
		// Reset cookie
		if(
			isset($_GET[ 'new' ]) &&
			isset($_COOKIE[ 'wp-event-manager-submitting-event-id' ]) &&
			isset($_COOKIE[ 'wp-event-manager-submitting-event-key' ]) &&
			esc_attr(get_post_meta(absint($_COOKIE[ 'wp-event-manager-submitting-event-id' ]), '_wpem_unique_key', true)) == $_COOKIE['wp-event-manager-submitting-event-key']
			) {
				delete_post_meta(absint($_COOKIE[ 'wp-event-manager-submitting-event-id' ]), '_wpem_unique_key');
				setcookie('wp-event-manager-submitting-event-id', '', 0, COOKIEPATH, COOKIE_DOMAIN, false);
				setcookie('wp-event-manager-submitting-event-key', '', 0, COOKIEPATH, COOKIE_DOMAIN, false);
				wp_redirect(esc_url(remove_query_arg(array('new', 'key'), esc_url_raw( wp_unslash($_SERVER[ 'REQUEST_URI' ])))));
			}
    			
    	$step_key = $this->get_step_key($this->step);
        if($step_key && is_callable($this->steps[ $step_key ]['handler'])) {
            call_user_func($this->steps[ $step_key ]['handler']);
        }
        $next_step_key = $this->get_step_key($this->step);
 
        // If the step changed, but the next step has no 'view', call the next handler in sequence.
        if($next_step_key && $step_key !== $next_step_key && !is_callable($this->steps[ $next_step_key ]['view'])) {
            $this->process();
        }
 
    }
	/**
	 * Get form name.
	 * @since 1.24.0
	 * @return string
	 */
	public function get_form_name() {
		return $this->form_name;
	}
		
	/**
	 * Output function. Call the view handler.
	 */
	public function output($atts = array()) {
		$step_key = $this->get_step_key($this->step);
		$this->show_errors();
		if($step_key && is_callable($this->steps[ $step_key ]['view'])) {
            call_user_func($this->steps[ $step_key ]['view'], $atts);
        }
	}

	/**
	 * Add an error.
	 * @param string $error
	 */
	public function add_error($error) {
		$this->errors[] = esc_attr($error);
	}

	/**
	 * Show errors.
	 */
	public function show_errors() {
		foreach ($this->errors as $error) {
			echo wp_kses_post('<div class="event-manager-error wpem-alert wpem-alert-danger">' .  esc_html($error) . '</div>');
		}
	}

	/**
	 * Get errors.
	 */
	public function get_errors() {
		foreach ($this->errors as $error) {
			return $error;
		}
	}
	
	/**
	 * Get action (URL for forms to post to).
	 * As of 1.22.2 this defaults to the current page permalink.
	 *
	 * @return string
	 */
	public function get_action() {
		return esc_url_raw($this->action ? $this->action : wp_unslash($_SERVER['REQUEST_URI']));
	}

	/**
	 * Get step from outside of the class.
	 */

	public function get_step() {
		return $this->step;
	}
	/**
	 * Get steps from outside of the class.
	 * @since 1.24.0
	 */
	public function get_steps() {
		return $this->steps;
	}
	/**
	 * Get step key from outside of the class.
	 * @since 1.0.17
	 */
	public function get_step_key($step = '') {
		if(!$step) {
			$step = $this->step;
		}
		$keys = array_keys($this->steps);
		return isset($keys[ $step ]) ? $keys[ $step ] : '';
	}
	
	/**
	 * Get step from outside of the class.
	 * @since 1.24.0
	 */
	public function set_step($step) {
		$this->step = absint($step);
	}
	/**
	 * Increase step from outside of the class.
	 */

	public function next_step() {
		$this->step ++;
	}

	/**
	 * Decrease step from outside of the class.
	 */

	public function previous_step() {
		$this->step --;
	}

	/**
	 * get_fields function.
	 *
	 * @param string $key
	 * @return array
	 */
	public function get_fields($key) {
		if(empty($this->fields[ $key ])) {
			return array();
		}
		$fields = $this->fields[ $key ];
		uasort($fields, array($this, 'sort_by_priority'));
		return $fields;
	}

	/**
	 * Sort array by priority value.
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	protected function sort_by_priority($a, $b) {
	    if(isset($a['priority']) && isset($b['priority']) && ($a['priority'] == $b['priority'])) {
	        return 0;
	    }
	    return (isset($a['priority']) && isset($b['priority']) && ($a['priority'] < $b['priority'])) ? -1 : 1;
	}
	
	/**
	 * Init form fields.
	 */
	protected function init_fields() {
		$this->fields = array();
	}
	
	/**
	 * Enqueue the scripts for the form.
	 */
	public function enqueue_scripts() {
		if($this->use_recaptcha_field()) {
			wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js');
		}
	}

	/**
	 * Get post data for fields.
	 *
	 * @return array of data
	 */
	public function get_posted_fields() {
	    
		// Init fields
		// $this->init_fields(); We dont need to initialize with this function because of field edior
		// Now field editor function will return all the fields 
		// Get merged fields from db and default fields.
		$this->merge_with_custom_fields('frontend');

		$values = array();

		foreach ($this->fields as $group_key => $group_fields) {
			foreach ($group_fields as $key => $field) {
				
				// Get the value
				$field_type = str_replace('-', '_', $field['type']);
				
				if($handler = apply_filters("event_manager_get_posted_{$field_type}_field", false)) {
					$values[ $group_key ][ $key ] = call_user_func($handler, $key, $field);
					if (is_string( $values[ $group_key ][ $key ] ) ) {
						$values[ $group_key ][ $key ] = wp_unslash( $values[ $group_key ][ $key ] );
					}
				} elseif(method_exists($this, "get_posted_{$field_type}_field")) {
					$values[ $group_key ][ $key ] = call_user_func(array($this, "get_posted_{$field_type}_field"), $key, $field);
					if (is_string( $values[ $group_key ][ $key ] ) ) {
						$values[ $group_key ][ $key ] = wp_unslash( $values[ $group_key ][ $key ] );
					}
				} else {
					$values[ $group_key ][ $key ] = $this->get_posted_field($key, $field);
					if ( is_string( $values[ $group_key ][ $key ])) {
						$values[ $group_key ][ $key ] = wp_unslash( $values[ $group_key ][ $key ]);
					}
				}
				// Set fields value
				$this->fields[ $group_key ][ $key ]['value'] = wp_unslash( $values[ $group_key ][ $key ]);
				
				if(isset($field['visibility']) && ($field['visibility'] == 0 || $field['visibility'] == false)) :
					unset($values[ $group_key ][ $key ]);
				endif; 
			}
		}
		return $values;
	}
	
	/* Get the value of a repeated fields (e.g. repeated).
	 * @param  array $fields
	 * @return array
	 */
	protected function get_repeated_field($field_prefix, $fields) {	
		if(empty($fields))
			return;
		// Get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format 	= WP_Event_Manager_Date_Time::get_datepicker_format();
		
		// Covert datepicker format  into php date() function date format
		$php_date_format 		= WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

		$items       = array();
		$field_keys  = array_keys($fields);
		$field_prefix = esc_attr($field_prefix);
		if(!empty($_POST[ 'repeated-row-' . $field_prefix ]) && is_array($_POST[ 'repeated-row-' . $field_prefix ])) {
			$indexes = array_map('absint', $_POST[ 'repeated-row-' . $field_prefix ]);
			foreach ($indexes as $index) {
				$item = array();
				foreach ($fields as $key => $field) {
					$field_name = $field_prefix . '_' . $key . '_' . $index;
					
					switch ($field['type']) {
						case 'textarea' :
							$item[ $key ] = wp_kses_post(stripslashes($_POST[ $field_name ]));
							break;
						case 'number' :
							if(is_array($_POST[ $field_name ])) {
								$item[ $key ] = array_filter(array_map('sanitize_text_field', array_map('stripslashes', $_POST[ $field_name ])));
							} else {
								$item[ $key ] = sanitize_text_field(stripslashes($_POST[ $field_name ]));
							}	
							break;
						case 'date' :
							if(!empty($_POST[ $field_name ])){
								//Convert date and time value into DB formatted format and save eg. 1970-01-01
								//$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, sanitize_text_field($_POST[ $field_name ]));	
								$item[ $key ] = sanitize_text_field($_POST[ $field_name ]);
							} else{
								$item[ $key ] = '';
							}
							break;
						case 'time' :
							if(!empty($_POST[ $field_name ])) {
								$time_dbformatted = WP_Event_Manager_Date_Time::get_db_formatted_time(sanitize_text_field($_POST[ $field_name ]));
								$item[ $key ] = !empty($time_dbformatted) ? $time_dbformatted : sanitize_text_field($_POST[ $field_name ]);
							} else {
								$item[ $key ] = '';
							}
							break;	
						case 'file' :
							$file = $this->upload_file($field_name, $field);

							if(!$file) {
								$file = $this->get_posted_field('current_' . $field_name, $field);
							} elseif(is_array($file)) {
								$file = array_filter(array_merge($file, (array) $this->get_posted_field('current_' . $field_name, $field)));
							}
							$item[ $key ] = $file;
							break;
						case 'checkbox':
								if(!empty($_POST[ $field_name ]) && $_POST[ $field_name ] > 0) {
									$item[ $key ] = wp_kses_post(stripslashes($_POST[ $field_name ]));			
								}
							break;
						default :
							if(!empty($_POST[ $field_name ])){
								if(is_array($_POST[ $field_name ])) {
									$item[ $key ] = array_filter(array_map('sanitize_text_field', array_map('stripslashes', $_POST[ $field_name ])));
								} else {
									$item[ $key ] = sanitize_text_field(stripslashes($_POST[ $field_name ]));
								}	
							}else{
									$item[ $key ] = '';
							}
							break;
					}
					if(empty($item[ $key ]) && !empty($field['required']) && $field['type'] != 'number') {
						continue 2;
					}
				}
				$items[] = $item;
			}
		}
		return $items;
	}
	
	/**
	 * Get the value of a posted repeated field.
	 * @since  1.22.4
	 * @param  string $key
	 * @param  array $field
	 * @return string
	 */
	protected function get_posted_repeated_field($key, $field) {
		if(isset($field['fields']) && !empty($field['fields']))
		return  $this->get_repeated_field($key, $field['fields']);
	}

	/**
	 * Get the value of a posted group field.
	 * @since  3.1.19
	 * @param  string $key
	 * @param  array $field
	 * @return string
	 */
	protected function get_posted_group_field($key, $field) {
		return  $this->get_repeated_field($key, $field['fields']);
	}

	/**
	 * Navigates through an array and sanitizes the field.
	 *
	 * @param array|string $value The array or string to be sanitized.
	 * @return array|string $value The sanitized array (or string from the callback).
	 */
	protected function sanitize_posted_field($value) {
		// Decode URLs
		if(is_string($value) && (strstr($value, 'http:') || strstr($value, 'https:'))) {
			$value = urldecode($value);
		}
		// Santize value
		$value = is_array($value) ? array_map(array($this, 'sanitize_posted_field'), $value) : sanitize_text_field(stripslashes(trim($value)));
		return $value;
	}

	/**
	 * Get the value of a posted field.
	 * @param  string $key
	 * @param  array $field
	 * @return string|array
	 */
	protected function get_posted_field($key, $field) {
		return isset($_POST[ $key ]) ? $this->sanitize_posted_field($_POST[ $key ]) : '';
	}
	
	/**
	 * Get the value of a posted multiselect field.
	 * @param  string $key
	 * @param  array $field
	 * @return array
	 */
	protected function get_posted_multiselect_field($key, $field) {
		return isset($_POST[ $key ]) ? array_map('sanitize_text_field', $_POST[ $key ]) : array();
	}

	/**
	 * Get the value of a posted file field.
	 * @param  string $key
	 * @param  array $field
	 * @return string|array
	 */
	protected function get_posted_file_field($key, $field) {

		$file = $this->upload_file($key, $field);

		if(!$file) {
			$file = $this->get_posted_field('current_' . $key, $field);
		} elseif(is_array($file)) {
			$file = array_filter(array_merge($file, (array) $this->get_posted_field('current_' . $key, $field)));
		}
		
		return $file;
	}

	/**
	 * Get the value of a posted textarea field.
	 * @param  string $key
	 * @param  array $field
	 * @return string
	 */
	protected function get_posted_textarea_field($key, $field) {
		return isset($_POST[ $key ]) ? wp_kses_post(trim(stripslashes($_POST[ $key ]))) : '';
	}

	/**
	 * Get the value of a posted textarea field.
	 * @param  string $key
	 * @param  array $field
	 * @return string
	 */
	protected function get_posted_wp_editor_field($key, $field) {
		return $this->get_posted_textarea_field($key, $field);
	}
	
	/**
	 * Get posted terms for the taxonomy.
	 * @param  string $key
	 * @param  array $field
	 * @return array
	 */
	protected function get_posted_term_checklist_field($key, $field) {
		if(isset($_POST[ 'tax_input' ]) && isset($_POST[ 'tax_input' ][ $field['taxonomy'] ])) {
			return array_map('absint', $_POST[ 'tax_input' ][ $field['taxonomy'] ]);
		} else {
			return array();
		}
	}

	/**
	 * Get posted terms for the taxonomy.
	 * @param  string $key
	 * @param  array $field
	 * @return int
	 */
	protected function get_posted_term_multiselect_field($key, $field) {
		return isset($_POST[ $key ]) ? array_map('absint', $_POST[ $key ]) : array();
	}

	/**
	 * Get posted terms for the taxonomy.
	 * @param  string $key
	 * @param  array $field
	 * @return int
	 */
	protected function get_posted_term_select_field($key, $field) {
		return !empty($_POST[ $key ]) && $_POST[ $key ] > 0 ? absint($_POST[ $key ]) : '';
	}

	/**
	 * Upload a file.
	 * @return  string or array
	 */
	protected function upload_file($field_key, $field) {
		if(isset($_FILES[ $field_key ]) && !empty($_FILES[ $field_key ]) && !empty($_FILES[ $field_key ]['name'])) {
			if(!empty($field['allowed_mime_types'])) {
				$allowed_mime_types =sanitize_text_field($field['allowed_mime_types']);
			} else {
				$allowed_mime_types = get_allowed_mime_types();
			}
			$file_urls       = array();
			$files_to_upload = event_manager_prepare_uploaded_files($_FILES[ $field_key ]);
			foreach ($files_to_upload as $file_to_upload) {
				$uploaded_file = event_manager_upload_file($file_to_upload, array('file_key' => $field_key ,'allowed_mime_types' => $allowed_mime_types));
				if(is_wp_error($uploaded_file)) {
					throw new Exception($uploaded_file->get_error_message());
				} else {
					$file_urls[] = $uploaded_file->url;
				}
			}

			if(!empty($field['multiple'])) {
				return $file_urls;
			} else {
				return current($file_urls);
			}
		}
	}

	/**
	 * Merge and replace $default_fields with custom fields.
	 *
	 * @return array Returns merged and replaced fields
	 */
	public function merge_with_custom_fields($field_view = 'frontend') { 
	
		$custom_fields  = $this->get_event_manager_fieldeditor_fields();
		$default_fields = $this->get_default_fields();
		
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

		if(!get_option('event_manager_enable_categories') || (wp_count_terms('event_listing_category') == 0 && isset($custom_fields['event']['event_category']))) {
			if(isset($custom_fields['event']['event_category']))
				$custom_fields['event']['event_category']['visibility']=false;
			
		    unset($default_fields['event']['event_category']);
		}
		
		if(!get_option('event_manager_enable_event_types') || (wp_count_terms('event_listing_type') == 0 && isset($custom_fields['event']['event_type']))) {
			if(isset($custom_fields['event']['event_type']))
				$custom_fields['event']['event_type']['visibility']=false;
		    
			unset($default_fields['event']['event_type']);
		}
		
		if(!is_array($custom_fields)){
		    $this->fields = apply_filters('merge_with_custom_fields',$default_fields,$default_fields) ;
		    return $this->fields;
		}
		
		// $updated_fields = !empty($custom_fields) ? $custom_fields : $default_fields;
		$updated_fields = !empty($custom_fields) ? array_replace_recursive($default_fields, $custom_fields) : $default_fields;
		/**
		 * Above array_replace_recursive function will replace the default fields by custom fields.
		 * If array key is not same then it will merge array. This is only case for the Radio and Select Field(In case of array if key is not same).
		 * For eg. options key it has any value or option as per user requested or overrided but array_replace_recursive will merge both 		options of default field and custom fields.
		 * User change the default value of the event_online (radio button) from Yes --> Y and No--> N then array_replace_recursive will merge both valus of the options array for event_online like options('yes'=>'yes', 'no'=>'no','y'=>'y','n'=>'n') but  we need to keep only updated options value of the event_online so we have to remove old default options values and for that we have to do the following procedure.
		 * In short: To remove default options need to replace the options array with custom options which is added by user.
		 **/
		foreach($default_fields as $default_group_key => $default_group){
			foreach ($default_group as $field_key => $field_value) {
				foreach($field_value as $key => $value){
					if(isset($custom_fields[$default_group_key][$field_key][$key]) && ($key == 'visibility')){
						$updated_fields[$default_group_key][$field_key][$key] = $custom_fields[$default_group_key][$field_key][$key];
					}
					
					if(isset($custom_fields[$default_group_key][$field_key][$key]) && ($key == 'options' || is_array($value)))
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
			foreach ($updated_fields as $group_key => $group_fields) {
				foreach ($group_fields as $key => $field) {

					$updated_fields[$group_key][$key]=array_map('stripslashes_deep',$updated_fields[$group_key][$key]);				
					//remove if visiblity is false
					if(isset($field['visibility']) && ($field['visibility'] == false || $field['visibility'] == 0))
						$updated_fields[$group_key][$key]['visibility'] = 0;
					//remove admin fields if view type is frontend
					if(isset($field['admin_only']) &&  $field_view == 'frontend' &&  $field['admin_only'] == true)
						unset($updated_fields[$group_key][$key]);
				}
				uasort($updated_fields[$group_key], array($this, 'sort_by_priority'));
			}
		
		//unset timezone field if setting is site wise timezone
		$timezone_setting = get_option('event_manager_timezone_setting' ,'site_timezone');
		if($timezone_setting != 'each_event' && isset($updated_fields['event']['event_timezone'])) {
			unset($updated_fields['event']['event_timezone']);
		}
		$this->fields = apply_filters('merge_with_custom_fields',$updated_fields,$default_fields) ;
		return $this->fields;
	}
}