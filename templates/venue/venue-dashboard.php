<?php do_action('event_manager_venue_dashboard_before'); ?>

<div class="wpem-dashboard-main-title wpem-dashboard-main-filter">
    <h3 class="wpem-theme-text"><?php _e('Venue Dashboard','wp-event-manager');?></h3>

    <div class="wpem-d-inline-block wpem-dashboard-i-block-btn">

        <?php do_action('event_manager_venue_dashboard_button_action_start'); ?>

        <?php $submit_venue = get_option('event_manager_submit_venue_form_page_id');
        if(!empty($submit_venue )) : ?>
            <a class="wpem-dashboard-header-btn wpem-dashboard-header-add-btn" title="<?php _e('Add venue','wp-event-manager');?>" href="<?php echo get_permalink($submit_venue);?>"><i class="wpem-icon-plus"></i></a>
        <?php endif; ?>

        <?php do_action('event_manager_venue_dashboard_button_action_end'); ?>

    </div>
</div>

<div id="event-manager-event-dashboard">
    <div class="wpem-responsive-table-block">
        <table class="wpem-main wpem-responsive-table-wrapper">
            <thead>
                <tr>
                    <?php foreach ($venue_dashboard_columns as $key => $column) : ?>
                        <th class="wpem-heading-text <?php echo esc_attr($key); ?>"><?php echo esc_html($column); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!$venues) : ?>
                    <tr>
                        <td colspan="4"><?php _e('There are no venues.', 'wp-event-manager'); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($venues as $venue) : ?>
                        <tr>

                            <?php foreach ($venue_dashboard_columns as $key => $column) : ?>
                                <td data-title="<?php echo esc_html($column); ?>" class="<?php echo esc_attr($key); ?>">                                    
                                    <?php if ('venue_name' === $key) : ?>
                                        <div class="wpem-venue-logo"><?php display_venue_logo('', '', $venue); ?></div>
                                        <a href="<?php echo get_permalink($venue->ID); ?>"><?php echo esc_html($venue->post_title); ?></a>

                                    <?php elseif ('venue_details' === $key ) :?>
                                        <div class="wpem-venue-social-links">
                                            <div class="wpem-venue-social-lists">
                                            
                                                <?php do_action('single_event_listing_venue_social_start', $venue->ID);

                                                $venue_website  = get_venue_website($venue);
                                                $venue_facebook = get_venue_facebook($venue);
                                                $venue_instagram = get_venue_instagram($venue);
                                                $venue_twitter  = get_venue_twitter($venue);
                                                $venue_youtube  = get_venue_youtube($venue);
                                                
                                                if (!empty($venue_website))
                                                {
                                                    ?>
                                                    <div class="wpem-social-icon wpem-weblink">
                                                        <a href="<?php echo esc_url($venue_website); ?>" target="_blank" title="<?php _e('Get Connect on Website', 'wp-event-manager'); ?>"><?php _e('Website', 'wp-event-manager'); ?></a>
                                                    </div>
                                                    <?php
                                                }

                                                if (!empty($venue_facebook))
                                                {
                                                    ?> 
                                                    <div class="wpem-social-icon wpem-facebook">
                                                        <a href="<?php echo esc_url($venue_facebook); ?>" target="_blank" title="<?php _e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php _e('Facebook', 'wp-event-manager'); ?></a>
                                                    </div>
                                                    <?php
                                                }

                                                if (!empty($venue_instagram))
                                                {
                                                    ?> 
                                                    <div class="wpem-social-icon wpem-instagram">
                                                        <a href="<?php echo esc_url($venue_instagram); ?>" target="_blank" title="<?php _e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php _e('Instagram', 'wp-event-manager'); ?></a>
                                                    </div>
                                                    <?php
                                                }

                                                if (!empty($venue_twitter))
                                                {
                                                    ?>
                                                    <div class="wpem-social-icon wpem-twitter">
                                                        <a href="<?php echo esc_url($venue_twitter); ?>" target="_blank" title="<?php _e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php _e('Twitter', 'wp-event-manager'); ?></a>
                                                    </div>
                                                    <?php
                                                }

                                                if (!empty($venue_youtube))
                                                {
                                                    ?>
                                                    <div class="wpem-social-icon wpem-youtube">
                                                        <a href="<?php echo esc_url($venue_youtube); ?>" target="_blank" title="<?php _e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php _e('Youtube', 'wp-event-manager'); ?></a>
                                                    </div>
                                                <?php } ?>

                                                <?php do_action('single_event_listing_venue_single_social_end', $venue->ID); ?>
                                            </div>
                                        </div>
                                
                                    <?php elseif ('venue_events' === $key) : ?>

                                        <?php
                                        // echo get_event_venue_count($venue->ID);
                                        $events = get_event_by_venue_id($venue->ID);
                                        ?>
                                        <div  class="event-venue-count wpem-tooltip wpem-tooltip-bottom"><a href="javaScript:void(0)"><?php echo sizeof($events); ?></a>
                                            <?php if(!empty($events)) : ?>
                                                <span class="venue-events-list wpem-tooltiptext">
                                                    <?php foreach ($events as $event) : ?>
                                                        <span><a href="<?php echo get_the_permalink($event->ID);?>"><?php echo get_the_title($event->ID);?></a></span>
                                                    <?php endforeach; ?>
                                                </span>
                                            <?php else: ?> 
                                            <span class="venue-events-list wpem-tooltiptext"><span><a href="#"><?php  _e('There is no event.','wp-event-manager'); ?></a></span></span>
                                            <?php endif; ?>
                                        </div>

                                        <?php elseif ('venue_action' === $key) : ?>
                                            <div class="wpem-dboard-event-action">
                                                <?php
                                                $actions = array();
                                                switch ($venue->post_status)
                                                {
                                                    case 'publish' :
                                                        $actions ['edit']      = array(
                                                            'label' => __('Edit', 'wp-event-manager'),
                                                            'nonce' => false
                                                        );
                                                        $actions ['duplicate'] = array(
                                                            'label' => __('Duplicate', 'wp-event-manager'),
                                                            'nonce' => true
                                                        );
                                                        break;
                                                }
                                                $actions ['delete'] = array(
                                                    'label' => __('Delete', 'wp-event-manager'),
                                                    'nonce' => true
                                                );
                                                $actions            = apply_filters('event_manager_my_venue_actions', $actions, $venue);
                                                foreach ($actions as $action => $value)
                                                {
                                                    $action_url = add_query_arg(array(
                                                        'action'   => $action,
                                                        'venue_id' => $venue->ID
                                                    ));
                                                    if ($value['nonce'])
                                                    {
                                                        $action_url = wp_nonce_url($action_url, 'event_manager_my_venue_actions');
                                                    }
                                                    echo '<div class="wpem-dboard-event-act-btn"><a href="' . esc_url($action_url) . '" class="event-dashboard-action-' . esc_attr($action) . '" title="' . esc_html($value ['label']) . '" >' . esc_html($value ['label']) . '</a></div>';
                                                }
                                                ?>
                                            </div>

                                        <?php else : ?>
                                            <?php do_action('event_manager_venue_dashboard_column_' . $key, $venue); ?>
                                        <?php endif; ?>
                                </td>
                            <?php endforeach; ?>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php get_event_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>


</div>
<?php do_action('event_manager_venue_dashboard_after'); ?>
