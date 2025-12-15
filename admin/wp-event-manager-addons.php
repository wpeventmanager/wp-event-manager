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
		public function output() { 
			wp_enqueue_script('wp-event-manager-admin-addons-js');
			$wpem_url = get_option('wp_event_manager_store_url');?>
			<div class="wrap wp_event_manager wp_event_manager_addons_wrap">
				<h2 style="display: none;">
					<?php esc_attr_e('WP Event Manager Add-ons', 'wp-event-manager'); ?>
				</h2>
				<div class="wpem-admin-extensions-page-header">
					<img src="<?php echo esc_url(EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-logo.svg'); ?>" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
					<a href="<?php echo esc_url($wpem_url);?>help-center/" class="wpem-admin-theme-button wpem-admin-extensions-help-button"><i class="wpem-icon-info"></i> <?php esc_attr_e('Help', 'wp-event-manager'); ?></a>
				</div>
				<div class="wpem-admin-tabs">
					<ul class="wpem-admin-tabs-list">
						<li><a class="wpem-extensions-btn" href="#"><?php esc_attr_e('Extensions', 'wp-event-manager'); ?></a></li>
						<li><a class="wpem-themes-btn" href="#"><?php esc_attr_e('Themes', 'wp-event-manager'); ?></a></li>
						<li><a class="wpem-bundle-save-btn" href="#"><?php esc_attr_e('Bundle & Save', 'wp-event-manager'); ?></a></li>
					</ul>
				</div>

				<!--Extensions-->
				<div id="wpem-extensions" class="wpem-admin-extensions-page-container" style="display:none;">
					<ul class="wpem-admin-addon-category-selector">
						<li class="wpem-admin-addon-category-item">
							<a class="wpem-feature" href="#"><?php esc_attr_e('Feature', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a class="wpem-ticket-selling" href="#"><?php esc_attr_e('Ticket selling', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a class="wpem-marketing" href="#"><?php esc_attr_e('Marketing', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a class="wpem-virtual" href="#"><?php esc_attr_e('Virtual', 'wp-event-manager'); ?></a>
						</li>
					</ul>

					<div class="wpem-admin-addon-category-extensions wpem-admin-addon-category-item-active" id="wpem-feature">
						<div class="product_cat-feature-add-ons">
							<h3><?php esc_attr_e('Feature Add-ons', 'wp-event-manager'); ?></h3>
							<p><?php esc_attr_e('Get all our useful plugins in a single package to build a fully functional and affordable event website.', 'wp-event-manager'); ?></p>
						</div>
						<div class="product_cat-ticket-selling-add-ons">
							<h3><?php esc_attr_e('Ticket selling Add-ons', 'wp-event-manager'); ?></h3>
							<p><?php esc_attr_e('Our ticket selling add-ons are designed to accelerate your ticket sales and help manage your registration data.', 'wp-event-manager'); ?></p>
						</div>
						<div class="product_cat-marketing-add-ons">
							<h3><?php esc_attr_e('Marketing Add-ons', 'wp-event-manager'); ?></h3>
							<p><?php esc_attr_e('Reach your target audience through simplified yet effective marketing strategies with our marketing add-on.', 'wp-event-manager'); ?></p>
						</div>
						<div class="product_cat-virtual-add-ons">
							<h3><?php esc_attr_e('Virtual Add-ons', 'wp-event-manager'); ?></h3>
							<p><?php esc_attr_e('Conduct interactive and professional hybrid event experiences using our virtual add-ons.', 'wp-event-manager'); ?></p>
						</div>
						<?php if(false === ($addons = get_transient('wp_event_manager_addons_html'))) {
							$raw_addons = wp_remote_get(
								'https://wp-eventmanager.com/plugins',
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
							echo wp_kses_post($addons);
							echo '</ul>';
						} ?>
						
					</div>
				</div>

				<!--Themes-->
				<div id="wpem-themes" class="wpem-admin-themes-page-container" style="display:none;">

						<!-- <ul class="wpem-admin-addon-category-selector mb-3">
							<li class="wpem-admin-addon-category-item wpem-admin-addon-category-item-active">
								<a class="wpem-all" href="#wpem-html-all"><?php esc_attr_e('All', 'wp-event-manager'); ?></a>
							</li>
							<li class="wpem-admin-addon-category-item">
								<a class="wpem-html" href="#"><?php esc_attr_e('HTML', 'wp-event-manager'); ?></a>
							</li>
							<li class="wpem-admin-addon-category-item">
								<a class="wpem-elementor" href="#"><?php esc_attr_e('Elementor', 'wp-event-manager'); ?></a>
							</li>
						</ul> -->
					<?php $raw_themes = wp_remote_get(
						'https://wp-eventmanager.com/wodpress-event-themes/',
						array(
							'timeout'     => 10,
							'redirection' => 5,
							'sslverify'   => false
						)
					);
					if(!is_wp_error($raw_themes)) {
						
						$raw_themes = wp_remote_retrieve_body($raw_themes);
						// Get Products
						$dom = new DOMDocument();
						libxml_use_internal_errors(true);
						$dom->loadHTML($raw_themes);
		
						$xpath = new DOMXPath($dom);
						$themes = "";
						$div_array = array("wpem-container");
						for($i = 0 ; $i <= 3 ; $i++){
							$tags  = $xpath->query('//div[@class="wpem-theme-box"]');
							foreach ($tags as $tag) {
								$themes .= $tag->ownerDocument->saveXML($tag);
							}
						}
					
						$themes = wp_kses_post($themes);
						if($themes) {
							set_transient('wp_event_manager_themes_html', $themes, 60 * 60 * 24 * 7); // Cached for a week
						}
					}
					echo '<div class="wpem-admin-theme-listing"><div class="wpem-row">';
					echo wp_kses_post($themes);
					echo '</div></div>'; ?>
				</div>

				<!--Bundle & Save-->
				<div id="wpem-bundle-save" class="wpem-admin-bundle-page-container" style="display:none;">
					<h1><?php esc_attr_e('Everything WP Event Manager in', 'wp-event-manager'); ?> <b><?php esc_attr_e('#4 simple bundle.', 'wp-event-manager'); ?></b></h1>

					<?php $raw_bundle = wp_remote_get(
						'https://wp-eventmanager.com/pricing/',
						array(
							'timeout'     => 10,
							'redirection' => 5,
							'sslverify'   => false
						)
					);
					if(!is_wp_error($raw_bundle)) {
						$raw_bundle = wp_remote_retrieve_body($raw_bundle);
						// Get Products
						$dom = new DOMDocument();
						libxml_use_internal_errors(true);
						$dom->loadHTML($raw_bundle);
		
						$xpath = new DOMXPath($dom);
						$bundle = "";
						for($i = 0 ; $i <= 3 ; $i++){
							$tags  = $xpath->query('//div[@class="pricing-plan-container"]');
							$bundle = $tag->ownerDocument->saveXML($tag);
						}
						$bundle = wp_kses_post($bundle);
					}
					echo '<div class="wpem-row">';
					echo wp_kses_post($bundle);
					echo '</div>'; ?>

					<!-- <div class="wpem-row">
						<div class="wpem-col-lg-3 wpem-col-md-6 wpem-col-sm-12">
							<div class="wpem-bundle-box wpem-plus-bundle">
								<div class="wpem-bundle-img-wrapper">
									<img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/plugins/product-icons/event-manager-plus.svg" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
								</div>
								<h2><?php esc_attr_e('Event Manager Plus', 'wp-event-manager'); ?></h2>
								<p><?php esc_attr_e('Ticket sales, attendee management, and email marketing for your events', 'wp-event-manager'); ?></p>
								<ul>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('20 Plugins', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Support', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Feature Add-ons', 'wp-event-manager'); ?></li>
								</ul>
								<a href="<?php echo esc_url($wpem_url);?>pricing/?add-to-cart=35707" class="wpem-admin-theme-button wpem-admin-bundle-button"><?php esc_attr_e('Buy Now', 'wp-event-manager'); ?></a>
							</div>
						</div>

						<div class="wpem-col-lg-3 wpem-col-md-6 wpem-col-sm-12">
							<div class="wpem-bundle-box wpem-pro-bundle">
								<div class="wpem-bundle-img-wrapper">
									<img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/plugins/product-icons/event-manager-pro.svg" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
								</div>
								<h2><?php esc_attr_e('Event Manager Pro', 'wp-event-manager'); ?></h2>
								<p><?php esc_attr_e('Ticket sales, attendee management, and email marketing for your events', 'wp-event-manager'); ?></p>
								<ul>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('20 Plugins', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Support', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Feature Add-ons', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Ticket Selling Add-ons', 'wp-event-manager'); ?></li>
								</ul>
								<a href="<?php echo esc_url($wpem_url);?>pricing/?add-to-cart=20377" class="wpem-admin-theme-button wpem-admin-bundle-button"><?php esc_attr_e('Buy Now', 'wp-event-manager'); ?></a>
							</div>
						</div>

						<div class="wpem-col-lg-3 wpem-col-md-6 wpem-col-sm-12">
							<div class="wpem-bundle-box wpem-virtual-bundle highlight-plan">
								<div class="wpem-bundle-img-wrapper">
									<img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/plugins/product-icons/virtual-event-manager-pro.svg" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
								</div>
								<h2><?php esc_attr_e('Virtual Event Manager Pro', 'wp-event-manager'); ?></h2>
								<p><?php esc_attr_e('Ticket sales, attendee management, and email marketing for your events', 'wp-event-manager'); ?></p>
								<ul>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('20 Plugins', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Support', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Feature Add-ons', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Ticket Selling Add-ons', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Virtual Add-ons', 'wp-event-manager'); ?></li>
								</ul>
								<a href="<?php echo esc_url($wpem_url);?>pricing/?add-to-cart=29038" class="wpem-admin-theme-button wpem-admin-bundle-button"><?php esc_attr_e('Buy Now', 'wp-event-manager'); ?></a>
							</div>
						</div>

						<div class="wpem-col-lg-3 wpem-col-md-6 wpem-col-sm-12">
							<div class="wpem-bundle-box wpem-all-pro-bundle">
								<div class="wpem-bundle-img-wrapper">
									<img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/plugins/product-icons/all-events-manager-pro.svg" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
								</div>
								<h2><?php esc_attr_e('All Events Manager Pro', 'wp-event-manager'); ?></h2>
								<p><?php esc_attr_e('Ticket sales, attendee management, and email marketing for your events', 'wp-event-manager'); ?></p>
								<ul>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('20 Plugins', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Support', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Feature Add-ons', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Ticket Selling Add-ons', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Virtual Add-ons', 'wp-event-manager'); ?></li>
									<li><i class="wpem-icon-checkmark"></i> <?php echo __('Marketing Add-ons', 'wp-event-manager'); ?></li>
								</ul>
								<a href="<?php echo esc_url($wpem_url);?>pricing/?add-to-cart=35706" class="wpem-admin-theme-button wpem-admin-bundle-button"><?php esc_attr_e('Buy Now', 'wp-event-manager'); ?></a>
							</div>
						</div>
					</div> -->
				</div>
			</div>
		<?php
		} 
	}
endif;
return new WP_Event_Manager_Addons();