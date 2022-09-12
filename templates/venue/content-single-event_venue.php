<?php $venue = get_post($venue_id); ?>

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
                    <?php $content = apply_filters('wpem_the_content',$venue->post_content);
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