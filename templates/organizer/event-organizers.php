<!-- Organizer Counter -->
<div class="wpem-organizer-connter">

    <?php if(count($organizers) > 0) : ?>

        <div class="organizer-related-data-counter">

            <div class="organizer-counter-number-icon">
                <div class="organizer-counter-upper-wrap">
                    <div class="organizer-counter-icon-wrap"><i class="wpem-icon-users"></i></div>
                    <div class="organizer-counter-number-wrap"><?php echo count($organizers); ?></div>
                </div>
                
                <div class="organizer-counter-bottom-wrap"><?php _e('Organizers', 'wp-event-manager'); ?></div>
            </div>


            <div class="wpem-available-events-number-icon">
                <a href="<?php echo get_the_permalink(get_option('event_manager_events_page_id')); ?>" class="wpem-list-group-item" title="<?php _e('Browse events', 'wp-event-manager'); ?>">
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
        <div class="wpem-main organizer-letters">
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
                    foreach ($organizers_array as $letter => $organizers) : ?>
                        <div id="show_<?php echo $letter; ?>" class="show-organizer-info wpem-col-sm-12 wpem-col-md-6 wpem-col-lg-4">
                            <div class="wpem-list-group">
                                <div class="organizer-group-header wpem-list-group-item wpem-list-group-item-success">
                                    <div><?php echo sprintf( __( '%s', 'wp-event-manager' ), $letter ); ?></div>
                                </div>

                                <div class="organizer-name-list">
                                    <?php foreach ($organizers as $organizer_id => $organizer_name) :
                                        
                                        $count = get_event_organizer_count($organizer_id); ?>
                                        
                                        <div class="organizer-list-items">
                                            <a href="<?php echo get_the_permalink($organizer_id) ?>" class="wpem-list-group-item list-color" title="<?php _e('Click here, for more info.', 'wp-event-manager'); ?>" >
                                                <?php $organizer = get_post($organizer_id); ?>
                                                <?php if ( $show_thumb && $show_thumb == 'true' ) : ?>
                                                    <div class="wpem-organizer-logo"><?php display_organizer_logo('', '', $organizer); ?></div>
                                                <?php endif; ?>

                                                <div class="wpem-organizer-name"><?php echo esc_attr($organizer_name) ?></div>
                                                
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
                <div class="no-organizer wpem-d-none">
                    <div class="wpem-alert wpem-alert-info">
                        <?php _e( 'There are no organizers.', 'wp-event-manager' ); ?>
                    </div>
                </div>
            </div>
            <!-- ends class col-md-12 -->
        </div>

    <?php else : ?>
        <div class="wpem-alert wpem-alert-info">
            <?php _e( 'There are no organizers.', 'wp-event-manager' ); ?>
        </div>
    <?php endif; ?>
    
</div>
<!-- end Organizer Counter -->