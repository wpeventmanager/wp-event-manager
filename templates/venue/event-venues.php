<!-- Organizer Counter -->
<div class="wpem-organizer-connter">

    <?php if(count($venues) > 0) : ?>

    <div class="organizer-related-data-counter">

        <div class="organizer-counter-number-icon">
            <div class="organizer-counter-upper-wrap">
                <div class="organizer-counter-icon-wrap"><i class="wpem-icon-location2"></i></div>
                <div class="organizer-counter-number-wrap"><?php echo count($venues); ?></div>
            </div>
            
            <div class="organizer-counter-bottom-wrap"><?php _e('Venues', 'wp-event-manager'); ?></div>
        </div>


        <div class="available-events-number-icon">
            <a href="<?php echo get_option('siteurl') ?>" class="list-group-item" title="<?php _e('Browse events', 'wp-event-manager'); ?>">
                <div class="organizer-counter-upper-wrap">
                    <div class="organizer-counter-icon-wrap"><i class="wpem-icon-calendar"></i></div>
                    <div class="organizer-counter-number-wrap"><?php echo $countAllEvents; ?></div>
                </div>

                <div class="organizer-counter-bottom-wrap"><?php _e('Available events', 'wp-event-manager'); ?></div>
            </a>
        </div>

    </div>
    <!-- end Organizer Counter -->

    <!-- shows numbers and alphabet -->
    <div class="wpem-main organizer-letters venue-letters">
        <div class="organizer-letters-list">
            <a id="ALL" href="#All"><?php _e('All', 'wp-event-manager'); ?></a>
        </div>

        <?php
        foreach (range('0', '9') as $letter) :
            echo '<div class="organizer-letters-list"><a id="' . $letter . '" href="#' . $letter . '">' . $letter . '</a></div>';
        endforeach;

        foreach (range('A', 'Z') as $letter) :
            echo '<div class="organizer-letters-list"><a id="' . $letter . '" href="#' . $letter . '">' . $letter . '</a></div>';
        endforeach;
        ?>

    </div>

    <!-- shows organizer related data -->
    <div class="wpem-main wpem-row organizer-related-data-wrapper">
        <div class="wpem-col-md-12 organizer-related-info-wrapper">
            <div class="wpem-row">
                <?php
                foreach ($venues_array as $letter => $venues) : ?>
                    <div id="show_<?php echo $letter; ?>"
                         class="show-organizer-info show-venue-info wpem-col-sm-12 wpem-col-md-6 wpem-col-lg-4">
                        <div class="wpem-list-group">
                            <div class="organizer-group-header list-group-item list-group-item-success">
                                <div id="<?php echo $letter; ?>"><?php echo $letter; ?></div>
                            </div>

                            <div class="organizer-name-list">
                                <?php foreach ($venues as $venue_id => $venue_name) :
                                    
                                    $count = get_event_venue_count($venue_id); ?>
                                    
                                    <div class="organizer-list-items">
                                        <a href="<?php echo get_the_permalink($venue_id) ?>" class="list-group-item list-color" title="<?php _e('Click here, for more info.', 'wp-event-manager'); ?>" >
                                            <?php $venue = get_post($venue_id); ?>
                                            <?php if ( $show_thumb && $show_thumb == 'true' ) : ?>
                                                <div class="wpem-organizer-logo"><?php display_venue_logo('', '', $venue); ?></div>
                                            <?php endif; ?>

                                            <div class="wpem-organizer-name"><?php echo esc_attr($venue_name) ?></div>
                                            
                                            <?php if ( $count != 0 && $show_count && $show_count == 'true' ) : ?>
                                                <div class="wpem-event-organizer-conunt-number"><?php echo esc_attr($count) ?></div>
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
                    <?php _e( 'There are not venue.', 'wp-event-manager' ); ?>
                </div>
            </div>
        </div>
        <!-- ends class col-md-12 -->

    <?php else : ?>
        <div class="wpem-alert wpem-alert-info">
            <?php _e( 'There are not organizer.', 'wp-event-manager' ); ?>
        </div>
    <?php endif; ?>
    
</div>
<!-- end Organizer Counter -->