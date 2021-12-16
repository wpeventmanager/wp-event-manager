<?php wp_enqueue_style( 'wp-event-manager-jquery-ui-daterangepicker' ); ?>
<?php wp_enqueue_style( 'wp-event-manager-jquery-ui-daterangepicker-style' ); ?>
<?php wp_enqueue_script( 'wp-event-manager-jquery-ui-daterangepicker' ); ?>
<?php wp_enqueue_script( 'wp-event-manager-ajax-filters' ); ?>
<?php do_action( 'event_manager_event_filters_before', $atts ); ?>
<form class="wpem-event-filter2-wrapper">
    <?php do_action( 'event_manager_event_filters_search_events_start', $atts ); ?>
    <div class="wpem-event-filter2">
        <div class="wpem-form-group">
            <i class="wpem-icon-search"></i>
            <input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'Keywords', 'wp-event-manager' ); ?>" value="<?php echo esc_attr( $keywords ); ?>" />
        </div>
        <div class="wpem-form-group">
            <i class="wpem-icon-location"></i>
             <input type="text" name="search_location" id="search_location"  placeholder="<?php esc_attr_e( 'Location', 'wp-event-manager' ); ?>" value="<?php echo esc_attr( $location ); ?>" />
           <!--  <button class="wpem-my-location" type="button"><img class="wpem-my-location-img" src="http://localhost/wpem2021/wp-content/plugins/wp-event-manager-google-maps/assets/images/wpem-my-location-black.png" alt="My Location"></button> -->
        </div>
        <a href="#" class="wpem-event-filter2-show-hide-link"><i class="wpem-icon-equalizer2"></i> Show More</a>
    </div>
    <div class="wpem-form-wrapper wpem-event-filter2-advance">

          <!-- Search by date section start -->
            <?php if ( $datetimes) : ?> 

                <?php
                $arr_selected_datetime = [];
                if(!empty($selected_datetime))
                {
                    $selected_datetime = explode(',', $selected_datetime);

                    $start_date = esc_attr( strip_tags( $selected_datetime[0] ) );
                    $end_date = esc_attr( strip_tags( $selected_datetime[1] ) );

                    

                    //get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
                    $datepicker_date_format     = WP_Event_Manager_Date_Time::get_datepicker_format();
        
                    //covert datepicker format  into php date() function date format
                    $php_date_format        = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format( $datepicker_date_format );

                    if($start_date == 'today')
                    {
                        $start_date = date($php_date_format);
                    }
                    else if($start_date == 'tomorrow')
                    {
                        $start_date = date($php_date_format, strtotime('+1 day'));
                    }

                    $arr_selected_datetime['start'] = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $start_date );
                    $arr_selected_datetime['end'] = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $end_date );

                    $arr_selected_datetime['start']     = date_i18n( $php_date_format, strtotime( $arr_selected_datetime['start'] ) );
                    $arr_selected_datetime['end']   = date_i18n( $php_date_format, strtotime( $arr_selected_datetime['end'] ) );

                    $selected_datetime = json_encode($arr_selected_datetime);
                }
                ?>

        <div class="wpem-form-group">
             <input type="text" name="search_datetimes[]" id="search_datetimes" value='<?php echo $selected_datetime; ?>' class="event-manager-category-dropdown date_range_picker form-control" placeholder="<?php _('Select Date','wp-event-manager');?>">
        </div>
        <?php endif; ?>
        <div class="wpem-form-group">
            <?php if ( $categories ) : ?>
                <?php foreach ( $categories as $category ) : ?>
                    <input type="hidden" name="search_categories[]" value="<?php  echo sanitize_title( $category ); ?>" />
                <?php endforeach; ?>
            <?php elseif ( $show_categories && ! is_tax( 'event_listing_category' ) && get_terms( 'event_listing_category', ['hide_empty' => false] ) ) : ?>
               
                    
                    <?php if ( $show_category_multiselect ) : ?>
                        <?php event_manager_dropdown_selection( array( 'value'=>'slug', 'taxonomy' => 'event_listing_category', 'hierarchical' => 1, 'name' => 'search_categories', 'orderby' => 'name', 'selected' => $selected_category, 'hide_empty' => false) ); ?>
                    <?php else : ?>
                        <?php event_manager_dropdown_selection( array( 
                                'value'=>'slug', 
                                'taxonomy' => 'event_listing_category', 
                                'hierarchical' => 1,
                                'show_option_all' => __( 'Choose a Category', 'wp-event-manager' ), 
                                'name' => 'search_categories', 
                                'orderby' => 'name', 
                                'selected' => $selected_category, 
                                'multiple' => false, 
                                'hide_empty' => false) 
                            ); ?>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- <select class="form-control">
                <option>Event Category</option>
                <option>Business</option>
                <option>Charity</option>
            </select> -->
        </div>
        <div class="wpem-form-group">

            <?php  if ( $event_types) :?>
                <?php foreach ( $event_types as $event_type) : ?>
                    <input type="hidden" name="search_event_types[]" value="<?php echo sanitize_title( $event_type); ?>" />
                <?php endforeach; ?>
            <?php elseif ( $show_event_types && ! is_tax( 'event_listing_type' ) && get_terms( 'event_listing_type', ['hide_empty' => false] ) ) : 
                     if ( $show_event_type_multiselect) :  event_manager_dropdown_selection( array( 'value'=>'slug', 'taxonomy' => 'event_listing_type', 'hierarchical' => 1, 'name' => 'search_event_types', 'orderby' => 'name', 'selected' => $selected_event_type, 'hide_empty' => false) );
                      else : 
                        event_manager_dropdown_selection( array( 'value'=>'slug', 'taxonomy' => 'event_listing_type', 'hierarchical' => 1, 'show_option_all' => __( 'Choose an Event Type', 'wp-event-manager' ), 'name' => 'search_event_types', 'orderby' => 'name', 'selected' => $selected_event_type, 'multiple' => false,'hide_empty' => false) ); 
                    
                     endif; 
                 
             endif; ?>       

           <!--  <select class="form-control">
                <option>Event Type</option>
                <option>Business</option>
                <option>Charity</option>
            </select> -->
        </div>
        <div class="wpem-form-group">
            <?php if ( $show_ticket_prices) : ?>

                <?php  if ( $ticket_prices) :?>
                    <?php foreach ( $ticket_prices as $ticket_price) : ?>
                        <input type="hidden" name="search_ticket_prices[]" value="<?php echo sanitize_title( $ticket_price); ?>" />
                    <?php endforeach; ?>

                <?php else : ?>
                        <select name="search_ticket_prices[]" id="search_ticket_prices" class="event-manager-category-dropdown" data-placeholder="Choose any ticket price…" data-no_results_text="<?php _e('No results match','wp-event-manager'); ?>" data-multiple_text="<?php __('Select Some Options','wp-event-manager'); ?>" >
                        <?php
                        $ticket_prices  =   WP_Event_Manager_Filters::get_ticket_prices_filter();
                        foreach ( $ticket_prices as $key => $value ) :
                            if(!strcasecmp($selected_ticket_price, $value) || $selected_ticket_price==$key) : ?>
                                <option selected=selected value="<?php echo $key !='ticket_price_any' ? $key : ""; ?>" ><?php echo  $value; ?></option>
                            <?php else : ?>
                                <option value="<?php echo $key !='ticket_price_any' ? $key : ""; ?>" ><?php echo  $value; ?></option>
                            <?php endif;
                        endforeach; ?>
                        </select>
                      
                <?php endif; ?>
            <?php endif; ?>   
        </div>
    </div>
    <?php do_action( 'event_manager_event_filters_search_events_end', $atts ); ?>
    <!-- <div class="wpem-event-show-filter2-data">
        
        <div class="wpem-event-show-filter2-data-title">Filter by</div>
        <div class="wpem-event-show-filter2-data-box"><span>India</span></div>
        <div class="wpem-event-show-filter2-data-box"><span>Business </span></div>
        <div class="wpem-event-show-filter2-data-right">
            <div class="wpem-event-show-filter2-data-result">0 Matching Records</div>
            <div class="wpem-event-filter2-alert"><a href="#">Add Alert</a></div>
            <div class="wpem-event-filter2-rss"><a href="#">RSS</a></div>
            <div class="wpem-event-filter2-clear-all"><a href="#">Clear All</a></div>
        </div>
    </div> -->
     <?php do_action( 'event_manager_event_filters_end', $atts ); ?>
</form>
<?php do_action( 'event_manager_event_filters_after', $atts ); ?>
<noscript><?php _e( 'Your browser does not support JavaScript, or it is disabled. JavaScript must be enabled in order to view listings.', 'wp-event-manager' ); ?></noscript>
