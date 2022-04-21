<?php
global $post;
$start_date = get_event_start_date();
$end_date   = get_event_end_date();
$start_time = get_event_start_time();
$end_time   = get_event_end_time();
$address = get_event_address();
$location =  get_event_location();
$separator = get_wpem_date_time_separator();
wp_enqueue_script('wp-event-manager-slick-script');
wp_enqueue_style('wp-event-manager-slick-style');
do_action('set_single_listing_view_count');
$event = $post;
?>
<div class="single_event_listing">

    <div class="wpem-main wpem-single-event-page">
        <?php if (get_option('event_manager_hide_expired_content', 1) && 'expired' === $post->post_status) : ?>
            <div class="wpem-alert wpem-alert-danger"><?php _e('This listing has been expired.', 'wp-event-manager'); ?></div>

        <?php else : ?>
            <?php if (is_event_cancelled()) : ?>
                <div class="wpem-alert wpem-alert-danger">
                    <span class="event-cancelled"><?php _e('This event has been cancelled.', 'wp-event-manager'); ?></span>
                </div>
            <?php elseif (!attendees_can_apply() && 'preview' !== $post->post_status) : ?>
                <div class="wpem-alert wpem-alert-danger">
                    <span class="listing-expired"><?php _e('Registrations have closed.', 'wp-event-manager'); ?></span>
                </div>
            <?php endif; ?>
            <?php
            /**
             * single_event_listing_start hook
             */
            do_action('single_event_listing_start');
            ?>
            <div class="wpem-single-event-wrapper">
                <div class="wpem-single-event-header-top">
                    <div class="wpem-row">

                        <div class="wpem-col-xs-12 wpem-col-sm-12 wpem-col-md-12 wpem-single-event-images">
                            <?php
                            $event_banners = get_event_banner();
                            if (is_array($event_banners) && sizeof($event_banners) >= 1) :
                            ?>
                                <div class="wpem-single-event-slider-wrapper">
                                    <div class="wpem-single-event-slider">
                                        <?php foreach ($event_banners as $banner_key => $banner_value) : ?>
                                            <div class="wpem-slider-items">
                                                <img src="<?php echo esc_url($banner_value); ?>" alt="<?php the_title(); ?>" />
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="wpem-event-single-image-wrapper">
                                    <div class="wpem-event-single-image"><?php display_event_banner(); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

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
                                                <?php printf(__('by %s', 'wp-event-manager'), get_organizer_name($post, true)); ?>
                                                <?php do_action('single_event_organizer_name_end'); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                    $view_count = get_post_views_count($post);
                                    if ($view_count) :
                                    ?>
                                        <div class="wpem-viewed-event wpem-tooltip wpem-tooltip-bottom"><i class="wpem-icon-eye"></i><?php printf(__(' %d', 'wp-event-manager'), $view_count); ?>
                                            <span class="wpem-tooltiptext"><?php printf(__('%d people viewed this event.', 'wp-event-manager'), $view_count); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (get_event_ticket_price()) : ?>
                                        <div><i class="wpem-icon-ticket"></i> <?php display_event_ticket_price('', '', true, $post); ?></div>
                                    <?php endif; ?>

                                    <?php if (get_event_ticket_option()) : ?>
                                        <div class="wpem-event-ticket-type"><span class="wpem-event-ticket-type-text"><?php display_event_ticket_option(); ?></span></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php do_action('single_event_overview_before'); ?>

                            <div class="wpem-single-event-body-content">
                                <?php do_action('single_event_overview_start'); ?>
                                <?php echo apply_filters('display_event_description', get_the_content()); ?>
                                <?php do_action('single_event_overview_end'); ?>
                            </div>

                            <!-- Additional Info Block Start -->
                            <?php
                            $show_additional_details = apply_filters('event_manager_show_additional_details', true);

                            if ($show_additional_details) :

                                if (!class_exists('WP_Event_Manager_Form_Submit_Event')) {
                                    include_once(EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php');
                                    include_once(EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php');
                                }

                                $form_submit_event_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Event', 'instance'));
                                $custom_fields = $form_submit_event_instance->get_event_manager_fieldeditor_fields();
                                $default_fields = $form_submit_event_instance->get_default_event_fields();

                                $additional_fields = [];
                                if (!empty($custom_fields) && isset($custom_fields) && !empty($custom_fields['event'])) {
                                    foreach ($custom_fields['event'] as $field_name => $field_data) {
                                        if (!array_key_exists($field_name, $default_fields['event'])) {
                                            $meta_key = '_' . $field_name;
                                            $field_value = $event->$meta_key;

                                            if (!empty($field_value)) {
                                                $additional_fields[$field_name] = $field_data;
                                            }
                                        }
                                    }

                                    if (isset($additional_fields['attendee_information_type']))
                                        unset($additional_fields['attendee_information_type']);

                                    if (isset($additional_fields['attendee_information_fields']))
                                        unset($additional_fields['attendee_information_fields']);

                                    $additional_fields = apply_filters('event_manager_show_additional_details_fields', $additional_fields);
                                }

                                if (!empty($additional_fields)) : ?>
                                    <div class="wpem-additional-info-block-wrapper">

                                        <div class="wpem-additional-info-block">
                                            <h3 class="wpem-heading-text"><?php _e('Additional Details', 'wp-event-manager'); ?></h3>
                                        </div>

                                        <div class="wpem-additional-info-block-details">

                                            <?php do_action('single_event_additional_details_start'); ?>

                                            <div class="wpem-row">

                                                <?php
                                                $date_format = WP_Event_Manager_Date_Time::get_event_manager_view_date_format();
                                                $time_format = WP_Event_Manager_Date_Time::get_timepicker_format();

                                                foreach ($additional_fields as $name => $field) : ?>

                                                    <?php
                                                    $field_key = '_' . $name;
                                                    $field_value = $event->$field_key;
                                                    ?>

                                                    <?php if (!empty($field_value)) : ?>

                                                        <?php if ($field['type'] == 'group') : ?>

                                                            <?php if (isset($field['fields']) && !empty($field['fields'])) : ?>

                                                                <div class="wpem-col-12 wpem-additional-info-block-group">

                                                                    <p class="wpem-additional-info-block-title"><strong><?php echo $field['label']; ?></strong></p>

                                                                    <?php foreach ($field_value as $child_index => $child_value) : ?>

                                                                        <?php foreach ($field['fields'] as $child_field_name => $child_field) : ?>

                                                                            <?php if (!empty($child_value[$child_field_name])) : ?><div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                    <div class="wpem-additional-info-block-details-content-items"> <?php
                                                                                                                                                    $my_value_arr = [];
                                                                                                                                                    foreach ($child_value[$child_field_name] as $key => $my_value) {
                                                                                                                                                        $my_value_arr[] = $child_field['options'][$my_value];
                                                                                                                                                    }
                                                                                                                                                    ?>
                                                                                        <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?> -</strong> <?php printf(__('%s', 'wp-event-manager'),  implode(', ', $my_value_arr)); ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            <?php elseif ($child_field['type'] == 'select') : ?>
                                                                                <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                    <div class="wpem-additional-info-block-details-content-items">
                                                                                        <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?> - </strong> <?php printf(__('%s', 'wp-event-manager'),  $child_value[$child_field_name]); ?></p>
                                                                                    </div>
                                                                                </div>


                                                                                <?php if ($child_field['type'] == 'textarea' || $child_field['type'] == 'wp-editor') : ?>
                                                                                    <div class="wpem-col-12 wpem-additional-info-block-textarea">
                                                                                        <div class="wpem-additional-info-block-details-content-items">
                                                                                            <p class="wpem-additional-info-block-title"><strong> <?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?></strong></p>
                                                                                            <p class="wpem-additional-info-block-textarea-text"><?php printf(__('%s', 'wp-event-manager'),  $child_value[$child_field_name]); ?></p>
                                                                                        </div>
                                                                                    </div>

                                                                                <?php elseif ($child_field['type'] == 'multiselect') : ?>

                                                                                    <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                        <div class="wpem-additional-info-block-details-content-items">
                                                                                            <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?> -</strong> <?php printf(__('%s', 'wp-event-manager'),  implode(', ', $my_value_arr)); ?></p>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php elseif ($child_field['type'] == 'select') : ?>
                                                                                    <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                        <div class="wpem-additional-info-block-details-content-items">
                                                                                            <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?> - </strong> <?php printf(__('%s', 'wp-event-manager'),  $child_value[$child_field_name]); ?></p>
                                                                                        </div>
                                                                                    </div>

                                                                                <?php elseif ($child_field['type'] == 'date') : ?>
                                                                                    <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                        <div class="wpem-additional-info-block-details-content-items">
                                                                                            <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?> - </strong> <?php echo date_i18n($date_format, strtotime($child_value[$child_field_name])); ?></p>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php elseif ($child_field['type'] == 'time') : ?>
                                                                                    <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                        <div class="wpem-additional-info-block-details-content-items">
                                                                                            <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?> - </strong> <?php echo date($time_format, strtotime($child_value[$child_field_name])); ?></p>
                                                                                        </div>
                                                                                    </div>

                                                                                <?php elseif ($child_field['type'] == 'file') : ?>
                                                                                    <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                        <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?> - </strong></p>
                                                                                        <div class="wpem-additional-info-block-details-content-items wpem-additional-file-slider">
                                                                                            <?php if (is_array($child_value[$child_field_name])) : ?>
                                                                                                <?php foreach ($child_value[$child_field_name] as $file) : ?>
                                                                                                    <?php if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg'])) : ?>
                                                                                                        <div><img src="<?php echo $file; ?>"></div>
                                                                                                    <?php else : ?>
                                                                                                        <div class="wpem-icon"><a target="_blank" class="wpem-icon-download3" href="<?php echo $file; ?>"> <?php _e('Download', 'wp-event-manager'); ?></a></div>
                                                                                                    <?php endif; ?>
                                                                                                <?php endforeach; ?>
                                                                                            <?php else : ?>
                                                                                                <?php if (in_array(pathinfo($child_value[$child_field_name], PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg'])) : ?>
                                                                                                    <div><img src="<?php echo $child_value[$child_field_name]; ?>"></div>
                                                                                                <?php else : ?>
                                                                                                    <div class="wpem-icon"><a target="_blank" class="wpem-icon-download3" href="<?php echo $child_value[$child_field_name]; ?>"> <?php _e('Download', 'wp-event-manager'); ?></a></div>
                                                                                                <?php endif; ?>
                                                                                            <?php endif; ?>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php elseif ($child_field['type'] == 'url') : ?>
                                                                                    <div class="wpem-col-12 wpem-additional-info-block-textarea">
                                                                                        <div class="wpem-additional-info-block-details-content-items">
                                                                                            <p class="wpem-additional-info-block-textarea-text"><a href="<?php if (isset($child_value[$child_field_name])) echo $child_value[$child_field_name]; ?>"><?php printf(__('%s', 'wp-event-manager'),  $child_field['label']); ?></a></p>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php else : ?>
                                                                                    <?php if (is_array($child_value[$child_field_name])) : ?>
                                                                                        <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                            <div class="wpem-additional-info-block-details-content-items">
                                                                                                <p class="wpem-additional-info-block-title"><strong><?php echo $child_field['label']; ?> -</strong> <?php echo implode(', ', $child_value[$child_field_name]); ?></p>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php else : ?>
                                                                                        <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                                            <div class="wpem-additional-info-block-details-content-items">
                                                                                                <p class="wpem-additional-info-block-title"><strong><?php echo $child_field['label']; ?> -</strong> <?php echo $child_value[$child_field_name]; ?></p>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php endif; ?>

                                                                                <?php endif; ?>
                                                                            <?php endif; ?>
                                                                        <?php endforeach; ?>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php elseif ($field['type'] == 'textarea' || $field['type'] == 'wp-editor') : ?>
                                                            <div class="wpem-col-12 wpem-additional-info-block-textarea">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong> <?php printf(__('%s', 'wp-event-manager'),  $field['label']); ?></strong></p>
                                                                    <p class="wpem-additional-info-block-textarea-text"><?php printf(__('%s', 'wp-event-manager'),  $field_value); ?></p>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($field['type'] == 'multiselect') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <?php
                                                                    $my_value_arr = [];
                                                                    foreach ($field_value as $key => $my_value) {
                                                                        $my_value_arr[] = $field['options'][$my_value];
                                                                    }
                                                                    ?>
                                                                    <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', '-event-manager'),  $field['label']); ?> -</strong> <?php printf(__('%s', 'wp-event-manager'),  implode(', ', $my_value_arr)); ?></p>
                                                                </div>
                                                            </div>

                                                        <?php elseif ($field['type'] == 'select') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $field['label']); ?> - </strong> <?php

                                                                                                                                                                                                        if (isset($field['options'][$field_value]))
                                                                                                                                                                                                            printf(__('%s', 'wp-event-manager'),  $field['options'][$field_value]);
                                                                                                                                                                                                        else
                                                                                                                                                                                                            printf(__('%s', 'wp-event-manager'), $field_value);
                                                                                                                                                                                                        ?></p>
                                                                </div>
                                                            </div>

                                                        <?php elseif (isset($field['type']) && $field['type'] == 'date') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $field['label']); ?> - </strong> <?php echo date_i18n($date_format, strtotime($field_value)); ?></p>
                                                                </div>
                                                            </div>

                                                        <?php elseif (isset($field['type']) && $field['type'] == 'time') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $field['label']); ?> - </strong> <?php echo date($time_format, strtotime($field_value)); ?></p>
                                                                </div>
                                                            </div>

                                                        <?php elseif ($field['type'] == 'file') : ?>
                                                            <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                <p class="wpem-additional-info-block-title"><strong><?php printf(__('%s', 'wp-event-manager'),  $field['label']); ?> - </strong></p>
                                                                <div class="wpem-additional-info-block-details-content-items wpem-additional-file-slider">
                                                                    <?php if (is_array($field_value)) : ?>
                                                                        <?php foreach ($field_value as $file) : ?>
                                                                            <?php if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg'])) : ?>
                                                                                <div><img src="<?php echo $file; ?>"></div>
                                                                            <?php else : ?>
                                                                                <div class="wpem-icon">
                                                                                    <p class="wpem-additional-info-block-title"><strong><?php echo esc_attr(wp_basename($file)); ?></strong></p>
                                                                                    <a target="_blank" class="wpem-icon-download3" href="<?php echo $file; ?>"> <?php _e('Download', 'wp-event-manager'); ?></a>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        <?php endforeach; ?>
                                                                    <?php else : ?>
                                                                        <?php if (in_array(pathinfo($field_value, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg'])) : ?>
                                                                            <div><img src="<?php echo $field_value; ?>"></div>
                                                                        <?php else : ?>
                                                                            <p class="wpem-additional-info-block-title"><strong><?php echo esc_attr(wp_basename($field_value)); ?></strong></p>
                                                                            <div class="wpem-icon"><a target="_blank" class="wpem-icon-download3" href="<?php echo $field_value; ?>"> <?php _e('Download', 'wp-event-manager'); ?></a></div>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                        <?php elseif ($field['type'] == 'url') : ?>
                                                            <div class="wpem-col-12 wpem-additional-info-block-textarea">
                                                                <div class="wpem-additional-info-block-details-content-items">
                                                                    <p class="wpem-additional-info-block-textarea-text"><a target="_blank" href="<?php if (isset($field_value)) echo esc_url($field_value); ?>"><?php printf(__('%s', 'wp-event-manager'),  $field['label']); ?></a></p>
                                                                </div>
                                                            </div>
                                                        <?php else : ?>
                                                            <?php if (is_array($field_value)) : ?>
                                                                <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                    <div class="wpem-additional-info-block-details-content-items">
                                                                        <p class="wpem-additional-info-block-title"><strong><?php echo $field['label']; ?> -</strong> <?php echo implode(', ', $field_value); ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php else : ?>
                                                                <div class="wpem-col-md-6 wpem-col-sm-12 wpem-additional-info-block-details-content-left">
                                                                    <div class="wpem-additional-info-block-details-content-items">
                                                                        <p class="wpem-additional-info-block-title"><strong><?php echo $field['label']; ?> -</strong> <?php echo $field_value; ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                        <?php endif; ?>

                                                    <?php endif; ?>

                                                <?php endforeach; ?>

                                            </div>

                                            <?php do_action('single_event_additional_details_end'); ?>

                                        </div>

                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>

                            <!-- Additional Info Block End  -->

                            <?php do_action('single_event_overview_after'); ?>

                        </div>
                        <div class="wpem-col-xs-12 wpem-col-sm-5 wpem-col-md-4 wpem-single-event-right-content">
                            <div class="wpem-single-event-body-sidebar">
                                <?php do_action('single_event_listing_button_start'); ?>

                                <?php
                                $date_format           = WP_Event_Manager_Date_Time::get_event_manager_view_date_format();
                                $registration_end_date = get_event_registration_end_date();
                                $registration_end_date = !empty($registration_end_date) ? $registration_end_date . ' 23:59:59' : '';
                                $registration_addon_form = apply_filters('event_manager_registration_addon_form', true);
                                $event_timezone          = get_event_timezone();

                                // check if timezone settings is enabled as each event then set current time stamp according to the timezone
                                // for eg. if each event selected then Berlin timezone will be different then current site timezone.
                                if (WP_Event_Manager_Date_Time::get_event_manager_timezone_setting() == 'each_event') {
                                    $current_timestamp = WP_Event_Manager_Date_Time::current_timestamp_from_event_timezone($event_timezone);
                                } else {
                                    $current_timestamp = strtotime(current_time('Y-m-d H:i:s'));
                                }
                                // If site wise timezone selected

                                if (attendees_can_apply() && ((strtotime($registration_end_date) >= $current_timestamp) || empty($registration_end_date)) && $registration_addon_form) {
                                    get_event_manager_template('event-registration.php');
                                } else if (!empty($registration_end_date) && strtotime($registration_end_date) < $current_timestamp) {
                                    echo '<div class="wpem-alert wpem-alert-warning">' . __('Event registration closed.', 'wp-event-manager') . '</div>';
                                }
                                ?>

                                <?php do_action('single_event_listing_button_end'); ?>

                                <div class="wpem-single-event-sidebar-info">

                                    <?php do_action('single_event_sidebar_start'); ?>
                                    <div class="clearfix">&nbsp;</div>
                                    <h3 class="wpem-heading-text"><?php _e('Date And Time', 'wp-event-manager') ?></h3>
                                    <div class="wpem-event-date-time">
                                        <span class="wpem-event-date-time-text"><?php echo date_i18n($date_format, strtotime($start_date)); ?>
                                            <?php if ($start_time) {
                                                echo $separator . ' ' . $start_time;
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        if (get_event_end_date() != '' || get_event_end_time()) {
                                            _e(' to', 'wp-event-manager');
                                        }
                                        ?>
                                        <br />
                                        <span class="wpem-event-date-time-text"><?php echo date_i18n($date_format, strtotime($end_date)); ?>
                                            <?php if ($end_time) {
                                                echo $separator . ' ' . $end_time;
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <!-- Event Registration End Date start-->
                                    <?php if (get_event_registration_end_date()) : ?>
                                        <div class="clearfix">&nbsp;</div>
                                        <h3 class="wpem-heading-text"><?php _e('Registration End Date', 'wp-event-manager'); ?></h3>
                                        <?php display_event_registration_end_date(); ?>
                                    <?php endif; ?>
                                    <!-- Registration End Date End-->

                                    <div>
                                        <div class="clearfix">&nbsp;</div>
                                        <h3 class="wpem-heading-text"><?php _e('Location', 'wp-event-manager'); ?></h3>
                                        <div>
                                            <?php
                                            if (get_event_address()) {
                                                display_event_address();
                                                echo ',';
                                            }
                                            ?>
                                            <?php display_event_location(); ?>
                                        </div>
                                    </div>

                                    <?php if (get_option('event_manager_enable_event_types') && get_event_type()) : ?>
                                        <div class="clearfix">&nbsp;</div>
                                        <h3 class="wpem-heading-text"><?php _e('Event Types', 'wp-event-manager'); ?></h3>
                                        <div class="wpem-event-type"><?php display_event_type(); ?></div>
                                    <?php endif; ?>

                                    <?php if (get_option('event_manager_enable_categories') && get_event_category()) : ?>
                                        <div class="clearfix">&nbsp;</div>
                                        <h3 class="wpem-heading-text"><?php _e('Event Category', 'wp-event-manager'); ?></h3>
                                        <div class="wpem-event-category"><?php display_event_category(); ?></div>
                                    <?php endif; ?>

                                    <?php if (get_organizer_youtube()) : ?>
                                        <div class="clearfix">&nbsp;</div>
                                        <a id="event-youtube-button" data-modal-id="wpem-youtube-modal-popup" class="wpem-theme-button wpem-modal-button"><?php _e('Watch video', 'wp-event-manager'); ?></a>
                                        <div id="wpem-youtube-modal-popup" class="wpem-modal" role="dialog" aria-labelledby="<?php _e('Watch video', 'wp-event-manager'); ?>">
                                            <div class="wpem-modal-content-wrapper">
                                                <div class="wpem-modal-header">
                                                    <div class="wpem-modal-header-title">
                                                        <h3 class="wpem-modal-header-title-text"><?php _e('Watch video', 'wp-event-manager'); ?></h3>
                                                    </div>
                                                    <div class="wpem-modal-header-close"><a href="javascript:void(0)" class="wpem-modal-close" id="wpem-modal-close">x</a></div>
                                                </div>
                                                <div class="wpem-modal-content">
                                                    <?php echo wp_oembed_get(get_organizer_youtube(), array('autoplay' => '1', 'rel' => 0)); ?>
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
                                $is_friend_share = apply_filters('event_manager_event_friend_share', true);

                                if ($is_friend_share) :
                                ?>
                                    <h3 class="wpem-heading-text"><?php _e('Share With Friends', 'wp-event-manager'); ?></h3>
                                    <div class="wpem-share-this-event">
                                        <div class="wpem-event-share-lists">
                                            <?php do_action('single_event_listing_social_share_start'); ?>
                                            <div class="wpem-social-icon wpem-facebook">
                                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php display_event_permalink(); ?>" title="Share this page on Facebook"><?php _e('Facebook', 'wp-event-manager'); ?></a>
                                            </div>
                                            <div class="wpem-social-icon wpem-twitter">
                                                <a href="https://twitter.com/share?text=twitter&url=<?php display_event_permalink(); ?>" title="Share this page on Twitter"><?php _e('Twitter', 'wp-event-manager'); ?></a>
                                            </div>
                                            <div class="wpem-social-icon wpem-linkedin">
                                                <a href="https://www.linkedin.com/sharing/share-offsite/?&url=<?php display_event_permalink(); ?>" title="Share this page on Linkedin"><?php _e('Linkedin', 'wp-event-manager'); ?></a>
                                            </div>
                                            <div class="wpem-social-icon wpem-xing">
                                                <a href="https://www.xing.com/spi/shares/new?url=<?php display_event_permalink(); ?>" title="Share this page on Xing"><?php _e('Xing', 'wp-event-manager'); ?></a>
                                            </div>
                                            <div class="wpem-social-icon wpem-pinterest">
                                                <a href="https://pinterest.com/pin/create/button/?url=<?php display_event_permalink(); ?>" title="Share this page on Pinterest"><?php _e('Pinterest', 'wp-event-manager'); ?></a>
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
                if (get_option('enable_event_organizer')) {
                    get_event_manager_template(
                        'content-single-event_listing-organizer.php',
                        array(),
                        'wp-event-manager/organizer',
                        EVENT_MANAGER_PLUGIN_DIR . '/templates/organizer'
                    );
                }

                if (get_option('enable_event_venue')) {
                    get_event_manager_template(
                        'content-single-event_listing-venue.php',
                        array(),
                        'wp-event-manager/venue',
                        EVENT_MANAGER_PLUGIN_DIR . '/templates/venue'
                    );
                }

                /**
                 * single_event_listing_end hook
                 */
                do_action('single_event_listing_end');
                ?>

            </div>
            <!-- / wpem-wrapper end  -->
        <?php endif; ?>
        <!-- Main if condition end -->
    </div>
    <!-- / wpem-main end  -->
</div>
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

        /* Assign the initially stored url back to the iframe src
        attribute when modal is displayed again */
        jQuery("#event-youtube-button").on('click', function() {
            jQuery("#wpem-youtube-modal-popup .wpem-modal-content iframe").attr('src', url);
        });



    });
</script>