<!-- venue Counter -->
<div class="wpem-venue-connter">

    <?php if(count($venues) > 0) : ?>

        <div class="venue-related-data-counter">

            <div class="venue-counter-number-icon">
                <div class="venue-counter-upper-wrap">
                    <div class="venue-counter-icon-wrap"><i class="wpem-icon-location2"></i></div>
                    <div class="venue-counter-number-wrap"><?php echo count($venues); ?></div>
                </div>
                
                <div class="venue-counter-bottom-wrap"><?php _e('Venues', 'wp-event-manager'); ?></div>
            </div>


            <div class="wpem-available-events-number-icon">
                <a href="<?php echo get_the_permalink(get_option('event_manager_events_page_id')); ?>" class="wpem-list-group-item" title="<?php _e('Browse events', 'wp-event-manager'); ?>">
                    <div class="venue-counter-upper-wrap">
                        <div class="venue-counter-icon-wrap"><i class="wpem-icon-calendar"></i></div>
                        <div class="venue-counter-number-wrap"><?php echo $countAllEvents; ?></div>
                    </div>

                    <div class="venue-counter-bottom-wrap"><?php _e('Available events', 'wp-event-manager'); ?></div>
                </a>
            </div>

        </div>
        <!-- end venue Counter -->

        <!-- shows numbers and alphabet -->
        <div class="wpem-main venue-letters venue-letters">
            <div class="venue-letters-list">
                <a id="ALL" href="#All"><?php _e('All', 'wp-event-manager'); ?></a>
            </div>

            <?php
            foreach (range('0', '9') as $letter) :
                echo '<div class="venue-letters-list"><a id="' . $letter . '" href="#' . $letter . '">' . $letter . '</a></div>';
            endforeach;

            foreach (range('A', 'Z') as $letter) :
                echo '<div class="venue-letters-list"><a id="' . $letter . '" href="#' . $letter . '">' . $letter . '</a></div>';
            endforeach;
            ?>

        </div>

        <!-- shows venue related data -->
        <div class="wpem-main wpem-row venue-related-data-wrapper">
            <div class="wpem-col-md-12 venue-related-info-wrapper">
                <div class="wpem-row">
                    <?php
                    foreach ($venues_array as $letter => $venues) : ?>
                        <div id="show_<?php echo $letter; ?>" class="show-venue-info show-venue-info wpem-col-sm-12 wpem-col-md-6 wpem-col-lg-4">
                            <div class="wpem-list-group">
                                <div class="venue-group-header wpem-list-group-item wpem-list-group-item-success">
                                    <div><?php echo sprintf( __( '%s', 'wp-event-manager' ), $letter ); ?></div>
                                </div>

                                <div class="venue-name-list">
                                    <?php foreach ($venues as $venue_id => $venue_name) :
                                        
                                        $count = get_event_venue_count($venue_id); ?>
                                        
                                        <div class="venue-list-items">
                                            <a href="<?php echo get_the_permalink($venue_id) ?>" class="wpem-list-group-item list-color" title="<?php _e('Click here, for more info.', 'wp-event-manager'); ?>" >
                                                <?php $venue = get_post($venue_id); ?>
                                                <?php if ( $show_thumb && $show_thumb == 'true' ) : ?>
                                                    <div class="wpem-venue-logo"><?php display_venue_logo('', '', $venue); ?></div>
                                                <?php endif; ?>

                                                <div class="wpem-venue-name"><?php echo esc_attr($venue_name) ?></div>
                                                
                                                <?php if ( $count != 0 && $show_count && $show_count == 'true' ) : ?>
                                                    <div class="wpem-event-venue-conunt-number"><?php echo esc_attr($count) ?></div>
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
                        <?php _e( 'There are no venues.', 'wp-event-manager' ); ?>
                    </div>
                </div>
            </div>
            <!-- ends class col-md-12 -->
        </div>

    <?php else : ?>
        <div class="wpem-alert wpem-alert-info">
            <?php _e( 'There are no venues.', 'wp-event-manager' ); ?>
        </div>
    <?php endif; ?>
    
</div>
<!-- end venue Counter -->