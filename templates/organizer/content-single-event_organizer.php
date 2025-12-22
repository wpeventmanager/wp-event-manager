<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$wpem_organizer = get_post($wpem_organizer_id);
$wpem_organizer_email = esc_html(get_post_meta($wpem_organizer_id, '_organizer_email', true)); 
if (get_option('event_manager_form_fields') && is_array(get_option('event_manager_form_fields'))) {
    $wpem_organizer_custom_fields = get_option('event_manager_form_fields', true)['organizer'];
} else {
    $GLOBALS['event_manager']->forms->get_form( 'submit-organizer', array() );
    $wpem_form_submit_organizer_instance = call_user_func( array( 'WPEM_Event_Manager_Form_Submit_Organizer', 'instance' ) );
    $wpem_organizer_custom_fields = $wpem_form_submit_organizer_instance->wpem_merge_with_custom_fields( 'backend' );
} 
// Check user is loggedin or not.
$wpem_check_user_access 	 = wpem_checked_guest_user_access();

// If user not logged in hide some fields from that
if($wpem_check_user_access == false && get_option('wpem_hide_data_from_guest')) {
    $wpem_field_to_hide = get_option('wpem_hide_organizer_fields');
}else {
	$wpem_field_to_hide = array();
}
?>

<div class="wpem-single-organizer-profile-wrapper" id="wpem_organizer_profile">
    <div class="wpem-organizer-profile">
        <?php do_action('single_event_listing_organizer_start'); ?>
        <div class="wpem-row">

            <div class="wpem-col-md-3">
                <div class="wpem-organizer-logo-wrapper">
                    <div class="wpem-organizer-logo">
                        <a><?php
                            if(!in_array('organizer_logo', $wpem_field_to_hide)) {
                                wpem_display_organizer_logo('', '', $wpem_organizer); 
                            }
                            ?></a>
                    </div>
                </div>
            </div>

            <div class="wpem-col-md-9 wpem-col-sm-12">
                <div class="wpem-organizer-infomation-wrapper">
                    <div class="wpem-organizer-name wpem-heading-text">
                        <span><?php 
                        if(!in_array('organizer_name', $wpem_field_to_hide)) {
                            echo esc_attr($wpem_organizer->post_title); 
                        }
                        ?></span>
                    </div>
                    <div class="wpem-organizer-description">
                        <?php 
                        if(!in_array('organizer_description', $wpem_field_to_hide)) {
                            $wpem_content = apply_filters('wpem_the_content',$wpem_organizer->post_content);
                            if(!empty($wpem_content)){
                                echo wp_kses_post($wpem_content);
                            }
                        }
                        ?>
                     </div>
                    <div class="wpem-organizer-social-links">
                        <div class="wpem-organizer-social-lists">
                            <?php do_action('single_event_listing_organizer_social_start', $wpem_organizer_id); ?>
                            <?php
                            //get disable organizer fields
                            $wpem_organizer_fields = wpem_get_hidden_form_fields( 'event_manager_submit_organizer_form_fields', 'organizer');
                            $wpem_organizer_website  = !in_array('organizer_website', $wpem_organizer_fields)?wpem_get_organizer_website($wpem_organizer):'';
                            $wpem_organizer_facebook = !in_array('organizer_facebook', $wpem_organizer_fields)?wpem_get_organizer_facebook($wpem_organizer):'';
                            $wpem_organizer_instagram = !in_array('organizer_instagram', $wpem_organizer_fields)?wpem_get_organizer_instagram($wpem_organizer):'';
                            $wpem_organizer_twitter  = !in_array('organizer_twitter', $wpem_organizer_fields)?wpem_get_organizer_twitter($wpem_organizer):'';
                            $wpem_organizer_youtube  = !in_array('organizer_youtube', $wpem_organizer_fields)?wpem_get_organizer_youtube($wpem_organizer):'';
                          
                            if (!empty($wpem_organizer_website) && !in_array('organizer_website', $wpem_field_to_hide) ) { ?>
                                <div class="wpem-social-icon wpem-weblink">
                                    <a href="<?php echo esc_url($wpem_organizer_website); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>"><?php esc_html_e('Website', 'wp-event-manager'); ?></a>
                                </div>
                            <?php }

                            if (!empty($wpem_organizer_facebook) && !in_array('organizer_facebook', $wpem_field_to_hide) ) { ?>
                                <div class="wpem-social-icon wpem-facebook">
                                    <a href="<?php echo esc_url($wpem_organizer_facebook); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php esc_html_e('Facebook', 'wp-event-manager'); ?></a>
                                </div>
                            <?php  }

                            if (!empty($wpem_organizer_instagram) && !in_array('organizer_instagram', $wpem_field_to_hide) ) { ?>
                                <div class="wpem-social-icon wpem-instagram">
                                    <a href="<?php echo esc_url($wpem_organizer_instagram); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a>
                                </div>
                            <?php }

                            if (!empty($wpem_organizer_twitter) && !in_array('organizer_twitter', $wpem_field_to_hide) ) { ?>
                                <div class="wpem-social-icon wpem-twitter">
                                    <a href="<?php echo esc_url($wpem_organizer_twitter); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a>
                                </div>
                            <?php  }
                            if (!empty($wpem_organizer_youtube) && !in_array('organizer_youtube', $wpem_field_to_hide)) { ?>
                                <div class="wpem-social-icon wpem-youtube">
                                    <a href="<?php echo esc_url($wpem_organizer_youtube); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a>
                                </div>
                            <?php } 
                            
                            do_action('single_event_listing_organizer_single_social_end', $wpem_organizer_id); ?>

                        </div>
                    </div>
                    <?php do_action('submit_organizer_form_organizer_fields_start'); 
                    if (isset($wpem_organizer_custom_fields)) {
                        foreach ($wpem_organizer_custom_fields as $wpem_key => $wpem_field) :
                            if (!in_array($wpem_key, $wpem_field_to_hide) && 
                                !strstr($wpem_key, 'organizer') && 
                                !strstr($wpem_key, 'vcv') && 
                                !strstr($wpem_key, 'submitting') && 
                                !empty(get_post_meta($wpem_organizer_id, '_' . $wpem_key))) : ?>
                                <div class="wpem-organizer-additional-information">
                                    <strong><?php echo esc_attr($wpem_field['label']); ?>:</strong>
                                    <span>
                                        <?php 
                                            $wpem_value = get_post_meta($wpem_organizer_id, '_' . $wpem_key, true);
                                            if ($wpem_field['type'] == 'url' && !empty($wpem_value)) {
                                                echo '<a href="'.esc_url($wpem_value).'" target="_blank">'.esc_url($wpem_value).'</a>';
                                            } else {
                                                echo esc_attr($wpem_value);
                                            }
                                        ?>
                                    </span>
                                </div>
                            <?php endif;
                        endforeach;
                    }                     
                    do_action('wpem_submit_organizer_form_organizer_fields_end'); ?>
                    <div class="wpem-organizer-contact-actions">
                        <?php do_action('single_event_listing_organizer_action_start', $wpem_organizer_id); ?>

                        <?php do_action('single_event_listing_organizer_action_end', $wpem_organizer_id); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php do_action('single_event_listing_organizer_end'); ?>
    </div>
</div>

<?php
wpem_get_event_manager_template(
    'organizer-event_listing.php',
    array(
        'organizer_id'    => $wpem_organizer_id,
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