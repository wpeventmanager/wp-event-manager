<?php

/**
 * Single view organizer information box
 *
 * Hooked into single_event_listing_start priority 30
 *
 * @since  3.1.6
 */
?>


<?php if (has_event_organizer_ids()) : ?>

    <div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
        <div class="wpem-row">
            <div class="wpem-col-md-12">
                <div class="wpem-organizer-profile-wrapper" id="wpem_organizer_profile">

                    <?php $event_content_toggle = apply_filters('event_manager_event_content_toggle', true);
                    $event_content_toggle_class = $event_content_toggle ? 'wpem-listing-accordion' : 'wpem-event-organizer-info-title';
                    ?>

                    <div class="<?php echo esc_attr($event_content_toggle_class); ?> active">
                        <h3 class="wpem-heading-text"><?php _e('Organizer', 'wp-event-manager'); ?></h3>
                        <?php if ($event_content_toggle) : ?>
                            <i class="wpem-icon-minus"></i><i class="wpem-icon-plus"></i>
                        <?php endif; ?>
                    </div>

                    <div class="wpem-organizer-wrapper wpem-listing-accordion-panel active" style="display: block;">
                        <div class="wpem-organizer-profile">

                            <?php do_action('single_event_listing_organizer_start'); ?>

                            <?php $organizer_ids = get_event_organizer_ids(); ?>

                            <?php if (!empty($organizer_ids)) : ?>
                                <?php foreach ($organizer_ids as $key => $organizer_id) : ?>

                                    <div class="wpem-organizer-inner-wrapper">
                                        <div class="wpem-row">

                                            <div class="wpem-col-md-3 wpem-col-sm-12">
                                                <div class="wpem-organizer-logo-wrapper">
                                                    <div class="wpem-organizer-logo"><a><?php display_organizer_logo('', '', $organizer_id); ?></a></div>
                                                </div>
                                            </div>

                                            <div class="wpem-col-md-9 wpem-col-sm-12">
                                                <div class="wpem-organizer-name wpem-heading-text"><span><?php display_organizer_name('', '', true, $organizer_id); ?></span></div>

                                                <?php do_action('single_event_listing_organizer_description_before', $organizer_id);
                                                $organizer = get_post($organizer_id); ?>
                                                <div class="wpem-organizer-description"><?php

                                                $organizer_content = get_post( $organizer_id );
                                                $content = apply_filters('wpem_the_content',$organizer_content->post_content);
                                                echo wp_kses_post($content);                                            
                                                ?></div>
                         
                                                <?php do_action('single_event_listing_organizer_description_after', $organizer_id); ?>

                                                <div class="wpem-organizer-social-links">
                                                    <div class="wpem-organizer-social-lists">
                                                        <?php do_action('single_event_listing_organizer_social_start', $organizer_id); ?>
                                                        <?php
                                                         //get disable organizer fields
                                                         $organizer_fields = get_hidden_form_fields( 'event_manager_submit_organizer_form_fields', 'organizer');

                                                         $organizer_website  = !in_array('organizer_website', $organizer_fields)?get_organizer_website($organizer_id):'';
                                                         $organizer_facebook = !in_array('organizer_facebook', $organizer_fields)?get_organizer_facebook($organizer_id):'';
                                                         $organizer_instagram = !in_array('organizer_instagram', $organizer_fields)?get_organizer_instagram($organizer_id):'';
                                                         $organizer_twitter  = !in_array('organizer_twitter', $organizer_fields)?get_organizer_twitter($organizer_id):'';
                                                         $organizer_youtube  = !in_array('organizer_youtube', $organizer_fields)?get_organizer_youtube($organizer_id):'';
                                                        ?>
                                                        <?php
                                                        if (!empty($organizer_website)) {
                                                        ?>
                                                            <div class="wpem-social-icon wpem-weblink"><a href="<?php echo esc_url($organizer_website); ?>" title="<?php _e('Get Connect on Website', 'wp-event-manager'); ?>" target="_blank"><?php _e('Website', 'wp-event-manager'); ?></a></div>
                                                        <?php
                                                        }
                                                        if (!empty($organizer_facebook)) {
                                                        ?>
                                                            <div class="wpem-social-icon wpem-facebook"><a href="<?php echo esc_url($organizer_facebook); ?>" title="<?php _e('Get Connect on Facebook', 'wp-event-manager'); ?>" target="_blank"><?php _e('Faceboo', 'wp-event-manager'); ?></a></div>
                                                        <?php
                                                        }
                                                        if (!empty($organizer_instagram)) {
                                                        ?>
                                                            <div class="wpem-social-icon wpem-instagram"><a href="<?php echo esc_url($organizer_instagram); ?>" title="<?php _e('Get Connect on Instagram', 'wp-event-manager'); ?>" target="_blank"><?php _e('Instagram', 'wp-event-manager'); ?></a></div>
                                                        <?php
                                                        }
                                                        if (!empty($organizer_twitter)) {
                                                        ?>
                                                            <div class="wpem-social-icon wpem-twitter"><a href="<?php echo esc_url($organizer_twitter); ?>" title="<?php _e('Get Connect on Twitter', 'wp-event-manager'); ?>" target="_blank"><?php _e('Twitter', 'wp-event-manager'); ?></a></div>
                                                        <?php
                                                        }
                                                        if (!empty($organizer_youtube)) {
                                                        ?>
                                                            <div class="wpem-social-icon wpem-youtube"><a href="<?php echo esc_url($organizer_youtube); ?>" title="<?php _e('Get Connect on Youtube', 'wp-event-manager'); ?>" target="_blank"><?php _e('Youtube', 'wp-event-manager'); ?></a></div>
                                                        <?php } ?>

                                                        <?php do_action('single_event_listing_organizer_single_social_end', $organizer_id); ?>

                                                    </div>

                                                </div>

                                                <div class="wpem-organizer-contact-actions">
                                                    <?php do_action('single_event_listing_organizer_action_start', $organizer_id); ?>

                                                    <?php do_action('single_event_listing_organizer_action_end', $organizer_id); ?>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php do_action('single_event_listing_organizer_end'); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php else : ?>

    <?php if (get_organizer_name()) : ?>

        <div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
            <div class="wpem-row">
                <div class="wpem-col-md-12">
                    <div class="wpem-organizer-profile-wrapper" id="wpem_organizer_profile">

                        <div class="wpem-event-organizer-info-title">
                            <h3 class="wpem-heading-text"><?php _e('Organizer', 'wp-event-manager'); ?></h3>
                        </div>
                        <div class="wpem-organizer-profile">

                            <?php do_action('single_event_listing_organizer_start'); ?>

                            <div class="wpem-organizer-inner-wrapper">
                                <div class="wpem-row">

                                    <div class="wpem-col-md-3 wpem-col-sm-12">
                                        <div class="wpem-organizer-logo-wrapper">
                                            <div class="wpem-organizer-logo"><a><?php display_organizer_logo(); ?></a></div>
                                        </div>
                                    </div>

                                    <div class="wpem-col-md-9 wpem-col-sm-12">
                                        <div class="wpem-organizer-name wpem-heading-text"><span><?php display_organizer_name(); ?></span></div>

                                        <?php do_action('single_event_listing_organizer_description_before'); ?>

                                        <div class="wpem-organizer-short-info"><?php printf(__('%s', 'wp-event-manager'), get_organizer_description()); ?></div>

                                        <?php do_action('single_event_listing_organizer_description_after'); ?>

                                        <div class="wpem-organizer-social-links">
                                            <div class="wpem-organizer-social-lists">
                                                <?php do_action('single_event_listing_organizer_social_start'); ?>
                                                <?php
                                                $organizer_website  = get_organizer_website();
                                                $organizer_facebook = get_organizer_facebook();
                                                $organizer_instagram = get_organizer_instagram();
                                                $organizer_twitter  = get_organizer_twitter();
                                                $organizer_youtube  = get_organizer_youtube();
                                                ?>
                                                <?php
                                                if (!empty($organizer_website)) {
                                                ?>
                                                    <div class="wpem-social-icon wpem-weblink"><a href="<?php echo esc_url($organizer_website); ?>" title="<?php _e('Get Connect on Website', 'wp-event-manager'); ?>" target="_blank"><?php _e('Website', 'wp-event-manager'); ?></a></div>
                                                <?php
                                                }
                                                if (!empty($organizer_facebook)) {
                                                ?>
                                                    <div class="wpem-social-icon wpem-facebook"><a href="<?php echo esc_url($organizer_facebook); ?>" title="<?php _e('Get Connect on Facebook', 'wp-event-manager'); ?>" target="_blank"><?php _e('Faceboo', 'wp-event-manager'); ?></a></div>
                                                <?php
                                                }
                                                if (!empty($organizer_instagram)) {
                                                ?>
                                                    <div class="wpem-social-icon wpem-instagram"><a href="<?php echo esc_url($organizer_instagram); ?>" title="<?php _e('Get Connect on Instagram', 'wp-event-manager'); ?>" target="_blank"><?php _e('Instagram', 'wp-event-manager'); ?></a></div>
                                                <?php
                                                }
                                                if (!empty($organizer_twitter)) {
                                                ?>
                                                    <div class="wpem-social-icon wpem-twitter"><a href="<?php echo esc_url($organizer_twitter); ?>" title="<?php _e('Get Connect on Twitter', 'wp-event-manager'); ?>" target="_blank"><?php _e('Twitter', 'wp-event-manager'); ?></a></div>
                                                <?php
                                                }
                                                if (!empty($organizer_youtube)) {
                                                ?>
                                                    <div class="wpem-social-icon wpem-youtube"><a href="<?php echo esc_url($organizer_youtube); ?>" title="<?php _e('Get Connect on Youtube', 'wp-event-manager'); ?>" target="_blank"><?php _e('Youtube', 'wp-event-manager'); ?></a></div>
                                                <?php } ?>
                                                <?php do_action('single_event_listing_organizer_single_social_end'); ?>
                                            </div>
                                        </div>

                                        <div class="wpem-organizer-contact-actions">
                                            <?php do_action('single_event_listing_organizer_action_start'); ?>

                                            <?php do_action('single_event_listing_organizer_action_end'); ?>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <?php do_action('single_event_listing_organizer_end'); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>



    <?php endif; ?>

<?php endif; ?>