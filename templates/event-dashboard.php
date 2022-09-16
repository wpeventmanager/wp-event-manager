<!-- Vertical Menu Start-->

<div class="wpem-main-vmenu-dashboard-wrapper wpem-row">
	<div class="wpem-main-vmenu-dashboard-nav-menu wpem-col-md-3">
		<div class="wpem-main-vmenu-dashboard-nav" id="wpem-main-vmenu-dashboard-nav">
			<ul class="wpem-main-vmenu-dashboard-ul">
				<?php

				$current_action = isset($_GET['action']) ? sanitize_title( $_GET['action'] ): 'event_dashboard';
				$event_id = isset($_GET['event_id']) ? absint($_GET['event_id']) : '';

				$menus = [
					'event_dashboard' => [
						'title' => __('Events', 'wp-event-manager'),
						'icon' => 'wpem-icon-meter',
						'query_arg' => ['action' => 'event_dashboard'],
					],
				];

				if (get_option('enable_event_organizer')) {
					$menus['organizer_dashboard'] = [
						'title' => __('Organizers', 'wp-event-manager'),
						'icon' => 'wpem-icon-user-tie',
						'query_arg' => ['action' => 'organizer_dashboard'],
					];
				}
				if (get_option('enable_event_venue')) {
					$menus['venue_dashboard'] = [
						'title' => __('Venues', 'wp-event-manager'),
						'icon' => 'wpem-icon-location',
						'query_arg' => ['action' => 'venue_dashboard'],
					];
				}

				$menus = apply_filters('wpem_dashboard_menu', $menus);

				$event_dashboard = get_option('event_manager_event_dashboard_page_id');

				do_action('wpem_dashboard_menu_before', $menus);

				foreach ($menus as $name => $menu) {
					if (isset($menu['submenu']) && !empty($menu['submenu'])) {
						$active_parent_menu = '';

						$child_menu_html = '<ul class="wpem-main-vmenu-dashboard-submenu-ul">';

						foreach ($menu['submenu'] as $sub_name => $submenu) {
							if (isset($submenu['query_arg']) && !empty($submenu['query_arg']) && is_array($submenu['query_arg'])) {
								$action_url = add_query_arg(
									$submenu['query_arg'],
									get_permalink($event_dashboard)
								);
							} else {
								$action_url = add_query_arg(
									array(),
									get_permalink($event_dashboard)
								);
							}

							$active_menu = '';
							if ($current_action === $sub_name) {
								$active_menu = 'wpem-main-vmenu-dashboard-link-active';
								$active_parent_menu = 'wpem-main-vmenu-dashboard-link-active';
							}

							$child_menu_html .= '<li class="wpem-main-vmenu-dashboard-submenu-li"><a class="wpem-main-vmenu-dashboard-link ' . $active_menu . '" href="' . $action_url . '">' . $submenu['title'] . '</a></li>';
						}

						$child_menu_html .= '</ul>';


						printf('<li class="wpem-main-vmenu-dashboard-li wpem-main-vmenu-dashboard-sub-menu"><a class="wpem-main-vmenu-dashboard-link %s" href="javascript:void(0)"><i class="%s"></i>%s<i class="wpem-icon-play3 wpem-main-vmenu-caret wpem-main-vmenu-caret-up"></i></a>', $active_parent_menu, $menu['icon'], $menu['title']);

						echo wp_kses_post($child_menu_html);

						printf('</li>');
					} else {
						if (isset($menu['query_arg']) && !empty($menu['query_arg']) && is_array($menu['query_arg'])) {
							$action_url = add_query_arg(
								$menu['query_arg'],
								get_permalink($event_dashboard)
							);
						} else {
							$action_url = add_query_arg(
								array(),
								get_permalink($event_dashboard)
							);
						}

						$active_menu = '';
						if ($current_action === $name) {
							$active_menu = 'wpem-main-vmenu-dashboard-link-active';
						}

						printf('<li class="wpem-main-vmenu-dashboard-li"><a class="wpem-main-vmenu-dashboard-link %s" href="%s"> <i class="%s"></i>%s</a></li>', $active_menu, $action_url, $menu['icon'], $menu['title']);
					}
				}
				?>
			</ul>
		</div>
	</div>

	<!-- Event Dashboard Start -->
	<div class="wpem-main-vmenu-dashboard-content-wrap wpem-col-md-9">
		<div class="wpem-dashboard-main-content">

			<?php do_action('event_manager_event_dashboard_before'); ?>

			<?php if ($current_action === 'organizer_dashboard' && !empty($current_action)) : ?>
				<?php echo do_shortcode('[organizer_dashboard]'); ?>

			<?php elseif ($current_action === 'venue_dashboard' && !empty($current_action)) : ?>
				<?php echo do_shortcode('[venue_dashboard]'); ?>

			<?php elseif (!in_array($current_action, ['event_dashboard', 'delete', 'mark_cancelled', 'mark_not_cancelled']) && !empty($current_action)) : ?>
				<?php if (has_action('event_manager_event_dashboard_content_' . $current_action)) : ?>
					<?php do_action('event_manager_event_dashboard_content_' . $current_action, $atts); ?>
				<?php endif; ?>

			<?php else : ?>
				<div class="wpem-dashboard-main-header">

					<div class="wpem-dashboard-main-title wpem-dashboard-main-filter">
						<h3 class="wpem-theme-text"><?php _e('Event Dashboard', 'wp-event-manager'); ?></h3>

						<div class="wpem-d-inline-block wpem-dashboard-i-block-btn">

							<?php do_action('event_manager_event_dashboard_button_action_start'); ?>

							<?php $submit_event = get_option('event_manager_submit_event_form_page_id');
							if (!empty($submit_event)) : ?>
								<a class="wpem-dashboard-header-btn wpem-dashboard-header-add-btn" title="<?php _e('Add Event', 'wp-event-manager'); ?>" href="<?php echo get_permalink($submit_event); ?>"><i class="wpem-icon-plus"></i></a>
							<?php endif; ?>

							<?php do_action('event_manager_event_dashboard_button_action_end'); ?>

							<a href="javascript:void(0)" title="<?php _e('Filter', 'wp-event-manager'); ?>" class="wpem-dashboard-event-filter wpem-dashboard-header-btn"><i class="wpem-icon-filter"></i></a>
						</div>
					</div>

					<?php
					$_GET = array_map('stripslashes_deep', $_GET);
					$search_keywords = isset($_GET['search_keywords']) ? sanitize_text_field($_GET['search_keywords']) : '';
					$search_order_by = isset($_GET['search_order_by']) ? sanitize_text_field($_GET['search_order_by']) : '';

					$display_block = '';
					if (!empty($search_keywords) || !empty($search_order_by)) {
						$display_block = 'wpem-d-block';
					}
					?>

					<form action="" method="get" class="wpem-form-wrapper wpem-event-dashboard-filter-toggle wpem-dashboard-main-filter-block <?php printf($display_block); ?>">
						<div class="wpem-events-filter">

							<?php do_action('event_manager_event_dashboard_event_filter_start'); ?>

							<div class="wpem-events-filter-block">
								<?php $search_keywords = isset($_GET['search_keywords']) ? $_GET['search_keywords'] : ''; ?>
								<div class="wpem-form-group"><input name="search_keywords" id="search_keywords" type="text" value="<?php echo esc_attr($search_keywords); ?>" placeholder="<?php _e('Keywords', 'wp-event-manager'); ?>"></div>
							</div>
							<div class="wpem-events-filter-block">
								<div class="wpem-form-group">
									<select name="search_order_by" id="search_order_by">
										<option value=""><?php _e('Order by', 'wp-event-manager'); ?></option>
										<?php
										foreach (get_event_order_by() as $order_by) : ?>
											<?php if (isset($order_by['type']) && !empty($order_by['type'])) : ?>
												<optgroup label="<?php echo esc_html($order_by['label']); ?>">
													<?php foreach ($order_by['type'] as $order_key => $order_value) : ?>
														<option value="<?php echo esc_html($order_key); ?>" <?php selected($order_key, $search_order_by); ?>><?php echo esc_html($order_value); ?></option>
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
									<button type="submit" class="wpem-theme-button"><?php _e('Filter', 'wp-event-manager'); ?></button>
								</div>
							</div>
						</div>
					</form>
				</div>

				<div class="wpem-dashboard-events-block-wrap">
					<div class="wpem-dashboard-event-list-wrapper" id="wpem-dashboard-event-list-wrapper">
						<div class="wpem-dashboard-event-list-body">

							<?php if (!$events) : ?>
								<div class="wpem-alert wpem-alert-danger"><?php _e('You do not have any active events.', 'wp-event-manager'); ?></div>

							<?php else : ?>
								<?php foreach ($events as $event) : ?>
									<div class="wpem-dashboard-event-list">
										<div class="wpem-dashboard-event-inner-list-wrap">
											<div class="wpem-dashboard-event-detail-front-block">
												<div class="wpem-dashboard-event-name">
													<?php if ($event->post_status == 'publish') : ?>
														<a href="<?php echo esc_attr(get_permalink($event->ID)); ?>"><?php echo esc_html($event->post_title); ?></a>
													<?php else : ?>
														<?php echo esc_attr($event->post_title); ?> <small class="wpem-event-status-<?php echo sanitize_title(get_event_status($event)); ?>"><?php display_event_status($event); ?></small>
													<?php endif; ?>

													<?php if (is_event_cancelled($event)) : ?>
														<small class="wpem-event-status-cancelled"><?php _e('Cancelled', 'wp-event-manager'); ?></small>
													<?php endif; ?>

													<?php if (is_event_featured($event)) : ?>
														<small class="wpem-event-status-featured"><?php _e('Featured', 'wp-event-manager'); ?></small>
													<?php endif; ?>

												</div>
											</div>
											<div class="wpem-dboard-event-action">

												<?php
												$actions = [];

												switch ($event->post_status) {
													case 'publish':
														$actions['details'] = array(
															'label' => __('Details', 'wp-event-manager'),
															'nonce' => false
														);
														$actions['edit'] = array(
															'label' => __('Edit', 'wp-event-manager'),
															'nonce' => false
														);
														if (is_event_cancelled($event)) {
															$actions['mark_not_cancelled'] = array(
																'label' => __('Mark not cancelled', 'wp-event-manager'),
																'nonce' => true
															);
														} else {
															$actions['mark_cancelled'] = array(
																'label' => __('Mark cancelled', 'wp-event-manager'),
																'nonce' => true
															);
														}
														$actions['duplicate'] = array(
															'label' => __('Duplicate', 'wp-event-manager'),
															'nonce' => true
														);
														break;

													case 'expired':
														if (event_manager_get_permalink('submit_event_form')) {
															$actions['relist'] = array(
																'label' => __('Relist', 'wp-event-manager'),
																'nonce' => true
															);
														}
														break;

													case 'pending_payment':
													case 'pending':
														if (event_manager_user_can_edit_pending_submissions()) {
															$actions['edit'] = array(
																'label' => __('Edit', 'wp-event-manager'),
																'nonce' => false
															);
														}
														break;
												}

												$actions['delete'] = array(
													'label' => __('Delete', 'wp-event-manager'),
													'nonce' => true
												);

												$actions = apply_filters('event_manager_my_event_actions', $actions, $event);

												foreach ($actions as $action => $value) {
													$action_url = add_query_arg(
														array(
															'action' => $action,
															'event_id' => $event->ID
														),
														get_permalink($event_dashboard)
													);

													if ($value['nonce']) {
														$action_url = wp_nonce_url($action_url, 'event_manager_my_event_actions');
													} ?>

													<div class="wpem-dboard-event-act-btn"><a href="<?php echo esc_url($action_url);?>" class="event-dashboard-action-<?php echo esc_attr($action);?>" title="<?php echo esc_html($value['label']);?>" ><?php echo esc_html($value['label']);?></a></div>
												<?php }
												?>
											</div>
										</div>
										<div class="wpem-dashboard-event-datetime-location">
											<?php do_action('wpem_event_dashboard_event_info_start', $event); ?>
											<div class="wpem-dashboard-event-date-time">
												<div class="wpem-dashboard-event-placeholder"><strong><?php _e('Date And Time', 'wp-event-manager') ?></strong></div>
												<?php display_event_start_date('', '', true, $event); ?> <?php
													if (get_event_start_time($event)) {
														display_date_time_separator();
													?> <?php
															display_event_start_time('', '', true, $event);
														} ?>

												-<br>

													<?php display_event_end_date('', '', true, $event); ?> <?php
													if (get_event_start_time($event)) {
														display_date_time_separator();
													?> <?php
															display_event_end_time('', '', true, $event);
														} ?>
											</div>
											<div class="wpem-dashboard-event-location">
												<div class="wpem-dashboard-event-placeholder"><strong><?php _e('Location', 'wp-event-manager') ?></strong></div>
												<?php
												if (get_event_location($event) === 'Online Event') :
													_e('Online Event', 'wp-event-manager');
												else :
													display_event_location(false, $event);
												endif;
												?>
											</div>
											<?php do_action('wpem_event_dashboard_event_info_end', $event); ?>


										</div>

										<section class="wpem-event-dashboard-information wpem-event-dashboard-information-toggle"><a href="#" class="hide_section" title="<?php _e('Hide', 'wp-event-manager'); ?>"><?php _e('Hide', 'wp-event-manager'); ?></a>
											<div class="wpem-event-dashboard-information-wrapper">
												<div class="wpem-event-dashboard-information-table">
													<h4 class="wpem-event-dashboard-information-title-box"><?php _e('Event Details', 'wp-event-manager'); ?></h4>

													<?php foreach ($event_dashboard_columns as $key => $column) : ?>

														<div class="wpem-row wpem-event-dashboard-information-table-row">
															<div class="wpem-col-md-6">
																<div class="wpem-event-dashboard-information-table-lines"><strong><?php echo esc_html($column); ?></strong></div>
															</div>
															<div class="wpem-col-md-6">
																<div class="wpem-event-dashboard-information-table-lines">

																	<?php if ('event_title' === $key) : ?>
																		<?php if ($event->post_status == 'publish') : ?>
																			<a href="<?php echo esc_attr(get_permalink($event->ID)); ?>"><?php echo esc_html($event->post_title); ?></a>
																		<?php else : ?>
																			<?php echo esc_attr($event->post_title); ?> <small>(<?php display_event_status($event); ?>)</small>
																		<?php endif; ?>

																	<?php elseif ('event_start_date' === $key) :
																		display_event_start_date('', '', true, $event);
																	?> &nbsp; <?php
																		display_event_start_time('', '', true, $event);
																		?>

																		<?php elseif ('event_end_date' === $key) :
																		display_event_end_date('', '', true, $event);
																		?>&nbsp;<?php
																			display_event_end_time('', '', true, $event);
																			?>

																	<?php elseif ('event_location' === $key) :
																		if (get_event_location($event) == 'Online Event') :
																			echo esc_attr('Online Event', 'wp-event-manager');
																		else :
																			display_event_location(false, $event);
																		endif;
																	?>

																	<?php elseif ('view_count' === $key) :
																		echo  wp_kses_post(get_post_views_count($event));
																	?>

																	<?php else : ?>
																		<?php do_action('event_manager_event_dashboard_column_' . $key, $event); ?>

																	<?php endif; ?>

																</div>
															</div>
														</div>

													<?php endforeach; ?>

												</div>
											</div>
										</section>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>

							<?php get_event_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>

						</div>
					</div>
				</div>

			<?php endif; ?>

			<?php do_action('event_manager_event_dashboard_after'); ?>

		</div>

	</div>
	<!-- Event Dashboard End -->

</div>