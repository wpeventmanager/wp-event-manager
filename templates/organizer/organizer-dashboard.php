<?php do_action('event_manager_organizer_dashboard_before'); ?>

<div class="wpem-dashboard-main-title wpem-dashboard-main-filter">
	<h3 class="wpem-theme-text"><?php _e('Organizer Dashboard', 'wp-event-manager'); ?></h3>

	<div class="wpem-d-inline-block wpem-dashboard-i-block-btn">

		<?php do_action('event_manager_organizer_dashboard_button_action_start'); ?>

		<?php $submit_organizer = get_option('event_manager_submit_organizer_form_page_id');
		if (!empty($submit_organizer)) : ?>
			<a class="wpem-dashboard-header-btn wpem-dashboard-header-add-btn" title="<?php _e('Add organizer', 'wp-event-manager'); ?>" href="<?php echo esc_url(get_permalink($submit_organizer)); ?>"><i class="wpem-icon-plus"></i></a>
		<?php endif; ?>

		<?php do_action('event_manager_organizer_dashboard_button_action_end'); ?>

	</div>
</div>

<div id="event-manager-event-dashboard">
	<div class="wpem-responsive-table-block">
		<table class="wpem-main wpem-responsive-table-wrapper">
			<thead>
				<tr>
					<?php foreach ($organizer_dashboard_columns as $key => $column) : ?>
						<th class="wpem-heading-text <?php echo esc_attr($key); ?>"><?php echo esc_html($column); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php if (!$organizers) : ?>
					<tr>
						<td colspan="<?php echo esc_attr(count($organizer_dashboard_columns)); ?>"><?php _e('There are no organizers.', 'wp-event-manager'); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($organizers as $organizer) : ?>
						<tr>

							<?php foreach ($organizer_dashboard_columns as $key => $column) : ?>
								<td data-title="<?php echo esc_html($column); ?>" class="<?php echo esc_attr($key); ?>">
									<?php if ('organizer_name' === $key) : ?>
										<div class="wpem-organizer-logo"><?php display_organizer_logo('', '', $organizer); ?></div>
										<a href="<?php echo esc_url(get_permalink($organizer->ID)); ?>"><?php echo esc_html($organizer->post_title); ?></a>

									<?php elseif ('organizer_details' === $key) : ?>

										<?php

										do_action('single_event_listing_organizer_social_start', $organizer->ID);

										//get disable organizer fields
										$organizer_fields = get_hidden_form_fields( 'event_manager_submit_organizer_form_fields', 'organizer');

										$organizer_website  = !in_array('organizer_website', $organizer_fields)?get_organizer_website($organizer):'';
										$organizer_facebook = !in_array('organizer_facebook', $organizer_fields)?get_organizer_facebook($organizer):'';
										$organizer_instagram = !in_array('organizer_instagram', $organizer_fields)?get_organizer_instagram($organizer):'';
										$organizer_twitter  = !in_array('organizer_twitter', $organizer_fields)?get_organizer_twitter($organizer):'';
										$organizer_youtube  = !in_array('organizer_youtube', $organizer_fields)?get_organizer_youtube($organizer):'';

										if (empty($organizer_website) && empty($organizer_facebook) && empty($organizer_instagram) && empty($organizer_twitter) && empty($organizer_youtube)) {
											echo wp_kses_post('<h1 class="text-center">-</h1>');
										} else {
										?>
											<div class="wpem-organizer-social-links">
												<div class="wpem-organizer-social-lists">

													<?php if (!empty($organizer_website)) {
													?>
														<div class="wpem-social-icon wpem-weblink">
															<a href="<?php echo esc_url($organizer_website); ?>" target="_blank" title="<?php _e('Get Connect on Website', 'wp-event-manager'); ?>"><?php _e('Website', 'wp-event-manager'); ?></a>
														</div>
													<?php
													}

													if (!empty($organizer_facebook)) {
													?>
														<div class="wpem-social-icon wpem-facebook">
															<a href="<?php echo esc_url($organizer_facebook); ?>" target="_blank" title="<?php _e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php _e('Facebook', 'wp-event-manager'); ?></a>
														</div>
													<?php
													}

													if (!empty($organizer_instagram)) {
													?>
														<div class="wpem-social-icon wpem-instagram">
															<a href="<?php echo esc_url($organizer_instagram); ?>" target="_blank" title="<?php _e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php _e('Instagram', 'wp-event-manager'); ?></a>
														</div>
													<?php
													}

													if (!empty($organizer_twitter)) {
													?>
														<div class="wpem-social-icon wpem-twitter">
															<a href="<?php echo esc_url($organizer_twitter); ?>" target="_blank" title="<?php _e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php _e('Twitter', 'wp-event-manager'); ?></a>
														</div>
													<?php
													}

													if (!empty($organizer_youtube)) {
													?>
														<div class="wpem-social-icon wpem-youtube">
															<a href="<?php echo esc_url($organizer_youtube); ?>" target="_blank" title="<?php _e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php _e('Youtube', 'wp-event-manager'); ?></a>
														</div>
													<?php } ?>

													<?php do_action('single_event_listing_organizer_single_social_end', $organizer->ID); ?>
												</div>
											</div>
										<?php } ?>

									<?php elseif ('organizer_events' === $key) : 
										$events = get_event_by_organizer_id($organizer->ID);
										?>

										<div class="event-organizer-count wpem-tooltip wpem-tooltip-bottom"><a href="javaScript:void(0)"><?php echo esc_attr(sizeof($events)); ?></a>
											<?php if (!empty($events)) : ?>
												<span class="organizer-events-list wpem-tooltiptext">
													<?php foreach ($events as $event) : ?>
														<span><a href="<?php echo esc_url(get_the_permalink($event->ID)); ?>"><?php echo esc_attr(get_the_title($event->ID)); ?></a></span>
													<?php endforeach; ?>
												</span>
											<?php else : ?>
												<span class="organizer-events-list wpem-tooltiptext"><span><a href="#"><?php _e('There is no event.', 'wp-event-manager'); ?></a></span></span>
											<?php endif; ?>
										</div>

									<?php elseif ('organizer_action' === $key) : ?>
										<div class="wpem-dboard-event-action">
											<?php
											$actions = array();
											switch ($organizer->post_status) {
												case 'publish':
													$actions['edit'] = array(
														'label' => __('Edit', 'wp-event-manager'),
														'nonce' => false
													);
													$actions['duplicate'] = array(
														'label' => __('Duplicate', 'wp-event-manager'),
														'nonce' => true
													);
													break;
											}
											$actions['delete'] = array(
												'label' => __('Delete', 'wp-event-manager'),
												'nonce' => true
											);
											$actions = apply_filters('event_manager_my_organizer_actions', $actions, $organizer);
											foreach ($actions as $action => $value) {
												$action_url = add_query_arg(array(
													'action' => $action,
													'organizer_id' => $organizer->ID
												));
												if ($value['nonce']) {
													$action_url = wp_nonce_url($action_url, 'event_manager_my_organizer_actions');
												}
												echo wp_kses_post('<div class="wpem-dboard-event-act-btn"><a href="' . esc_url($action_url) . '" class="event-dashboard-action-' . esc_attr($action) . '" title="' . esc_html($value['label']) . '" >' . esc_html($value['label']) . '</a></div>');
											}
											?>
										</div>

									<?php else : ?>
										<?php do_action('event_manager_organizer_dashboard_column_' . $key, $organizer); ?>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>

						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php get_event_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>


</div>
<?php do_action('event_manager_organizer_dashboard_after'); ?>