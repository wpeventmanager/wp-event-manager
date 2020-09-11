<?php do_action('event_manager_event_dashboard_before'); ?>

<?php do_action('event_manager_event_dashboard_button_action_start'); ?>

<?php $submit_event = get_option('event_manager_submit_event_form_page_id');
if(!empty($submit_event )) : ?>
	<div class="wpem-d-inline-block wpem-dashboard-i-block-btn">
		<a class="wpem-theme-button" href="<?php echo get_permalink($submit_event);?>"><span><?php _e('Add event','wp-event-manager');?></span></a>
	</div>
<?php endif; ?>

<?php do_action('event_manager_event_dashboard_button_action_end'); ?>

<div id="event-manager-event-dashboard">
	<div class="wpem-responsive-table-block">
		<table class="wpem-main wpem-responsive-table-wrapper">
			<thead>
				<tr>
					<?php $event_dashboard = get_option('event_manager_event_dashboard_page_id'); ?>
					<?php foreach( $event_dashboard_columns as $key => $column ) : ?>
						
						<?php if ('event_title' == $key ) : ?>
							<?php
							$order = 'desc';
							if( isset($_GET['orderby']) && $_GET['orderby'] == $key )
							{
								$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
							}

							if( $order == 'asc' )
							{
								$url = add_query_arg( array(
								    'orderby' => $key,
								    'order' => 'desc',
								), get_permalink($event_dashboard) );
							}
							else
							{
								$url = add_query_arg( array(
								    'orderby' => $key,
								    'order' => 'asc',
								), get_permalink($event_dashboard) );	
							}							
							?>
							<th class="wpem-heading-text <?php echo esc_attr( $key ); ?>"><a class="orderby-<?php echo $order; ?>" href="<?php echo $url; ?>"><?php echo esc_html( $column ); ?></a></th>

						<?php elseif ('event_location' == $key ) : ?>
							<?php
							$order = 'desc';
							if( isset($_GET['orderby']) && $_GET['orderby'] == $key )
							{
								$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
							}

							if( $order == 'asc' )
							{
								$url = add_query_arg( array(
								    'orderby' => $key,
								    'order' => 'desc',
								), get_permalink($event_dashboard) );
							}
							else
							{
								$url = add_query_arg( array(
								    'orderby' => $key,
								    'order' => 'asc',
								), get_permalink($event_dashboard) );	
							}							
							?>
							<th class="wpem-heading-text <?php echo esc_attr( $key ); ?>"><a class="orderby-<?php echo $order; ?>" href="<?php echo $url; ?>"><?php echo esc_html( $column ); ?></a></th>

						<?php elseif ('event_start_date' == $key ) : ?>
							<?php
							$order = 'desc';
							if( isset($_GET['orderby']) && $_GET['orderby'] == $key )
							{
								$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
							}

							if( $order == 'asc' )
							{
								$url = add_query_arg( array(
								    'orderby' => $key,
								    'order' => 'desc',
								), get_permalink($event_dashboard) );
							}
							else
							{
								$url = add_query_arg( array(
								    'orderby' => $key,
								    'order' => 'asc',
								), get_permalink($event_dashboard) );	
							}							
							?>
							<th class="wpem-heading-text <?php echo esc_attr( $key ); ?>"><a class="orderby-<?php echo $order; ?>" href="<?php echo $url; ?>"><?php echo esc_html( $column ); ?></a></th>

						<?php else : ?>
							<th class="wpem-heading-text <?php echo esc_attr( $key ); ?>"><?php echo esc_html( $column ); ?></th>
						<?php endif; ?>

					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! $events ) : ?>
				<tr>
					<td colspan="6"><?php _e( 'You do not have any active listings.', 'wp-event-manager' ); ?></td>
				</tr>
				<?php else : ?>
				<?php foreach ( $events as $event ) : ?>
					<tr>
					<?php  foreach ( $event_dashboard_columns as $key => $column ) : ?>
						<td data-title="<?php echo esc_html( $column ); ?>"
						class="<?php echo esc_attr( $key ); ?>">
							<?php if ('event_title' === $key ) : ?>
								<?php if ( $event->post_status == 'publish' ) : ?>
									<a href="<?php echo get_permalink( $event->ID ); ?>"><?php echo esc_html( $event->post_title ); ?></a>
								<?php else : ?>
									<?php echo $event->post_title; ?> <small>(<?php display_event_status( $event ); ?>)</small>
								<?php endif; ?>
								
							<?php elseif ('event_action' === $key ) :?>
		                            <div class="wpem-dboard-event-action">
									<?php
								$actions = array ();
								switch ($event->post_status) {
									case 'publish' :
										$actions ['edit'] = array (
												'label' => __ ( 'Edit', 'wp-event-manager' ),
												'nonce' => false
										);
										if (is_event_cancelled ( $event )) {
											$actions ['mark_not_cancelled'] = array (
													'label' => __ ( 'Mark not cancelled', 'wp-event-manager' ),
													'nonce' => true
											);
										} else {
											$actions ['mark_cancelled'] = array (
													'label' => __ ( 'Mark cancelled', 'wp-event-manager' ),
													'nonce' => true
											);
										}
										$actions ['duplicate'] = array (
												'label' => __ ( 'Duplicate', 'wp-event-manager' ),
												'nonce' => true
										);
										break;
									case 'expired' :
										if (event_manager_get_permalink ( 'submit_event_form' )) {
											$actions ['relist'] = array (
													'label' => __ ( 'Relist', 'wp-event-manager' ),
													'nonce' => true
											);
										}
										break;
									case 'pending_payment' :
									case 'pending' :
										if (event_manager_user_can_edit_pending_submissions ()) {
											$actions ['edit'] = array (
													'label' => __ ( 'Edit', 'wp-event-manager' ),
													'nonce' => false
											);
										}
										break;
								}
								$actions ['delete'] = array (
										'label' => __ ( 'Delete', 'wp-event-manager' ),
										'nonce' => true
								);
								$actions = apply_filters ( 'event_manager_my_event_actions', $actions, $event );
								foreach ( $actions as $action => $value ) {
									$action_url = add_query_arg ( array (
											'action' => $action,
											'event_id' => $event->ID
									) );
									if ($value ['nonce']) {
										$action_url = wp_nonce_url ( $action_url, 'event_manager_my_event_actions' );
									}
									echo '<div class="wpem-dboard-event-act-btn"><a href="' . esc_url ( $action_url ) . '" class="event-dashboard-action-' . esc_attr ( $action ) . '" title="' . esc_html ( $value ['label'] ) . '" >' . esc_html ( $value ['label'] ) . '</a></div>';
								}
								?>
								</div>		
							<?php

							elseif ('event_start_date' === $key) :
								display_event_start_date ( '', '', true, $event );
								?> &nbsp; <?php

								display_event_start_time ( '', '', true, $event );
								?>
							<?php

							elseif ('event_end_date' === $key) :
								display_event_end_date ( '', '', true, $event );
								?>&nbsp;<?php

								display_event_end_time ( '', '', true, $event );
								?>
		                    <?php

							elseif ('event_location' === $key) :
								if (get_event_location ( $event ) == 'Online Event') :
									echo __ ( 'Online Event', 'wp-event-manager' );
								else :
									display_event_location ( false, $event );
								endif;
								?>
							<?php

							elseif ('view_count' === $key) :
								echo get_post_views_count ( $event );
								?>
							<?php else : ?>
								<?php do_action( 'event_manager_event_dashboard_column_' . $key, $event ); ?>
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
<?php do_action('event_manager_event_dashboard_after'); ?>
