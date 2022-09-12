<?php
/**
 * Single event venue list information box
 *
 * @since  3.1.32
 */
?>
<?php if (has_event_venue_ids($event_id) && !is_event_online($event_id)) : ?>
  
    <?php $event_content_toggle = apply_filters('event_manager_event_content_toggle', true);
    $event_content_toggle_class = $event_content_toggle ? 'wpem-listing-accordion' : 'wpem-event-venue-info-title';?>

    <div class="<?php echo esc_attr($event_content_toggle_class); ?> active">
        <h3 class="wpem-heading-text"><?php _e('Venue', 'wp-event-manager'); ?></h3>
    </div>

    <div class="wpem-venue-wrapper wpem-listing-accordion-panel active" style="display: block;">

        <?php do_action('single_event_listing_venue_start'); 
        $venue_id = get_event_venue_ids($event_id); 
        $venue = get_post($venue_id); ?>
        <div class="wpem-single-venue-profile-wrapper" id="wpem_venue_profile">
            <div class="wpem-venue-profile">
                <?php do_action('single_event_listing_venue_start'); ?>
                <div class="wpem-row">
                    <div class="wpem-col-md-3">
                        <div class="wpem-venue-logo-wrapper">
                            <div class="wpem-venue-logo">
                                <a><?php display_venue_logo('', '', $venue); ?></a>
                            </div>
                            <?php /** <div class="wpem-venue-logo-title wpem-heading-text"><a><span><?php echo esc_attr($venue_name); ?></span></a></div> */ ?>
                        </div>
                    </div>
                    <div class="wpem-col-md-9 wpem-col-sm-12">
                        <div class="wpem-venue-infomation-wrapper">
                            <div class="wpem-venue-name wpem-heading-text">
                                <span><?php echo esc_attr($venue->post_title); ?></span>
                            </div>
                            <div class="wpem-venue-description">
                            <?php 
                            $content = apply_filters('wpem_the_content',$venue->post_content);
                            echo wp_kses_post( $content );?>
                        </div>
                            <div class="wpem-venue-social-links">
                                <div class="wpem-venue-social-lists">
                                    <?php do_action('single_event_listing_venue_social_start'); ?>
                                    <?php
                                    //get disable venue fields
                                    $venue_fields = get_hidden_form_fields( 'event_manager_submit_venue_form_fields', 'venue');

                                    $venue_website  = !in_array('venue_website', $venue_fields)?get_venue_website($venue):'';
                                    $venue_facebook = !in_array('venue_facebook', $venue_fields)?get_venue_facebook($venue):'';
                                    $venue_instagram = !in_array('venue_instagram', $venue_fields)?get_venue_instagram($venue):'';
                                    $venue_twitter  = !in_array('venue_twitter', $venue_fields)?get_venue_twitter($venue):'';
                                    $venue_youtube  = !in_array('venue_youtube', $venue_fields)?get_venue_youtube($venue):'';
                                    ?>
                                    <?php
                                    if (!empty($venue_website)) {
                                    ?>
                                        <div class="wpem-social-icon wpem-weblink">
                                            <a href="<?php echo esc_url($venue_website); ?>" target="_blank" title="<?php _e('Get Connect on Website', 'wp-event-manager'); ?>"><?php _e('Website', 'wp-event-manager'); ?></a>
                                        </div>
                                    <?php
                                    }

                                    if (!empty($venue_facebook)) {
                                    ?>
                                        <div class="wpem-social-icon wpem-facebook">
                                            <a href="<?php echo esc_url($venue_facebook); ?>" target="_blank" title="<?php _e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php _e('Facebook', 'wp-event-manager'); ?></a>
                                        </div>
                                    <?php
                                    }

                                    if (!empty($venue_instagram)) {
                                    ?>
                                        <div class="wpem-social-icon wpem-instagram">
                                            <a href="<?php echo esc_url($venue_instagram); ?>" target="_blank" title="<?php _e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php _e('Instagram', 'wp-event-manager'); ?></a>
                                        </div>
                                    <?php
                                    }

                                    if (!empty($venue_twitter)) {
                                    ?>
                                        <div class="wpem-social-icon wpem-twitter">
                                            <a href="<?php echo esc_url($venue_twitter); ?>" target="_blank" title="<?php _e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php _e('Twitter', 'wp-event-manager'); ?></a>
                                        </div>
                                    <?php
                                    }
                                    if (!empty($venue_youtube)) {
                                    ?>
                                        <div class="wpem-social-icon wpem-youtube">
                                            <a href="<?php echo esc_url($venue_youtube); ?>" target="_blank" title="<?php _e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php _e('Youtube', 'wp-event-manager'); ?></a>
                                        </div>
                                    <?php } ?>

                                    <?php do_action('single_event_listing_venue_single_social_end', $venue_id); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wpem-venue-contact-actions">
                    <?php do_action('single_event_listing_venue_action_start', $venue_id); ?>

                    <?php do_action('single_event_listing_venue_action_end', $venue_id); ?>
                </div>
                <?php do_action('single_event_listing_venue_end'); ?>
            </div>
        </div>
    </div>
<?php elseif (get_event_venue_name($event_id) != '' && !is_event_online($event_id)) : ?>
    <div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
        <div class="wpem-row">
            <div class="wpem-col-md-12">
                <div class="wpem-venue-profile-wrapper" id="wpem_venue_profile">

                    <?php $event_content_toggle = apply_filters('event_manager_event_content_toggle', true);
                    $event_content_toggle_class = $event_content_toggle ? 'wpem-listing-accordion' : 'wpem-event-venue-info-title';
                    ?>

                    <div class="<?php echo esc_attr($event_content_toggle_class); ?> active">
                        <h3 class="wpem-heading-text"><?php _e('Venue', 'wp-event-manager'); ?></h3>
                        <?php if ($event_content_toggle) : ?>
                            <i class="wpem-icon-minus"></i><i class="wpem-icon-plus"></i>
                        <?php endif; ?>
                    </div>

                    <div class="wpem-venue-wrapper wpem-listing-accordion-panel active" style="display: block;">
                        <div class="wpem-venue-profile">

                            <?php do_action('single_event_listing_venue_start'); ?>

                            <div class="wpem-venue-inner-wrapper">
                                <div class="wpem-row">

                                    <div class="wpem-col-md-12 wpem-col-sm-12">
                                        <div class="wpem-venue-name wpem-heading-text">
                                            <?php display_event_venue_name($event_id); ?></span></a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php do_action('single_event_listing_venue_end'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>