<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<!-- Vertical Menu Start-->
<div class="wpem-main-vmenu-dashboard-wrapper wpem-row">
	<div class="wpem-main-vmenu-dashboard-nav-menu wpem-col-md-3">
		<div class="wpem-main-vmenu-dashboard-nav" id="wpem-main-vmenu-dashboard-nav">
			<ul class="wpem-main-vmenu-dashboard-ul">
				<?php
				$wpem_current_action = 'event_dashboard';
				$event_id        = '';
				
				$wpem_current_action = isset( $_GET['action'] ) ? sanitize_title( wp_unslash( $_GET['action'] ) ) : 'event_dashboard';
				$event_id        = isset( $_GET['event_id'] ) ? absint( wp_unslash( $_GET['event_id'] ) ) : '';
				
				// Additional security: verify user can manage this event
				if ( $event_id && ! current_user_can( 'edit_post', $event_id ) ) {
					$event_id = '';
				}

				$wpem_menus = [
					'event_dashboard' => [
						'title' => __('Events', 'wp-event-manager'),
						'icon' => 'wpem-icon-meter',
						'query_arg' => ['action' => 'event_dashboard'],
					],
				];
				if (get_option('enable_event_organizer')) {
					$wpem_menus['organizer_dashboard'] = [
						'title' => __('Organizers', 'wp-event-manager'),
						'icon' => 'wpem-icon-user-tie',
						'query_arg' => ['action' => 'organizer_dashboard'],
					];
				}
				if (get_option('enable_event_venue')) {
					$wpem_menus['venue_dashboard'] = [
						'title' => __('Venues', 'wp-event-manager'),
						'icon' => 'wpem-icon-location',
						'query_arg' => ['action' => 'venue_dashboard'],
					];
				}

				$wpem_menus = apply_filters('wpem_dashboard_menu', $wpem_menus);
				$event_dashboard = get_option('event_manager_event_dashboard_page_id');
				do_action('wpem_dashboard_menu_before', $wpem_menus);
				foreach ($wpem_menus as $wpem_name => $menu) {
					if (($wpem_name === 'registration' || $wpem_name === 'guest_lists')  && !current_user_can('administrator') && !current_user_can('organizer')) {
						continue; // Skip rendering this menu item
					}
					if (isset($menu['submenu']) && !empty($menu['submenu'])) {
						$wpem_active_parent_menu = '';
						$wpem_child_menu_html = '<ul class="wpem-main-vmenu-dashboard-submenu-ul">';
						foreach ($menu['submenu'] as $wpem_sub_name => $submenu) {
							if (isset($submenu['query_arg']) && !empty($submenu['query_arg']) && is_array($submenu['query_arg'])) {
								$wpem_action_url = add_query_arg(
									$submenu['query_arg'],
									get_permalink($event_dashboard)
								);
							} else {
								$wpem_action_url = add_query_arg(
									array(),
									get_permalink($event_dashboard)
								);
							}
							$wpem_active_menu = '';
							if ($wpem_current_action === $wpem_sub_name) {
								$wpem_active_menu = 'wpem-main-vmenu-dashboard-link-active';
								$wpem_active_parent_menu = 'wpem-main-vmenu-dashboard-link-active';
							}
							$wpem_child_menu_html .= '<li class="wpem-main-vmenu-dashboard-submenu-li"><a class="wpem-main-vmenu-dashboard-link ' . esc_attr($wpem_active_menu) . '" href="' . esc_url($wpem_action_url) . '">' . esc_attr($submenu['title']) . '</a></li>';
						}
						
						$wpem_child_menu_html .= '</ul>';
						printf('<li class="wpem-main-vmenu-dashboard-li wpem-main-vmenu-dashboard-sub-menu"><a class="wpem-main-vmenu-dashboard-link %s" href="javascript:void(0)"><i class="%s"></i>%s<i class="wpem-icon-play3 wpem-main-vmenu-caret wpem-main-vmenu-caret-up"></i></a>', esc_attr($wpem_active_parent_menu), esc_attr($menu['icon']), esc_attr($menu['title']));
						echo wp_kses_post($wpem_child_menu_html);
						printf('</li>');
					} else {
						if (isset($menu['query_arg']) && !empty($menu['query_arg']) && is_array($menu['query_arg'])) {
							$wpem_action_url = add_query_arg(
								$menu['query_arg'],
								get_permalink($event_dashboard)
							);
						} else {
							$wpem_action_url = add_query_arg(
								array(),
								get_permalink($event_dashboard)
							);
						}
						$wpem_active_menu = '';
						if ($wpem_current_action === $wpem_name) {
							$wpem_active_menu = 'wpem-main-vmenu-dashboard-link-active';
						}
						printf('<li class="wpem-main-vmenu-dashboard-li"><a class="wpem-main-vmenu-dashboard-link %s" href="%s"> <i class="%s"></i>%s</a></li>', esc_attr($wpem_active_menu), esc_url($wpem_action_url), esc_attr($menu['icon']), esc_attr($menu['title']));
					}
				} ?>
			</ul>
		</div>
	</div>

	<!-- Event Dashboard Start -->
	<div class="wpem-main-vmenu-dashboard-content-wrap wpem-col-md-9">
		<div class="wpem-dashboard-main-content">

			<?php do_action('event_manager_event_dashboard_before'); ?>

			<?php if ($wpem_current_action === 'organizer_dashboard' && !empty($wpem_current_action)) :
				echo do_shortcode('[organizer_dashboard]');
			elseif ($wpem_current_action === 'venue_dashboard' && !empty($wpem_current_action)) :
				echo do_shortcode('[venue_dashboard]');
			elseif (!in_array($wpem_current_action, ['event_dashboard', 'delete', 'mark_cancelled', 'mark_not_cancelled']) && !empty($wpem_current_action)) :
				if (has_action('event_manager_event_dashboard_content_' . $wpem_current_action)) :
					do_action('event_manager_event_dashboard_content_' . $wpem_current_action, $atts);
				endif;?>
			<?php else : ?>
				<div class="wpem-dashboard-main-header">
					<div class="wpem-dashboard-main-title wpem-dashboard-main-filter">
						<h3 class="wpem-theme-text"><?php esc_html_e('Event Dashboard', 'wp-event-manager'); ?></h3>
						<div class="wpem-d-inline-block wpem-dashboard-i-block-btn">
							<?php do_action('event_manager_event_dashboard_button_action_start'); ?>
							<?php $submit_event = get_option('event_manager_submit_event_form_page_id');
							if (!empty($submit_event)) : ?>
								<a class="wpem-dashboard-header-btn wpem-dashboard-header-add-btn" title="<?php esc_attr_e('Add Event', 'wp-event-manager'); ?>" href="<?php echo esc_url(get_permalink($submit_event)); ?>"><i class="wpem-icon-plus"></i></a>
							<?php endif; ?>
							<?php do_action('event_manager_event_dashboard_button_action_end'); ?>
							<a href="javascript:void(0)" title="<?php esc_attr_e('Filter', 'wp-event-manager'); ?>" class="wpem-dashboard-event-filter wpem-dashboard-header-btn"><i class="wpem-icon-filter"></i></a>
						</div>
					</div>

					<?php
					$wpem_search_keywords = '';
					$wpem_search_order_by = '';

					if ( isset( $_GET['wpem_event_dashboard_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['wpem_event_dashboard_nonce'] ) ), 'wpem_event_dashboard_filter' ) ) {
						$wpem_search_keywords = isset( $_GET['search_keywords'] ) ? sanitize_text_field( wp_unslash( $_GET['search_keywords'] ) ) : '';
						$wpem_search_order_by = isset( $_GET['search_order_by'] ) ? sanitize_title( wp_unslash( $_GET['search_order_by'] ) ) : '';
					}

					$wpem_display_block = '';
					if ( ! empty( $wpem_search_keywords ) || ! empty( $wpem_search_order_by ) ) {
						$wpem_display_block = 'wpem-d-block';
					} ?>

					<form action="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" method="get" class="wpem-form-wrapper wpem-event-dashboard-filter-toggle wpem-dashboard-main-filter-block <?php printf( esc_attr( $wpem_display_block ) ); ?>">
						<?php wp_nonce_field( 'wpem_event_dashboard_filter', 'wpem_event_dashboard_nonce' ); ?>
						<div class="wpem-events-filter">
							<?php do_action('event_manager_event_dashboard_event_filter_start'); ?>
							<div class="wpem-events-filter-block">
								<div class="wpem-form-group"><input name="search_keywords" id="search_keywords" type="text" value="<?php echo esc_attr( $wpem_search_keywords ); ?>" placeholder="<?php esc_attr_e( 'Keywords', 'wp-event-manager' ); ?>"></div>
							</div>
							<div class="wpem-events-filter-block">
								<div class="wpem-form-group">
									<select name="search_order_by" id="search_order_by">
										<option value=""><?php esc_attr_e('Order by', 'wp-event-manager'); ?></option>
										<?php
										foreach (wpem_get_event_order_by() as $wpem_order_by) : ?>
											<?php if (isset($wpem_order_by['type']) && !empty($wpem_order_by['type'])) : ?>
												<optgroup label="<?php echo esc_html($wpem_order_by['label']); ?>">
													<?php foreach ($wpem_order_by['type'] as $wpem_order_key => $wpem_order_value) : ?>
														<option value="<?php echo esc_html($wpem_order_key); ?>" <?php selected($wpem_order_key, $wpem_search_order_by); ?>><?php echo esc_html($wpem_order_value); ?></option>
													<?php endforeach; ?>
												</optgroup>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<?php do_action('event_manager_event_dashboard_event_filter_end'); ?>
							<div class="wpem-events-filter-block wpem-events-filter-submit">
								<div class="wpem-form-group">
									<button type="submit" class="wpem-theme-button"><?php esc_html_e('Filter', 'wp-event-manager'); ?></button>
								</div>
							</div>
							<div class="wpem-events-filter-block wpem-events-filter-submit">
								<div class="wpem-form-group">
									<button type="reset" class="wpem-theme-button" id="reset_dashboard"><?php esc_html_e('Reset', 'wp-event-manager'); ?></button>
								</div>
							</div>
						</div>
					</form>
				</div>

				<div class="wpem-dashboard-events-block-wrap">
					<div class="wpem-dashboard-event-list-wrapper" id="wpem-dashboard-event-list-wrapper">
						<div class="wpem-dashboard-event-list-body">

							<?php if (!$events) : ?>
								<div class="wpem-alert wpem-alert-danger"><?php esc_html_e('You do not have any active events.', 'wp-event-manager'); ?></div>
							<?php else :
								foreach ($events as $event) : ?>
									<div class="wpem-dashboard-event-list">
										<div class="wpem-dashboard-event-inner-list-wrap">
											<div class="wpem-dashboard-event-detail-front-block">
												<div class="wpem-dashboard-event-name">
													<?php if ($event->post_status == 'publish') : ?>
														<a href="<?php echo esc_attr(get_permalink($event->ID)); ?>"><?php echo esc_html($event->post_title); ?></a>
													<?php else : ?>
														<?php echo esc_attr($event->post_title); ?> <small class="wpem-event-status-<?php echo esc_attr(sanitize_title(wpem_get_event_status($event))); ?>"><?php wpem_display_event_status($event); ?></small>
													<?php endif; 
													if (wpem_is_event_cancelled($event)) : ?>
														<small class="wpem-event-status-cancelled"><?php esc_html_e('Cancelled', 'wp-event-manager'); ?></small>
													<?php endif;
													if (wpem_is_event_featured($event)) : ?>
														<small class="wpem-event-status-featured"><?php esc_html_e('Featured', 'wp-event-manager'); ?></small>
													<?php endif; ?>
												</div>
											</div>
											<div class="wpem-dboard-event-action">
												<?php
												$wpem_actions = [];
												switch ($event->post_status) {
													case 'publish':
														$wpem_actions['details'] = array(
															'label' => __('Details', 'wp-event-manager'),
															'nonce' => false
														);
														$wpem_actions['edit'] = array(
															'label' => __('Edit', 'wp-event-manager'),
															'nonce' => true
														);
														if (wpem_is_event_cancelled($event)) {
															$wpem_actions['mark_not_cancelled'] = array(
																'label' => __('Mark not cancelled', 'wp-event-manager'),
																'nonce' => true
															);
														} else {
															$wpem_actions['mark_cancelled'] = array(
																'label' => __('Mark cancelled', 'wp-event-manager'),
																'nonce' => true
															);
														}
														$wpem_actions['duplicate'] = array(
															'label' => __('Duplicate', 'wp-event-manager'),
															'nonce' => true
														);
														break;
													case 'expired':
														if (event_manager_get_permalink('submit_event_form')) {
															$wpem_actions['relist'] = array(
																'label' => __('Relist', 'wp-event-manager'),
																'nonce' => true
															);
														}
														break;
													case 'pending_payment':
													case 'pending':
														if (event_manager_user_can_edit_pending_submissions()) {
															$wpem_actions['edit'] = array(
																'label' => __('Edit', 'wp-event-manager'),
																'nonce' => false
															);
														}
														break;
												}
												$wpem_actions['delete'] = array(
													'label' => __('Delete', 'wp-event-manager'),
													'nonce' => true
												);
												$wpem_actions = apply_filters('event_manager_my_event_actions', $wpem_actions, $event);
												foreach ($wpem_actions as $action => $wpem_value) {
													$wpem_action_url = add_query_arg(
														array(
															'action' => $action,
															'event_id' => $event->ID
														),
														get_permalink($event_dashboard)
													);
													if (sanitize_key($wpem_value['nonce'])) {
														$wpem_action_url = wp_nonce_url($wpem_action_url, 'event_manager_my_event_actions');
													} ?>
													<div class="wpem-dboard-event-act-btn"><a href="<?php echo esc_url($wpem_action_url);?>" class="event-dashboard-action-<?php echo esc_attr($action);?>" title="<?php echo esc_html($wpem_value['label']);?>" ><?php echo esc_html($wpem_value['label']);?></a></div>
												<?php }
												?>
											</div>
										</div>
										<div class="wpem-dashboard-event-datetime-location">
											<?php do_action('wpem_event_dashboard_event_info_start', $event); ?>
											<div class="wpem-dashboard-event-date-time">
												<div class="wpem-dashboard-event-placeholder"><strong><?php esc_attr_e('Date And Time', 'wp-event-manager') ?></strong></div>
												<?php 	wpem_display_event_start_date('', '', true, $event); ?> <?php
													if (wpem_get_event_start_time($event)) {
														echo esc_html(wpem_display_date_time_separator()) . ' ';
														wpem_display_event_start_time('', '', true, $event);
													} ?>
													-<br>
													<?php wpem_display_event_end_date('', '', true, $event); ?> <?php
													if (wpem_get_event_start_time($event)) {
														echo esc_html(wpem_display_date_time_separator()) . ' ';
														wpem_display_event_end_time('', '', true, $event);
													} ?>
											</div>
											<div class="wpem-dashboard-event-location">
												<div class="wpem-dashboard-event-placeholder"><strong><?php esc_html_e('Location', 'wp-event-manager') ?></strong></div>
												<?php
												if (wpem_get_event_location($event) === 'Online Event') :
													esc_html_e('Online Event', 'wp-event-manager');
												else :
													wpem_display_event_location(false, $event);
												endif; ?>
											</div>
											<?php do_action('wpem_event_dashboard_event_info_end', $event); ?>
										</div>

										<section class="wpem-event-dashboard-information wpem-event-dashboard-information-toggle"><a href="#" class="hide_section" title="<?php esc_attr_e('Hide', 'wp-event-manager'); ?>"><?php esc_html_e('Hide', 'wp-event-manager'); ?></a>
											<div class="wpem-event-dashboard-information-wrapper">
												<div class="wpem-event-dashboard-information-table">
													<h4 class="wpem-event-dashboard-information-title-box"><?php esc_html_e('Event Details', 'wp-event-manager'); ?></h4>
													<?php foreach ($event_dashboard_columns as $wpem_key => $wpem_column) : 
														if ( $wpem_key === 'event_action' && ! has_action( 'event_manager_event_dashboard_column_event_action' ) ) {
															continue;
														}
														?>
														<div class="wpem-row wpem-event-dashboard-information-table-row">
															<div class="wpem-col-md-6">
																<div class="wpem-event-dashboard-information-table-lines"><strong><?php echo esc_html($wpem_column); ?></strong></div>
															</div>
															<div class="wpem-col-md-6">
																<div class="wpem-event-dashboard-information-table-lines">

																	<?php if ('event_title' === $wpem_key) : 
																		if ($event->post_status == 'publish') : ?>
																			<a href="<?php echo esc_attr(get_permalink($event->ID)); ?>"><?php echo esc_html($event->post_title); ?></a>
																		<?php else : 
																			echo esc_attr($event->post_title); ?> <small>(<?php wpem_display_event_status($event); ?>)</small>
																		<?php endif;
																	elseif ('event_start_date' === $wpem_key) :
																		wpem_display_event_start_date('', '', true, $event);
																	?> &nbsp; <?php
																		wpem_display_event_start_time('', '', true, $event);
																		
																	elseif ('event_end_date' === $wpem_key) :
																		wpem_display_event_end_date('', '', true, $event);
																		?>&nbsp;<?php
																			wpem_display_event_end_time('', '', true, $event);
																	elseif ('event_location' === $wpem_key) :
																		if (wpem_get_event_location($event) == 'Online Event') :
																			echo esc_attr('Online Event', 'wp-event-manager');
																		else :
																			wpem_display_event_location(false, $event);
																		endif;
																	
																	elseif ('view_count' === $wpem_key) :
																		echo  wp_kses_post(wpem_get_post_views_count($event)); ?>

																	<?php else : 
																		do_action('event_manager_event_dashboard_column_' . $wpem_key, $event);
																	endif; ?>
																</div>
															</div>
														</div>
													<?php endforeach; ?>
												</div>
											</div>
										</section>
									</div>
								<?php endforeach; 
							endif;
							wpem_get_event_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>
						</div>
					</div>
				</div>
			<?php endif; 
			do_action('event_manager_event_dashboard_after'); ?>
		</div>
	</div>
	<!-- Event Dashboard End -->
</div>