<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $post;
$wpem_start_date = wpem_get_event_start_date();
$wpem_end_date   = wpem_get_event_end_date();
$wpem_start_time = wpem_get_event_start_time();
$wpem_end_time   = wpem_get_event_end_time();
$wpem_address = wpem_get_event_address();
$wpem_location =  wpem_get_event_location();
$wpem_separator = wpem_get_date_time_separator();
$wpem_check_ticket_visibility = get_option('event_manager_enable_event_ticket_prices_filter', true);
wp_enqueue_script('wp-event-manager-slick-script');
wp_enqueue_style('wp-event-manager-slick-style');
do_action('wpem_set_single_listing_view_count');
$event = $post; ?>

<div class="single_event_listing">
    <div class="wpem-main wpem-single-event-page">
        <?php 
        //check if event is expired/cancelled/preview mode then display message else display event details
        if (get_option('event_manager_hide_expired_content', 1) && 'expired' === $post->post_status) : ?>
            <div class="wpem-alert wpem-alert-danger"><?php esc_html_e('This listing has been expired.', 'wp-event-manager'); ?></div>
        <?php else : 
            if (wpem_is_event_cancelled()) : ?>
                <div class="wpem-alert wpem-alert-danger">
                    <span class="event-cancelled"><?php esc_html_e('This event has been cancelled.', 'wp-event-manager'); ?></span>
                </div>
            <?php elseif (!wpem_attendees_can_apply() && 'preview' !== $post->post_status) : ?>
                <div class="wpem-alert wpem-alert-danger">
                    <span class="listing-expired"><?php esc_html_e('Registrations have closed.', 'wp-event-manager'); ?></span>
                </div>
            <?php endif;
            
            /**
             * single_event_listing_start hook
             */
            do_action('single_event_listing_start'); ?>
            <div class="wpem-single-event-wrapper">
                <div class="wpem-single-event-header-top">
                    <div class="wpem-row">
                         <!-- Event banner section start-->
                        <div class="wpem-col-xs-12 wpem-col-sm-12 wpem-col-md-12 wpem-single-event-images">
                            <?php
                            $event_banners = wpem_get_event_banner();
                            if (is_array($event_banners) && sizeof($event_banners) >= 1) :
                                $event_banners = array_filter($event_banners);
					            $event_banners = array_values($event_banners); ?>
                                <div class="wpem-single-event-slider-wrapper">
                                    <div class="wpem-single-event-slider">
                                        <?php foreach ($event_banners as $wpem_banner_key => $wpem_banner_value) : ?>
                                            <div class="wpem-slider-items">
                                                <img src="<?php echo esc_url($wpem_banner_value); ?>" alt="<?php the_title(); ?>" />
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="wpem-event-single-image-wrapper">
                                    <div class="wpem-event-single-image"><?php wpem_display_event_banner(); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Event banner section end-->
                    </div>
                </div>

                <div class="wpem-single-event-body">
                    <div class="wpem-row">
                        <div class="wpem-col-xs-12 wpem-col-sm-7 wpem-col-md-8 wpem-single-event-left-content">
                            <div class="wpem-single-event-short-info">
                                <div class="wpem-event-details">
                                    <div class="wpem-event-title">
                                        <h3 class="wpem-heading-text"><?php the_title(); ?></h3>
                                    </div>
                                    <?php if (get_option('enable_event_organizer')) : ?>
                                        <div class="wpem-event-organizer">
                                            <div class="wpem-event-organizer-name">
                                                <?php do_action('single_event_organizer_name_start'); ?>
                                                <?php 
												// translators: %s is the name of the event organizer.
												printf(wp_kses_post(__('by %s', 'wp-event-manager')), wp_kses_post(wpem_get_organizer_name($post, true))); ?>
                                                <?php do_action('single_event_organizer_name_end'); ?>
                                            </div>
                                        </div>
                                    <?php endif;
                                    
                                    $wpem_view_count = wpem_get_post_views_count($post);
									
                                    if ($wpem_view_count) : ?>
                                        <div class="wpem-viewed-event wpem-tooltip wpem-tooltip-bottom"><i class="wpem-icon-eye"></i><?php 
										// translators: %d is the number of views for the event.
										printf(esc_html(' %d', 'wp-event-manager'), esc_attr($wpem_view_count)); ?>
                                            <span class="wpem-tooltiptext"><?php
											// translators: %d is the number of people who viewed the event.
											printf(esc_attr('%d people viewed this event.', 'wp-event-manager'), esc_attr($wpem_view_count)); ?></span>
                                        </div>
                                    <?php endif; 
                                    do_action('single_event_ticket_overview_before'); 
                                    if(isset($wpem_check_ticket_visibility) && !empty($wpem_check_ticket_visibility)) : 
                                        if (wpem_get_event_ticket_price() && wpem_get_event_ticket_option()) : ?>
                                            <div class="wpem-event-ticket-price"><i class="wpem-icon-ticket"></i> <?php wpem_display_event_ticket_price('', '', true, $post); ?></div>
                                        <?php endif; 
                                    endif; 
                                    do_action('single_event_ticket_overview_after'); ?>
                                </div>
                            </div>

                            <?php do_action('single_event_overview_before'); ?>
                            <!-- Event description section start-->
                            <div class="wpem-single-event-body-content">
                                <?php do_action('single_event_overview_start'); 
                                echo esc_attr(apply_filters('wpem_the_content', $event->post_content)); 
                                do_action('single_event_overview_end'); ?>
                            </div>
                            <!-- Event description section end-->
                            
                            <!-- Additional Info Block Start -->
                            <?php
                            $wpem_show_additional_details = apply_filters('event_manager_show_additional_details', true);

                            if ($wpem_show_additional_details) :
                                if (!class_exists('WPEM_Event_Manager_Form_Submit_Event')) {
                                    include_once(EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php');
                                    include_once(EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php');
                                }

                                $wpem_form_submit_event_instance = call_user_func(array('WPEM_Event_Manager_Form_Submit_Event', 'instance'));
                                $wpem_custom_fields = $wpem_form_submit_event_instance->get_event_manager_fieldeditor_fields();
                                $wpem_default_fields = $wpem_form_submit_event_instance->get_default_event_fields();

                                $wpem_additional_fields = [];
                                if (!empty($wpem_custom_fields) && isset($wpem_custom_fields) && !empty($wpem_custom_fields['event'])) {
                                    foreach ($wpem_custom_fields['event'] as $wpem_field_name => $wpem_field_data) {
                                        if (!array_key_exists($wpem_field_name, $wpem_default_fields['event'])) {
                                            $wpem_meta_key = '_' . $wpem_field_name;
                                            $wpem_field_value = $event->$wpem_meta_key;
                                            if(isset($wpem_field_data['visibility']) && ($wpem_field_data['visibility'] == false || $wpem_field_data['visibility'] == 0 )){
                                                continue;
                                            }
                                            if(is_array($wpem_field_value) )
                                                $wpem_field_value = array_filter($wpem_field_value);
											if($wpem_field_value == "" || (is_array($wpem_field_value) && empty($wpem_field_value)) || $wpem_field_value == null){
												continue;  //Skips over empty additional fields
					    					}
                                            if (isset($wpem_field_value)) {
                                                $wpem_additional_fields[$wpem_field_name] = $wpem_field_data;
                                            }
                                        }
                                    }

                                    if (isset($wpem_additional_fields['attendee_information_type']))
                                        unset($wpem_additional_fields['attendee_information_type']);

                                    if (isset($wpem_additional_fields['attendee_information_fields']))
                                        unset($wpem_additional_fields['attendee_information_fields']);
                                    if (isset($wpem_additional_fields['event_thumbnail'])){
                                        unset($wpem_additional_fields['event_thumbnail']);
                                    }
                                    $wpem_additional_fields = apply_filters('event_manager_show_additional_details_fields', $wpem_additional_fields);
                                }

                                if (!empty($wpem_additional_fields)) : ?>
                                    <div class="wpem-additional-info-block-wrapper">
                                        <div class="wpem-additional-info-block">
                                            <h3 class="wpem-heading-text"><?php 
											
											echo esc_attr('Additional Details', 'wp-event-manager'); ?></h3>
                                        </div>

                                        <div class="wpem-additional-info-block-details">
                                            <?php do_action('single_event_additional_details_start'); ?>
                                            <div class="wpem-row">
                                                <?php
                                                $wpem_date_format = WP_Event_Manager_Date_Time::get_event_manager_view_date_format();
                                                $wpem_time_format = WP_Event_Manager_Date_Time::get_timepicker_format();
                                                foreach ($wpem_additional_fields as $wpem_name => $wpem_field) :
                                                    $wpem_field_key = '_' . stripslashes($wpem_name);
                                                    $wpem_field_label = stripslashes( $wpem_field['label'] );
                                                    $wpem_field_value = $event->$wpem_field_key;
                                                    
                                                    if (!empty($wpem_field_value) && apply_filters('wpem_single_event_additional_detail', true, $wpem_name, $wpem_field, $event)) :
                                                        do_action('single_event_additional_details_field_start');
                                                        if ($wpem_field['type'] == 'textarea' || $wpem_field['type'] == 'wp-editor') : ?>
                                                            <div class="wpem-col-12 wpem-additional-info-block-textarea">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong> <?php 
                                                                    // translators: %s is the label for the field.
                                                                    printf(esc_html('%s', 'wp-event-manager'), esc_attr($wpem_field_label)); ?></strong></p>
                                                                                                                                    <p class="wpem-additional-info-block-textarea-text"><?php
                                                                    // translators: %s is the value of the field.
                                                                    printf(esc_html('%s', 'wp-event-manager'),  wp_kses_post($wpem_field_value)); ?></p>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($wpem_field['type'] == 'multiselect') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <?php
                                                                    $wpem_my_value_arr = [];
                                                                    foreach ($wpem_field_value as $wpem_key => $wpem_my_value) {
                                                                        $wpem_my_value_arr[] = $wpem_field['options'][$wpem_my_value];
                                                                    } ?>
                                                                    <p class="wpem-additional-info-block-title"><strong><?php
                                                                    // translators: %s is the label of the field.
                                                                    printf(esc_html('%s', 'wp-event-manager'),   esc_attr($wpem_field_label)); ?> -</strong> <?php printf(esc_html('%s', 'wp-event-manager'),  esc_attr(implode(', ', $wpem_my_value_arr))); ?></p>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($wpem_field['type'] == 'select') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong><?php
                                                                        // translators: %s is the label for the field.
                                                                        printf(esc_html('%s', 'wp-event-manager'),   esc_attr($wpem_field_label)); ?> - </strong> <?php
                                                                        if (isset($wpem_field['options'][$wpem_field_value]))
                                                                        // translators: %s is the value for the field.
                                                                        printf(esc_html('%s', 'wp-event-manager'),  esc_attr($wpem_field['options'][$wpem_field_value]));
                                                                                                                                        else
                                                                        // translators: %s is the label for the field.
                                                                        printf(esc_html('%s', 'wp-event-manager'), esc_attr($wpem_field_value));
                                                                        ?></p>
                                                                </div>
                                                            </div>
                                                        <?php elseif (isset($wpem_field['type']) && $wpem_field['type'] == 'date') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title">
                                                                        <strong>
                                                                            <?php echo esc_html( $wpem_field_label ); ?> - 
                                                                        </strong> 
                                                                        <?php echo esc_html( date_i18n( $wpem_date_format, strtotime( $wpem_field_value ) ) ); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        <?php elseif (isset($wpem_field['type']) && $wpem_field['type'] == 'time') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong><?php 
                                                                    // translators: %s is the label for the field.
                                                                    printf(esc_html('%s', 'wp-event-manager'),   esc_attr($wpem_field_label)); ?> - </strong> <?php echo esc_attr(gmdate($wpem_time_format, strtotime($wpem_field_value))); ?></p>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($wpem_field['type'] == 'file') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <p class="wpem-additional-info-block-title"><strong><?php 
                                                                // translators: %s is the label for the field.
																printf(esc_html('%s', 'wp-event-manager'),   esc_attr($wpem_field_label)); ?> - </strong></p>
                                                                <div class="wpem-additional-info-block-details-content-items wpem-additional-file-slider">
                                                                    <?php if (is_array($wpem_field_value)) : 
                                                                        foreach ($wpem_field_value as $wpem_file) : 
                                                                            if (in_array(pathinfo($wpem_file, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg'])) : ?>
                                                                                <div><img src="<?php echo esc_attr($wpem_file); ?>"></div>
                                                                            <?php else : ?>
                                                                                <div class="wpem-icon">
                                                                                    <p class="wpem-additional-info-block-title"><strong><?php echo esc_attr(wp_basename($wpem_file)); ?></strong></p>
                                                                                    <a target="_blank" class="wpem-icon-download3" href="<?php echo esc_attr($wpem_file); ?>"> <?php esc_attr_e('Download', 'wp-event-manager'); ?></a>
                                                                                </div>
                                                                            <?php endif; 
                                                                        endforeach; ?>
                                                                    <?php else :
                                                                        if (in_array(pathinfo($wpem_field_value, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg'])) : ?>
                                                                            <div><img src="<?php echo esc_attr($wpem_field_value); ?>"></div>
                                                                        <?php else : ?>
                                                                            <p class="wpem-additional-info-block-title"><strong><?php echo esc_attr(wp_basename($wpem_field_value)); ?></strong></p>
                                                                            <div class="wpem-icon"><a target="_blank" class="wpem-icon-download3" href="<?php echo esc_attr($wpem_field_value); ?>"> <?php esc_attr_e('Download', 'wp-event-manager'); ?></a></div>
                                                                        <?php endif;
                                                                    endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($wpem_field['type'] == 'url') : ?>
                                                            <div class="wpem-col-12 wpem-additional-info-block-textarea">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-textarea-text"><a target="_blank" href="<?php if (isset($wpem_field_value)) echo esc_url($wpem_field_value); ?>"><?php 
	                                                                // translators: %s is the label for the field.
																	printf(esc_html('%s', 'wp-event-manager'),   esc_attr($wpem_field_label)); ?></a></p>
                                                                </div>
                                                            </div>
                                                       <?php elseif ($wpem_field['type'] == 'media-library-image') : ?>
                                                            <div class="wpem-col-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <div class="wpem-additional-info-block-title wpem-mb-2">
                                                                        <strong><?php echo esc_attr($wpem_field_label); ?></strong>
                                                                    </div>    
                                                                    <div class="wpem-additional-info-block-details-content-items-images">
                                                                        <?php 
                                                                        if (!empty($wpem_field_value)) {
                                                                            $wpem_files = is_array($wpem_field_value) ? $wpem_field_value : array($wpem_field_value);
                                                                            $wpem_image_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                                                                            foreach ($wpem_files as $wpem_file_url) {
                                                                                if(empty($wpem_file_url)) continue;
                                                                                $wpem_file_ext = strtolower(pathinfo($wpem_file_url, PATHINFO_EXTENSION));
                                                                                if (in_array($wpem_file_ext, $wpem_image_exts)) {
                                                                                    echo '<img src="' . esc_url($wpem_file_url) . '" alt="' . esc_attr($wpem_field_label) . '" />';
                                                                                } else {
                                                                                    echo '<a href="' . esc_url($wpem_file_url) . '" download class="wpem-download-button" style="margin-right:10px;">Download File</a>';
                                                                                }
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($wpem_field['type'] == 'radio' && array_key_exists('options',$wpem_field)) : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong><?php echo esc_attr( $wpem_field_label); ?> -</strong> <?php echo isset($wpem_field['options'][$wpem_field_value]) ? esc_attr($wpem_field['options'][$wpem_field_value]) : ''; ?></p>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($wpem_field['type'] == 'term-checklist' && array_key_exists('taxonomy',$wpem_field)) : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong><?php
                                                                    // translators: %s is the label for the field.
                                                                    printf(esc_html('%s', 'wp-event-manager'),   esc_attr($wpem_field_label)); ?> - </strong>
                                                                    <?php 
                                                                    $wpem_terms = wp_get_post_terms($post->ID, $wpem_field['taxonomy']);
                                                                    $wpem_term_checklist = '';
                                                                    if (!empty($wpem_terms)):
                                                                        $wpem_numTerm = count($wpem_terms);
                                                                        $wpem_i = 0;
                                                                        foreach ($wpem_terms as $term) :
                                                                            $wpem_term_checklist .= $term->name;
                                                                            if ($wpem_numTerm > ++$wpem_i)
                                                                            $wpem_term_checklist .= ', ';
                                                                        endforeach;
                                                                    endif;
                                                                    echo esc_attr($wpem_term_checklist); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($wpem_field['type'] == 'checkbox') : ?>
                                                            <div class="wpem-col-12 wpem-additional-info-block-textarea">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-textarea-text">
                                                                        <strong><?php echo esc_attr( $wpem_field_label); ?></strong> - <?php
                                                                        if ($wpem_field_value == 1) {
                                                                            echo esc_attr("Yes");
                                                                        } else {
                                                                            echo esc_attr("No");
                                                                        } ?> 
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        <?php else : ?>
                                                            <?php if (is_array($wpem_field_value)) : ?>
                                                                <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                    <div class="wpem-additional-info-block-details-content-items">
                                                                        <p class="wpem-additional-info-block-title"><strong><?php echo esc_attr( $wpem_field_label); ?> -</strong> <?php echo  esc_attr(implode(', ', $wpem_field_value)); ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php else : ?>
                                                                <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                    <div class="wpem-additional-info-block-details-content-items">
                                                                        <p class="wpem-additional-info-block-title"><strong><?php echo esc_attr( $wpem_field_label); ?> -</strong> <?php echo esc_attr($wpem_field_value); ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; 
                                                            do_action('single_event_additional_details_field_end');
                                                        endif; 
                                                    endif;
                                                endforeach; ?>
                                            </div>
                                            <?php do_action('single_event_additional_details_end'); ?>
                                        </div>
                                    </div>
                                <?php endif;
                            endif; ?>
                            <!-- Additional Info Block End  -->
                            <?php do_action('single_event_overview_after'); ?>
                        </div>
                        <div class="wpem-col-xs-12 wpem-col-sm-5 wpem-col-md-4 wpem-single-event-right-content">
                            <div class="wpem-single-event-body-sidebar">
                                <?php do_action('single_event_listing_button_start'); ?>
                                <!-- Event registration button section start-->
                               <?php
                                $post = $event;
                                $wpem_date_format           = WP_Event_Manager_Date_Time::get_event_manager_view_date_format();
                                $wpem_registration_end_date = wpem_get_event_registration_end_date();
                                $wpem_registration_end_date = !empty($wpem_registration_end_date) ? $wpem_registration_end_date . ' 23:59:59' : '';
                                $wpem_registration_addon_form = apply_filters('event_manager_registration_addon_form', true);
                                $event_timezone          = wpem_get_event_timezone();

                                // check if timezone settings is enabled as each event then set current time stamp according to the timezone
                                // for eg. if each event selected then Berlin timezone will be different then current site timezone.
                                if (WP_Event_Manager_Date_Time::get_event_manager_timezone_setting() == 'each_event') {
                                    $wpem_current_timestamp = WP_Event_Manager_Date_Time::current_timestamp_from_event_timezone($event_timezone);
                                } else {
                                    $wpem_current_timestamp = strtotime(current_time('Y-m-d H:i:s'));
                                }
                                // If site wise timezone selected
                                if (wpem_attendees_can_apply() && ((strtotime($wpem_registration_end_date) >= $wpem_current_timestamp) || empty($wpem_registration_end_date)) && $wpem_registration_addon_form) {
                                    wpem_get_event_manager_template('event-registration.php');
                                } else if (!empty($wpem_registration_end_date) && strtotime($wpem_registration_end_date) < $wpem_current_timestamp) {
                                    echo '<div class="wpem-alert wpem-alert-warning">' . esc_html('Event registration closed.', 'wp-event-manager') . '</div>';
                                }
                                ?>
                                <!-- Event registration button section end-->
                                <?php do_action('single_event_listing_button_end'); ?>

                                <div class="wpem-single-event-sidebar-info">

                                    <?php do_action('single_event_sidebar_start'); ?>
                                    <div class="clearfix">&nbsp;</div>
                                    <!-- Event date section start-->
                                    <h3 class="wpem-heading-text"><?php esc_attr_e('Date And Time', 'wp-event-manager') ?></h3>
                                    <div class="wpem-event-date-time">
                                        <span class="wpem-event-date-time-text">
                                            <?php if($wpem_start_date){ 
                                                echo  esc_attr(date_i18n($wpem_date_format, strtotime($wpem_start_date))); ?>
                                                <?php if ($wpem_start_time) {
                                                    echo esc_html(wpem_display_date_time_separator() . ' ' . esc_attr($wpem_start_time));
                                                }
                                            }else{echo esc_attr('-');  } ?>
                                        </span>
                                        <?php
                                        if (wpem_get_event_end_date() != '') {
                                            esc_html_e(' to', 'wp-event-manager'); ?>
                                            <br />
                                            <span class="wpem-event-date-time-text"><?php echo  esc_attr(date_i18n($wpem_date_format, strtotime($wpem_end_date))); ?>
                                                <?php if ($wpem_end_time) {
                                                    echo esc_html(wpem_display_date_time_separator() . ' ' . esc_attr($wpem_end_time));
                                                }
                                                ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <!-- Event date section end-->

                                    <!-- Event Registration End Date start-->
                                    <?php if (wpem_get_event_registration_end_date()) : ?>
                                        <div class="clearfix">&nbsp;</div>
                                        <h3 class="wpem-heading-text"><?php esc_html_e('Registration End Date', 'wp-event-manager'); ?></h3>
                                        <?php wpem_display_event_registration_end_date(); ?>
                                    <?php endif; ?>
                                    <!-- Registration End Date End-->
                                     <!-- Event location section start-->
                                    <div>
                                        <div class="clearfix">&nbsp;</div>
                                        <h3 class="wpem-heading-text"><?php esc_html_e('Location', 'wp-event-manager'); ?></h3>
                                        <div>
                                            <?php
                                            /* if (wpem_get_event_address()) { ?>
                                                <a href="http://maps.google.com/maps?q=<?php wpem_display_event_address();?>">  
                                                    <?php wpem_display_event_address();
                                                    echo esc_attr(',');?>
                                                </a><?php
                                            } */
                                            if (!wpem_is_event_online()) {?> 
                                                    <?php wpem_display_event_location();?>
                                            <?php } else {?>
                                                <?php esc_attr_e('Online event', 'wp-event-manager'); ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <!-- Event location section end-->
                                    <?php /*event types section */ ?>
                                    <?php if (get_option('event_manager_enable_event_types') && wpem_get_event_type($event)) : ?>
                                        <div class="clearfix">&nbsp;</div>
                                        <h3 class="wpem-heading-text"><?php esc_html_e('Event Types', 'wp-event-manager'); ?></h3>
                                        <div class="wpem-event-type"><?php wpem_display_event_type($event); ?></div>
                                    <?php endif;
                                    /* event categories section */
                                    if (get_option('event_manager_enable_categories') && wpem_get_event_category($event)) : ?>
                                        <div class="clearfix">&nbsp;</div>
                                        <h3 class="wpem-heading-text"><?php esc_html_e('Event Category', 'wp-event-manager'); ?></h3>
                                        <div class="wpem-event-category"><?php wpem_display_event_category($event); ?></div>
                                    <?php endif; 
                                    /* youtube video button section */    
                                    if (wpem_get_organizer_youtube($event)) : ?>
                                        <div class="clearfix">&nbsp;</div>
                                        <a id="event-youtube-button" data-modal-id="wpem-youtube-modal-popup" class="wpem-theme-button wpem-modal-button"><?php esc_html_e('Watch video', 'wp-event-manager'); ?></a>
                                        <div id="wpem-youtube-modal-popup" class="wpem-modal" role="dialog" aria-labelledby="<?php esc_attr_e('Watch video', 'wp-event-manager'); ?>">
                                            <div class="wpem-modal-content-wrapper">
                                                <div class="wpem-modal-header">
                                                    <div class="wpem-modal-header-title">
                                                        <h3 class="wpem-modal-header-title-text"><?php esc_html_e('Watch video', 'wp-event-manager'); ?></h3>
                                                    </div>
                                                    <div class="wpem-modal-header-close"><a href="javascript:void(0)" class="wpem-modal-close" id="wpem-modal-close">x</a></div>
                                                </div>
                                                <div class="wpem-modal-content">
                                                    <div class="wpem-modal-content">
                                                        <?php 
                                                        $wpem_youtube_url = wpem_get_organizer_youtube( $event );

                                                        // Sanitize the URL first
                                                        $wpem_youtube_url = esc_url_raw( $wpem_youtube_url );

                                                        // Get the embed HTML safely
                                                        $wpem_embed_html = wp_oembed_get( $wpem_youtube_url, array(
                                                            'autoplay' => '1',
                                                            'rel'      => 0,
                                                        ));

                                                        // Output the embed HTML, properly escaped
                                                        echo wp_kses(
                                                            $wpem_embed_html,
                                                            array(
                                                                'iframe' => array(
                                                                    'src'             => true,
                                                                    'width'           => true,
                                                                    'height'          => true,
                                                                    'frameborder'     => true,
                                                                    'allowfullscreen' => true,
                                                                    'allow'           => true,
                                                                    'style'           => true,
                                                                ),
                                                            )
                                                        );
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="#">
                                                <div class="wpem-modal-overlay"></div>
                                            </a>
                                        </div>
                                        <div class="clearfix">&nbsp;</div>
                                    <?php endif; ?>
                                    <?php do_action('single_event_sidebar_end'); ?>
                                </div>

                                <?php
                                /* social share section */
                                $wpem_is_friend_share = apply_filters('event_manager_event_friend_share', true);

                                if ($wpem_is_friend_share) : ?>
                                    <h3 class="wpem-heading-text"><?php esc_html_e('Share With Friends', 'wp-event-manager'); ?></h3>
                                    <div class="wpem-share-this-event">
                                        <div class="wpem-event-share-lists">
                                            <?php do_action('single_event_listing_social_share_start'); ?>
                                            <div class="wpem-social-icon wpem-facebook">
                                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php wpem_display_event_permalink(); ?>" title="Share this page on Facebook"><?php esc_html_e('Facebook', 'wp-event-manager'); ?></a>
                                            </div>
                                            <div class="wpem-social-icon wpem-twitter">
                                                <a href="https://twitter.com/share?text=twitter&url=<?php wpem_display_event_permalink(); ?>" title="Share this page on Twitter"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a>
                                            </div>
                                            <div class="wpem-social-icon wpem-linkedin">
                                                <a href="https://www.linkedin.com/sharing/share-offsite/?&url=<?php wpem_display_event_permalink(); ?>" title="Share this page on Linkedin"><?php esc_html_e('Linkedin', 'wp-event-manager'); ?></a>
                                            </div>
                                            <div class="wpem-social-icon wpem-xing">
                                                <a href="https://www.xing.com/spi/shares/new?url=<?php wpem_display_event_permalink(); ?>" title="Share this page on Xing"><?php esc_html_e('Xing', 'wp-event-manager'); ?></a>
                                            </div>
                                            <div class="wpem-social-icon wpem-pinterest">
                                                <a href="https://pinterest.com/pin/create/button/?url=<?php wpem_display_event_permalink(); ?>" title="Share this page on Pinterest"><?php esc_html_e('Pinterest', 'wp-event-manager'); ?></a>
                                            </div>
                                            <?php do_action('single_event_listing_social_share_end'); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $post = $event;
                //if organizer setting is enable then display organizer section on single event listing
                if (get_option('enable_event_organizer')) {
                    wpem_get_event_manager_template(
                        'content-single-event_listing-organizer.php',
                        array(),
                        'wp-event-manager/organizer',
                        EVENT_MANAGER_PLUGIN_DIR . '/templates/organizer'
                   );
                }
                //if venue setting is enable then display venue section on single event listing
                if (get_option('enable_event_venue')) {
                    wpem_get_event_manager_template(
                        'content-single-event_listing-venue.php',
                        array(),
                        'wp-event-manager/venue',
                        EVENT_MANAGER_PLUGIN_DIR . '/templates/venue'
                   );
                }
                /**
                 * single_event_listing_end hook
                 */
                do_action('single_event_listing_end');  ?>
                <?php
                $wpem_enable_health_guideline = get_post_meta($post->ID, '_enable_health_guideline', true);
                $event_health_guidelines = get_post_meta($post->ID, '_event_health_guidelines', true);
                $event_health_guidelines = !empty($event_health_guidelines) ? (array) $event_health_guidelines : [];

                $wpem_enable_health_guideline_other = get_post_meta($post->ID, '_enable_health_guideline_other', true);
                $wpem_other_guidelines_text = get_post_meta($post->ID, '_event_health_guidelines_other', true);

                $wpem_health_guidelines_list = array(
                    'face_masks_required'      => array('label' => __('Face masks required', 'wp-event-manager'), 'icon' => 'wpem-icon-head-side-mask'),
                    'temperature_checked'      => array('label' => __('Temperature will be checked at entrance', 'wp-event-manager'), 'icon' => 'wpem-icon-temperature'),
                    'physical_distance'        => array('label' => __('Physical distance maintained event', 'wp-event-manager'), 'icon' => 'wpem-icon-people-distance'),
                    'event_sanitized'          => array('label' => __('Event area sanitized before event', 'wp-event-manager'), 'icon' => 'wpem-icon-house-medical'),
                    'event_outside'            => array('label' => __('Event is held outside', 'wp-event-manager'), 'icon' => 'wpem-icon-spruce-tree'),
                    'vaccination_required'     => array('label' => __('Vaccination Required', 'wp-event-manager'), 'icon' => 'wpem-icon-syringe'),
                );

                if ($wpem_enable_health_guideline === 'yes' && !empty($event_health_guidelines)) :
                ?>
                    <!-- Health Guidelines Start -->
                    <div class="wpem-single-event-footer">
                        <div class="wpem-event-health-guidelines-wrapper">
                            <div class="wpem-listing-accordion active">
                                <h3 class="wpem-heading-text"><?php esc_html_e('Health Guidelines', 'wp-event-manager'); ?></h3>
                                <i class="wpem-icon-minus"></i><i class="wpem-icon-plus"></i>
                            </div>

                            <div class="wpem-listing-accordion-panel active" style="display: block;">
                                <div class="wpem-event-health-guideline-list">
                                    <div class="wpem-row">

                                        <?php foreach ($wpem_health_guidelines_list as $wpem_key => $wpem_data) :
                                            if (isset($event_health_guidelines[$wpem_key])) : ?>
                                                <div class="wpem-col-md-6">
                                                    <div class="wpem-event-health-guideline-list-item wpem-d-flex wpem-align-items-center wpem-my-2">
                                                        <div class="wpem-event-health-guideline-list-item-icon wpem-d-flex wpem-align-items-center wpem-justify-content-center">
                                                            <i class="<?php echo esc_attr($wpem_data['icon']); ?>"></i>
                                                        </div>
                                                        <div class="wpem-event-health-guideline-list-item-title">
                                                            <span><?php echo esc_html($wpem_data['label']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif;
                                        endforeach; ?>

                                        <?php 
                                        if ($wpem_enable_health_guideline_other === 'yes' && !empty($wpem_other_guidelines_text)) : ?>
                                            <div class="wpem-col-md-12">
                                                <div class="wpem-event-health-guideline-list-item wpem-d-flex wpem-align-items-center wpem-my-2">
                                                    <div class="wpem-event-health-guideline-list-item-icon wpem-d-flex wpem-align-items-center wpem-justify-content-center">
                                                        <i class="wpem-icon-notes-medical"></i>
                                                    </div>
                                                    <div class="wpem-event-health-guideline-list-item-title">
                                                        <b><?php esc_html_e('Other Health Guidelines', 'wp-event-manager'); ?></b>
                                                        <span><?php echo esc_html($wpem_other_guidelines_text); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Health Guidelines End -->
                <?php endif; ?>
            </div>
            <!-- / wpem-wrapper end  -->
        <?php endif; ?>
        <!-- Main if condition end -->
    </div>
    <!-- / wpem-main end  -->
</div>
<?php 
    if (!get_option('event_manager_hide_related_events')) {
	$wpem_related_events_output = do_shortcode('[related_events event_id="' . get_the_ID() . '"]'); 
	// check related events available or not
	if (!empty($wpem_related_events_output)) {
?>
    <div class="wpem_related_events wpem-mt-3">
        <h3 class="wpem-heading-text wpem-mb-3">Related Events</h3>
        <div class="wpem_related_events-slider">
            <?php
            // Display related events with a proper design
            echo wp_kses_post($wpem_related_events_output);
            ?>
        </div>
    </div>
<?php }
    }?>

<!-- Related event slider override the script if needed -->
<script>
    jQuery(document).ready(function() {
        jQuery('.wpem_related_events-slider').slick({
            arrow: true,
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2
                }
                },
                {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1
                }
                }
            ]
        });
    });
</script>

<!-- override the script if needed -->

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.wpem-single-event-slider').slick({
            dots: true,
            infinite: true,
            speed: 500,
            fade: true,
            cssEase: 'linear',
            adaptiveHeight: true,
            responsive: [{
                breakpoint: 992,
                settings: {
                    dots: true,
                    infinite: true,
                    speed: 500,
                    fade: true,
                    cssEase: 'linear',
                    adaptiveHeight: true
                }
            }]
        });

        /* Get iframe src attribute value i.e. YouTube video url
        and store it in a variable */
        var url = jQuery("#wpem-youtube-modal-popup .wpem-modal-content iframe").attr('src');

        /* Assign empty url value to the iframe src attribute when
        modal hide, which stop the video playing */
        jQuery(".wpem-modal-close").on('click', function() {
            jQuery("#wpem-youtube-modal-popup .wpem-modal-content iframe").attr('src', '');
        });
        jQuery(".wpem-modal-overlay").on('click', function() {
            jQuery("#wpem-youtube-modal-popup .wpem-modal-content iframe").attr('src', '');
        });

        /* Assign the initially stored url back to the iframe src
        attribute when modal is displayed again */
        jQuery("#event-youtube-button").on('click', function() {
            jQuery("#wpem-youtube-modal-popup .wpem-modal-content iframe").attr('src', url);
        });
    });
</script>
