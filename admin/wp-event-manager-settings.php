<?php
/*
* This file use for settings at admin site for wp event manager plugin.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Event_Manager_Settings class.
 */

class WP_Event_Manager_Settings {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */

	public function __construct() {

		$this->settings_group = 'event_manager';

		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * init_settings function.
	 *
	 * @access protected
	 * @return void
	 */

	protected function init_settings() {

		// Prepare roles option

		$roles         = get_editable_roles();

		$account_roles = array();
		foreach ( $roles as $key => $role ) {

			if ( $key == 'administrator' ) {

				continue;
			}

			$account_roles[ $key ] = $role['name'];
		}

		$this->settings = apply_filters( 'event_manager_settings',

			array(

				'event_listings' => array(

					__( 'Event Listings', 'wp-event-manager' ),

					array(

						array(

							'name'        => 'event_manager_per_page',

							'std'         => '10',

							'placeholder' => '',

							'label'       => __( 'Listings Per Page', 'wp-event-manager' ),

							'desc'        => __( 'How many listings should be shown per page by default?', 'wp-event-manager' ),

							'attributes'  => array()
						),

						array(

							'name'       => 'event_manager_hide_cancelled_events',

							'std'        => '0',

							'label'      => __( 'Cancelled Events', 'wp-event-manager' ),

							'cb_label'   => __( 'Hide cancelled events', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, cancelled events will be hidden from archives.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_hide_expired_content',

							'std'        => '1',

							'label'      => __( 'Expired Listings', 'wp-event-manager' ),

							'cb_label'   => __( 'Hide content within expired listings', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, the content within expired listings will be hidden. Otherwise, expired listings will be displayed as normal (without the event registration area).', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_categories',

							'std'        => '0',

							'label'      => __( 'Categories', 'wp-event-manager' ),

							'cb_label'   => __( 'Enable categories for listings', 'wp-event-manager' ),

							'desc'       => __( 'Choose whether to enable categories. Categories must be setup by an admin to allow users to choose them during submission.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_event_types',

							'std'        => '0',

							'label'      => __( 'Event Types', 'wp-event-manager' ),

							'cb_label'   => __( 'Enable event types for listings', 'wp-event-manager' ),

							'desc'       => __( 'Choose whether to enable event types. event types must be setup by an admin to allow users to choose them during submission.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_event_ticket_prices',

							'std'        => '0',

							'label'      => __( 'Ticket prices', 'wp-event-manager' ),

							'cb_label'   => __( 'Enable ticket prices for listings', 'wp-event-manager' ),

							'desc'       => __( 'Choose whether to enable ticket prices. Ticket prices must be setup by an admin to allow users to choose them during submission.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),						

						array(

							'name'       => 'event_manager_enable_default_category_multiselect',

							'std'        => '0',

							'label'      => __( 'Multi-select Categories', 'wp-event-manager' ),

							'cb_label'   => __( 'Enable category multiselect by default', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, the category select box will default to a multi select on the [events] shortcode.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_default_event_type_multiselect',

							'std'        => '0',

							'label'      => __( 'Multi-select Event Types', 'wp-event-manager' ),

							'cb_label'   => __( 'Enable event type multiselect by default', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, the event type select box will default to a multi select on the [events] shortcode.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_category_filter_type',

							'std'        => 'any',

							'label'      => __( 'Category Filter Type', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, the category select box will default to a multi select on the [events] shortcode.', 'wp-event-manager' ),

							'type'       => 'select',

							'options' => array(

								'any'  => __( 'Events will be shown if within ANY selected category', 'wp-event-manager' ),

								'all' => __( 'Events will be shown if within ALL selected categories', 'wp-event-manager' ),
							)
						),

						array(

							'name'       => 'event_manager_event_type_filter_type',

							'std'        => 'any',

							'label'      => __( 'Event Type Filter', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, the event type select box will default to a multi select on the [events] shortcode.', 'wp-event-manager' ),

							'type'       => 'select',

							'options' => array(

								'any'  => __( 'Events will be shown if within ANY selected event type', 'wp-event-manager' ),

								'all' => __( 'Events will be shown if within ALL selected event types', 'wp-event-manager' ),
							)
						),
					  array(
									'name'       => 'event_manager_date_format',
									'std'        => 'relative',
									'label'      => __( 'Date Format', 'wp-event-manager' ),
									'desc'       => __( 'Choose how you want the published date for events to be displayed on the front-end.', 'wp-event-manager' ),
									'type'       => 'select',
									'options'    => array(
											'relative' => __( 'Relative to the current date (e.g., 1 day, 1 week, 1 month ago)', 'wp-event-manager' ),
											'default'   => __( 'Default date format as defined in Setttings', 'wp-event-manager' ),
									)
							)
					
						
						
					),
				),

				'event_submission' => array(

					__( 'Event Submission', 'wp-event-manager' ),

					array(

						array(

							'name'       => 'event_manager_user_requires_account',

							'std'        => '1',

							'label'      => __( 'Account Required', 'wp-event-manager' ),

							'cb_label'   => __( 'Submitting listings requires an account', 'wp-event-manager' ),

							'desc'       => __( 'If disabled, non-logged in users will be able to submit listings without creating an account.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_registration',

							'std'        => '1',

							'label'      => __( 'Account Creation', 'wp-event-manager' ),

							'cb_label'   => __( 'Allow account creation', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, non-logged in users will be able to create an account by entering their email address on the submission form.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_generate_username_from_email',

							'std'        => '1',

							'label'      => __( 'Account Username', 'wp-event-manager' ),

							'cb_label'   => __( 'Automatically Generate Username from Email Address', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, a username will be generated from the first part of the user email address. Otherwise, a username field will be shown.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_registration_role',

							'std'        => 'employer',

							'label'      => __( 'Account Role', 'wp-event-manager' ),

							'desc'       => __( 'If you enable registration on your submission form, choose a role for the new user.', 'wp-event-manager' ),

							'type'       => 'select',

							'options'    => $account_roles
						),

						array(

							'name'       => 'event_manager_submission_requires_approval',

							'std'        => '1',

							'label'      => __( 'Moderate New Listings', 'wp-event-manager' ),

							'cb_label'   => __( 'New listing submissions require admin approval', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, new submissions will be inactive, pending admin approval.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_user_can_edit_pending_submissions',

							'std'        => '0',

							'label'      => __( 'Allow Pending Edits', 'wp-event-manager' ),

							'cb_label'   => __( 'Submissions awaiting approval can be edited', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, submissions awaiting admin approval can be edited by the user.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),
						array(		
								'name'       => 'event_manager_user_can_add_multiple_banner',

								'std'        => '0',
	
								'label'      => __( 'Allow Multiple Banners', 'wp-event-manager' ),
	
								'cb_label'   => __( 'User can submit multiple banner', 'wp-event-manager' ),

								'desc'       => __( 'If enabled, Multiple banner can add at frontend by user and backend side by admin.', 'wp-event-manager' ),
	
								'type'       => 'checkbox',
		
								'attributes' => array()
						),
						array(

							'name'       => 'event_manager_delete_expired_events',

							'std'        => '0',

							'label'      => __( 'Delete Expired listings', 'wp-event-manager' ),

							'cb_label'   => __( 'Expired listings are deleted after 30 days', 'wp-event-manager' ),

							'desc'       => __( 'If enabled, expired listings will automatically deleted after 30 days.', 'wp-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_submission_expire_options',

							'std'        => 'event_end_date',

							'label'      => __( 'Listing Expire', 'wp-event-manager' ),

							'desc'       => __( 'You can set event submission expiry time either event end date or specific days.', 'wp-event-manager' ),

							'type'       => 'select',

							'options' => array(

								'event_end_date'  => __( 'Listing expire on Event End Date', 'wp-event-manager' ),

								'days' => __( 'Listing expire on Specified Below Days', 'wp-event-manager' ),
							)
						),

						array(

							'name'       => 'event_manager_submission_duration',

							'std'        => '30',

							'label'      => __( 'Listing Duration', 'wp-event-manager' ),

							'desc'       => __( 'How many <strong>days</strong> listings are live before expiring. Can be left blank to never expire.', 'wp-event-manager' ),

							'attributes' => array()
						),
						array(
							'name'       => 'event_manager_allowed_registration_method',
							'std'        => '',
							'label'      => __( 'Registration Method', 'wp-event-manager' ),
							'desc'       => __( 'Choose the registratoin method for listings.', 'wp-event-manager' ),
							'type'       => 'select',
							'options'    => array(
								''      => __( 'Email address or website URL', 'wp-event-manager' ),
								'email' => __( 'Email addresses only', 'wp-event-manager' ),
								'url'   => __( 'Website URLs only', 'wp-event-manager' ),
							)
						),
						array(
									
								'name'       => 'event_manager_multiselect_event_type',
									
								'std'        => '0',
									
								'label'      => __( 'Multi-select Event Types For Submission', 'wp-event-manager' ),
									
								'cb_label'   => __( 'Enable multi select event type for event listing submission', 'wp-event-manager' ),
									
								'desc'       => __( 'If enabled each event can have more than one type. The metabox on the post editor and the select box for event type on the frontend event submission form are changed by this.', 'wp-event-manager' ),
									
								'type'       => 'checkbox',
									
								'attributes' => array()
						),
						array(
									
								'name'       => 'event_manager_multiselect_event_category',
									
								'std'        => '0',
									
								'label'      => __( 'Multi-select Event Category For Submission', 'wp-event-manager' ),
									
								'cb_label'   => __( 'Enable multi select event category for event listing submission', 'wp-event-manager' ),
									
								'desc'       => __( 'If enabled each event can have more than one category. The metabox on the post editor and the select box for event category on the frontend event submission form are changed by this.', 'wp-event-manager' ),
									
								'type'       => 'checkbox',
									
								'attributes' => array()
						),
					)
				),

				'event_pages' => array(

					__( 'Pages', 'wp-event-manager' ),

					array(

						array(

							'name' 		=> 'event_manager_submit_event_form_page_id',

							'std' 		=> '',

							'label' 	=> __( 'Submit Event Form Page', 'wp-event-manager' ),

							'desc'		=> __( 'Select the page where you have placed the [submit_event_form] shortcode. This lets the plugin know where the form is located.', 'wp-event-manager' ),

							'type'      => 'page'
						),

						array(

							'name' 		=> 'event_manager_event_dashboard_page_id',

							'std' 		=> '',

							'label' 	=> __( 'Event Dashboard Page', 'wp-event-manager' ),

							'desc'		=> __( 'Select the page where you have placed the [event_dashboard] shortcode. This lets the plugin know where the dashboard is located.', 'wp-event-manager' ),

							'type'      => 'page'
						),

						array(

							'name' 		=> 'event_manager_events_page_id',

							'std' 		=> '',

							'label' 	=> __( 'Event Listings Page', 'wp-event-manager' ),

							'desc'		=> __( 'Select the page where you have placed the [events] shortcode. This lets the plugin know where the event listings page is located.', 'wp-event-manager' ),

							'type'      => 'page'
						),
					)
				)
			)
		);
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */

	public function register_settings() {

		$this->init_settings();

		foreach ( $this->settings as $section ) {

			foreach ( $section[1] as $option ) {

				if ( isset( $option['std'] ) )

					add_option( $option['name'], $option['std'] );

				register_setting( $this->settings_group, $option['name'] );
			}
		}
	}

	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */

	public function output() {

		$this->init_settings();

		?>
		
	
       
		<div class="wrap event-manager-settings-wrap">	

			<form method="post" name="event-manager-settings-form" action="options.php">	

				<?php settings_fields( $this->settings_group ); ?>

			    <h2 class="nav-tab-wrapper">

			    	<?php

			    		foreach ( $this->settings as $key => $section ) {

			    			echo '<a href="#settings-' . sanitize_title( $key ) . '" class="nav-tab">' . esc_html( $section[0] ) . '</a>';
			    		}
			    	?>
			    </h2>
			    
			 <div class="admin-setting-left">
			     	
			     <div class="white-background">
			     		
				<?php

					if ( ! empty( $_GET['settings-updated'] ) ) {

						flush_rewrite_rules();

						echo '<div class="updated fade event-manager-updated"><p>' . __( 'Settings successfully saved', 'wp-event-manager' ) . '</p></div>';
					}

					foreach ( $this->settings as $key => $section ) {

						echo '<div id="settings-' . sanitize_title( $key ) . '" class="settings_panel">';

						echo '<table class="form-table">';

						foreach ( $section[1] as $option ) {

							$placeholder    = ( ! empty( $option['placeholder'] ) ) ? 'placeholder="' . $option['placeholder'] . '"' : '';

							$class          = ! empty( $option['class'] ) ? $option['class'] : '';

							$value          = get_option( $option['name'] );

							$option['type'] = ! empty( $option['type'] ) ? $option['type'] : '';

							$attributes     = array();

							if ( ! empty( $option['attributes'] ) && is_array( $option['attributes'] ) )

								foreach ( $option['attributes'] as $attribute_name => $attribute_value )

									$attributes[] = esc_attr( $attribute_name ) . '="' . esc_attr( $attribute_value ) . '"';

							echo '<tr valign="top" class="' . $class . '"><th scope="row"><label for="setting-' . $option['name'] . '">' . $option['label'] . '</a></th><td>';

							switch ( $option['type'] ) {

								case "checkbox" :

									?><label><input id="setting-<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" type="checkbox" value="1" <?php echo implode( ' ', $attributes ); ?> <?php checked( '1', $value ); ?> /> <?php echo $option['cb_label']; ?></label><?php

									if ( $option['desc'] )

										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;

								case "textarea" :

									?><textarea id="setting-<?php echo $option['name']; ?>" class="large-text" cols="50" rows="3" name="<?php echo $option['name']; ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?>><?php echo esc_textarea( $value ); ?></textarea><?php

									if ( $option['desc'] )

										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;

								case "select" :

									?><select id="setting-<?php echo $option['name']; ?>" class="regular-text" name="<?php echo $option['name']; ?>" <?php echo implode( ' ', $attributes ); ?>><?php

										foreach( $option['options'] as $key => $name )

											echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $name ) . '</option>';

									?></select><?php

									if ( $option['desc'] ) {

										echo ' <p class="description">' . $option['desc'] . '</p>';

									}

								break;

								case "page" :

									$args = array(

										'name'             => $option['name'],

										'id'               => $option['name'],

										'sort_column'      => 'menu_order',

										'sort_order'       => 'ASC',

										'show_option_none' => __( '--no page--', 'wp-event-manager' ),

										'echo'             => false,

										'selected'         => absint( $value )

									);

									echo str_replace(' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'wp-event-manager' ) .  "' id=", wp_dropdown_pages( $args ) );

									if ( $option['desc'] ) {

										echo ' <p class="description">' . $option['desc'] . '</p>';

									}
									
								break;

								case "password" :

									?><input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="password" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> /><?php

									if ( $option['desc'] ) {

										echo ' <p class="description">' . $option['desc'] . '</p>';
									}

								break;

								case "" :

								case "input" :

								case "text" :

									?><input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="text" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> /><?php

									if ( $option['desc'] ) {

										echo ' <p class="description">' . $option['desc'] . '</p>';
								}

								break;		
								
								case "multi-select-checkbox":
								    $this->create_multi_select_checkbox($option);
									break;

								default :

									do_action( 'wp_event_manager_admin_field_' . $option['type'], $option, $attributes, $value, $placeholder );

								break;
							}
							echo '</td></tr>';
						}
						echo '</table></div>';
					}
				?>
				 </div>   <!-- .white-background- -->
				<p class="submit">
					<input type="submit" class="button-primary" id="save-changes" value="<?php _e( 'Save Changes', 'wp-event-manager' ); ?>" />
				</p>
			 </div>  <!-- .admin-setting-left -->						
		    </form>
		    
            <div id="plugin_info" class="box-info">
                <div class="box-title" title="Click to toggle"><br></div><h3><span>Plugin Info</span></h3>
                    <div class="inside">
                        <p> 
                             <span class="premium-icon"></span><b>Help to improve this plugin!</b> <br>Enjoyed this plugin? You can help by 5 stars rating this plugin on <a href="https://wordpress.org/plugins/wp-event-manager/" target="_blank" >wordpress.org.</a>
                        </p>
                        <p>  
                           <span class="help-icon"></span><b>Need help?</b> <br>
                           Read the <a href="https://wp-eventmanager.com/documentation/" target="_blank" >Documentation.</a><br>
                           Check the <a href="https://wp-eventmanager.com/faqs/" target="_blank">FAQs.</a><br>
                        </p>
                        <p>  
                           <span class="connect-icon"></span><b>Demo</b> <br>Visit the <a href="http://www.wp-eventmanager.com/demo" target="_blank">Plugin Demo.</a><br>
                           Visit the <a href="http://www.wp-eventmanager.com/plugins/" target="_blank">Premium Add-ons</a>.<br>                           
                        </p>
                        
                        <p><span class="light-grey">This plugin was made by</span> <a href="https://wp-eventmanager.com/" target="_blank">WP Event Manager</a>.
                        </p>
                    </div>
                </div>
            </div>
	  	

		<?php  wp_enqueue_script( 'wp-event-manager-admin-settings');
	}
	
	/**
	 * Creates Multiselect checkbox.
	 * This function generate multiselect 
	 * @param $value
	 * @return void
	 */ 
	public function create_multi_select_checkbox($value) 
	{ 
		
		echo '<ul class="mnt-checklist" id="'.$value['name'].'" >'."\n";
		foreach ($value['options'] as $option_value => $option_list) {
			$checked = " ";
			if (get_option($value['name'] ) ) {
			
                                 $all_country = get_option( $value['name'] );
                                 $start_string = strpos($option_list['name'],'[');
                                 $country_code = substr($option_list['name'] ,$start_string + 1 ,  2 );
                                 $coutry_exist = array_key_exists($country_code , $all_country);
                              if( $coutry_exist ){
                                     $checked = " checked='checked' ";       
                                     
                              }
			}
			echo "<li>\n";

			echo '<input id="setting-'.$option_list['name'].'" name="'.$option_list['name'].'" type="checkbox" '.$checked.'/>'.$option_list['cb_label']."\n";
			echo "</li>\n";
		}
		echo "</ul>\n";
    }	
}