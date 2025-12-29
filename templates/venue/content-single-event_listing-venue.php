<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Single view organizer information box
 *
 * Hooked into single_event_listing_start priority 30
 *
 * @since  3.1.6
 */
if (wpem_has_event_venue_ids() && !wpem_is_event_online()) : ?>
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

                            <?php $wpem_venue_ids = wpem_get_event_venue_ids(); 
                            if ( ! empty( $wpem_venue_ids ) ) {
                                if ( ! is_array( $wpem_venue_ids ) ) {
                                    $wpem_venue_ids = array( $wpem_venue_ids );
                                }                                    
                                foreach ($wpem_venue_ids as $wpem_key => $wpem_venue_id): ?>
                                    <div class="wpem-venue-inner-wrapper">
                                        <div class="wpem-row">
                                            <div class="wpem-col-md-3 wpem-col-sm-12">
                                                <div class="wpem-venue-logo-wrapper">
                                                    <div class="wpem-venue-logo">
                                                        <a href="<?php echo esc_url(get_the_permalink($wpem_venue_id)); ?>">
                                                            <?php wpem_display_venue_logo('', '', $wpem_venue_id); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="wpem-col-md-9 wpem-col-sm-12">
                                                <div class="wpem-venue-name wpem-heading-text">
                                                    <a href="<?php echo esc_attr(get_the_permalink($wpem_venue_id)); ?>"><span><?php wpem_display_event_venue_name('', '', true, $wpem_venue_id); ?></span></a>
                                                </div>
                                                <?php do_action('single_event_listing_venue_description_before', $wpem_venue_id);
                                                $wpem_venue = get_post($wpem_venue_id);  ?>
                                                <div class="wpem-venue-description"><?php
                                                    $wpem_venue_content = get_post( $wpem_venue_id );
                                                    $wpem_content = apply_filters('wpem_the_content',$wpem_venue_content->post_content);
                                                    if(!empty($wpem_content)){
                                                    echo wp_kses_post( $wpem_content );}?>
                                                </div>

                                                <?php do_action('single_event_listing_venue_description_after', $wpem_venue_id); ?>

                                                <div class="wpem-venue-social-links">
                                                    <div class="wpem-venue-social-lists">

                                                        <?php do_action('single_event_listing_venue_social_start', $wpem_venue_id); 
                                                        
                                                        //get disable venue fields
                                                        $wpem_venue_fields = wpem_get_hidden_form_fields( 'event_manager_submit_venue_form_fields', 'venue');

                                                        $wpem_venue_website  = !in_array('venue_website', $wpem_venue_fields)?wpem_get_venue_website($wpem_venue_id):'';
                                                        $wpem_venue_facebook = !in_array('venue_facebook', $wpem_venue_fields)?wpem_get_venue_facebook($wpem_venue_id):'';
                                                        $wpem_venue_instagram = !in_array('venue_instagram', $wpem_venue_fields)?wpem_get_venue_instagram($wpem_venue_id):'';
                                                        $wpem_venue_twitter  = !in_array('venue_twitter', $wpem_venue_fields)?wpem_get_venue_twitter($wpem_venue_id):'';
                                                        $wpem_venue_youtube  = !in_array('venue_youtube', $wpem_venue_fields)?wpem_get_venue_youtube($wpem_venue_id):'';

                                                        if (!empty($wpem_venue_website)) {  ?>
                                                            <div class="wpem-social-icon wpem-weblink">
                                                                <a href="<?php echo esc_url($wpem_venue_website); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Website', 'wp-event-manager'); ?>"><?php esc_html_e('Website', 'wp-event-manager'); ?></a>
                                                            </div>
                                                        <?php   }

                                                        if (!empty($wpem_venue_facebook)) {   ?>
                                                            <div class="wpem-social-icon wpem-facebook">
                                                                <a href="<?php echo esc_url($wpem_venue_facebook); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php esc_html_e('Facebook', 'wp-event-manager'); ?></a>
                                                            </div>
                                                        <?php  }

                                                        if (!empty($wpem_venue_instagram)) { ?>
                                                            <div class="wpem-social-icon wpem-instagram">
                                                                <a href="<?php echo esc_url($wpem_venue_instagram); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php esc_html_e('Instagram', 'wp-event-manager'); ?></a>
                                                            </div>
                                                        <?php   }

                                                        if (!empty($wpem_venue_twitter)) {  ?>
                                                            <div class="wpem-social-icon wpem-twitter">
                                                                <a href="<?php echo esc_url($wpem_venue_twitter); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php esc_html_e('Twitter', 'wp-event-manager'); ?></a>
                                                            </div>
                                                        <?php }

                                                        if (!empty($wpem_venue_youtube)) { ?>
                                                            <div class="wpem-social-icon wpem-youtube">
                                                                <a href="<?php echo esc_url($wpem_venue_youtube); ?>" target="_blank" title="<?php esc_attr_e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php esc_html_e('Youtube', 'wp-event-manager'); ?></a>
                                                            </div>
                                                        <?php } ?>

                                                        <?php do_action('single_event_listing_venue_social_end', $wpem_venue_id); ?>

                                                    </div>
                                                </div>
                                            </div>
                                            <?php do_action('single_event_listing_venue_map'); ?>
                                        </div>
                                    </div>
                                <?php endforeach;
                            }
                            do_action('single_event_listing_venue_end'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php elseif (wpem_get_event_venue_name() != '' && !wpem_is_event_online()) : ?>
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
                                            <?php wpem_display_event_venue_name(); ?></span></a>
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