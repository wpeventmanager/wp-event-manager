<?php wp_enqueue_style('wp-event-manager-jquery-ui-daterangepicker'); 
wp_enqueue_style('wp-event-manager-jquery-ui-daterangepicker-style');
wp_enqueue_script('wp-event-manager-jquery-ui-daterangepicker');
wp_enqueue_script('wp-event-manager-ajax-filters');

do_action('event_manager_event_filters_before', $atts); 
$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
					
					//covert datepicker format  into php date() function date format
					$php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);?>

<!-- Event Filter Version 2 Start -->

<form class="wpem-main wpem-form-wrapper wpem-event-filter-wrapper event_filters wpem-main wpem-form-wrapper wpem-event-filter-version-2" id="event_filters">
	<?php wp_nonce_field( 'wpem_filter_action', 'wpem_filter_nonce' ); ?>
	<?php do_action('event_manager_event_filters_start', $atts); ?>
	<div class="wpem-event-filter-version-2-search-row">
		<div class="wpem-event-filter-version-2-search-col">
			<div class="wpem-event-filter-version-2-icon">
				<i class="wpem-icon-search" aria-hidden="true"></i>
			</div>
			<div class="wpem-form-group">
				<label for="search_keywords" class="wpem-form-label"><?php esc_attr_e('Search for events', 'wp-event-manager'); ?></label>
				<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e('Search for events', 'wp-event-manager'); ?>" value="<?php echo esc_attr($keywords); ?>" />
			</div>
		</div>

		<div class="wpem-event-filter-version-2-search-col">
			<div class="wpem-event-filter-version-2-icon">
				<i class="wpem-icon-location" aria-hidden="true"></i>
			</div>
			<div class="wpem-form-group wpem-location-container">
				<label for="search_location" class="wpem-form-label"><?php esc_attr_e('Location', 'wp-event-manager'); ?></label>
				<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e('Location', 'wp-event-manager'); ?>" value="<?php echo esc_attr($location); ?>" />
			</div>
		</div>
		
		<div class="wpem-event-filter-version-2-filter-button">
			<button type="button" id="wpem-event-filter-version-2-filter-action" class="wpem-event-filter-version-2-filter-action" aria-label="Event filter button"><i class="wpem-icon-equalizer2" aria-hidden="true"></i></button>
		</div>
		<button type="button" id="wpem-event-filter-version-2-search-btn" class="wpem-event-filter-version-2-search-btn wpem-theme-button"><?php esc_attr_e('Find Events', 'wp-event-manager'); ?></button>
		
	</div>

	<div class="wpem-event-filter-version-2-dropdown" id="wpem-event-filter-version-2-dropdown">
		<?php do_action('event_manager_event_filters_search_events_start', $atts); ?>

		<div class="wpem-event-filter-version-2-dropdown-additional-filters">
			<div class="wpem-event-filter-version-2-dropdown-title"><?php esc_attr_e('Additional Filters', 'wp-event-manager');?></div>
			<div class="wpem-row">
				<!-- Search by date section start -->
				<div class="wpem-col">
					<div class="wpem-form-group">
						<label for="search_fromdate" class="wpem-form-label"><?php esc_attr_e('From', 'wp-event-manager'); ?></label>
						<input type="text" name="search_fromdate" id="search_fromdate" value='' placeholder="<?php esc_attr_e('From', 'wp-event-manager'); ?>" data-date-format="<?php echo esc_attr($datepicker_date_format); ?>" />
					</div>
				</div>
				<div class="wpem-col">
					<div class="wpem-form-group">
						<label for="search_todate" class="wpem-form-label"><?php esc_attr_e('To', 'wp-event-manager'); ?></label>
						<input type="text" name="search_todate" id="search_todate" value='' placeholder="<?php esc_attr_e('To', 'wp-event-manager'); ?>" data-date-format="<?php echo esc_attr($datepicker_date_format); ?>" />
					</div>
				</div>
				<!-- Search by date section end -->

			</div> <!-- /row -->

			<div class="wpem-row">
				<!-- Search by event categories section start -->
				<?php if(isset($categories) && !empty($categories)) :
					foreach ($categories as $category) : ?>
						<input type="hidden" name="search_categories[]" value="<?php echo esc_attr(sanitize_title($category)); ?>" />
					<?php endforeach;
				elseif(isset($show_categories) && !empty($show_categories) && !is_tax('event_listing_category') && get_terms('event_listing_category', ['hide_empty' => false])) : ?>
					<div class="wpem-col">
						<div class="wpem-form-group">
							<label for="search_categories" class="wpem-form-label"><?php esc_attr_e('Category', 'wp-event-manager'); ?></label>
							<?php if($show_category_multiselect) :
								event_manager_dropdown_selection(array('value' => 'slug', 'taxonomy' => 'event_listing_category', 'hierarchical' => 1, 'name' => 'search_categories', 'orderby' => 'name', 'selected' => $selected_category, 'hide_empty' => false)); ?>
							<?php else :
								event_manager_dropdown_selection(
									array(
										'value' => 'slug',
										'taxonomy' => 'event_listing_category',
										'hierarchical' => 1,
										'show_option_all' => __('Choose an Event Category', 'wp-event-manager'),
										'name' => 'search_categories',
										'orderby' => 'name',
										'selected' => $selected_category,
										'multiple' => false,
										'hide_empty' => false
									)
								); 
							endif; ?>
						</div>
					</div>
				<?php endif; ?>
				<!-- Search by event categories section end -->

				<!-- Search by event type section start -->
				<?php if(isset($event_types) && !empty($event_types)) :
					foreach ($event_types as $event_type) : ?>
						<input type="hidden" name="search_event_types[]" value="<?php echo esc_attr(sanitize_title($event_type)); ?>" />
					<?php endforeach;
				elseif(isset($show_event_types) && !empty($show_event_types) && !is_tax('event_listing_type') && get_terms('event_listing_type', ['hide_empty' => false])) : ?>
					<div class="wpem-col">
						<div class="wpem-form-group">
							<label for="search_event_types" class="wpem-form-label"><?php esc_attr_e('Event Type', 'wp-event-manager'); ?></label>
							<?php if($show_event_type_multiselect) :
								event_manager_dropdown_selection(
									array('value' => 'slug',
										'taxonomy' => 'event_listing_type', 
										'hierarchical' => 1, 
										'name' => 'search_event_types',
										'orderby' => 'name', 
										'selected' => $selected_event_type, 
										'hide_empty' => false, 
										'placeholder' => __('Choose an event type', 'wp-event-manager'), 
										'multiple_text' => __('Choose event types', 'wp-event-manager'))); ?>
							<?php else :
								event_manager_dropdown_selection(
									array('value' => 'slug',
										'taxonomy' => 'event_listing_type',
										'hierarchical' => 1,
										'show_option_all' => __('Choose an Event Type', 'wp-event-manager'),
										'name' => 'search_event_types',
										'orderby' => 'name',
										'selected' => $selected_event_type,
										'multiple' => false, 
										'hide_empty' => false,
										'placeholder' => __('Choose an event type', 'wp-event-manager'), 
										'multiple_text' => __('Choose event types', 'wp-event-manager')
									)
								);
							endif; ?>
						</div>
					</div>
				<?php endif;

				if(isset($show_ticket_prices) && !empty($show_ticket_prices)) :
					if(isset($ticket_prices) && !empty($ticket_prices)) :
						foreach ($ticket_prices as $ticket_price) : ?>
							<input type="hidden" name="search_ticket_prices[]" value="<?php echo esc_attr(sanitize_title($ticket_price)); ?>" />
						<?php endforeach; ?>
					<?php else : ?>
						<div class="wpem-col">
							<div class="wpem-form-group">
								<label for="search_ticket_prices" class="wpem-form-label"><?php esc_attr_e('Ticket Prices', 'wp-event-manager'); ?></label>
								<select name="search_ticket_prices[]" id="search_ticket_prices" class="event-manager-category-dropdown" data-placeholder="Choose any ticket price…" data-no_results_text="<?php esc_attr_e('No results match', 'wp-event-manager'); ?>" data-multiple_text="<?php __('Select Some Options', 'wp-event-manager'); ?>">
									<?php
									$ticket_prices	=	WP_Event_Manager_Filters::get_ticket_prices_filter();
									foreach ($ticket_prices as $key => $value) :
										if(!strcasecmp($selected_ticket_price, $value) || $selected_ticket_price == $key) : ?>
											<option selected=selected value="<?php echo esc_attr($key) != 'ticket_price_any' ? esc_attr($key) : ""; ?>"><?php echo  esc_attr($value); ?></option>
										<?php else : ?>
											<option value="<?php echo esc_attr($key) != 'ticket_price_any' ? esc_attr($key) : ""; ?>"><?php echo  esc_attr($value); ?></option>
									<?php endif;
									endforeach; ?>
								</select>
							</div>
						</div>
					<?php endif;
				endif; ?>
				<!-- Search by event type section end -->
			</div> <!-- /row -->
		

		<?php do_action('event_manager_event_filters_search_events_end', $atts); ?>
		</div>
	</div>
	<?php do_action('event_manager_event_filters_end', $atts); ?>
</form>

	<!-- Event Filter Version 2 End -->
<?php do_action('event_manager_event_filters_after', $atts); ?>
<noscript><?php esc_attr_e('Your browser does not support JavaScript, or it is disabled. JavaScript must be enabled in order to view listings.', 'wp-event-manager'); ?></noscript>