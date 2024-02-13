<?php
/**
 * Single event organizer list information box(used for elementor)
 *
 * @since  3.1.32
 */
if (has_event_organizer_ids($event_id)) :
if (get_option('event_manager_form_fields')) {
    $organizer_custom_fields = get_option('event_manager_form_fields', true)['organizer'];
}
?>

<div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
    <div class="wpem-row">
        <div class="wpem-col-md-12">
            <div class="wpem-organizer-profile-wrapper" id="wpem_organizer_profile">

                <?php $event_content_toggle = apply_filters('event_manager_event_content_toggle', true);
                $event_content_toggle_class = $event_content_toggle ? 'wpem-listing-accordion' : 'wpem-event-organizer-info-title'; ?>
                <div class="<?php echo esc_attr($event_content_toggle_class); ?> active">
                    <h3 class="wpem-heading-text"><?php esc_html_e('Organizer', 'wp-event-manager'); ?></h3>
                </div>
                <div class="wpem-organizer-wrapper wpem-listing-accordion-panel active" style="display: block;">
                    <div class="wpem-organizer-profile">

                        <?php do_action('single_event_listing_organizer_start'); 
                        $organizer_ids = get_event_organizer_ids($event_id);
                        if (!empty($organizer_ids)) :
                            foreach ($organizer_ids as $key => $organizer_id) : 
                                $organizer = get_post($organizer_id);

                                if (get_option('event_manager_form_fields')) {
                                    $organizer_fields = get_option('event_manager_form_fields', true)['organizer'];
                                }
                                $organizer_email = esc_html(get_post_meta($organizer_id, '_organizer_email', true));
                                
                                do_action('single_event_listing_organizer_start'); ?>
                                
                                <div class="wpem-single-organizer-profile-wrapper" id="wpem_organizer_profile">
                                     <div class="wpem-organizer-profile">
                                        <div class="wpem-row">
                                            <!-- Organizer logo section start-->
                                            <div class="wpem-col-md-3">
                                                <div class="wpem-organizer-logo-wrapper">
                                                    <div class="wpem-organizer-logo">
                                                        <a><?php display_organizer_logo('', '', $organizer); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Organizer logo section end-->
                                            <div class="wpem-col-md-9 wpem-col-sm-12">
                                                <div class="wpem-organizer-infomation-wrapper">
                                                    <!-- Organizer title-->
                                                    <div class="wpem-organizer-name wpem-heading-text">
                                                        <span><?php echo esc_attr($organizer->post_title); ?></span>
                                                    </div>
                                                    <!-- Organizer description-->
                                                    <div class="wpem-organizer-description"><?php 
                                                        $content = apply_filters('wpem_the_content',$organizer->post_content);
                                                        echo wp_kses_post( $content );?>
                                                    </div>
                                                    <!-- Organizer social link section start-->
                                                    <div class="wpem-organizer-social-links">
                                                        <div class="wpem-organizer-social-lists">
                                                            <?php do_action('single_event_listing_organizer_social_start', $organizer_id);
                                                            //get disable organizer fields
                                                            $organizer_fields = get_hidden_form_fields( 'event_manager_submit_organizer_form_fields', 'organizer');
                                                            $organizer_website  = !in_array('organizer_website', $organizer_fields)?get_organizer_website($organizer):'';
                                                            $organizer_facebook = !in_array('organizer_facebook', $organizer_fields)?get_organizer_facebook($organizer):'';
                                                            $organizer_instagram = !in_array('organizer_instagram', $organizer_fields)?get_organizer_instagram($organizer):'';
                                                            $organizer_twitter  = !in_array('organizer_twitter', $organizer_fields)?get_organizer_twitter($organizer):'';
                                                            $organizer_youtube  = !in_array('organizer_youtube', $organizer_fields)?get_organizer_youtube($organizer):'';
                                                           
                                                            if (!empty($organizer_website)) { ?>
                                                                <div class="wpem-social-icon wpem-weblink">
                                                                    <a href="<?php echo esc_url($organizer_website); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>"><?php esc_html_e('Website', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php }

                                                            if (!empty($organizer_facebook)) { ?>
                                                                <div class="wpem-social-icon wpem-facebook">
                                                                    <a href="<?php echo esc_url($organizer_facebook); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php esc_html_e('Facebook', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php }

                                                            if (!empty($organizer_instagram)) { ?>
                                                                <div class="wpem-social-icon wpem-instagram">
                                                                    <a href="<?php echo esc_url($organizer_instagram); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php }

                                                            if (!empty($organizer_twitter)) { ?>
                                                                <div class="wpem-social-icon wpem-twitter">
                                                                    <a href="<?php echo esc_url($organizer_twitter); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php }
                                                            if (!empty($organizer_youtube)) { ?>
                                                                <div class="wpem-social-icon wpem-youtube">
                                                                    <a href="<?php echo esc_url($organizer_youtube); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a>
                                                                </div>
                                                            <?php } ?>

                                                            <?php do_action('single_event_listing_organizer_single_social_end', $organizer_id); ?>

                                                        </div>
                                                    </div>
                                                    <!-- Organizer social link section end-->
                                                    <?php do_action('submit_organizer_form_organizer_fields_start'); ?>
                                                    <!-- Organizer additional meta section start-->
                                                    <?php
                                                    if (isset($organizer_custom_fields)) {
                                                        foreach ($organizer_custom_fields as $key => $field) : ?>
                                                            <?php if (!strstr($key, 'organizer') && !strstr($key, 'vcv') && !strstr($key, 'submitting') && !empty(get_post_meta($organizer_id, '_' . $key))) : ?>
                                                                <div class="wpem-organizer-additional-information">
                                                                    <strong><?php echo esc_attr($field['label']); ?>:</strong>
                                                                    <span><?php 
                                                                        $value = get_post_meta($organizer_id, '_' . $key, true);
                                                                        if($field['type'] == 'url' && !empty($value))
                                                                            echo '<a href="'.esc_url($value).'" target="_blank">'.esc_url($value).'</a>';
                                                                        else
                                                                            echo esc_attr($value); ?>
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                    <?php endforeach;
                                                    } ?>
                                                     <!-- Organizer additional meta section end-->
                                                    <?php do_action('organizer_form_organizer_fields_end'); ?>
                                                    <div class="wpem-organizer-contact-actions">
                                                        <?php do_action('single_event_listing_organizer_action_start', $organizer_id); ?>

                                                        <?php do_action('single_event_listing_organizer_action_end', $organizer_id); ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <?php do_action('single_event_listing_organizer_end');
                                endforeach;
                            endif;
                        do_action('single_event_listing_organizer_end'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else : 
    if (get_organizer_name($event_id)) : ?>

        <div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
            <div class="wpem-row">
                <div class="wpem-col-md-12">
                    <div class="wpem-organizer-profile-wrapper" id="wpem_organizer_profile">

                        <div class="wpem-event-organizer-info-title">
                            <h3 class="wpem-heading-text"><?php esc_html_e('Organizer', 'wp-event-manager'); ?></h3>
                        </div>
                        <div class="wpem-organizer-profile">

                            <?php do_action('single_event_listing_organizer_start'); ?>

                            <div class="wpem-organizer-inner-wrapper">
                                <div class="wpem-row">
                                     <!-- Organizer logo section start-->
                                    <div class="wpem-col-md-3 wpem-col-sm-12">
                                        <div class="wpem-organizer-logo-wrapper">
                                            <div class="wpem-organizer-logo"><a><?php display_organizer_logo($event_id); ?></a></div>
                                        </div>
                                    </div>
                                    <!-- Organizer logo section end-->
                                    <div class="wpem-col-md-9 wpem-col-sm-12">
                                        <div class="wpem-organizer-name wpem-heading-text"><span><?php display_organizer_name($event_id); ?></span></div>

                                        <?php do_action('single_event_listing_organizer_description_before'); ?>

                                        <div class="wpem-organizer-short-info"><?php printf(__('%s', 'wp-event-manager'), get_organizer_description($event_id)); ?></div>

                                        <?php do_action('single_event_listing_organizer_description_after'); ?>
                                         <!-- Organizer social link section start-->
                                        <div class="wpem-organizer-social-links">
                                            <div class="wpem-organizer-social-lists">
                                                <?php do_action('single_event_listing_organizer_social_start'); 

                                                $organizer_website  = get_organizer_website($event_id);
                                                $organizer_facebook = get_organizer_facebook($event_id);
                                                $organizer_instagram = get_organizer_instagram($event_id);
                                                $organizer_twitter  = get_organizer_twitter($event_id);
                                                $organizer_youtube  = get_organizer_youtube($event_id);
                                                
                                                if (!empty($organizer_website)) { ?>
                                                    <div class="wpem-social-icon wpem-weblink"><a href="<?php echo esc_url($organizer_website); ?>" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Website', 'wp-event-manager'); ?></a></div>
                                                <?php }
                                                if (!empty($organizer_facebook)) { ?>
                                                    <div class="wpem-social-icon wpem-facebook"><a href="<?php echo esc_url($organizer_facebook); ?>" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Faceboo', 'wp-event-manager'); ?></a></div>
                                                <?php }
                                                if (!empty($organizer_instagram)) { ?>
                                                    <div class="wpem-social-icon wpem-instagram"><a href="<?php echo esc_url($organizer_instagram); ?>" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a></div>
                                                <?php }
                                                if (!empty($organizer_twitter)) { ?>
                                                    <div class="wpem-social-icon wpem-twitter"><a href="<?php echo esc_url($organizer_twitter); ?>" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a></div>
                                                <?php }
                                                if (!empty($organizer_youtube)) { ?>
                                                    <div class="wpem-social-icon wpem-youtube"><a href="<?php echo esc_url($organizer_youtube); ?>" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a></div>
                                                <?php } ?>
                                                <?php do_action('single_event_listing_organizer_single_social_end'); ?>
                                            </div>
                                        </div>
                                         <!-- Organizer social link section end-->
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
    <?php endif;
endif; ?>