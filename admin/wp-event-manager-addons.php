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
				<div class="wpem-admin-extensions-page-header">
					<img src="<?php echo esc_url(EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-logo.svg'); ?>" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
					<button class="wpem-admin-theme-button wpem-admin-extensions-help-button"><i class="wpem-icon-info"></i> <?php esc_attr_e('Help', 'wp-event-manager'); ?></button>
				</div>
				<div class="wpem-admin-tabs">
					<ul class="wpem-admin-tabs-list">
						<li><a href="#wpem-extensions"><?php esc_attr_e('Extensions', 'wp-event-manager'); ?></a></li>
						<li><a href="#wpem-themes"><?php esc_attr_e('Themes', 'wp-event-manager'); ?></a></li>
						<li class="wpem-admin-tab-active"><a href="#wpem-bundle-save"><?php esc_attr_e('Bundle & Save', 'wp-event-manager'); ?></a></li>
					</ul>
				</div>

				<!--Extensions-->
				<!-- <div class="wpem-admin-extensions-page-container">
					<ul class="wpem-admin-addon-category-selector">
						<li class="wpem-admin-addon-category-item wpem-admin-addon-category-item-active">
							<a href="#wpem-feature"><?php esc_attr_e('Feature', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a href="#wpem-ticket-selling"><?php esc_attr_e('Ticket selling', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a href="#wpem-marketing"><?php esc_attr_e('Marketing', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a href="#wpem-virtual"><?php esc_attr_e('Virtual', 'wp-event-manager'); ?></a>
						</li>
					</ul>

					<div class="wpem-admin-addon-category-extensions wpem-admin-addon-category-item-active" id="wpem-feature">
						<h3><?php esc_attr_e('Feature Add-ons', 'wp-event-manager'); ?></h3>
						<p><?php esc_attr_e('Get all our useful plugins in a single package to build a fully functional and affordable event website.', 'wp-event-manager'); ?></p>
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
								echo wp_kses_post($addons);
								echo '</ul>';
						} ?>
					</div>
				</div> -->


				<!--Themes-->
				<!-- <div class="wpem-admin-themes-page-container">

					<ul class="wpem-admin-addon-category-selector mb-3">
						<li class="wpem-admin-addon-category-item wpem-admin-addon-category-item-active">
							<a href="#wpem-html-all"><?php esc_attr_e('All', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a href="#wpem-html-theme"><?php esc_attr_e('HTML', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a href="#wpem-elementor-theme"><?php esc_attr_e('Elementor', 'wp-event-manager'); ?></a>
						</li>
					</ul>

					<div class="wpem-admin-theme-listing">
						<div class="wpem-row">
							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/charity/charity.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Charity</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/businex/businex.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Businex</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/yogasan/yogasan.jpg" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Yogasan</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/jobs-fair/jobs-fair.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Job Fair</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/charity/charity.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Charity</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/businex/businex.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Businex</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/yogasan/yogasan.jpg" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Yogasan</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/jobs-fair/jobs-fair.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Job Fair</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/charity/charity.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Charity</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/businex/businex.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Businex</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/yogasan/yogasan.jpg" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Yogasan</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wp-eventmanager.com/product/charity/"><img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/themes/jobs-fair/jobs-fair.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wp-eventmanager.com/product/charity/"><h2 class="woocommerce-loop-product__title">Job Fair</h2></a>
										</div>
										<div class="wpem-theme-description">Get WordPress themes for your charity  events that perfectly reflect your mission.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration">annually</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> -->


				<!--Bundle & Save-->
				<div class="wpem-admin-bundle-page-container">
					<h1><?php esc_attr_e('Everything WP Event Manager in', 'wp-event-manager'); ?> <b><?php esc_attr_e('#4 simple bundle.', 'wp-event-manager'); ?></b></h1>
					<div class="wpem-row">
						<div class="wpem-col-lg-3 wpem-col-md-6 wpem-col-sm-12">
							<div class="wpem-bundle-box wpem-plus-bundle">
								<div class="wpem-bundle-img-wrapper">
									<img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/plugins/product-icons/event-manager-plus.svg" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
								</div>
								<h2><?php esc_attr_e('Event Manager Plus', 'wp-event-manager'); ?></h2>
								<p><?php esc_attr_e('Ticket sales, attendee management, and email marketing for your events', 'wp-event-manager'); ?></p>
								<ul>
									<li><i class="wpem-icon-checkmark"></i> 20 Plugins</li>
									<li><i class="wpem-icon-checkmark"></i> Support</li>
									<li><i class="wpem-icon-checkmark"></i> Feature Add-ons</li>
								</ul>
								<a href="https://wp-eventmanager.com/pricing/" class="wpem-admin-theme-button wpem-admin-bundle-button"><?php esc_attr_e('Buy Now', 'wp-event-manager'); ?></a>
							</div>
						</div>

						<div class="wpem-col-lg-3 wpem-col-md-6 wpem-col-sm-12">
							<div class="wpem-bundle-box wpem-pro-bundle">
								<div class="wpem-bundle-img-wrapper">
									<img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/plugins/product-icons/event-manager-pro.svg" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
								</div>
								<h2><?php esc_attr_e('Event Manager Pro', 'wp-event-manager'); ?></h2>
								<p><?php esc_attr_e('Ticket sales, attendee management, and email marketing for your events', 'wp-event-manager'); ?></p>
								<ul>
									<li><i class="wpem-icon-checkmark"></i> 20 Plugins</li>
									<li><i class="wpem-icon-checkmark"></i> Support</li>
									<li><i class="wpem-icon-checkmark"></i> Feature Add-ons</li>
									<li><i class="wpem-icon-checkmark"></i> Ticket Selling Add-ons</li>
								</ul>
								<a href="https://wp-eventmanager.com/pricing/" class="wpem-admin-theme-button wpem-admin-bundle-button"><?php esc_attr_e('Buy Now', 'wp-event-manager'); ?></a>
							</div>
						</div>

						<div class="wpem-col-lg-3 wpem-col-md-6 wpem-col-sm-12">
							<div class="wpem-bundle-box wpem-virtual-bundle highlight-plan">
								<div class="wpem-bundle-img-wrapper">
									<img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/plugins/product-icons/virtual-event-manager-pro.svg" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
								</div>
								<h2><?php esc_attr_e('Virtual Event Manager Pro', 'wp-event-manager'); ?></h2>
								<p><?php esc_attr_e('Ticket sales, attendee management, and email marketing for your events', 'wp-event-manager'); ?></p>
								<ul>
									<li><i class="wpem-icon-checkmark"></i> 20 Plugins</li>
									<li><i class="wpem-icon-checkmark"></i> Support</li>
									<li><i class="wpem-icon-checkmark"></i> Feature Add-ons</li>
									<li><i class="wpem-icon-checkmark"></i> Ticket Selling Add-ons</li>
									<li><i class="wpem-icon-checkmark"></i> Virtual Add-ons</li>
								</ul>
								<a href="https://wp-eventmanager.com/pricing/" class="wpem-admin-theme-button wpem-admin-bundle-button"><?php esc_attr_e('Buy Now', 'wp-event-manager'); ?></a>
							</div>
						</div>

						<div class="wpem-col-lg-3 wpem-col-md-6 wpem-col-sm-12">
							<div class="wpem-bundle-box wpem-all-pro-bundle">
								<div class="wpem-bundle-img-wrapper">
									<img src="https://wp-eventmanager.com/wp-content/themes/wpemstore/assets/images/plugins/product-icons/all-events-manager-pro.svg" alt="<?php esc_attr_e('WP Event Manager', 'wp-event-manager'); ?>" />
								</div>
								<h2><?php esc_attr_e('All Events Manager Pro', 'wp-event-manager'); ?></h2>
								<p><?php esc_attr_e('Ticket sales, attendee management, and email marketing for your events', 'wp-event-manager'); ?></p>
								<ul>
									<li><i class="wpem-icon-checkmark"></i> 20 Plugins</li>
									<li><i class="wpem-icon-checkmark"></i> Support</li>
									<li><i class="wpem-icon-checkmark"></i> Feature Add-ons</li>
									<li><i class="wpem-icon-checkmark"></i> Ticket Selling Add-ons</li>
									<li><i class="wpem-icon-checkmark"></i> Virtual Add-ons</li>
									<li><i class="wpem-icon-checkmark"></i> Marketing Add-ons</li>
								</ul>
								<a href="https://wp-eventmanager.com/pricing/" class="wpem-admin-theme-button wpem-admin-bundle-button"><?php esc_attr_e('Buy Now', 'wp-event-manager'); ?></a>
							</div>
						</div>

					</div>
				</div>

			</div>
		<?php
		} 
	}
endif;
return new WP_Event_Manager_Addons();