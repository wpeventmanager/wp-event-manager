<?php
/**
 * Single event organizer list information box
 *
 * @since  3.1.32
 */
?>
<?php if (has_event_organizer_ids($event_id)) : ?>

<div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
    <div class="wpem-row">
        <div class="wpem-col-md-12">
            <div class="wpem-organizer-profile-wrapper" id="wpem_organizer_profile">

                <?php $event_content_toggle = apply_filters('event_manager_event_content_toggle', true);
                $event_content_toggle_class = $event_content_toggle ? 'wpem-listing-accordion' : 'wpem-event-organizer-info-title';
                ?>
                <div class="<?php echo esc_attr($event_content_toggle_class); ?> active">
                    <h3 class="wpem-heading-text"><?php _e('Organizer', 'wp-event-manager'); ?></h3>
                </div>
                <div class="wpem-organizer-wrapper wpem-listing-accordion-panel active" style="display: block;">
                    <div class="wpem-organizer-profile">

                        <?php do_action('single_event_listing_organizer_start'); ?>

                        <?php $organizer_ids = get_event_organizer_ids($event_id); ?>

                        <?php if (!empty($organizer_ids)) : ?>                            
                                
                            <?php foreach ($organizer_ids as $key => $organizer_id) : ?>
                                <?php $organizer = get_post($organizer_id);

                                if (get_option('event_manager_form_fields')) {
                                    $organizer_fields = get_option('event_manager_form_fields', true)['organizer'];
                                }
                                $organizer_email = get_post_meta($organizer_id, '_organizer_email', true);
                                     do_action('single_event_listing_organizer_start'); ?>
                                <div class="wpem-single-organizer-profile-wrapper" id="wpem_organizer_profile">
                                     <div class="wpem-organizer-profile">
                                        <div class="wpem-row">

                                            <div class="wpem-col-md-3">
                                                <div class="wpem-organizer-logo-wrapper">
                                                    <div class="wpem-organizer-logo">
                                                        <a><?php display_organizer_logo('', '', $organizer); ?></a>
                                                    </div>
                                                    <?php /** <div class="wpem-organizer-logo-title wpem-heading-text"><a><span><?php echo esc_attr($organizer_name); ?></span></a></div> */ ?>
                                                </div>
                                            </div>

                                            <div class="wpem-col-md-9 wpem-col-sm-12">
                                                <div class="wpem-organizer-infomation-wrapper">

                                                    <div class="wpem-organizer-name wpem-heading-text">
                                                    <span><?php echo esc_attr($organizer->post_title); ?></span>
                                                    </div>

                                                    <div class="wpem-organizer-description"> 
                                                    <?php $content = apply_filters('wpem_the_content',$organizer->post_content);
                                                    echo wp_kses_post( $content );?>
                                                </div>
                                                    <div class="wpem-organizer-social-links">
                                                        <div class="wpem-organizer-social-lists">
                                                            <?php do_action('single_event_listing_organizer_social_start', $organizer_id); ?>
                                                            <?php
                                                            //get disable organizer fields
                                                            $organizer_fields = get_hidden_form_fields( 'event_manager_submit_organizer_form_fields', 'organizer');
                                                            $organizer_website  = !in_array('organizer_website', $organizer_fields)?get_organizer_website($organizer):'';
                                                            $organizer_facebook = !in_array('organizer_facebook', $organizer_fields)?get_organizer_facebook($organizer):'';
                                                            $organizer_instagram = !in_array('organizer_instagram', $organizer_fields)?get_organizer_instagram($organizer):'';
                                                            $organizer_twitter  = !in_array('organizer_twitter', $organizer_fields)?get_organizer_twitter($organizer):'';
                                                            $organizer_youtube  = !in_array('organizer_youtube', $organizer_fields)?get_organizer_youtube($organizer):'';
                                                            ?>
                                                            <?php
                                                            if (!empty($organizer_website)) {
                                                            ?>
                                                                <div class="wpem-social-icon wpem-weblink">
                                                                    <a href="<?php echo esc_url($organizer_website); ?>" target="_blank" title="<?php _e('Get Connect on Website', 'wp-event-manager'); ?>"><?php _e('Website', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php
                                                            }

                                                            if (!empty($organizer_facebook)) {
                                                            ?>
                                                                <div class="wpem-social-icon wpem-facebook">
                                                                    <a href="<?php echo esc_url($organizer_facebook); ?>" target="_blank" title="<?php _e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php _e('Facebook', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php
                                                            }

                                                            if (!empty($organizer_instagram)) {
                                                            ?>
                                                                <div class="wpem-social-icon wpem-instagram">
                                                                    <a href="<?php echo esc_url($organizer_instagram); ?>" target="_blank" title="<?php _e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php _e('Instagram', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php
                                                            }

                                                            if (!empty($organizer_twitter)) {
                                                            ?>
                                                                <div class="wpem-social-icon wpem-twitter">
                                                                    <a href="<?php echo esc_url($organizer_twitter); ?>" target="_blank" title="<?php _e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php _e('Twitter', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php
                                                            }
                                                            if (!empty($organizer_youtube)) {
                                                            ?>
                                                                <div class="wpem-social-icon wpem-youtube">
                                                                    <a href="<?php echo esc_url($organizer_youtube); ?>" target="_blank" title="<?php _e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php _e('Youtube', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php } ?>

                                                            <?php do_action('single_event_listing_organizer_single_social_end', $organizer_id); ?>

                                                        </div>
                                                    </div>
                                                    <?php do_action('submit_organizer_form_organizer_fields_start'); ?>
                                                    <?php
                                                    if (isset($organizer_fields)) {
                                                        foreach ($organizer_fields as $key => $field) : ?>
                                                            <?php if (!strstr($key, 'organizer') && !strstr($key, 'vcv') && !strstr($key, 'submitting') && !empty(get_post_meta($organizer_id, '_' . $key))) : ?>
                                                                <div class="wpem-organizer-additional-information">
                                                                    <strong><?= $field['label'] ?>:</strong>
                                                                    <span><?= get_post_meta($organizer_id, '_' . $key, true) ? get_post_meta($organizer_id, '_' . $key, true) : '-'  ?></span></span>
                                                                </div>
                                                            <?php endif; ?>
                                                    <?php endforeach;
                                                    } ?>
                                                    <?php do_action('submit_organizer_form_organizer_fields_end'); ?>
                                                    <div class="wpem-organizer-contact-actions">
                                                        <?php do_action('single_event_listing_organizer_action_start', $organizer_id); ?>

                                                        <?php do_action('single_event_listing_organizer_action_end', $organizer_id); ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <?php do_action('single_event_listing_organizer_end'); ?>
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

    <?php if (get_organizer_name($event_id)) : ?>

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
                                            <div class="wpem-organizer-logo"><a><?php display_organizer_logo($event_id); ?></a></div>
                                        </div>
                                    </div>

                                    <div class="wpem-col-md-9 wpem-col-sm-12">
                                        <div class="wpem-organizer-name wpem-heading-text"><span><?php display_organizer_name($event_id); ?></span></div>

                                        <?php do_action('single_event_listing_organizer_description_before'); ?>

                                        <div class="wpem-organizer-short-info"><?php printf(__('%s', 'wp-event-manager'), get_organizer_description($event_id)); ?></div>

                                        <?php do_action('single_event_listing_organizer_description_after'); ?>

                                        <div class="wpem-organizer-social-links">
                                            <div class="wpem-organizer-social-lists">
                                                <?php do_action('single_event_listing_organizer_social_start'); ?>
                                                <?php
                                                $organizer_website  = get_organizer_website($event_id);
                                                $organizer_facebook = get_organizer_facebook($event_id);
                                                $organizer_instagram = get_organizer_instagram($event_id);
                                                $organizer_twitter  = get_organizer_twitter($event_id);
                                                $organizer_youtube  = get_organizer_youtube($event_id);
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