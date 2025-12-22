<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
do_action('event_manager_venue_dashboard_before'); ?>
<!-- Venue dashboard title section start-->
<div class="wpem-dashboard-main-title wpem-dashboard-main-filter">
    <h3 class="wpem-theme-text"><?php esc_html_e('Venue Dashboard', 'wp-event-manager'); ?></h3>

    <div class="wpem-d-inline-block wpem-dashboard-i-block-btn">

        <?php do_action('event_manager_venue_dashboard_button_action_start');
        $submit_venue = get_option('event_manager_submit_venue_form_page_id');
        if (!empty($submit_venue)) : ?>
            <a class="wpem-dashboard-header-btn wpem-dashboard-header-add-btn" title="<?php esc_html_e('Add venue', 'wp-event-manager'); ?>" href="<?php echo esc_url(get_permalink($submit_venue)); ?>"><i class="wpem-icon-plus"></i></a>
        <?php endif;
        do_action('event_manager_venue_dashboard_button_action_end'); ?>

    </div>
</div>
<!-- Venue dashboard title section end-->

<!-- Venue list section start-->
<div id="event-manager-event-dashboard">
    <div class="wpem-responsive-table-block">
        <table class="wpem-main wpem-responsive-table-wrapper">
            <thead>
                <tr>
                    <?php foreach ($wpem_venue_dashboard_columns as $wpem_key => $wpem_column) : ?>
                        <th class="wpem-heading-text <?php echo esc_attr($wpem_key); ?>"><?php echo esc_html($wpem_column); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($wpem_venues)) : ?>
                    <tr>
                        <td colspan="4" class="wpem_data_td_empty"><?php esc_html_e('There are no venues.', 'wp-event-manager'); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($wpem_venues as $wpem_venue) : ?>
                        <tr>
                            <?php foreach ($wpem_venue_dashboard_columns as $wpem_key => $wpem_column) : ?>
                                <td data-title="<?php echo esc_html($wpem_column); ?>" class="<?php echo esc_attr($wpem_key); ?>">
                                    <?php if ('venue_name' === $wpem_key) : ?>
                                        <div class="wpem-venue-logo"><?php wpem_display_venue_logo('', '', $wpem_venue); ?></div>
                                        <a href="<?php echo esc_url(get_permalink($wpem_venue->ID)); ?>"><?php echo esc_html($wpem_venue->post_title); ?></a>
                                    <?php elseif ('venue_details' === $wpem_key) : 
                                        do_action('single_event_listing_venue_social_start', $wpem_venue->ID);

                                        //get disable venue fields
                                        $wpem_venue_fields = wpem_get_hidden_form_fields( 'event_manager_submit_venue_form_fields', 'venue');
                                        $wpem_venue_website  = !in_array('venue_website', $wpem_venue_fields)?wpem_get_venue_website($wpem_venue):'';
                                        $wpem_venue_facebook = !in_array('venue_facebook', $wpem_venue_fields)?wpem_get_venue_facebook($wpem_venue):'';
                                        $wpem_venue_instagram = !in_array('venue_instagram', $wpem_venue_fields)?wpem_get_venue_instagram($wpem_venue):'';
                                        $wpem_venue_twitter  = !in_array('venue_twitter', $wpem_venue_fields)?wpem_get_venue_twitter($wpem_venue):'';
                                        $wpem_venue_youtube  = !in_array('venue_youtube', $wpem_venue_fields)?wpem_get_venue_youtube($wpem_venue):'';

                                        if (empty($wpem_venue_website) && empty($wpem_venue_facebook) && empty($wpem_venue_instagram) && empty($wpem_venue_twitter) && empty($wpem_venue_youtube)) {
                                            ?><span class="no-social-links">-</span><?php
                                        } else { ?>
                                            <div class="wpem-venue-social-links">
                                                <div class="wpem-venue-social-lists">
                                                    <?php if (!empty($wpem_venue_website)) {  ?>
                                                        <div class="wpem-social-icon wpem-weblink">
                                                            <a href="<?php echo esc_url($wpem_venue_website); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>"><?php esc_html_e('Website', 'wp-event-manager'); ?></a>
                                                        </div>
                                                    <?php }
                                                    if (!empty($wpem_venue_facebook)) { ?>
                                                        <div class="wpem-social-icon wpem-facebook">
                                                            <a href="<?php echo esc_url($wpem_venue_facebook); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php esc_html_e('Facebook', 'wp-event-manager'); ?></a>
                                                        </div>
                                                    <?php }
                                                    if (!empty($wpem_venue_instagram)) { ?>
                                                        <div class="wpem-social-icon wpem-instagram">
                                                            <a href="<?php echo esc_url($wpem_venue_instagram); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a>
                                                        </div>
                                                    <?php }
                                                    if (!empty($wpem_venue_twitter)) { ?>
                                                        <div class="wpem-social-icon wpem-twitter">
                                                            <a href="<?php echo esc_url($wpem_venue_twitter); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a>
                                                        </div>
                                                    <?php }
                                                    if (!empty($wpem_venue_youtube)) { ?>
                                                        <div class="wpem-social-icon wpem-youtube">
                                                            <a href="<?php echo esc_url($wpem_venue_youtube); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a>
                                                        </div>
                                                    <?php } ?>

                                                    <?php do_action('single_event_listing_venue_single_social_end', $wpem_venue->ID); ?>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    <?php elseif ('venue_events' === $wpem_key) : 

                                        $wpem_venue_events = wpem_get_event_by_venue_id($wpem_venue->ID); ?>
                                        <div class="event-venue-count wpem-tooltip wpem-tooltip-bottom"><a href="javaScript:void(0)"><?php echo esc_html(count($wpem_venue_events)); ?></a>
                                            <?php if (!empty($wpem_venue_events)) : ?>
                                                <span class="venue-events-list wpem-tooltiptext">
                                                    <?php foreach ($wpem_venue_events as $wpem_venue_event) : ?>
                                                        <span><a href="<?php echo esc_url(get_the_permalink($wpem_venue_event->ID)); ?>"><?php  echo wp_kses_post(get_the_title($wpem_venue_event->ID)); ?></a></span>
                                                    <?php endforeach; ?>
                                                </span>
                                            <?php else : ?>
                                                <span class="venue-events-list wpem-tooltiptext"><span><a href="#"><?php esc_html_e('There is no event.', 'wp-event-manager'); ?></a></span></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ('venue_action' === $wpem_key) : ?>
                                        <div class="wpem-dboard-event-action">
                                            <?php
                                            $wpem_actions = array();
                                            switch ($wpem_venue->post_status) {
                                                case 'publish':
                                                    $wpem_actions['edit']      = array(
                                                        'label' => __('Edit', 'wp-event-manager'),
                                                        'nonce' => true
                                                    );
                                                    $wpem_actions['duplicate'] = array(
                                                        'label' => __('Duplicate', 'wp-event-manager'),
                                                        'nonce' => true
                                                    );
                                                    break;
                                            }
                                            $wpem_actions['delete'] = array(
                                                'label' => __('Delete', 'wp-event-manager'),
                                                'nonce' => true
                                            );
                                            $wpem_actions            = apply_filters('event_manager_my_venue_actions', $wpem_actions, $wpem_venue);
                                            foreach ($wpem_actions as $action => $wpem_value) {
                                                $wpem_action_url = add_query_arg(array(
                                                    'action'   => $action,
                                                    'venue_id' => $wpem_venue->ID
                                                ));
                                                if (sanitize_key($wpem_value['nonce'])) {
                                                    $wpem_action_url = wp_nonce_url($wpem_action_url, 'event_manager_my_venue_actions');
                                                }
                                                echo wp_kses_post('<div class="wpem-dboard-event-act-btn"><a href="' . esc_url($wpem_action_url) . '" class="event-dashboard-action-' . esc_attr($action) . '" title="' . esc_html($wpem_value['label']) . '" >' . esc_html($wpem_value['label']) . '</a></div>');
                                            }
                                            ?>
                                        </div>
                                    <?php else : ?>
                                        <?php do_action('event_manager_venue_dashboard_column_' . $wpem_key, $wpem_venue); ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php wpem_get_event_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>
</div>
<!-- Venue list section end-->
<?php do_action('event_manager_venue_dashboard_after'); ?>