<?php $organizer = get_post($organizer_id);
$organizer_email = esc_html(get_post_meta($organizer_id, '_organizer_email', true)); 
if (get_option('event_manager_form_fields') && is_array(get_option('event_manager_form_fields'))) {
    $organizer_custom_fields = get_option('event_manager_form_fields', true)['organizer'];
} else {
    $GLOBALS['event_manager']->forms->get_form( 'submit-organizer', array() );
    $form_submit_organizer_instance = call_user_func( array( 'WP_Event_Manager_Form_Submit_Organizer', 'instance' ) );
    $organizer_custom_fields = $form_submit_organizer_instance->merge_with_custom_fields( 'backend' );
} ?>

<div class="wpem-single-organizer-profile-wrapper" id="wpem_organizer_profile">
    <div class="wpem-organizer-profile">
        <?php do_action('single_event_listing_organizer_start'); ?>
        <div class="wpem-row">

            <div class="wpem-col-md-3">
                <div class="wpem-organizer-logo-wrapper">
                    <div class="wpem-organizer-logo">
                        <a><?php display_organizer_logo('', '', $organizer); ?></a>
                    </div>
                </div>
            </div>

            <div class="wpem-col-md-9 wpem-col-sm-12">
                <div class="wpem-organizer-infomation-wrapper">
                    <div class="wpem-organizer-name wpem-heading-text">
                        <span><?php echo esc_attr($organizer->post_title); ?></span>
                    </div>
                    <div class="wpem-organizer-description">
                        <?php $content = apply_filters('wpem_the_content',$organizer->post_content);
                        echo wp_kses_post($content);?>
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
                          
                            if (!empty($organizer_website)) { ?>
                                <div class="wpem-social-icon wpem-weblink">
                                    <a href="<?php echo esc_url($organizer_website); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>"><?php esc_html_e('Website', 'wp-event-manager'); ?></a>
                                </div>
                            <?php }

                            if (!empty($organizer_facebook)) { ?>
                                <div class="wpem-social-icon wpem-facebook">
                                    <a href="<?php echo esc_url($organizer_facebook); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php esc_html_e('Facebook', 'wp-event-manager'); ?></a>
                                </div>
                            <?php  }

                            if (!empty($organizer_instagram)) { ?>
                                <div class="wpem-social-icon wpem-instagram">
                                    <a href="<?php echo esc_url($organizer_instagram); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a>
                                </div>
                            <?php }

                            if (!empty($organizer_twitter)) { ?>
                                <div class="wpem-social-icon wpem-twitter">
                                    <a href="<?php echo esc_url($organizer_twitter); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a>
                                </div>
                            <?php  }
                            if (!empty($organizer_youtube)) { ?>
                                <div class="wpem-social-icon wpem-youtube">
                                    <a href="<?php echo esc_url($organizer_youtube); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a>
                                </div>
                            <?php } 
                            
                            do_action('single_event_listing_organizer_single_social_end', $organizer_id); ?>

                        </div>
                    </div>
                    <?php do_action('submit_organizer_form_organizer_fields_start'); 
                    if (isset($organizer_custom_fields)) {
                        foreach ($organizer_custom_fields as $key => $field) :?>
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
                            <?php endif;
                        endforeach;
                    } 
                    do_action('organizer_form_organizer_fields_end'); ?>
                    <div class="wpem-organizer-contact-actions">
                        <?php do_action('single_event_listing_organizer_action_start', $organizer_id); ?>

                        <?php do_action('single_event_listing_organizer_action_end', $organizer_id); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php do_action('single_event_listing_organizer_end'); ?>
    </div>
</div>

<?php
get_event_manager_template(
    'organizer-event_listing.php',
    array(
        'organizer_id'    => $organizer_id,
        'per_page'        => $per_page,
        'show_pagination' => $show_pagination,
        'upcomingEvents'  => $upcomingEvents,
        'currentEvents'   => $currentEvents,
        'pastEvents'      => $pastEvents,
        'current_page'    => $current_page,
    ),
    'wp-event-manager/organizer',
    EVENT_MANAGER_PLUGIN_DIR . '/templates/organizer/'
);