<?php
/**
 * Shortcodes Page
*/

if (!defined('ABSPATH')){
	 exit;// Exit if accessed directly
} 

if (!class_exists('WP_Event_Manager_Shortcode_List')) :

	/**
	 * WP_Event_Manager_Shortcode_List Class
	*/
	class WP_Event_Manager_Shortcode_List {
		/**
		 * Handles output of the reports page in admin.
		 */
		public function shortcode_list() { ?>
			<div class="wrap wp_event_manager wp_event_manager_shortcodes_wrap">
			<h2><?php _e('WP Event Manager shortcodes', 'wp-event-manager'); ?></h2>
			
			
			</div>
		<?php
		} 
	}
endif;
return new WP_Event_Manager_Shortcode_List();