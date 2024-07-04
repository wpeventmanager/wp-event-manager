<?php
/**
 * Addons page of WP Event Manager. 
*/

if(!defined('ABSPATH')){
	 exit;// Exit if accessed directly
} 

if(!class_exists('WP_Event_Manager_Addons')) :

	/**
	 * WP_Event_Manager_Addons Class.
	*/
	class WP_Event_Manager_Addons {
		/**
		 * Handles output of the reports page in admin.
		 */
		public function output() { ?>
			<div class="wrap wp_event_manager wp_event_manager_addons_wrap">
			<h2><?php _e('WP Event Manager Add-ons', 'wp-event-manager'); ?></h2>
			<?php 
			
			if(false === ($addons = get_transient('wp_event_manager_addons_html'))) {
					$raw_addons = wp_remote_get(
						'http://www.wp-eventmanager.com/plugins',
							array(
									'timeout'     => 10,
									'redirection' => 5,
									'sslverify'   => false
							)
						);
						
					if(!is_wp_error($raw_addons)) {
						$raw_addons = wp_remote_retrieve_body($raw_addons);
		
						// Get Products
						$dom = new DOMDocument();
						libxml_use_internal_errors(true);
						$dom->loadHTML($raw_addons);
		
						$xpath = new DOMXPath($dom);

						$div_array = array("wpem-container feature", "wpem-container ticket", "wpem-container marketing", "wpem-container virtual");
						for($i = 0 ; $i <= 3 ; $i++){
							$tags  = $xpath->query('//div[@class="'.$div_array[$i].'"]/ul[@class="products columns-4"]//li');
							foreach ($tags as $tag) {
								$addons .= $tag->ownerDocument->saveXML($tag);
							}
						}
					
						$addons = wp_kses_post($addons);
						if($addons) {
							set_transient('wp_event_manager_addons_html1', $addons, 60 * 60 * 24 * 7); // Cached for a week
						}
					}
					echo '<ul class="products columns-4">';
					echo $addons; 
					echo '</ul>';
			} ?>
			</div>
		<?php
		} 
	}
endif;
return new WP_Event_Manager_Addons();