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
    <div class="wpem-col-md-3 organizer-related-data-counter">
        <div class="wpem-related-data-well">
            <div class="wpem-list-group">
                <div class="list-group-item-box">
                    <div href="#" class="list-group-item">
                        <div class="list-group-title"><?php _e('Organizers', 'wp-event-manager'); ?></div>
                        <div class="list-group-content">
                            <h3 class="h-height"><?php echo count($orgnizers); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="list-group-item-box">
                    <a href="<?php echo get_option('siteurl') ?>"
                       class="list-group-item"
                       title="<?php _e('Browse events', 'wp-event-manager'); ?>">
                        <div class="list-group-title"><?php _e('Available events', 'wp-event-manager'); ?></div>
                        <div class="list-group-content">
                            <h3 class="h-height"><?php echo $countAllEvents; ?></h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- end class col-md-3 -->

    <div class="wpem-col-md-9 organizer-related-info-wrapper">
        <div class="wpem-row">
            <?php
            foreach (range('0', '9') as $letter) :
                if (!isset($orgnizers_array [$letter]))
                    continue;
                ?>				
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
                            foreach ($orgnizers_array [$letter] as $organizer_name) :
                                
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
                if (!isset($orgnizers_array [$letter]))
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
                            foreach ($orgnizers_array [$letter] as $organizer_id => $organizer_name) :
                                
                                $count = get_event_organizer_count($organizer_id);

                                if ($count != 0)
                                {
                                    ?>
                                    <div class="organizer-name">
                                        <a href="<?php echo get_the_permalink($organizer_id) ?>" class="list-group-item list-color" title="<?php _e('Click here, for more info.', 'wp-event-manager'); ?>" ><?php echo esc_attr($organizer_name) . ' (' . $count . ')' ?></a>
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
            <?php endforeach; ?> <!-- foreach loop - A to Z ends -->
        </div>
    </div>
    <!-- ends class col-md-9 -->
</div>
<!-- end class col-md-12-->

