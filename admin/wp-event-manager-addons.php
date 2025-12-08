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
			<h2 style="display: none;"><?php esc_attr_e('WP Event Manager Add-ons', 'wp-event-manager'); ?></h2>
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
				</div>


				<!--Themes-->
				<div id="wpem-themes" class="wpem-admin-themes-page-container" style="display:none;">

					<ul class="wpem-admin-addon-category-selector mb-3">
						<li class="wpem-admin-addon-category-item wpem-admin-addon-category-item-active">
							<a class="wpem-all" href="#wpem-html-all"><?php esc_attr_e('All', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a class="wpem-html" href="#"><?php esc_attr_e('HTML', 'wp-event-manager'); ?></a>
						</li>
						<li class="wpem-admin-addon-category-item">
							<a class="wpem-elementor" href="#"><?php esc_attr_e('Elementor', 'wp-event-manager'); ?></a>
						</li>
					</ul>

					<div class="wpem-admin-theme-listing">
						<div class="wpem-row">
							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/charity/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/charity/charity.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/charity/"><h2 class="woocommerce-loop-product__title"><?php echo __('Charity', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Get WordPress themes for your charity  events that perfectly reflect your mission.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/businex/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/businex/businex.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/businex/"><h2 class="woocommerce-loop-product__title"><?php echo __('Businex', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Transform the online presence of your business using our attractive themes.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/yogasan/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/yogasan/yogasan.jpg" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/yogasan/"><h2 class="woocommerce-loop-product__title"><?php echo __('Yogasan', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Express your passion for fitness through your Yogasana theme-based WordPress site.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/job-fair/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/jobs-fair/jobs-fair.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/job-fair/"><h2 class="woocommerce-loop-product__title"><?php echo __('Job Fair', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Boost the visibility of your job fair portal with our job fair themes', 'wp-event-manager');?>.</div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/christmas-party/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/christmas-party/christmas-party.png" alt="Charity" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/christmas-party/"><h2 class="woocommerce-loop-product__title"><?php echo __('Christmas Party', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Design your website with our exclusive Christmas themes to elevate the party mood.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/birthday/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/birthday/birthday.png" alt="Birthday" width="100%" height="100%" title="Charity"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/birthday/"><h2 class="woocommerce-loop-product__title"><?php echo __('Birthday', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Creatively craft your event website with our birthday themes.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/conference/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/conference/conference.png" alt="Conference" width="100%" height="100%" title="Conference"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/conference/"><h2 class="woocommerce-loop-product__title"><?php echo __('Conference', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Use conference themes to build highly professional websites.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/dj-theme/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/dj-theme/dj-theme.png" alt="DJ Theme" width="100%" height="100%" title="DJ Theme"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/dj-theme/"><h2 class="woocommerce-loop-product__title"><?php echo __('DJ Theme', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Attract party people to your website by using our DJ themes.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/expo/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/expo/expo.png" alt="Expo" width="100%" height="100%" title="Expo"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/expo/"><h2 class="woocommerce-loop-product__title"><?php echo __('Expo', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Get inspiring WordPress themes to represent your expo and exhibitions.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/fashion-beauty/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/fashion-beauty/fashion-beauty.png" alt="Fashion Beauty" width="100%" height="100%" title="Fashion Beauty"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/fashion-beauty/"><h2 class="woocommerce-loop-product__title"><?php echo __('Fashion Beauty', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Glamourize your fashion and beauty website using our exclusive themes.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/festival/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/festival/festival.png" alt="Festival" width="100%" height="100%" title="Festival"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/festival/"><h2 class="woocommerce-loop-product__title"><?php echo __('Festival', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Paint your website with festivities with our festival-based themes.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/cinema/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/cinema/cinema.png" alt="Cinema" width="100%" height="100%" title="Cinema"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/cinema/"><h2 class="woocommerce-loop-product__title"><?php echo __('Cinema', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Get Filmy WordPress themes for your filmy website.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/fitness/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/fitness/fitness.png" alt="Fitness" width="100%" height="100%" title="Fitness"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/fitness/"><h2 class="woocommerce-loop-product__title"><?php echo __('Fitness', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Use our fitness themes on your website to inspire people to stay fit.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/food-drink/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/food-drink/food-drink.png" alt="Food Drink" width="100%" height="100%" title="Food Drink"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/food-drink/"><h2 class="woocommerce-loop-product__title"><?php echo __('Food Drink', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Attract foodies to our website by using our Food and Drink theme.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/games/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/games/games.png" alt="Games" width="100%" height="100%" title="Games"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/games/"><h2 class="woocommerce-loop-product__title"><?php echo __('Games', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Discover unconventional gaming themes to design your game-based events.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/online-class/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/online-class/online-class.png" alt="Online Class" width="100%" height="100%" title="Online Class"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/online-class/"><h2 class="woocommerce-loop-product__title"><?php echo __('Online Class', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Choose the most suitable themes to maximize the visibility of your online events.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/online-education/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/online-education/online-education.png" alt="Online Education" width="100%" height="100%" title="Online Education"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/online-education/"><h2 class="woocommerce-loop-product__title"><?php echo __('Online Education', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Empower your online education business by using our educational theme.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/theater/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/theater/theater.png" alt="Theater" width="100%" height="100%" title="Theater"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/theater/"><h2 class="woocommerce-loop-product__title"><?php echo __('Theater', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Get responsive theater themes to amplify the online presence of your website.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/running/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/running/running.png" alt="Running" width="100%" height="100%" title="Running"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/running/"><h2 class="woocommerce-loop-product__title"><?php echo __('Running', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Energize and inspire people to run and stay fit.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/science-research/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/science-research/science-research.png" alt="Science Research" width="100%" height="100%" title="Science Research"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/science-research/"><h2 class="woocommerce-loop-product__title"><?php echo __('Science Research', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Let every element of your website speak science.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>/product/soccer-club/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/soccer-club/soccer-club.png" alt="Soccer Club" width="100%" height="100%" title="Soccer Club"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/soccer-club/"><h2 class="woocommerce-loop-product__title"><?php echo __('Soccer Club', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Attract soccer to your website by using our soccer club themes.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/virtual-jobs-fair/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/virtual-jobs-fair/virtual-jobs-fair.png" alt="Virtual Jobs Fair" width="100%" height="100%" title="Virtual Jobs Fair"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/virtual-jobs-fair/"><h2 class="woocommerce-loop-product__title"><?php echo __('Virtual Jobs Fair', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Use our virtual jobs fair themes to assemble job seekers and recruiters.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/water-sports/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/water-sports/water-sports.png" alt="Water Sports" width="100%" height="100%" title="Water Sports"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/water-sports/"><h2 class="woocommerce-loop-product__title"><?php echo __('Science Research', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Create a perfect website for all water sports addicts.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/web-summit/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/web-summit/web-summit.png" alt="Web Summit" width="100%" height="100%" title="Web Summit"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/web-summit/"><h2 class="woocommerce-loop-product__title"><?php echo __('Web Summit', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Get clean designs & graphic elements in our WordPress website.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/webinar/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/webinar/webinar.png" alt="Webinar" width="100%" height="100%" title="Webinar"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/webinar/"><h2 class="woocommerce-loop-product__title"><?php echo __('Webinar', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Experience innovative theme designs that are perfect for your webinar.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/wedding-planner/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/wedding-planner/wedding-planner.png" alt="Wedding Planner" width="100%" height="100%" title="Wedding Planner"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/wedding-planner/"><h2 class="woocommerce-loop-product__title"><?php echo __('Wedding Planner', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Get exceptional wedding planning themes designed with creativity.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 html">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="https://wordpress.org/themes/event-listing/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/recommended-themes/event-listing.jpg" alt="Event Listing Theme" width="100%" height="100%" title="Event Listing Theme"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="https://wordpress.org/themes/event-listing/"><h2 class="woocommerce-loop-product__title"><?php echo __('Event Listing Theme', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Find the most alluring WordPress event listing theme to list your events.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 elementor">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/community/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/community/community.jpg" alt="Community" width="100%" height="100%" title="Community"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/community/"><h2 class="woocommerce-loop-product__title"><?php echo __('Community', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Get exceptional Community themes designed with creativity.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>

							<div class="wpem-col-lg-3 wpem-col-md-4 wpem-col-sm-6 elementor">
								<div class="wpem-theme-box">
									<div class="wpem-theme-img-wrapper">
										<a href="<?php echo esc_url($wpem_url);?>product/insighter/"><img src="<?php echo esc_url($wpem_url);?>wp-content/themes/wpemstore/assets/images/themes/insighter/insighter.jpg" alt="Insighter" width="100%" height="100%" title="Insighter"></a>
									</div>
									<div class="wpem-theme-content-wrapper">
										<div class="wpem-theme-title">
											<a href="<?php echo esc_url($wpem_url);?>product/insighter/"><h2 class="woocommerce-loop-product__title"><?php echo __('Insighter', 'wp-event-manager');?></h2></a>
										</div>
										<div class="wpem-theme-description"><?php echo __('Draw the party crowd to your website with the vibrant and dynamic Insighter.', 'wp-event-manager');?></div>
										<div class="wpem-theme-price">
											<div class="wpem-theme-price-wrapper">
												<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>49.00</bdi></span></span>
											</div>
											<span class="wpem-theme-duration"><?php echo __('annually', 'wp-event-manager');?></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!--Bundle & Save-->
				<div id="wpem-bundle-save" class="wpem-admin-bundle-page-container" style="display:none;">
					<h1><?php esc_attr_e('Everything WP Event Manager in', 'wp-event-manager'); ?> <b><?php esc_attr_e('#4 simple bundle.', 'wp-event-manager'); ?></b></h1>
					<div class="wpem-row">
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
					</div>
				</div>
			</div>
		<?php
		} 
	}
endif;
return new WP_Event_Manager_Addons();