<?php wp_enqueue_style('wp-event-manager-jquery-ui-daterangepicker'); 
wp_enqueue_style('wp-event-manager-jquery-ui-daterangepicker-style');
wp_enqueue_script('wp-event-manager-jquery-ui-daterangepicker');
wp_enqueue_script('wp-event-manager-ajax-filters');

do_action('event_manager_event_filters_before', $atts); ?>

<form class="wpem-main wpem-form-wrapper wpem-event-filter-wrapper event_filters" id="event_filters">
	<?php do_action('event_manager_event_filters_start', $atts); ?>
	<div class="search_events search-form-container">
		<?php do_action('event_manager_event_filters_search_events_start', $atts); ?>
		<div class="wpem-row">
			<!-- Search by keywords section start -->
			<div class="wpem-col">
				<!-- shows default keywords text field  start-->
				<div class="wpem-form-group">
					<label for="search_keywords" class="wpem-form-label"><?php _e('Keywords', 'wp-event-manager'); ?></label>
					<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e('Keywords', 'wp-event-manager'); ?>" value="<?php echo esc_attr($keywords); ?>" />
				</div>
				<!-- shows default keywords text field end -->
			</div>
			<!-- Search by keywords section end-->

			<!-- Search by location section start -->
			<div class="wpem-col">
				<div class="wpem-form-group wpem-location-container">
					<label for="search_location" class="wpem-form-label"><?php _e('Location', 'wp-event-manager'); ?></label>
					<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e('Location', 'wp-event-manager'); ?>" value="<?php echo esc_attr($location); ?>" />
				</div>
			</div>
			<!-- Search by location section end -->

			<!-- Search by date section start -->
			<?php if(isset($datetimes)) : 
				$arr_selected_datetime = [];
				if(!empty($selected_datetime)) {
					//get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
					$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
					
					//covert datepicker format  into php date() function date format
					$php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

					$selected_datetime = explode(',', $selected_datetime);

					$start_date = esc_attr(strip_tags($selected_datetime[0]));
					if(isset($selected_datetime[1]) == false) {
						$end_date = esc_attr(strip_tags($selected_datetime[0]));
					} else {
						if(strtotime($selected_datetime[1]) !== false && $selected_datetime[1] == 'tomorrow'){
							$end_date =  date($php_date_format, strtotime('+1 day'));
						}else{
							$end_date = esc_attr(strip_tags($selected_datetime[1]));
						}
					}

					if($start_date == 'today') {
						$start_date = date($php_date_format);
					} else if($start_date == 'tomorrow') {
						$start_date = date($php_date_format, strtotime('+1 day'));
					}

					$arr_selected_datetime['start'] = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $start_date);
					$arr_selected_datetime['end'] = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $end_date);

					$arr_selected_datetime['start'] 	= date_i18n($php_date_format, strtotime($arr_selected_datetime['start']));
					$arr_selected_datetime['end'] 	= date_i18n($php_date_format, strtotime($arr_selected_datetime['end']));

					$selected_datetime = json_encode($arr_selected_datetime);
				} ?>

				<div class="wpem-col">
					<div class="wpem-form-group">
						<label for="search_datetimes" class="wpem-form-label"><?php _e('Any dates', 'wp-event-manager'); ?></label>
						<input type="text" name="search_datetimes[]" id="search_datetimes" value='<?php echo esc_attr($selected_datetime); ?>' class="event-manager-category-dropdown date_range_picker">
					</div>
				</div>
			<?php endif; ?>
			<!-- Search by date section end -->

		</div> <!-- /row -->
		<div class="wpem-row">
			<!-- Search by event categories section start -->
			<?php if(isset($categories) && !empty($categories)) :
				foreach ($categories as $category) : ?>
					<input type="hidden" name="search_categories[]" value="<?php echo sanitize_title($category); ?>" />
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
					<input type="hidden" name="search_event_types[]" value="<?php echo sanitize_title($event_type); ?>" />
				<?php endforeach;
			elseif(isset($show_event_types) && !empty($show_event_types) && !is_tax('event_listing_type') && get_terms('event_listing_type', ['hide_empty' => false])) : ?>
				<div class="wpem-col">
					<div class="wpem-form-group">
						<label for="search_event_types" class="wpem-form-label"><?php _e('Event Type', 'wp-event-manager'); ?></label>
						<?php if($show_event_type_multiselect) :
							 event_manager_dropdown_selection(array('value' => 'slug', 'taxonomy' => 'event_listing_type', 'hierarchical' => 1, 'name' => 'search_event_types', 'orderby' => 'name', 'selected' => $selected_event_type, 'hide_empty' => false)); ?>
						<?php else :
							event_manager_dropdown_selection(array('value' => 'slug', 'taxonomy' => 'event_listing_type', 'hierarchical' => 1, 'show_option_all' => __('Choose an Event Type', 'wp-event-manager'), 'name' => 'search_event_types', 'orderby' => 'name', 'selected' => $selected_event_type, 'multiple' => false, 'hide_empty' => false));
						endif; ?>
					</div>
				</div>
			<?php endif;

			if(isset($show_ticket_prices) && !empty($show_ticket_prices)) :
				if(isset($ticket_prices) && !empty($ticket_prices)) :
					foreach ($ticket_prices as $ticket_price) : ?>
						<input type="hidden" name="search_ticket_prices[]" value="<?php echo sanitize_title($ticket_price); ?>" />
					<?php endforeach; ?>
				<?php else : ?>
					<div class="wpem-col">
						<div class="wpem-form-group">
							<label for="search_ticket_prices" class="wpem-form-label"><?php _e('Ticket Prices', 'wp-event-manager'); ?></label>
							<select name="search_ticket_prices[]" id="search_ticket_prices" class="event-manager-category-dropdown" data-placeholder="Choose any ticket price…" data-no_results_text="<?php _e('No results match', 'wp-event-manager'); ?>" data-multiple_text="<?php __('Select Some Options', 'wp-event-manager'); ?>">
								<?php
								$ticket_prices	=	WP_Event_Manager_Filters::get_ticket_prices_filter();
								foreach ($ticket_prices as $key => $value) :
									if(!strcasecmp($selected_ticket_price, $value) || $selected_ticket_price == $key) : ?>
										<option selected=selected value="<?php echo esc_attr($key) != 'ticket_price_any' ? $key : ""; ?>"><?php echo  $value; ?></option>
									<?php else : ?>
										<option value="<?php echo esc_attr($key) != 'ticket_price_any' ? $key : ""; ?>"><?php echo  $value; ?></option>
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
	<?php do_action('event_manager_event_filters_end', $atts); ?>
</form>
<?php do_action('event_manager_event_filters_after', $atts); ?>
<noscript><?php _e('Your browser does not support JavaScript, or it is disabled. JavaScript must be enabled in order to view listings.', 'wp-event-manager'); ?></noscript>