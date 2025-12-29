<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$wpem_venue = get_post($wpem_venue_id); 
if (get_option('event_manager_form_fields') && is_array(get_option('event_manager_form_fields'))) {
    $wpem_venue_custom_fields = get_option('event_manager_form_fields', true)['venue'];
} else {
    $GLOBALS['event_manager']->forms->get_form( 'submit-venue', array() );
    $wpem_form_submit_venue_instance = call_user_func( array( 'WPEM_Event_Manager_Form_Submit_Venue', 'instance' ) );
    $wpem_venue_custom_fields = $wpem_form_submit_venue_instance->wpem_merge_with_custom_fields( 'backend' );
} ?>

<div class="wpem-single-venue-profile-wrapper" id="wpem_venue_profile">
    <div class="wpem-venue-profile">
        <?php do_action('single_event_listing_venue_start'); ?>
        <div class="wpem-row">
            <div class="wpem-col-md-3">
                <div class="wpem-venue-logo-wrapper">
                    <div class="wpem-venue-logo">
                        <a><?php wpem_display_venue_logo('', '', $wpem_venue); ?></a>
                    </div>
                </div>
            </div>
            <div class="wpem-col-md-9 wpem-col-sm-12">
                <div class="wpem-venue-infomation-wrapper">
                    <div class="wpem-venue-name wpem-heading-text">
                        <span><?php echo esc_attr($wpem_venue->post_title); ?></span>
                    </div>
                    <div class="wpem-venue-description">
                        <?php $wpem_content = apply_filters('wpem_the_content',$wpem_venue->post_content);
                        if ( is_string( $wpem_content ) && ! empty( $wpem_content ) ) {
                            echo wp_kses_post( $wpem_content );
                        }?>
                    </div>
                    <div class="wpem-venue-social-links">
                        <div class="wpem-venue-social-lists">
                            <?php do_action('single_event_listing_venue_social_start'); ?>
                            <?php
                             //get disable venue fields
                             $wpem_venue_fields = wpem_get_hidden_form_fields( 'event_manager_submit_venue_form_fields', 'venue');

                             $wpem_venue_website  = !in_array('venue_website', $wpem_venue_fields)?wpem_get_venue_website($wpem_venue):'';
                             $wpem_venue_facebook = !in_array('venue_facebook', $wpem_venue_fields)?wpem_get_venue_facebook($wpem_venue):'';
                             $wpem_venue_instagram = !in_array('venue_instagram', $wpem_venue_fields)?wpem_get_venue_instagram($wpem_venue):'';
                             $wpem_venue_twitter  = !in_array('venue_twitter', $wpem_venue_fields)?wpem_get_venue_twitter($wpem_venue):'';
                             $wpem_venue_youtube  = !in_array('venue_youtube', $wpem_venue_fields)?wpem_get_venue_youtube($wpem_venue):'';
                            
                            if (!empty($wpem_venue_website)) { ?>
                                <div class="wpem-social-icon wpem-weblink">
                                    <a href="<?php echo esc_url($wpem_venue_website); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>"><?php esc_html_e('Website', 'wp-event-manager'); ?></a>
                                </div>
                            <?php  }
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
                            <?php do_action('single_event_listing_venue_single_social_end', $wpem_venue_id); ?>
                        </div>
                    </div>
                    <?php do_action('wpem_custom_venue_fields_start'); 
                    if (isset($wpem_venue_custom_fields)) {
                        foreach ($wpem_venue_custom_fields as $wpem_key => $wpem_field) :?>
                            <?php if (!strstr($wpem_key, 'venue') && !strstr($wpem_key, 'vcv') && !strstr($wpem_key, 'submitting') && !empty(get_post_meta($wpem_venue_id, '_' . $wpem_key))) : ?>
                                <div class="wpem-organizer-additional-information">
                                    <strong><?php echo esc_attr($wpem_field['label']); ?>:</strong>
                                    <span><?php 
                                        $wpem_value = get_post_meta($wpem_venue_id, '_' . $wpem_key, true);
                                        if($wpem_field['type'] == 'url' && !empty($wpem_value))
                                            echo wp_kses_post('<a href="'.esc_url($wpem_value).'" target="_blank">'.esc_html($wpem_value).'</a>');
                                        else
                                            echo esc_attr($wpem_value); ?>
                                    </span>
                                </div>
                            <?php endif;
                        endforeach;
                    } 
                    do_action('wpem_custom_venue_fields_end'); ?>
                </div>
            </div>
        </div>
        <div class="wpem-venue-contact-actions">
            <?php do_action('single_event_listing_venue_action_start', $wpem_venue_id); ?>

            <?php do_action('single_event_listing_venue_action_end', $wpem_venue_id); ?>
        </div>
        <?php do_action('single_event_listing_venue_end'); ?>
    </div>
</div>