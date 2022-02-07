<?php
/*
* Main Admin functions class which responsible for the entire amdin functionality and scripts loaded and files.
*
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Event_Manager_Admin class.
 */

class WP_Event_Manager_Admin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */

	public function __construct() {

		include_once( 'wp-event-manager-cpt.php' );

		include_once( 'wp-event-manager-settings.php' );

		include_once( 'wp-event-manager-writepanels.php' );

		include_once( 'wp-event-manager-setup.php' );
		
		include_once( 'wp-event-manager-field-editor.php' );

		$this->settings_page = new WP_Event_Manager_Settings();

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		
		//add_action( 'admin_notices', array( $this,'run_setup_wizard_admin_notice') );
		
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'current_screen', array( $this, 'conditional_includes' ) );

		if ( version_compare( get_option( 'wp_event_manager_db_version', 0 ), '3.1.13', '<' ) )
		{
			add_action( 'admin_notices', array( $this, 'upgrade_database_notice' ) );
		}

		if ( get_option( 'wpem_installation_skip', false ) )
		{
			add_action( 'admin_notices', array( $this, 'wpem_installation_notices' ) );
		}

		// Ajax
		add_action( 'wp_ajax_wpem_upgrade_database', array( $this, 'wpem_upgrade_database' ) );		
		//add_action( 'wp_ajax_nopriv_wpem_upgrade_database', array( $this, 'wpem_upgrade_database' ) );
	}

	/**
	 * upgrade_database_notice function.
	 *
	 * @access public
	 * @return void
	 */
	public function upgrade_database_notice() {

		if ( version_compare( get_option( 'wp_event_manager_db_version', 0 ), '3.1.13', '<' ) )
		{
			?>
		    <div class="notice notice-warning wpem-upgrade-database-notice is-dismissible">
		        <p><?php echo sprintf( __( 'Upgrade your database! <a class="" href="%s">Please update now</a>.', 'wp-event-manager' ), admin_url( 'edit.php?post_type=event_listing&page=event-manager-upgrade-database' ) ); ?></p>
		    </div>
		    <?php	
		}
	}

	/**
	 * wpem_installation_notices function.
	 *
	 * @access public
	 * @return void
	 */
	public function wpem_installation_notices() {

		if ( get_option( 'wpem_installation_skip', false ) )
		{
			?>
		    <div class="notice notice-warning wpem-upgrade-database-notice is-dismissible">
		        <p><?php echo sprintf( __( '<strong>Welcome to WP Event Manager</strong> – All in One Event Management Plugin for WordPress', 'wp-event-manager' ) ); ?></p>
		        <p><?php echo sprintf( __( '<a class="button button-primary" href="%s">Run the Setup Wizard</a> <a class="button" href="%s">Skip setup</a>', 'wp-event-manager' ), admin_url( 'index.php?page=event-manager-setup&step=1' ), admin_url( 'index.php?page=event-manager-setup&step=3&skip-event-manager-setup=1' ) ); ?></p>
		    </div>
		    <?php	
		}
	}

	/**
	 * admin_enqueue_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $wp_scripts;

		$screen = get_current_screen();

		//main frontend style 	
		wp_enqueue_style( 'event_manager_admin_css', EVENT_MANAGER_PLUGIN_URL . '/assets/css/backend.min.css' );	
	
		if ( in_array( $screen->id, apply_filters( 'event_manager_admin_screen_ids', array( 'edit-event_listing', 'event_listing', 'event_listing_page_event-manager-settings', 'event_listing_page_event-manager-addons', 'event_listing_page_event-manager-upgrade-database' ,'edit-event_organizer','event_organizer','edit-event_venue','event_venue') ) ) )
		{
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			
			wp_enqueue_style( 'jquery-ui-style', EVENT_MANAGER_PLUGIN_URL. '/assets/js/jquery-ui/jquery-ui.min.css', array(), $jquery_version );			

			wp_register_script( 'jquery-tiptip', EVENT_MANAGER_PLUGIN_URL. '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), EVENT_MANAGER_VERSION, true );	

			wp_register_script( 'wp-event-manager-admin-js', EVENT_MANAGER_PLUGIN_URL. '/assets/js/admin.min.js', array( 'jquery', 'jquery-tiptip','jquery-ui-core','jquery-ui-datepicker'), EVENT_MANAGER_VERSION, true );
			wp_localize_script( 'wp-event-manager-admin-js', 'wp_event_manager_admin_js', array(

				'ajax_url' 	 => admin_url( 'admin-ajax.php' ),

				'start_of_week' => get_option( 'start_of_week' ),

				'upgrade_database_before_send_text' 	 => __( 'Your database upgrading now', 'wp-event-manager' ),
				'upgrade_database_success_send_text'  	=> __( 'Your database has been upgraded successfully! In order to take advantage, save the permalink and proceed.', 'wp-event-manager'),
			
				'i18n_datepicker_format' => WP_Event_Manager_Date_Time::get_datepicker_format(),
				
				'i18n_timepicker_format' => WP_Event_Manager_Date_Time::get_timepicker_format(),
				
				'i18n_timepicker_step' => WP_Event_Manager_Date_Time::get_timepicker_step(),

				'show_past_date' => apply_filters( 'event_manager_show_past_date', true ),
				
				) );
			wp_enqueue_script('wp-event-manager-admin-js');			
		}	
		
		wp_register_script( 'wp-event-manager-admin-settings', EVENT_MANAGER_PLUGIN_URL. '/assets/js/admin-settings.min.js', array( 'jquery' ), EVENT_MANAGER_VERSION, true );
		wp_register_script( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
		wp_enqueue_script('chosen');
		wp_enqueue_style( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/css/chosen.css' );
		
		wp_enqueue_style( 'wp-event-manager-jquery-timepicker-css', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-timepicker/jquery.timepicker.min.css');
		wp_register_script( 'wp-event-manager-jquery-timepicker', EVENT_MANAGER_PLUGIN_URL. '/assets/js/jquery-timepicker/jquery.timepicker.min.js', array( 'jquery' ,'jquery-ui-core'), EVENT_MANAGER_VERSION, true );
		wp_enqueue_script( 'wp-event-manager-jquery-timepicker');
	}

	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */

	public function admin_menu() {

		global $wpdb;

		add_submenu_page( 'edit.php?post_type=event_listing', __( 'Settings', 'wp-event-manager' ), __( 'Settings', 'wp-event-manager' ), 'manage_options', 'event-manager-settings', array( $this->settings_page, 'output' ) );

		if ( version_compare( get_option( 'wp_event_manager_db_version', 0 ), '3.1.13', '<' ) ) 
		{
			add_submenu_page(  'edit.php?post_type=event_listing', __( 'Upgrade Database', 'wp-event-manager' ),  __( 'Upgrade Database', 'wp-event-manager' ) , 'manage_options', 'event-manager-upgrade-database', array( $this, 'upgrade_database' ) );
		}

		if ( apply_filters( 'event_manager_show_addons_page', true ) )
		{
			add_submenu_page(  'edit.php?post_type=event_listing', __( 'WP Event Manager Add-ons', 'wp-event-manager' ),  __( 'Add-ons', 'wp-event-manager' ) , 'manage_options', 'event-manager-addons', array( $this, 'addons_page' ) );
		}
	}

	/**
	 * Upgrade database page
	 */
	public function upgrade_database() 
	{
		?>
		<div class="wrap wp_event_manager wp_event_manager_upgrade_database">
        	<table class="widefat">

        		<thead>
	                <tr>
	                    <th><h3><?php _e( 'Upgrade yor database for new version of WP Event Manager', 'wp-event-manager' ); ?></h3></th>
	                </tr>
                </thead>

                <tbody>
                	<td>
						<p><?php _e('3.1.15 has released!
We are constantly working to improve your event management experience, We have a new release focusing on a handle of fixes and updates & here is a summary of what has been improved...

Manage your Organizers directly at the frontend and backend.
Migration of Old Organizer data would be transferred to the list Automatically.
A prior Backup does no harm before updating the plugin!','wp-event-manager');?>.</p>
					</td>
                </tbody>

            	<tfoot>
	                <tr>
	                    <td>
							<a class="button-primary" id="wp_event_manager_upgrade_database" href="javascript:void(0)"><?php _e( 'Upgrade', 'wp-event-manager' ); ?></a>
						</td>
	                </tr>
                </tfoot>
                
            </table>
    	</div>
		<?php
	}

	public function wpem_upgrade_database() {

		$GLOBALS['event_manager']->forms->get_form( 'submit-organizer', array() );
		$form_submit_organizer_instance = call_user_func( array( 'WP_Event_Manager_Form_Submit_Organizer', 'instance' ) );
		$organizer_fields =	$form_submit_organizer_instance->merge_with_custom_fields('backend');

   		if( !empty($organizer_fields) && isset($organizer_fields['organizer']) && !empty($organizer_fields['organizer']) )
   		{
   			$args = [
   				'post_type' 		=> 'event_listing',
   				'post_status' 		=> ['publish'],
   				'posts_per_page'	=> '-1',
   			];

   			$events = new WP_Query($args);

   			if( $events->found_posts > 0 )
   			{
   				foreach ($events->posts as $event) 
   				{   					
   					if( isset($event->_organizer_email) && !empty($event->_organizer_email) )
   					{
   						$organizer_data = [];

	   					foreach ($organizer_fields['organizer'] as $key => $field) {
	   						$name = '_'.$key;

	   						if($key == 'organizer_logo')
	   						{
	   							$organizer_data[$key] = $event->_thumbnail_id;
	   						}
	   						else
	   						{
	   							$organizer_data[$key] = $event->$name;	
	   						}	   						
	   					}

	   					$this->migrate_organizer_from_event_meta($event, $organizer_data);

	   					$this->banner_image_set_thumnail($event);
   					}
   				}
   			}

   			update_option( 'wp_event_manager_db_version', '3.1.13' );
   		}

   		wp_send_json( __( 'Your database upgraded successfully!', 'wp-event-manager' ) );

   		wp_die();
	}

	/**
	 * migrate_organizer_from_event_meta
	 */
	public function migrate_organizer_from_event_meta($event, $organizer_data) {

		$organizer_id = check_organizer_exist($organizer_data['organizer_email']);

		if( !$organizer_id )
		{
			$args = apply_filters('wpem_create_event_organizer_data',array(
				'post_title'     => wp_strip_all_tags( $organizer_data['organizer_name'] ),
				'post_content'   => $organizer_data['organizer_description'],
				'post_status'    => 'publish',
				'post_type'      => 'event_organizer',
				'comment_status' => 'closed',
				'post_author'    => $event->post_author,
			) );

			$organizer_id = wp_insert_post( $args );
		}

		foreach ($organizer_data as $name => $value) 
		{
			if($name == 'organizer_logo')
			{
				update_post_meta( $organizer_id, '_thumbnail_id', sanitize_text_field($value) );
			}
			else
			{
				update_post_meta( $organizer_id, '_'.$name, sanitize_text_field($value) );

				delete_post_meta( $event->ID, '_'.$name );
			}			
		}

		update_post_meta( $event->ID, '_event_organizer_ids', [$organizer_id] );
	}

	/**
	 * banner_image_set_thumnail
	 */
	public function banner_image_set_thumnail($event) {

		$banner = get_event_banner($event);

		if(is_array($banner))
		{
			$image_url = $banner[0];
		}
		else
		{
			$image_url = $banner;
		}

		if( isset($image_url) && !empty($image_url) )
		{
			$wp_upload_dir = wp_get_upload_dir();

			$baseurl = $wp_upload_dir['baseurl'] . '/';

			$wp_attached_file = str_replace($baseurl, '', $image_url);

			$args = array(
		        'meta_key'         	=> '_wp_attached_file',
		        'meta_value'       	=> $wp_attached_file,
		        'post_type'        	=> 'attachment',
		        'posts_per_page'	=> 1,
		    );

			$attachments = get_posts($args);

			if(!empty($attachments))
			{
				foreach ($attachments as $attachment) 
				{
					update_post_meta( $event->ID, '_thumbnail_id', $attachment->ID );
				}
			}
		}
	}

	/**
	 * Output addons page
	 */
	public function addons_page() {

		$addons = include( 'wp-event-manager-addons.php' );

		$addons->output();
	}
	
	/**
	 * Show Installtion setup wizard admin notice
	 */
	public function run_setup_wizard_admin_notice(){
		$installation 		= get_option( 'wpem_installation', 0 );
		$skip_intallation 	= get_option( 'wpem_installation_skip', 0 );
	
		if ( !$installation || !$skip_intallation  ) {
    	 ?>
        <div class="notice wp-event-manager-notice">
		    <div class="wp-event-manager-notice-logo"><span></span></div>
		    <div class="wp-event-manager-notice-message wp-wp-event-manager-fresh"><?php _e( 'We\'ve noticed you\'ve been using <strong>WP Event Manager</strong> for some time now. we hope you love it! We\'d be thrilled if you could <strong><a href="https://wordpress.org/support/plugin/wp-event-manager/reviews/" target="_blank">give us a nice rating on WordPress.org!</a></strong> Don\'t forget to submit your site to <strong><a href="https://wp-eventmanager.com/showcase/" target="_blank">our showcase</a></strong> and generate more traffic from our site.', 'wp-event-manager' ); ?></div>
		    <div class="wp-event-manager-notice-cta">
		        <a href="https://wp-eventmanager.com/plugins/" target="_blank" class="wp-event-manager-notice-act button-primary"><?php _e('Run Setup','wp-event-manager');?></a>
		        <button class="wp-event-manager-notice-dismiss wp-event-manager-dismiss-welcome"><a href="<?php echo esc_url( add_query_arg( 'event-manager-main-admin-dismiss' ,'1' ) ) ?>"><?php _e('Dismiss','wp-event-manager');?></a></span></button>
			</div>
		</div>
        <?php
		}	
  	}

  	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}
		switch ( $screen->id ) {
			case 'options-permalink':
				include 'wp-event-manager-permalink-settings.php';
				break;
		}
	}
	  	
		/**
		 * Ran on WP admin_init hook
		 */
		public function admin_init() {
		    if( ! empty( $_GET[ 'event-manager-main-admin-dismiss']) ){
			    update_option('event_manager_rating_showcase_admin_notices_dismiss', 1);
			}			
		}
}
new WP_Event_Manager_Admin();
