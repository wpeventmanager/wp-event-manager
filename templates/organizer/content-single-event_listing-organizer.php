<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Single view organizer information box
 *
 * Hooked into single_event_listing_start priority 30
 * @since  3.1.6
 */

$wpem_check_user_access = wpem_checked_guest_user_access();
if($wpem_check_user_access == false && get_option('wpem_hide_data_from_guest')) {
    $wpem_field_to_hide = get_option('wpem_hide_organizer_fields');
}else {
	$wpem_field_to_hide = array();
}

if (wpem_has_event_organizer_ids()) : ?>
    <div class="wpem-single-event-footer" itemscope itemtype="http://data-vocabulary.org/Organization">
        <div class="wpem-row">
            <div class="wpem-col-md-12">
                <div class="wpem-organizer-profile-wrapper" id="wpem_organizer_profile">

                    <?php $event_content_toggle = apply_filters('event_manager_event_content_toggle', true);
                    $event_content_toggle_class = $event_content_toggle ? 'wpem-listing-accordion' : 'wpem-event-organizer-info-title'; ?>

                    <div class="<?php echo esc_attr($event_content_toggle_class); ?> active">
                        <h3 class="wpem-heading-text"><?php esc_html_e('Organizer', 'wp-event-manager'); ?></h3>
                        <?php if ($event_content_toggle) : ?>
                            <i class="wpem-icon-minus"></i><i class="wpem-icon-plus"></i>
                        <?php endif; ?>
                    </div>

                    <div class="wpem-organizer-wrapper wpem-listing-accordion-panel active" style="display: block;">
                        <div class="wpem-organizer-profile">

                            <?php do_action('single_event_listing_organizer_start'); 

                            $wpem_organizer_ids = wpem_get_event_organizer_ids(); 

                            if (!empty($wpem_organizer_ids)) : 
                                foreach ($wpem_organizer_ids as $wpem_key => $wpem_organizer_id) : ?>

                                    <div class="wpem-organizer-inner-wrapper">
                                        <div class="wpem-row">

                                            <div class="wpem-col-md-3 wpem-col-sm-12">
                                                <div class="wpem-organizer-logo-wrapper">
                                                    <div class="wpem-organizer-logo">
                                                    <a>
                                                        <?php 
                                                        if(!in_array('organizer_logo', $wpem_field_to_hide)) {
                                                            wpem_display_organizer_logo('', '', $wpem_organizer_id); 
                                                        }
                                                        ?>
                                                    </a></div>
                                                </div>
                                            </div>

                                            <div class="wpem-col-md-9 wpem-col-sm-12">
                                                <div class="wpem-organizer-name wpem-heading-text">
                                                <span>
                                                    <?php 
                                                    if(!in_array('organizer_name', $wpem_field_to_hide)) {
                                                        wpem_display_organizer_name('', '', true, $wpem_organizer_id); 
                                                    } 
                                                    ?>
                                                </span></div>

                                                <?php do_action('single_event_listing_organizer_description_before', $wpem_organizer_id);
                                                $wpem_organizer = get_post($wpem_organizer_id); ?>
                                                <div class="wpem-organizer-description">
                                                <?php
                                                if(!in_array('organizer_description', $wpem_field_to_hide)) {
                                                    $wpem_organizer_content = get_post( $wpem_organizer_id );
                                                    $wpem_content = apply_filters('wpem_the_content',$wpem_organizer_content->post_content);
                                                    if(!empty($wpem_content)){
                                                        echo wp_kses_post($wpem_content);
                                                    }
                                                }
                                                    ?>
                                                </div>
                         
                                                <?php do_action('single_event_listing_organizer_description_after', $wpem_organizer_id); ?>

                                                <div class="wpem-organizer-social-links">
                                                    <div class="wpem-organizer-social-lists">
                                                        <?php do_action('single_event_listing_organizer_social_start', $wpem_organizer_id); ?>
                                                        <?php
                                                         //get disable organizer fields
                                                         $wpem_organizer_fields = wpem_get_hidden_form_fields( 'event_manager_submit_organizer_form_fields', 'organizer');

                                                         $wpem_organizer_website  = !in_array('organizer_website', $wpem_organizer_fields)?wpem_get_organizer_website($wpem_organizer_id):'';
                                                         $wpem_organizer_facebook = !in_array('organizer_facebook', $wpem_organizer_fields)?wpem_get_organizer_facebook($wpem_organizer_id):'';
                                                         $wpem_organizer_instagram = !in_array('organizer_instagram', $wpem_organizer_fields)?wpem_get_organizer_instagram($wpem_organizer_id):'';
                                                         $wpem_organizer_twitter  = !in_array('organizer_twitter', $wpem_organizer_fields)?wpem_get_organizer_twitter($wpem_organizer_id):'';
                                                         $wpem_organizer_youtube  = !in_array('organizer_youtube', $wpem_organizer_fields)?wpem_get_organizer_youtube($wpem_organizer_id):'';
                                                        
                                                        if (!empty($wpem_organizer_website) && !in_array('organizer_website', $wpem_field_to_hide)) { ?>
                                                            <div class="wpem-social-icon wpem-weblink"><a href="<?php echo esc_url($wpem_organizer_website); ?>" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Website', 'wp-event-manager'); ?></a></div>
                                                        <?php  }
                                                        if (!empty($wpem_organizer_facebook) && !in_array('organizer_facebook', $wpem_field_to_hide)) { ?>
                                                            <div class="wpem-social-icon wpem-facebook"><a href="<?php echo esc_url($wpem_organizer_facebook); ?>" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Faceboo', 'wp-event-manager'); ?></a></div>
                                                        <?php }
                                                        if (!empty($wpem_organizer_instagram) && !in_array('organizer_instagram', $wpem_field_to_hide)) { ?>
                                                            <div class="wpem-social-icon wpem-instagram"><a href="<?php echo esc_url($wpem_organizer_instagram); ?>" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a></div>
                                                        <?php  }
                                                        if (!empty($wpem_organizer_twitter) && !in_array('organizer_twitter', $wpem_field_to_hide) ) { ?>
                                                            <div class="wpem-social-icon wpem-twitter"><a href="<?php echo esc_url($wpem_organizer_twitter); ?>" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a></div>
                                                        <?php }
                                                        if (!empty($wpem_organizer_youtube) && !in_array('organizer_youtube', $wpem_field_to_hide)) { ?>
                                                            <div class="wpem-social-icon wpem-youtube"><a href="<?php echo esc_url($wpem_organizer_youtube); ?>" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a></div>
                                                        <?php } ?>

                                                        <?php do_action('single_event_listing_organizer_single_social_end', $wpem_organizer_id); ?>

                                                    </div>

                                                </div>

                                                <div class="wpem-organizer-contact-actions">
                                                    <?php do_action('single_event_listing_organizer_action_start', $wpem_organizer_id); ?>

                                                    <?php do_action('single_event_listing_organizer_action_end', $wpem_organizer_id); ?>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                <?php endforeach;
                            endif;
                            do_action('single_event_listing_organizer_end'); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else : 
    if (wpem_get_organizer_name()) : ?>

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

                                    <div class="wpem-col-md-3 wpem-col-sm-12">
                                        <div class="wpem-organizer-logo-wrapper">
                                            <div class="wpem-organizer-logo"><a><?php wpem_display_organizer_logo(); ?></a></div>
                                        </div>
                                    </div>

                                    <div class="wpem-col-md-9 wpem-col-sm-12">
                                        <div class="wpem-organizer-name wpem-heading-text"><span><?php wpem_display_organizer_name(); ?></span></div>

                                        <?php do_action('single_event_listing_organizer_description_before'); ?>

                                        <div class="wpem-organizer-short-info"><?php printf(esc_attr('%s', 'wp-event-manager'), wp_kses_post(wpem_get_organizer_description())); ?></div>

                                        <?php do_action('single_event_listing_organizer_description_after'); ?>

                                        <div class="wpem-organizer-social-links">
                                            <div class="wpem-organizer-social-lists">
                                                <?php do_action('single_event_listing_organizer_social_start');
                                                
                                                $wpem_organizer_website  = wpem_get_organizer_website();
                                                $wpem_organizer_facebook = wpem_get_organizer_facebook();
                                                $wpem_organizer_instagram = wpem_get_organizer_instagram();
                                                $wpem_organizer_twitter  = wpem_get_organizer_twitter();
                                                $wpem_organizer_youtube  = wpem_get_organizer_youtube();
                                                
                                                if (!empty($wpem_organizer_website)) { ?>
                                                    <div class="wpem-social-icon wpem-weblink"><a href="<?php echo esc_url($wpem_organizer_website); ?>" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Website', 'wp-event-manager'); ?></a></div>
                                                <?php }
                                                if (!empty($wpem_organizer_facebook)) { ?>
                                                    <div class="wpem-social-icon wpem-facebook"><a href="<?php echo esc_url($wpem_organizer_facebook); ?>" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Faceboo', 'wp-event-manager'); ?></a></div>
                                                <?php }
                                                if (!empty($wpem_organizer_instagram)) { ?>
                                                    <div class="wpem-social-icon wpem-instagram"><a href="<?php echo esc_url($wpem_organizer_instagram); ?>" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a></div>
                                                <?php }
                                                if (!empty($wpem_organizer_twitter)) { ?>
                                                    <div class="wpem-social-icon wpem-twitter"><a href="<?php echo esc_url($wpem_organizer_twitter); ?>" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a></div>
                                                <?php }
                                                if (!empty($wpem_organizer_youtube)) { ?>
                                                    <div class="wpem-social-icon wpem-youtube"><a href="<?php echo esc_url($wpem_organizer_youtube); ?>" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>" target="_blank"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a></div>
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
    <?php endif; 
endif; ?>