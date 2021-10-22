<?php $organizer = get_post($organizer_id);
$organizer_email = get_post_meta($organizer_id, '_organizer_email', true)
?>

<div class="wpem-single-organizer-profile-wrapper" id="wpem_organizer_profile">
    <div class="wpem-organizer-profile">

        <?php do_action('single_event_listing_organizer_start'); ?>
        <div class="wpem-row">
        
            <div class="wpem-col-md-3">
                <div class="wpem-organizer-logo-wrapper">
                    <div class="wpem-organizer-logo">
                        <a><?php display_organizer_logo('', '', $organizer); ?></a>
                    </div>
                    <?php /** <div class="wpem-organizer-logo-title wpem-heading-text"><a><span><?php echo $organizer_name; ?></span></a></div> */ ?>
                    
                </div>
            </div>

            <div class="wpem-col-md-9 wpem-col-sm-12">
                <div class="wpem-organizer-infomation-wrapper">

                    <div class="wpem-organizer-name wpem-heading-text">
                        <span><?php echo $organizer->post_title; ?></span>
                    </div>

                    <div class="wpem-organizer-description"><?php printf(__('%s', 'wp-event-manager'), get_organizer_description($organizer)); ?></div>
                        
                    <div class="wpem-organizer-social-links">
                        <div class="wpem-organizer-social-lists">
                            <?php do_action('single_event_listing_organizer_social_start', $organizer_id); ?>
                            <?php
                            $organizer_website  = get_organizer_website($organizer);
                            $organizer_facebook = get_organizer_facebook($organizer);
                            $organizer_instagram = get_organizer_instagram($organizer);
                            $organizer_twitter  = get_organizer_twitter($organizer);
                            $organizer_youtube  = get_organizer_youtube($organizer);
                            ?>
                            <?php
                            if (!empty($organizer_website))
                            {
                                ?>
                                <div class="wpem-social-icon wpem-weblink">
                                    <a href="<?php echo esc_url($organizer_website); ?>" target="_blank" title="<?php _e('Get Connect on Website', 'wp-event-manager'); ?>"><?php _e('Website', 'wp-event-manager'); ?></a>
                                </div>
                                <?php
                            }

                            if (!empty($organizer_facebook))
                            {
                                ?> 
                                <div class="wpem-social-icon wpem-facebook">
                                    <a href="<?php echo esc_url($organizer_facebook); ?>" target="_blank" title="<?php _e('Get Connect on Facebook', 'wp-event-manager'); ?>"><?php _e('Facebook', 'wp-event-manager'); ?></a>
                                </div>
                                <?php
                            }

                            if (!empty($organizer_instagram))
                            {
                                ?> 
                                <div class="wpem-social-icon wpem-instagram">
                                    <a href="<?php echo esc_url($organizer_instagram); ?>" target="_blank" title="<?php _e('Get Connect on Instagram', 'wp-event-manager'); ?>"><?php _e('Instagram', 'wp-event-manager'); ?></a>
                                </div>
                                <?php
                            }

                            if (!empty($organizer_twitter))
                            {
                                ?>
                                <div class="wpem-social-icon wpem-twitter">
                                    <a href="<?php echo esc_url($organizer_twitter); ?>" target="_blank" title="<?php _e('Get Connect on Twitter', 'wp-event-manager'); ?>"><?php _e('Twitter', 'wp-event-manager'); ?></a>
                                </div>
                                <?php
                            }
                            if (!empty($organizer_youtube))
                            {
                                ?>
                                <div class="wpem-social-icon wpem-youtube">
                                    <a href="<?php echo esc_url($organizer_youtube); ?>" target="_blank" title="<?php _e('Get Connect on Youtube', 'wp-event-manager'); ?>"><?php _e('Youtube', 'wp-event-manager'); ?></a>
                                </div>
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