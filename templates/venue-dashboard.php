<?php do_action('event_manager_organizer_dashboard_before'); ?>

<?php do_action('event_manager_venue_dashboard_button_action_start'); ?>

<?php $submit_venue = get_option('event_manager_submit_venue_form_page_id');
if(!empty($submit_venue )) : ?>
	<div class="wpem-d-inline-block wpem-dashboard-i-block-btn">
		<a class="wpem-theme-button" href="<?php echo get_permalink($submit_venue);?>"><span><?php _e('Add venue','wp-event-manager');?></span></a>
	</div>
<?php endif; ?>

<?php do_action('event_manager_venue_dashboard_button_action_end'); ?>

<div id="event-manager-event-dashboard">
	<div class="wpem-responsive-table-block">
		<table class="wpem-main wpem-responsive-table-wrapper">
			<thead>
				<tr>
					<?php foreach ( $venue_dashboard_columns as $key => $column ) : ?>
					<th class="wpem-heading-text <?php echo esc_attr( $key ); ?>"><?php echo esc_html( $column ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! $venues ) : ?>
				<tr>
					<td colspan="3"><?php _e( 'There are not venue.', 'wp-event-manager' ); ?></td>
				</tr>
				<?php else : ?>
				<?php foreach ( $venues as $venue ) : ?>
				<tr>

					<?php  foreach ( $venue_dashboard_columns as $key => $column ) : ?>
						<td data-title="<?php echo esc_html( $column ); ?>"
						class="<?php echo esc_attr( $key ); ?>">
							<?php if ('venue_name' === $key ) : ?>
								
									<a href="<?php echo get_permalink( $venue->ID ); ?>"><?php echo esc_html( $venue->post_title ); ?></a>
								
								<?php elseif ('venue_events' === $key ) :?>

									<?php // echo get_event_venue_count($venue->ID);
										$events = get_event_by_venue_id($venue->ID);
										?>
										<div  class="event-venue-count wpem-tooltip wpem-tooltip-bottom">
										<a href="javaScript:void(0)"><?php echo sizeof($events);?></a>
										<span class="venue-events-list wpem-tooltiptext">
											<?php $i=1; ?>
											<?php foreach ($events as  $event) : ?>

												<?php if($i > 1) : ?>
													<span>, </span>
												<?php endif; ?>

												<span><a href="<?php echo get_the_permalink($event->ID);?>"><?php echo get_the_title($event->ID);?></a></span>

												<?php $i++; ?>
											<?php endforeach; ?>
										</span>

								<?php elseif ('venue_action' === $key ) :?>
		                            <div class="wpem-dboard-event-action">
									<?php
								$actions = array ();
								switch ($venue->post_status) {
									case 'publish' :
										$actions ['edit'] = array (
												'label' => __ ( 'Edit', 'wp-event-manager' ),
												'nonce' => false
										);
										$actions ['duplicate'] = array (
												'label' => __ ( 'Duplicate', 'wp-event-manager' ),
												'nonce' => true
										);
										break;
								}
								$actions ['delete'] = array (
										'label' => __ ( 'Delete', 'wp-event-manager' ),
										'nonce' => true
								);
								$actions = apply_filters ( 'event_manager_my_venue_actions', $actions, $venue );
								foreach ( $actions as $action => $value ) {
									$action_url = add_query_arg ( array (
											'action' => $action,
											'venue_id' => $venue->ID
									) );
									if ($value['nonce']) {
										$action_url = wp_nonce_url ( $action_url, 'event_manager_my_venue_actions' );
									}
									echo '<div class="wpem-dboard-event-act-btn"><a href="' . esc_url ( $action_url ) . '" class="event-dashboard-action-' . esc_attr ( $action ) . '" title="' . esc_html ( $value ['label'] ) . '" >' . esc_html ( $value ['label'] ) . '</a></div>';
								}
								?>
								</div>		
							

						
							<?php else : ?>
								<?php do_action( 'event_manager_organizer_dashboard_column_' . $key, $venue ); ?>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>

				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php get_event_manager_template( 'pagination.php', array( 'max_num_pages' => $max_num_pages ) ); ?>


   </div>
<?php do_action('event_manager_organizer_dashboard_after'); ?>
