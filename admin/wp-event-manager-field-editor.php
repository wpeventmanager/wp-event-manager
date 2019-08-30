<?php

/**
 * WP_Event_Manager_Registrations_Form_Editor class.
 */
class WP_Event_Manager_Field_Editor {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}
	
	/**
	 * Add form editor menu item
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=event_listing', __( 'Field Editor', 'wp-event-manager' ),  __( 'Field Editor', 'wp-event-manager' ) , 'manage_options', 'event-manager-form-editor', array( $this, 'output' ) );
	}
	
	/**
	 * Register scripts
	 */
	public function admin_enqueue_scripts() {
		wp_register_script( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
		wp_register_script( 'wp-event-manager-form-field-editor', EVENT_MANAGER_PLUGIN_URL .'/assets/js/field-editor.min.js' , array( 'jquery', 'jquery-ui-sortable', 'chosen' ), EVENT_MANAGER_VERSION, true );
		wp_localize_script( 'wp-event-manager-form-field-editor', 'wp_event_manager_form_editor', array(
				'cofirm_delete_i18n' => __( 'Are you sure you want to delete this row?', 'wp-event-manager' ),
				'cofirm_reset_i18n'  => __( 'Are you sure you want to reset your changes? This cannot be undone.', 'wp-event-manager' )
		) );
		
	}
	
	/**
	 * Output the screen
	 */
	public function output() {
		wp_enqueue_style( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/css/chosen.min.css' );
		wp_enqueue_script( 'wp-event-manager-form-field-editor' );
		?>
		<div class="wrap wp-event-manager-form-field-editor">
			<form method="post" id="mainform" action="edit.php?post_type=event_listing&amp;page=event-manager-form-editor">
				<?php $this->form_editor(); ?>
				<?php wp_nonce_field( 'save-wp-event-manager-form-field-editor' ); ?>
			</form>
		</div>
		<?php
	}
	
	/**
	 * Output the fronted form editor
	 */
	private function form_editor() {
		if ( ! empty( $_GET['reset-fields'] ) && ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'reset' ) ) {
			delete_option( 'event_manager_form_fields' );
			echo '<div class="updated"><p>' . __( 'The fields were successfully reset.', 'wp-event-manager' ) . '</p></div>';
		}

		if ( ! empty( $_POST ) && ! empty( $_POST['_wpnonce'] ) ) {
			echo $this->form_editor_save();
		}

		if(!class_exists('WP_Event_Manager_Form_Submit_Event') ) {
			include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php' );
			include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php' );	
		}
		$form_submit_event_instance = call_user_func( array( 'WP_Event_Manager_Form_Submit_Event', 'instance' ) );
		$fields = $form_submit_event_instance->merge_with_custom_fields('backend');
		
		$disbled_fields = array('event_title','event_description');
		$field_types = apply_filters( 'event_manager_form_field_types', array(
			'text'           		=> __( 'Text', 'wp-event-manager' ),
			'time'           		=> __( 'Time', 'wp-event-manager' ),
			'button'           		=> __( 'Button', 'wp-event-manager' ),
			'button-options'       	=> __( 'Button Options', 'wp-event-manager' ),			
			'checkbox'       		=> __( 'Checkbox', 'wp-event-manager' ),			
			'date'       			=> __( 'Date', 'wp-event-manager' ),
			'timezone'           	=> __( 'Timezone', 'wp-event-manager' ),			
			'file'       			=> __( 'File', 'wp-event-manager' ),			
			'hidden'       			=> __( 'Hidden', 'wp-event-manager' ),			
			'multiselect'       	=> __( 'Multiselect', 'wp-event-manager' ),			
			'number'       			=> __( 'Number', 'wp-event-manager' ),			
			'password'       		=> __( 'Password', 'wp-event-manager' ),			
			'radio'       			=> __( 'Radio', 'wp-event-manager' ),			
			'repeated'       		=> __( 'Repeated', 'wp-event-manager' ),			
			'select'         		=> __( 'Select', 'wp-event-manager' ),
			'term-checklist'    	=> __( 'Term Checklist', 'wp-event-manager' ),
			'term-multiselect'    	=> __( 'Term Multiselect', 'wp-event-manager' ),
			'term-select'    		=> __( 'Term Select', 'wp-event-manager' ),
			'textarea'    			=> __( 'Textarea', 'wp-event-manager' ),
			'wp-editor'       		=> __( 'WP Editor', 'wp-event-manager' )
		) );
		?>
		<?php	
		foreach($fields  as $group_key => $group_fields){ ?>
			<div class="wp-event-manager-event-form-field-editor">
				<?php 
					if($group_key == 'event'){ ?>
					<h3><?php _e('Event fields','wp-event-manager');?></h3>
					<?php	
					}
					else if($group_key == 'organizer'){ ?>
					<h3><?php _e('Organizer fields','wp-event-manager');?></h3>
					<?php	
					}
					?>
				<table class="widefat">
					<thead>
						<tr>
							<th width="1%">&nbsp;</th>
							<th><?php _e( 'Field Label', 'wp-event-manager' ); ?></th>
							<th width="1%"><?php _e( 'Type', 'wp-event-manager' ); ?></th>
							<th><?php _e( 'Description', 'wp-event-manager' ); ?></th>
							<th><?php _e( 'Placeholder / Options', 'wp-event-manager' ); ?></th>
							<th width="1%"><?php _e( 'Meta Key', 'wp-event-manager' ); ?></th>
							<th width="1%"><?php _e( 'Only For Admin', 'wp-event-manager' ); ?></th>
							<th width="1%"><?php _e( 'Priority', 'wp-event-manager' ); ?></th>
							<th width="1%"><?php _e( 'Validation', 'wp-event-manager' ); ?></th>
							<th width="1%" class="field-actions">&nbsp;</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="4">
								<a class="button add-field" href="#"><?php _e( 'Add field', 'wp-event-manager' ); ?></a>
							</th>
							<?php if($group_key == 'organizer'){ ?>
							<th colspan="4" class="save-actions">
								<a href="<?php echo wp_nonce_url( add_query_arg( 'reset-fields', 1 ), 'reset' ); ?>" class="reset"><?php _e( 'Reset to default', 'wp-event-manager' ); ?></a>
								<input type="submit" class="save-fields button-primary" value="<?php _e( 'Save Changes', 'wp-event-manager' ); ?>" />
							</th>
							<?php } ?>
						</tr>
					</tfoot>
					<tbody id="form-fields" data-field="<?php
						ob_start();
						$index     = -1;
						$field_key = '';
						$field     = array(
							'type'        => 'text',
							'label'       => '',
							'placeholder' => ''
						);
						include( 'wp-event-manager-form-field-editor-field.php' );
						echo esc_attr( ob_get_clean() );
					?>"><?php
							if(isset($group_fields))
							foreach ( $group_fields as $field_key => $field ) {
								$index ++;
								include( 'wp-event-manager-form-field-editor-field.php' );
							}
					?></tbody>
				</table>
			</div>
<?php	} 
	}	
	
	/**
	 * Save the form fields
	 */
	private function form_editor_save() {
		if( wp_verify_nonce( $_POST['_wpnonce'], 'save-wp-event-manager-form-field-editor' ) )
		{
			$event_field          	  = ! empty( $_POST['event'] ) ?  $_POST['event'] 				: array();
			$event_organizer          = ! empty( $_POST['organizer'] ) ?  $_POST['organizer']   	: array();
			$index = 0;
			if(!empty($event_field) && !empty($event_organizer)){
				$new_fields = array('event' => $event_field ,'organizer' =>$event_organizer);
				
				//find the numers keys from the fields array and replace with lable if label not exist remove that field
				 foreach($new_fields as $group_key => $group_fields) {
					 foreach( $group_fields as $field_key => $field_value ) {
							$index++;
							$new_fields[$group_key][$field_key]['priority'] = $index;
							if ( isset($new_fields[$group_key][$field_key]['type']) && ! in_array($new_fields[$group_key][$field_key]['type'],  array('term-select', 'term-multiselect', 'term-checklist') ) ) {
								unset($new_fields[$group_key][$field_key]['taxonomy']);
							}
							if(isset($new_fields[$group_key][$field_key]['type']) && $new_fields[$group_key][$field_key]['type'] == 'select'  || $new_fields[$group_key][$field_key]['type'] == 'radio'  || $new_fields[$group_key][$field_key]['type'] == 'multiselect' || $new_fields[$group_key][$field_key]['type'] == 'button-options') {
								if(isset($new_fields[$group_key][$field_key]['options'])){
									$new_fields[$group_key][$field_key]['options'] = explode( '|', $new_fields[$group_key][$field_key]['options']);
									$temp_options = array();
									foreach($new_fields[$group_key][$field_key]['options'] as $val){
										$option_key = explode( ':', $val);
										if(isset($option_key[1]))
											$temp_options[strtolower(str_replace(' ', '_',trim($option_key[0])) )] =  $option_key[1] ;
										else
											$temp_options[strtolower(str_replace(' ', '_',trim($option_key[0])) )] =  $option_key[0] ;
									}
									$new_fields[$group_key][$field_key]['options'] = $temp_options;
								}
							}
							else{
								unset($new_fields[$group_key][$field_key]['options']);
							}
							
							if(!is_int($field_key)) continue;
							 
							if(isset($new_fields[$group_key][$field_key]['label'])){
								$label_key =  str_replace(' ',"_",$new_fields[$group_key][$field_key]['label']);
								$new_fields[$group_key][strtolower($label_key)]= $new_fields[$group_key][$field_key];
							}
							unset($new_fields[$group_key][$field_key]);
					}
				}
				
				//merge field with default fields
				if(!class_exists('WP_Event_Manager_Form_Submit_Event') ) {
					include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php' );
					include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php' );
				}
				
				$form_submit_event_instance = call_user_func( array( 'WP_Event_Manager_Form_Submit_Event', 'instance' ) );
				$default_fields = $form_submit_event_instance->get_default_fields('backend');
				//if field in not exist in new fields array then
				if(!empty($default_fields))
				foreach ( $default_fields as $group_key => $group_fields ) {
					foreach ($group_fields as $key => $field) {
						if( !isset( $new_fields[$group_key][$key] ) ){
							$new_fields[$group_key][$key] 				= $field;
							$new_fields[$group_key][$key]['visibility'] = 0;
						}
					}
				}
				
				$result = update_option( 'event_manager_form_fields', $new_fields );
			
			}	  
		}
		 
		 if ( isset($result) && true === $result ) {
				echo '<div class="updated"><p>' . __( 'The fields were successfully saved.', 'wp-event-manager' ) . '</p></div>';
		}
	}

	/**
	 * Sanitize a 2d array
	 * @param  array $array
	 * @return array
	 */
	private function sanitize_array( $input ) {
		if ( is_array( $input ) ) {
			foreach ( $input as $k => $v ) {
				$input[ $k ] = $this->sanitize_array( $v );
			}
			return $input;
		} else {
			return sanitize_text_field( $input );
		}
	}
	
}

new WP_Event_Manager_Field_Editor();