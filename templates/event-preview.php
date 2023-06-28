<?php
/**
 * Event listing preview when submitting event listings.
 * This template can be overridden by copying it to yourtheme/wp-event-manager/event-preview.php.
 *
 * @see         https://www.wp-eventmanager.com/template-files-override/
 * @author      WP Event Manager
 * @category    template
 * @version     2.5
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
} ?>

<form method="post" id="event_preview" action="<?php echo esc_url($form->get_action()); ?>">
	<div class="event_listing_preview_title">
		<input type="submit" name="edit_event" class="button event-manager-button-edit-listing wpem-theme-button" value="<?php esc_attr_e('← Edit listing', 'wp-event-manager'); ?>" />
		<h2><?php esc_html_e('Preview', 'wp-event-manager'); ?></h2>
		<input type="submit" name="continue" id="event_preview_submit_button" class="button event-manager-button-submit-listing wpem-theme-button" value="<?php echo esc_attr(apply_filters('submit_event_step_preview_submit_text', __('Submit Listing →', 'wp-event-manager'))); ?>" />		
	</div>
	<div class="event_listing_preview single_event_listing">
		<?php get_event_manager_template_part('content-single', 'event_listing'); ?>
		<input type="hidden" name="event_id" value="<?php echo esc_attr($form->get_event_id()); ?>" />
		<input type="hidden" name="step" value="<?php echo esc_attr($form->get_step()); ?>" />
		<input type="hidden" name="event_manager_form" value="<?php echo esc_attr($form->get_form_name()); ?>" />
	</div>
</form>
