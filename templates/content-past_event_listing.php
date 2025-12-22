<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$wpem_start_date = wpem_get_event_start_date();
$wpem_end_date   = wpem_get_event_end_date();
$event_type = wpem_get_event_type();
$wpem_start_time = wpem_get_event_start_time();
$wpem_end_time   = wpem_get_event_end_time();
if (is_array($event_type) && isset($event_type[0]))
    $event_type = $event_type[0]->slug;

$wpem_thumbnail     = wpem_get_event_thumbnail(); ?>

<div class="wpem-event-box-col wpem-col wpem-col-12 wpem-col-md-6 wpem-col-lg-4 ">
    <!----- wpem-col-lg-4 value can be change by admin settings ------->
    <div class="wpem-event-layout-wrapper">
        <div <?php event_listing_class(''); ?>>
            <a href="<?php wpem_display_event_permalink(); ?>" class="wpem-event-action-url event-style-color <?php echo esc_attr($event_type); ?>">
                <div class="wpem-event-banner">
                    <div class="wpem-event-banner-img" style="background-image: url(<?php echo esc_attr($wpem_thumbnail); ?> ) ">
                        <!-- Hide in list View // Show in Box View -->
                        <?php do_action('event_already_registered_title'); ?>
                        <div class="wpem-event-date">
                            <div class="wpem-event-date-type">
                                <div class="wpem-from-date">
                                    <div class="wpem-date"><?php echo  wp_kses_post(date_i18n('d', strtotime($wpem_start_date))); ?></div>
                                    <div class="wpem-month"><?php echo  wp_kses_post(date_i18n('M', strtotime($wpem_start_date))); ?></div>
                                </div>
                            </div>
                        </div>
                        <!-- Hide in list View // Show in Box View -->
                    </div>
                </div>

                <div class="wpem-event-infomation">
                    <div class="wpem-event-date">
                        <div class="wpem-event-date-type">
                            <div class="wpem-from-date">
                                <div class="wpem-date"><?php echo  wp_kses_post(date_i18n('d', strtotime($wpem_start_date))); ?></div>
                                <div class="wpem-month"><?php echo  wp_kses_post(date_i18n('M', strtotime($wpem_start_date))); ?></div>
                            </div>
                            <div class="wpem-to-date">
                                 <?php if(!empty($wpem_end_date)){ ?>
                                    <div class="wpem-date-separator">-</div>
                                    <div class="wpem-date">                                    
                                        <?php echo  wp_kses_post(date_i18n('d', strtotime($wpem_end_date)));?>
                                    </div>
                                <?php } ?>
                                 <?php if(!empty($wpem_end_date)){ ?>
                                    <div class="wpem-month">
                                        <?php echo  wp_kses_post(date_i18n('M', strtotime($wpem_end_date))); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="wpem-event-details">
                        <div class="wpem-event-title">
                            <h3 class="wpem-heading-text"><?php echo esc_html(get_the_title()); ?></h3>
                        </div>
                        <div class="wpem-event-date-time">
                            <span class="wpem-event-date-time-text"><?php wpem_display_event_start_date(); ?>
                            <?php 
                                if (!empty($wpem_start_time)) { 
                                        echo ' ' . esc_html(wpem_display_date_time_separator()) .' ';
                                    }
                            ?>
                            <?php wpem_display_event_start_time(); ?> - <?php wpem_display_event_end_date(); ?>
                            <?php
                                if (!empty($wpem_end_date) && !empty($wpem_end_time)) {
                                    echo ' ' . esc_html(wpem_display_date_time_separator()) . ' ';
                                }
                            ?>
                            <?php wpem_display_event_end_time(); ?></span>
                        </div>
                        <div class="wpem-event-location">
                            <span class="wpem-event-location-text">
                                <?php
                                if (wpem_get_event_location() == 'Online Event' || wpem_get_event_location() == '') :
                                    echo esc_attr__('Online Event', 'wp-event-manager');
                                else :
                                    wpem_display_event_location(false);
                                endif;  ?>
                            </span>
                        </div>

                        <?php
                        if (get_option('event_manager_enable_event_types') && wpem_get_event_type()) {  ?>
                            <div class="wpem-event-type"><?php wpem_display_event_type(); ?></div>
                        <?php } 
                        do_action('event_already_registered_title'); ?>

                        <!-- Show in list View // Hide in Box View -->
                        <?php
                        if (wpem_get_event_ticket_option()) {  ?>
                            <div class="wpem-event-ticket-type" class="wpem-event-ticket-type-text">
                                <span class="wpem-event-ticket-type-text"><?php echo  wp_kses_post('#' . esc_html(wpem_get_event_ticket_option())); ?></span>
                            </div>
                        <?php } ?>
                        <!-- Show in list View // Hide in Box View -->
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>