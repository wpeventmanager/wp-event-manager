<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
do_action('event_manager_organizer_dashboard_before'); ?>
<!-- organizer dashboard title section start-->
<div class="wpem-dashboard-main-title wpem-dashboard-main-filter">
	<h3 class="wpem-theme-text"><?php esc_html_e('Organizer Dashboard', 'wp-event-manager'); ?></h3>

	<div class="wpem-d-inline-block wpem-dashboard-i-block-btn">

		<?php do_action('event_manager_organizer_dashboard_button_action_start'); 
		
		$submit_organizer = get_option('event_manager_submit_organizer_form_page_id');
		if (!empty($submit_organizer)) : ?>
			<a class="wpem-dashboard-header-btn wpem-dashboard-header-add-btn" title="<?php esc_attr_e('Add organizer', 'wp-event-manager'); ?>" href="<?php echo esc_url(get_permalink($submit_organizer)); ?>"><i class="wpem-icon-plus"></i></a>
		<?php endif;
		
		do_action('event_manager_organizer_dashboard_button_action_end'); ?>

	</div>
</div>
<!-- organizer dashboard title section start-->

<!-- organizer list section start-->
<div id="event-manager-event-dashboard">
	<div class="wpem-responsive-table-block">
		<table class="wpem-main wpem-responsive-table-wrapper">
			<thead>
				<tr>
					<?php foreach ($wpem_organizer_dashboard_columns as $wpem_key => $wpem_column) : ?>
						<th class="wpem-heading-text <?php echo esc_attr($wpem_key); ?>"><?php echo esc_html($wpem_column); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($wpem_organizers)) : ?>
					<tr>
						<td class="wpem_data_td_empty" colspan="<?php echo esc_attr(count($wpem_organizer_dashboard_columns)); ?>"><?php esc_html_e('There are no organizers.', 'wp-event-manager'); ?></td>
					</tr>
				<?php else :
					foreach ($wpem_organizers as $wpem_organizer) : ?>
						<tr>
							<?php foreach ($wpem_organizer_dashboard_columns as $wpem_key => $wpem_column) : ?>
								<td data-title="<?php echo esc_html($wpem_column); ?>" class="<?php echo esc_attr($wpem_key); ?>">
									<?php if ('organizer_name' === $wpem_key) : ?>
										<div class="wpem-organizer-logo"><?php wpem_display_organizer_logo('', '', $wpem_organizer); ?></div>
										<a href="<?php echo esc_url(get_permalink($wpem_organizer->ID)); ?>"><?php echo esc_html($wpem_organizer->post_title); ?></a>

									<?php elseif ('organizer_details' === $wpem_key) : 

										do_action('single_event_listing_organizer_social_start', $wpem_organizer->ID);

										//get disable organizer fields
										$wpem_organizer_fields = wpem_get_hidden_form_fields( 'event_manager_submit_organizer_form_fields', 'organizer');
										$wpem_organizer_website  = !in_array('organizer_website', $wpem_organizer_fields)?wpem_get_organizer_website($wpem_organizer):'';
										$wpem_organizer_facebook = !in_array('organizer_facebook', $wpem_organizer_fields)?wpem_get_organizer_facebook($wpem_organizer):'';
										$wpem_organizer_instagram = !in_array('organizer_instagram', $wpem_organizer_fields)?wpem_get_organizer_instagram($wpem_organizer):'';
										$wpem_organizer_twitter  = !in_array('organizer_twitter', $wpem_organizer_fields)?wpem_get_organizer_twitter($wpem_organizer):'';
										$wpem_organizer_youtube  = !in_array('organizer_youtube', $wpem_organizer_fields)?wpem_get_organizer_youtube($wpem_organizer):'';

										if (empty($wpem_organizer_website) && empty($wpem_organizer_facebook) && empty($wpem_organizer_instagram) && empty($wpem_organizer_twitter) && empty($wpem_organizer_youtube)) {
											?><h1 class="text-left" style="font-weight: 200;">-</h1><?php
										} else { ?>
											<div class="wpem-organizer-social-links">
												<div class="wpem-organizer-social-lists">

													<?php if (!empty($wpem_organizer_website)) { ?>
														<div class="wpem-social-icon wpem-weblink">
															<a href="<?php echo esc_url($wpem_organizer_website); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>"><?php esc_html_e('Website', 'wp-event-manager'); ?></a>
														</div>
													<?php }

													if (!empty($wpem_organizer_facebook)) { ?>
														<div class="wpem-social-icon wpem-facebook">
															<a href="<?php echo esc_url($wpem_organizer_facebook); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php esc_html_e('Facebook', 'wp-event-manager'); ?></a>
														</div>
													<?php }

													if (!empty($wpem_organizer_instagram)) { ?>
														<div class="wpem-social-icon wpem-instagram">
															<a href="<?php echo esc_url($wpem_organizer_instagram); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a>
														</div>
													<?php }

													if (!empty($wpem_organizer_twitter)) { ?>
														<div class="wpem-social-icon wpem-twitter">
															<a href="<?php echo esc_url($wpem_organizer_twitter); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a>
														</div>
													<?php }

													if (!empty($wpem_organizer_youtube)) { ?>
														<div class="wpem-social-icon wpem-youtube">
															<a href="<?php echo esc_url($wpem_organizer_youtube); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a>
														</div>
													<?php } ?>

													<?php do_action('single_event_listing_organizer_single_social_end', $wpem_organizer->ID); ?>
												</div>
											</div>
										<?php } 
									elseif ('organizer_events' === $wpem_key) : 
										$wpem_organizer_events = wpem_get_event_by_organizer_id($wpem_organizer->ID); ?>

										<div class="event-organizer-count wpem-tooltip wpem-tooltip-bottom"><a href="javaScript:void(0)"><?php echo esc_attr(sizeof($wpem_organizer_events)); ?></a>
											<?php if (!empty($wpem_organizer_events)) : ?>
												<span class="organizer-events-list wpem-tooltiptext">
													<?php foreach ($wpem_organizer_events as $wpem_organizer_event) : ?>
														<span><a href="<?php echo esc_url(get_the_permalink($wpem_organizer_event->ID)); ?>"><?php echo esc_html(get_the_title($wpem_organizer_event->ID)); ?></a></span>
													<?php endforeach; ?>
												</span>
											<?php else : ?>
												<span class="organizer-events-list wpem-tooltiptext"><span><a href="#" onclick="return false;"><?php esc_html_e('There is no event.', 'wp-event-manager'); ?></a></span></span>
											<?php endif; ?>
										</div>

									<?php elseif ('organizer_action' === $wpem_key) : ?>
										<div class="wpem-dboard-event-action">
											<?php
											$wpem_actions = array();
											switch ($wpem_organizer->post_status) {
												case 'publish':
													$wpem_actions['edit'] = array(
														'label' => __('Edit', 'wp-event-manager'),
														'nonce' => true
													);
													$wpem_actions['duplicate'] = array(
														'label' => __('Duplicate', 'wp-event-manager'),
														'nonce' => true
													);
													break;
											}
											$wpem_actions['delete'] = array(
												'label' => __('Delete', 'wp-event-manager'),
												'nonce' => true
											);
											$wpem_actions = apply_filters('event_manager_my_organizer_actions', $wpem_actions, $wpem_organizer);
											foreach ($wpem_actions as $action => $wpem_value) {
												$wpem_action_url = add_query_arg(array(
													'action' => $action,
													'organizer_id' => $wpem_organizer->ID
												));
												if (sanitize_key($wpem_value['nonce'])) {
													$wpem_action_url = wp_nonce_url($wpem_action_url, 'event_manager_my_organizer_actions');
												}
												echo wp_kses_post('<div class="wpem-dboard-event-act-btn"><a href="' . esc_url($wpem_action_url) . '" class="event-dashboard-action-' . esc_attr($action) . '" title="' . esc_html($wpem_value['label']) . '" >' . esc_html($wpem_value['label']) . '</a></div>');
											} ?>
										</div>

									<?php else : 
										do_action('event_manager_organizer_dashboard_column_' . $wpem_key, $wpem_organizer); ?>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach;
				endif; ?>
			</tbody>
		</table>
	</div>
	<?php wpem_get_event_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>
<!-- organizer list section end-->
</div>
<?php do_action('event_manager_organizer_dashboard_after'); ?>