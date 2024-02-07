<?php
/**
 * Single view organizer information box
 *
 * Hooked into single_event_listing_start priority 30
 *
 * @since  3.1.6
 */
if (has_event_venue_ids() && !is_event_online()) : ?>
    <div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
        <div class="wpem-row">
            <div class="wpem-col-md-12">
                <div class="wpem-venue-profile-wrapper" id="wpem_venue_profile">
                    <?php $event_content_toggle = apply_filters('event_manager_event_content_toggle', true);
                    $event_content_toggle_class = $event_content_toggle ? 'wpem-listing-accordion' : 'wpem-event-venue-info-title'; ?>

                    <div class="<?php echo esc_attr($event_content_toggle_class); ?> active">
                        <h3 class="wpem-heading-text"><?php esc_html_e('Venue', 'wp-event-manager'); ?></h3>
                        <?php if ($event_content_toggle) : ?>
                            <i class="wpem-icon-minus"></i><i class="wpem-icon-plus"></i>
                        <?php endif; ?>
                    </div>

                    <div class="wpem-venue-wrapper wpem-listing-accordion-panel active" style="display: block;">
                        <div class="wpem-venue-profile">
                            <?php do_action('single_event_listing_venue_start'); ?>

                            <?php $venue_id = get_event_venue_ids(); ?>

                            <div class="wpem-venue-inner-wrapper">
                                <div class="wpem-row">
                                    <div class="wpem-col-md-3 wpem-col-sm-12">
                                        <div class="wpem-venue-logo-wrapper">
                                            <div class="wpem-venue-logo">
                                                <a href="<?php echo esc_url(get_the_permalink($venue_id)); ?>">
                                                    <?php display_venue_logo('', '', $venue_id); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wpem-col-md-9 wpem-col-sm-12">
                                        <div class="wpem-venue-name wpem-heading-text">
                                            <a href="<?php echo esc_attr(get_the_permalink($venue_id)); ?>"><span><?php display_event_venue_name('', '', true, $venue_id); ?></span></a>
                                        </div>
                                        <?php do_action('single_event_listing_venue_description_before', $venue_id);
                                         $venue = get_post($venue_id);  ?>
                                        <div class="wpem-venue-description"><?php
                                            $venue_content = get_post( $venue_id );
                                            $content = apply_filters('wpem_the_content',$venue_content->post_content);
                                            echo wp_kses_post( $content ); ?>
                                        </div>

                                         <?php do_action('single_event_listing_venue_description_after', $venue_id); ?>

                                        <div class="wpem-venue-social-links">
                                            <div class="wpem-venue-social-lists">

                                                <?php do_action('single_event_listing_venue_social_start', $venue_id); 
                                                
                                                 //get disable venue fields
                                                 $venue_fields = get_hidden_form_fields( 'event_manager_submit_venue_form_fields', 'venue');

                                                 $venue_website  = !in_array('venue_website', $venue_fields)?get_venue_website($venue_id):'';
                                                 $venue_facebook = !in_array('venue_facebook', $venue_fields)?get_venue_facebook($venue_id):'';
                                                 $venue_instagram = !in_array('venue_instagram', $venue_fields)?get_venue_instagram($venue_id):'';
                                                 $venue_twitter  = !in_array('venue_twitter', $venue_fields)?get_venue_twitter($venue_id):'';
                                                 $venue_youtube  = !in_array('venue_youtube', $venue_fields)?get_venue_youtube($venue_id):'';

                                                if (!empty($venue_website)) {  ?>
                                                    <div class="wpem-social-icon wpem-weblink">
                                                        <a href="<?php echo esc_url($venue_website); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>"><?php esc_html_e('Website', 'wp-event-manager'); ?></a>
                                                    </div>
                                                <?php   }

                                                if (!empty($venue_facebook)) {   ?>
                                                    <div class="wpem-social-icon wpem-facebook">
                                                        <a href="<?php echo esc_url($venue_facebook); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php esc_html_e('Facebook', 'wp-event-manager'); ?></a>
                                                    </div>
                                                <?php  }

                                                if (!empty($venue_instagram)) { ?>
                                                    <div class="wpem-social-icon wpem-instagram">
                                                        <a href="<?php echo esc_url($venue_instagram); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a>
                                                    </div>
                                                <?php   }

                                                if (!empty($venue_twitter)) {  ?>
                                                    <div class="wpem-social-icon wpem-twitter">
                                                        <a href="<?php echo esc_url($venue_twitter); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a>
                                                    </div>
                                                <?php }

                                                if (!empty($venue_youtube)) { ?>
                                                    <div class="wpem-social-icon wpem-youtube">
                                                        <a href="<?php echo esc_url($venue_youtube); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a>
                                                    </div>
                                                <?php } ?>

                                                <?php do_action('single_event_listing_venue_social_end', $venue_id); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php do_action('single_event_listing_venue_map'); ?>
                                </div>
                            </div>
                            <?php do_action('single_event_listing_venue_end'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php elseif (get_event_venue_name() != '' && !is_event_online()) : ?>
    <div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
        <div class="wpem-row">
            <div class="wpem-col-md-12">
                <div class="wpem-venue-profile-wrapper" id="wpem_venue_profile">
                    <?php $event_content_toggle = apply_filters('event_manager_event_content_toggle', true);
                    $event_content_toggle_class = $event_content_toggle ? 'wpem-listing-accordion' : 'wpem-event-venue-info-title'; ?>
                    <div class="<?php echo esc_attr($event_content_toggle_class); ?> active">
                        <h3 class="wpem-heading-text"><?php esc_html_e('Venue', 'wp-event-manager'); ?></h3>
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
                                            <?php display_event_venue_name(); ?></span></a>
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