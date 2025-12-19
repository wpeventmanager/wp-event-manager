<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $post;
$start_date = wpem_get_event_start_date();
$start_time = wpem_get_event_start_time();
$end_date   = wpem_get_event_end_date();
$end_time   = wpem_get_event_end_time();
$event_type = wpem_get_event_type(); 
if (is_array($event_type) && isset($event_type[0]))
    $event_type = $event_type[0]->slug;

$thumbnail  = wpem_get_event_thumbnail( $post, 'full' ); ?>
<div class="wpem-event-box-col wpem-col wpem-col-12 wpem-col-md-6 wpem-col-lg-<?php echo esc_attr(apply_filters('event_manager_event_wpem_column', '4')); ?>">
    <!----- wpem-col-lg-4 value can be change by admin settings ------->
    <div class="wpem-event-layout-wrapper">
        <div <?php event_listing_class(''); ?>>
            <a href="<?php wpem_display_event_permalink(); ?>" class="wpem-event-action-url event-style-color <?php echo esc_attr($event_type); ?>">
                <div class="wpem-event-banner">
                    <div class="wpem-event-banner-img" style="background-image: url(<?php echo esc_attr($thumbnail) ?>)">
                        <!-- Hide in list View // Show in Box View -->
                        <?php do_action('event_already_registered_title'); 
                        if (wpem_is_event_cancelled()){
                            echo '<div class="wpem-event-cancel-label">
                                <span class="wpem-event-cancel-label-text">' .
                                    esc_html__( 'Cancelled', 'wp-event-manager' ) .
                                '</span>
                            </div>';
                         } ?>
                        <div class="wpem-event-date">
                            <div class="wpem-event-date-type">
                                <?php
                                if (!empty($start_date)) { ?>
                                    <div class="wpem-from-date">
                                        <div class="wpem-date"><?php echo esc_html( date_i18n( 'd', strtotime( $start_date ) ) ); ?></div>
                                        <div class="wpem-month"><?php echo esc_html( date_i18n( 'M', strtotime( $start_date ) ) ); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- Hide in list View // Show in Box View -->
                    </div>
                </div>

                <div class="wpem-event-infomation">
                    <div class="wpem-event-date">
                        <div class="wpem-event-date-type">
                            <?php
                            if (!empty($start_date)) { ?>
                                <div class="wpem-from-date">
                                    <div class="wpem-date"><?php echo esc_html( date_i18n( 'd', strtotime( $start_date ) ) ); ?></div>
                                    <div class="wpem-month"><?php echo esc_html( date_i18n( 'M', strtotime( $start_date ) ) ); ?></div>
                                </div>
                            <?php } 
                            
                            if ($start_date != $end_date && !empty($end_date)) {  ?>
                                <div class="wpem-to-date">
                                    <div class="wpem-date-separator">-</div>
                                    <div class="wpem-date"><?php echo esc_html( date_i18n( 'd', strtotime( $end_date ) ) ); ?></div>
                                    <div class="wpem-month"><?php echo esc_html( date_i18n( 'M', strtotime( $end_date ) ) ); ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="wpem-event-details">
                        <?php do_action('wpem_event_listing_event_detail_start', $post->ID); ?>
                        <div class="wpem-event-title">
                            <h3 class="wpem-heading-text"><?php echo esc_html( get_the_title() ); ?></h3>
                        </div>

                        <div class="wpem-event-date-time">
                            <span class="wpem-event-date-time-text">
                                <?php wpem_display_event_start_date(); ?>
                                <?php
                                if (!empty($start_time)) {
                                    echo ' ' . wp_kses_post(wpem_display_date_time_separator()) .' ';
                                }
									wpem_display_event_start_time(); 
                                if (!empty($end_date) || !empty($end_time)) {
                                ?> - <?php
                                } 
                                if (isset($start_date) && isset($end_date) && $start_date != $end_date) {
                                    wpem_display_event_end_date();
                                }
                                if (!empty($end_date) && !empty($end_time)) {
                                    echo ' ' . wp_kses_post(wpem_display_date_time_separator()) . ' ';
                                }
									wpem_display_event_end_time(); ?>
                            </span>
                        </div>
                        <div class="wpem-event-location">
                            <span class="wpem-event-location-text">
                                <?php
                                if (wpem_get_event_location() == 'Online Event' || wpem_get_event_location() == '') : 
                                    echo esc_attr__('Online Event', 'wp-event-manager');
                                else : 
                                    wpem_display_event_location(false);
                                endif; ?>
                            </span>
                        </div>
                        <?php
                        if (get_option('event_manager_enable_event_types') && wpem_get_event_type()) { ?>
                            <div class="wpem-event-type"><?php wpem_display_event_type(); ?></div>
                        <?php } 
                        do_action('event_already_registered_title');
                        if (wpem_is_event_cancelled()){
                            echo ' <div class="wpem-event-cancel-label"><span class="wpem-event-cancel-label-text">'.esc_html__('Cancelled', 'wp-event-manager').'</span></div>';  
                         } ?>

                        <!-- Show in list View // Hide in Box View -->
                        <?php
                        if (wpem_get_event_ticket_option()) { ?>
                            <div class="wpem-event-ticket-type <?php echo esc_attr(wpem_display_event_ticket_option()); ?>" class="wpem-event-ticket-type-text">
                                <span class="wpem-event-ticket-type-text"><?php wpem_display_event_ticket_option(); ?></span>
                            </div>
                        <?php } ?>
                        <!-- Show in list View // Hide in Box View -->
                        <?php do_action('wpem_event_listing_event_detail_end', $post->ID); ?>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>