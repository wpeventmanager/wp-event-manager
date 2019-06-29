<?php wp_enqueue_script( 'wp-event-manager-ajax-filters' ); ?>
<?php do_action( 'event_manager_event_filters_before', $atts ); ?>
<form class="wpem-main wpem-form-wrapper wpem-event-filter-wrapper event_filters" id="event_filters">
	<?php do_action( 'event_manager_event_filters_start', $atts ); ?>
	<div class="search_events search-form-container">
	<?php do_action( 'event_manager_event_filters_search_events_start', $atts ); ?>
		 <div class="wpem-row">
			<!-- Search by keywords section start -->
			<div class="wpem-col">
				<!-- shows default keywords text field  start-->
				<div class="wpem-form-group">
				<label for="search_keywords" class="wpem-form-label"><?php _e( 'Keywords', 'wp-event-manager' ); ?></label>
				<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'Keywords', 'wp-event-manager' ); ?>" value="<?php echo esc_attr( $keywords ); ?>" /> 
				</div>
				<!-- shows default keywords text field end -->
			</div>
			<!-- Search by keywords section end-->

			<!-- Search by location section start -->
			<div class="wpem-col">
			<div class="wpem-form-group">
				<label for="search_location" class="wpem-form-label"><?php _e( 'Location', 'wp-event-manager' ); ?></label>
				<input type="text" name="search_location" id="search_location"  placeholder="<?php esc_attr_e( 'Location', 'wp-event-manager' ); ?>" value="<?php echo esc_attr( $location ); ?>" />
			</div>
			</div>

			<!-- Search by location section end -->

			<!-- Search by date section start -->
			<?php if ( $datetimes) : ?>				
				<div class="wpem-col">
			<div class="wpem-form-group">
					<label for="search_datetimes" class="wpem-form-label"><?php _e( 'Any dates', 'wp-event-manager' ); ?></label>
					<select name="search_datetimes[]" id="search_datetimes" class="event-manager-category-dropdown" data-placeholder="Choose any date…" data-no_results_text="No results match" data-multiple_text="<?php _e('Select Some Options','wp-event-manager'); ?>" >
					<?php foreach ( $datetimes as $key => $value  ) :
						if(!strcasecmp($selected_datetime, $value) || $selected_datetime==$key) : ?>
							<option selected=selected  value="<?php echo $key !='datetime_any' ? $key : ""; ?>" ><?php echo  $value; ?></option>
						<?php else : ?>
							<option value="<?php echo $key !='datetime_any' ? $key : ""; ?>" ><?php echo  $value; ?></option>
						<?php endif;						
					 endforeach; ?>
					</select>
					</div>
				</div>
			<?php endif; ?>	  			
			<!-- Search by date section end -->
	         </div> <!-- /row -->
		<div class="wpem-row">
			<!-- Search by event categories section start -->
			<?php if ( $categories ) : ?>
				<?php foreach ( $categories as $category ) : ?>
					<input type="hidden" name="search_categories[]" value="<?php  echo sanitize_title( $category ); ?>" />
				<?php endforeach; ?>
			<?php elseif ( $show_categories && ! is_tax( 'event_listing_category' ) && get_terms( 'event_listing_category' ) ) : ?>
				<div class="wpem-col">
					<div class="wpem-form-group">
					<label for="search_categories" class="wpem-form-label"><?php _e( 'Category', 'wp-event-manager' ); ?></label>
					<?php if ( $show_category_multiselect ) : ?>
						<?php event_manager_dropdown_selection( array( 'value'=>'slug', 'taxonomy' => 'event_listing_category', 'hierarchical' => 1, 'name' => 'search_categories', 'orderby' => 'name', 'selected' => $selected_category, 'hide_empty' => false) ); ?>
					<?php else : ?>
						<?php event_manager_dropdown_selection( array( 'value'=>'slug', 'taxonomy' => 'event_listing_category', 'hierarchical' => 1, 'show_option_all' => __( 'Any Category', 'wp-event-manager' ), 'name' => 'search_categories', 'orderby' => 'name', 'selected' => $selected_category, 'multiple' => false, 'hide_empty' => false) ); ?>
					<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>	 
			<!-- Search by event categories section end -->

			<!-- Search by event type section start -->
			<?php  if ( $event_types) :?>
				<?php foreach ( $event_types as $event_type) : ?>
					<input type="hidden" name="search_event_types[]" value="<?php echo sanitize_title( $event_type); ?>" />
				<?php endforeach; ?>
			<?php elseif ( $show_event_types && ! is_tax( 'event_listing_type' ) && get_terms( 'event_listing_type' ) ) : ?>		
				<div class="wpem-col">
					<div class="wpem-form-group">
					<label for="search_event_types" class="wpem-form-label"><?php _e( 'Event Type', 'wp-event-manager' ); ?></label>
					<?php if ( $show_event_type_multiselect) : ?>
 					    <?php event_manager_dropdown_selection( array( 'value'=>'slug', 'taxonomy' => 'event_listing_type', 'hierarchical' => 1, 'name' => 'search_event_types', 'orderby' => 'name', 'selected' => $selected_event_type, 'hide_empty' => false) ); ?>
					<?php else : ?>
						<?php event_manager_dropdown_selection( array( 'value'=>'slug', 'taxonomy' => 'event_listing_type', 'hierarchical' => 1, 'show_option_all' => __( 'Any Event Type', 'wp-event-manager' ), 'name' => 'search_event_types', 'orderby' => 'name', 'selected' => $selected_event_type, 'multiple' => false,'hide_empty' => false) ); ?>
					<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>		        
			<!-- Search by event type section end -->
			<!-- Search by any ticket price section start -->			
			<?php if ( $show_ticket_prices && $ticket_prices) : ?>				
				<div class="wpem-col">
				<div class="wpem-form-group">
					<label for="search_ticket_prices" class="wpem-form-label"><?php _e( 'Ticket Prices', 'wp-event-manager' ); ?></label>
					<select name="search_ticket_prices[]" id="search_ticket_prices" class="event-manager-category-dropdown" data-placeholder="Choose any ticket price…" data-no_results_text="<?php _e('No results match','wp-event-manager'); ?>" data-multiple_text="<?php __('Select Some Options','wp-event-manager'); ?>" >
					<?php foreach ( $ticket_prices as $key => $value ) :
						if(!strcasecmp($selected_ticket_price, $value) || $selected_ticket_price==$key) : ?>
							<option selected=selected value="<?php echo $key !='ticket_price_any' ? $key : ""; ?>" ><?php echo  $value; ?></option>
						<?php else : ?>
							<option value="<?php echo $key !='ticket_price_any' ? $key : ""; ?>" ><?php echo  $value; ?></option>
						<?php endif;
					endforeach; ?>
					</select>
					</div>
				</div>
			<?php endif; ?>	  
			<!-- Search by any ticket price section end -->  
    </div> <!-- /row -->

    <?php do_action( 'event_manager_event_filters_search_events_end', $atts ); ?>	

  </div>
  <?php do_action( 'event_manager_event_filters_end', $atts ); ?>
</form>
<?php do_action( 'event_manager_event_filters_after', $atts ); ?>
<noscript><?php _e( 'Your browser does not support JavaScript, or it is disabled. JavaScript must be enabled in order to view listings.', 'wp-event-manager' ); ?></noscript>