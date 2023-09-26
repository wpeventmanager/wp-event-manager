<?php
/**
 * Event Submission Form
 */
if (!defined('ABSPATH')) exit;
global $event_manager; ?>

<form action="<?php echo esc_url($action); ?>" method="post" id="submit-venue-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
	<?php if (is_user_logged_in()) { ?>
		<h2 class="wpem-form-title wpem-heading-text"><?php _e('Venue Details', 'wp-event-manager'); ?></h2>
		<?php
		if (isset($resume_edit) && $resume_edit) {
			printf('<p class="wpem-alert wpem-alert-info"><strong>' . __("You are editing an existing venue. %s", "wp-event-manager") . '</strong></p>', '<a href="?new=1&key=%s">' . __('Create A New venue', 'wp-event-manager') . '</a>',esc_attr($resume_edit));
		} 
		
		do_action('submit_venue_form_venue_fields_start'); 
		
		foreach ($venue_fields as $key => $field) : 
			if(isset($field['visibility']) && ($field['visibility'] == 0 || $field['visibility'] = false)) :
				continue;
			endif;?>
			<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
				<label for="<?php esc_attr_e($key); ?>"><?php _e(esc_attr($field['label']), 'wp-event-manager'); echo apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field); ?></label>
				<div class="field <?php echo esc_attr($field['required'] ? 'required-field' : ''); ?>">
					<?php get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
				</div>
			</fieldset>
		<?php endforeach; 
		 do_action('submit_venue_form_venue_fields_end'); ?>

		<div class="wpem-form-footer">
			<input type="hidden" name="event_manager_form" value="<?php echo esc_attr($form); ?>" />
			<input type="hidden" name="venue_id" value="<?php echo esc_attr($venue_id); ?>" />
			<input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
			<input type="submit" name="submit_venue" class="wpem-theme-button" id="submit-venue-button" value="<?php esc_attr_e($submit_button_text); ?>" />
		</div>

	<?php	} else { ?>
		<div class="wpem-form-group">
			<div class="field account-sign-in wpem-alert wpem-alert-info">
				<a href="<?php echo !empty(get_option('event_manager_login_page_url')) ? esc_url(apply_filters('submit_event_form_login_url', get_option('event_manager_login_page_url'))) : esc_url(home_url() . '/wp-login.php'); ?>"><?php _e('Log In', 'wp-event-manager'); ?></a>
				<?php echo esc_attr(" to Submit the List of Venue from your account.", "wp-event-manager"); ?>
			</div>
		</div>
	<?php	}	?>
</form>