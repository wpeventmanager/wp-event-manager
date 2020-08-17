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
                foreach (range('0', '9') as $letter) :
                    if (!isset($organizers_array [$letter]))
                        continue;
                    ?>              
                    <div id="show_<?php echo $letter; ?>"
                         class="show-organizer-info wpem-col-sm-12 wpem-col-md-6 wpem-col-lg-4">
                        <div class="wpem-list-group">
                            <div
                                class="organizer-group-header list-group-item list-group-item-success">
                                <div id="<?php echo $letter; ?>"><?php echo $letter; ?></div>
                            </div>

                            <div class="organizer-name-list">
                                <!-- shows the organizer name with number of event organizer posted -->
                                <?php
                                foreach ($organizers_array [$letter] as $organizer_name) :
                                    
                                    $count = get_event_organizer_count($organizer_id);

                                    if ($count != 0)
                                    {
                                        ?>
                                        <div class="organizer-name">
                                            <a href="<?php echo get_the_permalink($organizer_id) ?>" class="list-group-item list-color" title="<?php _e('Click here, for more info.', 'wp-event-manager'); ?>"><?php echo esc_attr($organizer_name) . ' (' . $count . ')' ?> 
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    else
                                    {
                                        echo '<div class="organizer-name"><a class="list-group-item list-color" href="#">' . esc_attr($organizer_name) . '</a></div>';
                                    }

                                endforeach;
                                ?>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?> <!-- foreach loop - 0 to 9 ends -->

                <?php
                foreach (range('A', 'Z') as $letter) :
                    if (!isset($organizers_array [$letter]))
                        continue;
                    ?>
                    <!-- shows all the letter from which organizer will start -->
                    <div id="show_<?php echo $letter; ?>"
                         class="show-organizer-info wpem-col-md-6 wpem-col-lg-4">
                        <div class="wpem-list-group">
                            <div
                                class="organizer-group-header list-group-item list-group-item-success">
                                <div id="<?php echo $letter; ?>"><?php echo $letter; ?></div>
                            </div>

                            <div class="organizer-name-list">
                                <!-- shows the organizer name with number of event organizer posted -->
                                <?php
                                foreach ($organizers_array [$letter] as $organizer_id => $organizer_name) :
                                    
                                    $count = get_event_organizer_count($organizer_id);

                                    if ($count != 0)
                                    {
                                        ?>
                                        <div class="organizer-list-items">
                                            <a href="<?php echo get_the_permalink($organizer_id) ?>" class="list-group-item list-color" title="<?php _e('Click here, for more info.', 'wp-event-manager'); ?>" ><?php $organizer = get_post($organizer_id); ?><div class="wpem-organizer-logo"><?php display_organizer_logo('', '', $organizer); ?></div><div class="wpem-organizer-name"><?php echo esc_attr($organizer_name) ?></div><div class="wpem-event-organizer-conunt-number"><?php echo esc_attr($count) ?></div></a>
                                        </div>
                                        <?php
                                    }
                                    else
                                    {
                                        echo '<div class="organizer-list-items"><a class="list-group-item list-color" href="#">' . esc_attr($organizer_name) . '</a></div>';
                                    }

                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?> <!-- foreach loop - A to Z ends -->
            </div>
        </div>
        <!-- ends class col-md-12 -->

    <?php else : ?>
        <div class="wpem-alert wpem-alert-info">
            <?php _e( 'You do not have any organizer.', 'wp-event-manager' ); ?>
        </div>
    <?php endif; ?>
    
</div>
<!-- end Organizer Counter -->