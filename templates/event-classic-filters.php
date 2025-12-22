<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('wp-event-manager-jquery-ui-daterangepicker'); 
wp_enqueue_style('wp-event-manager-jquery-ui-daterangepicker-style');
wp_enqueue_script('wp-event-manager-jquery-ui-daterangepicker');
wp_enqueue_script('wp-event-manager-ajax-filters');

do_action('event_manager_event_filters_before', $atts); ?>

<form class="wpem-main wpem-form-wrapper wpem-event-filter-wrapper event_filters" id="event_filters">
	<?php wp_nonce_field( 'wpem_filter_action', 'wpem_filter_nonce' ); ?>
	<?php do_action('event_manager_event_filters_start', $atts); ?>
	<div class="search_events search-form-container">
		<?php do_action('event_manager_event_filters_search_events_start', $atts); ?>
		<div class="wpem-row">
			<!-- Search by keywords section start -->
			<div class="wpem-col">
				<!-- shows default keywords text field  start-->
				<div class="wpem-form-group">
					<label for="search_keywords" class="wpem-form-label"><?php esc_attr_e('Keywords', 'wp-event-manager'); ?></label>
					<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e('Keywords', 'wp-event-manager'); ?>" value="<?php echo esc_attr($wpem_keywords); ?>" />
				</div>
				<!-- shows default keywords text field end -->
			</div>
			<!-- Search by keywords section end-->

			<!-- Search by location section start -->
			<div class="wpem-col">
				<div class="wpem-form-group wpem-location-container">
					<label for="search_location" class="wpem-form-label"><?php esc_attr_e('Location', 'wp-event-manager'); ?></label>
					<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e('Location', 'wp-event-manager'); ?>" value="<?php echo esc_attr($location); ?>" />
				</div>
			</div>
			<!-- Search by location section end -->

			<!-- Search by date section start -->
			<?php if(isset($datetimes)) : 
				$wpem_arr_selected_datetime = [];
				if(!empty($wpem_selected_datetime)) {
					//get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
					$wpem_datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
					
					//covert datepicker format  into php date() function date format
					$wpem_php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($wpem_datepicker_date_format);

					$wpem_selected_datetime = explode(',', $wpem_selected_datetime);

					$wpem_start_date = esc_attr(wp_strip_all_tags($wpem_selected_datetime[0]));
					if(isset($wpem_selected_datetime[1]) == false) {
						$wpem_end_date = esc_attr(wp_strip_all_tags($wpem_selected_datetime[0]));
					} else {
						if(strtotime($wpem_selected_datetime[1]) !== false && $wpem_selected_datetime[1] == 'tomorrow'){
							$wpem_end_date =  gmdate($wpem_php_date_format, strtotime('+1 day'));
						}else{
							$wpem_end_date = esc_attr(wp_strip_all_tags($wpem_selected_datetime[1]));
						}
					}

					if($wpem_start_date == 'today') {
						$wpem_start_date = gmdate($wpem_php_date_format);
					} else if($wpem_start_date == 'tomorrow') {
						$wpem_start_date = gmdate($wpem_php_date_format, strtotime('+1 day'));
					}

					$wpem_arr_selected_datetime['start'] = WP_Event_Manager_Date_Time::date_parse_from_format($wpem_php_date_format, $wpem_start_date);
					$wpem_arr_selected_datetime['end'] = WP_Event_Manager_Date_Time::date_parse_from_format($wpem_php_date_format, $wpem_end_date);

					$wpem_arr_selected_datetime['start'] 	= date_i18n($wpem_php_date_format, strtotime($wpem_arr_selected_datetime['start']));
					$wpem_arr_selected_datetime['end'] 	= date_i18n($wpem_php_date_format, strtotime($wpem_arr_selected_datetime['end']));

					$wpem_selected_datetime = json_encode($wpem_arr_selected_datetime);
				} ?>

				<div class="wpem-col">
					<div class="wpem-form-group">
						<label for="search_datetimes" class="wpem-form-label"><?php esc_attr_e('Any dates', 'wp-event-manager'); ?></label>
						<input type="text" name="search_datetimes[]" id="search_datetimes" value='<?php echo esc_attr($wpem_selected_datetime); ?>' class="event-manager-category-dropdown date_range_picker">
					</div>
				</div>
			<?php endif; ?>
			<!-- Search by date section end -->

		</div> <!-- /row -->
		<div class="wpem-row">
			<!-- Search by event categories section start -->
			<?php if(isset($categories) && !empty($categories)) :
				foreach ($categories as $wpem_category) : ?>
					<input type="hidden" name="search_categories[]" value="<?php echo esc_attr(sanitize_title($wpem_category)); ?>" />
				<?php endforeach;
			elseif(isset($show_categories) && !empty($show_categories) && !is_tax('event_listing_category') && get_terms(['taxonomy' => 'event_listing_category', 'hide_empty' => false])) : ?>
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
			elseif(isset($show_event_types) && !empty($show_event_types) && !is_tax('event_listing_type') && get_terms(['taxonomy' => 'event_listing_type', 'hide_empty' => false])) : ?>
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
				if(isset($wpem_ticket_prices) && !empty($wpem_ticket_prices)) :
					foreach ($wpem_ticket_prices as $wpem_ticket_price) : ?>
						<input type="hidden" name="search_ticket_prices[]" value="<?php echo esc_attr(sanitize_title($wpem_ticket_price)); ?>" />
					<?php endforeach; ?>
				<?php else : ?>
					<div class="wpem-col">
						<div class="wpem-form-group">
							<label for="search_ticket_prices" class="wpem-form-label"><?php esc_attr_e('Ticket Prices', 'wp-event-manager'); ?></label>
							<select name="search_ticket_prices[]" id="search_ticket_prices" class="event-manager-category-dropdown" data-placeholder="Choose any ticket price…" data-no_results_text="<?php esc_attr_e('No results match', 'wp-event-manager'); ?>" data-multiple_text="<?php __('Select Some Options', 'wp-event-manager'); ?>">
								<?php
								$wpem_ticket_prices	=	WP_Event_Manager_Filters::get_ticket_prices_filter();
								foreach ($wpem_ticket_prices as $wpem_key => $wpem_value) :
									if(!strcasecmp($selected_ticket_price, $wpem_value) || $selected_ticket_price == $wpem_key) : ?>
										<option selected=selected value="<?php echo esc_attr($wpem_key) != 'ticket_price_any' ? esc_attr($wpem_key) : ""; ?>"><?php echo  esc_attr($wpem_value); ?></option>
									<?php else : ?>
										<option value="<?php echo esc_attr($wpem_key) != 'ticket_price_any' ? esc_attr($wpem_key) : ""; ?>"><?php echo  esc_attr($wpem_value); ?></option>
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
<noscript><?php esc_attr_e('Your browser does not support JavaScript, or it is disabled. JavaScript must be enabled in order to view listings.', 'wp-event-manager'); ?></noscript>