<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<!-- venue Counter -->
<div class="wpem-venue-connter">
    <?php if (count($wpem_venues) > 0) : ?>
        <div class="venue-related-data-counter">
            <div class="venue-counter-number-icon">
                <div class="venue-counter-upper-wrap">
                    <div class="venue-counter-icon-wrap"><i class="wpem-icon-location2"></i></div>
                    <div class="venue-counter-number-wrap"><?php echo esc_attr(count($wpem_venues)); ?></div>
                </div>
                <div class="venue-counter-bottom-wrap"><?php esc_html_e('Venues', 'wp-event-manager'); ?></div>
            </div>
            <div class="wpem-available-events-number-icon">
                <a href="<?php echo esc_url(get_the_permalink(get_option('event_manager_events_page_id'))); ?>" class="wpem-list-group-item" title="<?php esc_attr_e('Browse events', 'wp-event-manager'); ?>">
                    <div class="venue-counter-upper-wrap">
                        <div class="venue-counter-icon-wrap"><i class="wpem-icon-calendar"></i></div>
                        <div class="venue-counter-number-wrap"><?php echo esc_attr($wpem_countAllEvents); ?></div>
                    </div>
                    <div class="venue-counter-bottom-wrap"><?php esc_html_e('Available events', 'wp-event-manager'); ?></div>
                </a>
            </div>
        </div>
        <!-- end venue Counter -->

        <!-- shows numbers and alphabet -->
        <div class="wpem-main venue-letters venue-letters">
            <div class="venue-letters-list">
                <a id="ALL" href="#All"><?php esc_html_e('All', 'wp-event-manager'); ?></a>
            </div>
            <?php
            foreach (range('0', '9') as $wpem_letter) :?>
                <div class="venue-letters-list"><a id="<?php echo esc_attr($wpem_letter);?>" href="#<?php echo esc_attr($wpem_letter);?>"><?php echo esc_attr($wpem_letter);?></a></div> <?php
            endforeach;
            foreach (range('A', 'Z') as $wpem_letter) : ?>
                <div class="venue-letters-list"><a id="<?php echo esc_attr($wpem_letter);?>" href="#<?php echo esc_attr($wpem_letter);?>"><?php echo esc_attr($wpem_letter);?></a></div>
            <?php endforeach; ?>
        </div>

        <!-- shows venue related data -->
        <div class="wpem-main wpem-row venue-related-data-wrapper">
            <div class="wpem-col-md-12 venue-related-info-wrapper">
                <div class="wpem-row">
                    <?php
                    foreach ($wpem_venues_array as $wpem_letter => $wpem_venues) : ?>
                        <div id="show_<?php echo esc_attr($wpem_letter); ?>" class="show-venue-info show-venue-info wpem-col-sm-12 wpem-col-md-6 wpem-col-lg-4">
                            <div class="wpem-list-group">
                                <div class="venue-group-header wpem-list-group-item wpem-list-group-item-success">
                                    <div><?php echo esc_attr($wpem_letter); ?></div>
                                </div>
                                <div class="venue-name-list">
                                    <?php foreach ($wpem_venues as $wpem_venue_id => $wpem_venue_name) :
                                        $wpem_count = wpem_get_event_venue_count($wpem_venue_id); ?>
                                        <div class="venue-list-items">
                                            <a href="<?php echo esc_url(get_the_permalink($wpem_venue_id)) ?>" class="wpem-list-group-item list-color" title="<?php esc_attr_e('Click here, for more info.', 'wp-event-manager'); ?>">
                                                <?php $wpem_venue = get_post($wpem_venue_id); ?>
                                                <?php if ($show_thumb && $show_thumb == 'true') : ?>
                                                    <div class="wpem-venue-logo"><?php wpem_display_venue_logo('', '', $wpem_venue); ?></div>
                                                <?php endif; ?>

                                                <div class="wpem-venue-name"><?php echo esc_attr($wpem_venue_name); ?></div>

                                                <?php if ($wpem_count != 0 && $show_count && $show_count == 'true') : ?>
                                                    <div class="wpem-event-venue-conunt-number"><?php echo esc_attr($wpem_count); ?></div>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="no-venue wpem-d-none">
                    <div class="wpem-alert wpem-alert-info">
                        <?php esc_html_e('There are no venues.', 'wp-event-manager'); ?>
                    </div>
                </div>
            </div>
            <!-- ends class col-md-12 -->
        </div>
    <?php else : ?>
        <div class="wpem-alert wpem-alert-info">
            <?php esc_html_e('There are no venues.', 'wp-event-manager'); ?>
        </div>
    <?php endif; ?>
</div>
<!-- end venue Counter -->