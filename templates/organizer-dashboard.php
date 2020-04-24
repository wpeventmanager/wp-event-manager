<?php do_action('event_manager_organizer_dashboard_before'); ?>
<p>
	<?php $submit_organizer 		= get_option('event_manager_submit_organizer_form_page_id');
	if(!empty($submit_organizer )){ ?>
		<a  href="<?php echo get_permalink($submit_organizer);?>"><?php  _e('Add organizer','wp-event-manager');?></a>
	<?php
	}
	?>	
</p>
<div id="event-manager-event-dashboard">
	<div class="wpem-responsive-table-block">
		<table class="wpem-main wpem-responsive-table-wrapper">
			<thead>
				<tr>
					<?php foreach ( $organizer_dashboard_columns as $key => $column ) : ?>
					<th class="wpem-heading-text <?php echo esc_attr( $key ); ?>"><?php echo esc_html( $column ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! $organizers ) : ?>
				<tr>
					<td colspan="3"><?php _e( 'You do not have any organizer.', 'wp-event-manager' ); ?></td>
				</tr>
				<?php else : ?>
				<?php foreach ( $organizers as $organizer ) : ?>
				<tr>

					<?php  foreach ( $organizer_dashboard_columns as $key => $column ) : ?>
						<td data-title="<?php echo esc_html( $column ); ?>"
						class="<?php echo esc_attr( $key ); ?>">
							<?php if ('organizer_name' === $key ) : ?>
								
									<a href="<?php echo get_permalink( $organizer->ID ); ?>"><?php echo esc_html( $organizer->post_title ); ?></a>
								
								<?php elseif ('organizer_events' === $key ) :?>

									<?php // echo get_event_organizer_count($organizer->ID);
										$events = get_event_by_organizer_id($organizer->ID);
										?>
										<a  class="event-organizer-count" ><?php echo sizeof($events);?></a>
										<div class="organizer-events-list">
											<?php 
											foreach ($events as  $event) { ?>
												<span><?php echo get_the_title($event->ID);?></span>
											<?php
											}
											?>
										</div>

								<?php elseif ('organizer_action' === $key ) :?>
		                            <div class="wpem-dboard-event-action">
									<?php
								$actions = array ();
								switch ($organizer->post_status) {
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
								$actions = apply_filters ( 'event_manager_my_organizer_actions', $actions, $organizer );
								foreach ( $actions as $action => $value ) {
									$action_url = add_query_arg ( array (
											'action' => $action,
											'organizer_id' => $organizer->ID
									) );
									if ($value['nonce']) {
										$action_url = wp_nonce_url ( $action_url, 'event_manager_my_organizer_actions' );
									}
									echo '<div class="wpem-dboard-event-act-btn"><a href="' . esc_url ( $action_url ) . '" class="event-dashboard-action-' . esc_attr ( $action ) . '" title="' . esc_html ( $value ['label'] ) . '" >' . esc_html ( $value ['label'] ) . '</a></div>';
								}
								?>
								</div>		
							

						
							<?php else : ?>
								<?php do_action( 'event_manager_organizer_dashboard_column_' . $key, $organizer ); ?>
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
