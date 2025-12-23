<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Verify nonce for tab parameter
$wpem_active_tab = 'upcoming';
if ( isset( $_REQUEST['tab'] ) ) {
	$wpem_nonce_verified = false;
	if ( ! empty( $_REQUEST['_wpnonce'] ) ) {
		$wpem_nonce_verified = wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'organizer_event_tabs' );
	}
	if ( $wpem_nonce_verified ) {
		$wpem_active_tab = esc_attr(sanitize_text_field(wp_unslash($_REQUEST['tab'])));
	}
} ?>

<div class="wpem-event-organizer-tabs">
    <div class="wpem-tabs-wrapper">
        <ul class="wpem-tabs-wrap">
            <li class="wpem-tab-link <?php echo esc_attr($wpem_active_tab) == 'current' ? 'active' : '' ?>" data-tab="current">
                <?php esc_html_e('Current Events', 'wp-event-manager'); ?>
            </li>
            <li class="wpem-tab-link <?php echo esc_attr($wpem_active_tab) == 'upcoming' ? 'active' : '' ?>" data-tab="upcoming">
                <?php esc_html_e('Upcoming Events', 'wp-event-manager'); ?>
            </li>
            <li class="wpem-tab-link <?php echo esc_attr($wpem_active_tab) == 'past' ? 'active' : '' ?>" data-tab="past">
                <?php esc_html_e('Past Events', 'wp-event-manager'); ?>
            </li>
        </ul>

        <div class="event-organizer-tab-contents wpem-tab-content current">
            <!-- upcoming events list start-->
            <div id="upcoming" class="wpem-tab-pane <?php echo esc_attr($wpem_active_tab) == 'upcoming' ? 'active' : '' ?>">
                <?php if ($upcomingEvents->have_posts()) : ?>
                    <div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view">
                        <?php while ($upcomingEvents->have_posts()) : $upcomingEvents->the_post(); ?>
                            <?php wpem_get_event_manager_template_part('content', 'event_listing'); ?>
                        <?php endwhile; ?>
                    </div>
                    <?php if ($upcomingEvents->found_posts > $per_page) : ?>
                        <?php if ($show_pagination == "true") : ?>
                            <?php wpem_display_get_query_pagination($upcomingEvents->max_num_pages, $current_page, 'upcoming'); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php
                else :
                    wpem_get_event_manager_template_part('content', 'no-events-found');
                endif; ?>
            </div>
            <!-- upcoming events list end-->

            <!-- current events list start-->
            <div id="current" class="wpem-tab-pane <?php echo esc_attr($wpem_active_tab) == 'current' ? 'active' : '' ?>">
                <?php if ($currentEvents->have_posts()) : ?>
                    <div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view">
                        <?php while ($currentEvents->have_posts()) : $currentEvents->the_post(); ?>
                            <?php wpem_get_event_manager_template_part('content', 'event_listing'); ?>
                        <?php endwhile; ?>
                    </div>

                    <?php if ($currentEvents->found_posts > $per_page) : ?>
                        <?php if ($show_pagination == "true") : ?>
                            <?php wpem_display_get_query_pagination($currentEvents->max_num_pages, $current_page, 'current'); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php
                else :
                    wpem_get_event_manager_template_part('content', 'no-events-found');
                endif;  ?>
            </div>
            <!-- current events list end-->

            <!-- past event listing- start-->
            <div id="past" class="wpem-tab-pane <?php echo esc_attr($wpem_active_tab) == 'past' ? 'active' : '' ?>">
                <?php if ($pastEvents->have_posts()) : ?>
                    <div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view">
                        <?php while ($pastEvents->have_posts()) : $pastEvents->the_post(); ?>
                            <?php wpem_get_event_manager_template_part('content', 'event_listing'); ?>
                        <?php endwhile; ?>
                    </div>
                    <?php if ($pastEvents->found_posts > $per_page) : ?>
                        <?php if ($show_pagination == "true") : ?>
                            <?php wpem_display_get_query_pagination($pastEvents->max_num_pages, $current_page, 'past'); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php
                else :
                    wpem_get_event_manager_template_part('content', 'no-events-found');
                endif; ?>
            </div>
            <!-- past events list end-->
        </div>
        <!-- tab-content end tab-->
    </div>
</div>
<!-- end of wpem-tabs-wrapper -->