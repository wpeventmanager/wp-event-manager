<?php

/**
 * Event Submission Form
 */
if (!defined('ABSPATH')) exit;

global $event_manager;
?>
<form action="<?php echo esc_url($action); ?>" method="post" id="submit-event-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
	<?php if (apply_filters('submit_event_form_show_signin', true)) : ?>
		<?php get_event_manager_template('account-signin.php'); ?>
	<?php endif; ?>
	<?php if (event_manager_user_can_post_event() || event_manager_user_can_edit_event($event_id)) : ?>
		<!-- Event Information Fields -->
		<h2 class="wpem-form-title wpem-heading-text"><?php _e('Event Details', 'wp-event-manager'); ?></h2>
		<?php
		if (isset($resume_edit) && $resume_edit) {
			printf('<p class="wpem-alert wpem-alert-info"><strong>' . __("You are editing an existing event. %s", "wp-event-manager") . '</strong></p>', '<a href="?new=1&key= %s ">' . __('Create A New Event', 'wp-event-manager') . '</a>', esc_attr($resume_edit));
		}
		?>

		<?php do_action('submit_event_form_event_fields_start'); ?>
		<?php foreach ($event_fields as $key => $field) : ?>
			<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
				<label for="<?php esc_attr_e($key); ?>"><?php echo esc_attr($field['label'], 'wp-event-manager') . apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field); ?></label>
				<div class="field <?php echo esc_attr($field['required'] ? 'required-field' : ''); ?>">
					<?php get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
				</div>
			</fieldset>
		<?php endforeach; ?>
		<?php do_action('submit_event_form_event_fields_end'); ?>

		<!-- Organizer Information Fields -->
		<?php if (get_option('enable_event_organizer')) : ?>
			<?php if ($organizer_fields) : ?>
				<?php do_action('submit_event_form_organizer_fields_start'); ?>
				<?php foreach ($organizer_fields as $key => $field) : ?>
					<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
						<h2 class="wpem-form-title wpem-heading-text"><?php _e('Organizer Details', 'wp-event-manager'); ?></h2>
						<label for="<?php esc_attr_e($key); ?>"><?php echo esc_attr($field['label']) . apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field); ?></label>
						<div class="field <?php echo esc_attr($field['required'] ? 'required-field' : ''); ?>">
							<?php get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
						</div>
					</fieldset>
				<?php endforeach; ?>
				<?php do_action('submit_event_form_organizer_fields_end'); ?>
			<?php endif; ?>
		<?php endif; ?>

		<!-- Venue Information Fields -->
		<?php if (get_option('enable_event_venue')) : ?>
			<?php if ($venue_fields) : ?>
				<?php do_action('submit_event_form_venue_fields_start'); ?>
				<?php foreach ($venue_fields as $key => $field) : ?>
					<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
						<h2 class="wpem-form-title wpem-heading-text"><?php _e('Venue Details', 'wp-event-manager'); ?></h2>
						<label for="<?php esc_attr_e($key); ?>"><?php echo esc_attr($field['label']) . apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field); ?></label>
						<div class="field <?php echo esc_attr($field['required'] ? 'required-field' : ''); ?>">
							<?php get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
						</div>
					</fieldset>
				<?php endforeach; ?>
				<?php do_action('submit_event_form_venue_fields_end'); ?>
			<?php endif; ?>
		<?php endif; ?>

		<div class="wpem-form-footer">
			<input type="hidden" name="event_manager_form" value="<?php echo esc_attr($form); ?>" />
			<input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>" />
			<input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
			<input type="submit" name="submit_event" class="wpem-theme-button" value="<?php esc_attr_e($submit_button_text); ?>" />
		</div>
	<?php else : ?>

		<?php do_action('submit_event_form_disabled'); ?>

	<?php endif; ?>
</form>

<?php if (get_option('enable_event_organizer')) : ?>

	<?php
	$organizer_fields =	$GLOBALS['event_manager']->forms->get_fields('submit-organizer');
	if (is_user_logged_in()) {
		$current_user = wp_get_current_user();
		if (isset($organizer_fields['organizer']['organizer_name']))
			$organizer_fields['organizer']['organizer_name']['value'] =  $current_user->display_name;
		if (isset($organizer_fields['organizer']['organizer_email']))
			$organizer_fields['organizer']['organizer_email']['value'] =  $current_user->user_email;
	}
	?>

	<div id="wpem_add_organizer_popup" class="wpem-modal" role="dialog" aria-labelledby="<?php _e('Add Organizer', 'wp-event-manager'); ?>">
		<div class="wpem-modal-content-wrapper">
			<div class="wpem-modal-header">
				<div class="wpem-modal-header-title">
					<h3 class="wpem-modal-header-title-text"><?php _e('Add Organizer', 'wp-event-manager'); ?></h3>
				</div>
				<div class="wpem-modal-header-close"><a href="javascript:void(0)" class="wpem-modal-close" id="wpem-modal-close">x</a></div>
			</div>
			<div class="wpem-modal-content">
				<form method="post" id="submit-organizer-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
					<h2 class="wpem-form-title wpem-heading-text"><?php _e('Organizer Details', 'wp-event-manager'); ?></h2>

					<?php do_action('submit_organizer_form_organizer_fields_start'); ?>

					<?php foreach ($organizer_fields['organizer'] as $key => $field) : ?>
						<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
							<label for="<?php esc_attr_e($key); ?>"><?php echo esc_attr($field['label'], 'wp-event-manager') . apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field); ?></label>
							<div class="field <?php echo esc_attr($field['required'] ? 'required-field' : ''); ?>">
								<?php get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
							</div>
						</fieldset>
					<?php endforeach; ?>
					<?php do_action('submit_organizer_form_organizer_fields_end'); ?>

					<div class="wpem-form-footer">
						<input type="hidden" name="organizer_id" value="0">
						<input type="hidden" name="step" value="0">
						<input type="button" name="submit_organizer" class="wpem-theme-button wpem_add_organizer" value="<?php _e('Add Organizer', 'wp-event-manager'); ?>" />
					</div>
				</form>
			</div>
		</div>
		<a href="#">
			<div class="wpem-modal-overlay"></div>
		</a>
	</div>
<?php endif; ?>


<?php if (get_option('enable_event_venue')) : ?>

	<?php
	$GLOBALS['event_manager']->forms->get_form('submit-venue', array());
	$form_submit_venue_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Venue', 'instance'));
	$venue_fields =	$form_submit_venue_instance->merge_with_custom_fields('backend');
	?>

	<div id="wpem_add_venue_popup" class="wpem-modal" role="dialog" aria-labelledby="<?php _e('Add Venue', 'wp-event-manager'); ?>">
		<div class="wpem-modal-content-wrapper">
			<div class="wpem-modal-header">
				<div class="wpem-modal-header-title">
					<h3 class="wpem-modal-header-title-text"><?php _e('Add Venue', 'wp-event-manager'); ?></h3>
				</div>
				<div class="wpem-modal-header-close"><a href="javascript:void(0)" class="wpem-modal-close" id="wpem-modal-close">x</a></div>
			</div>
			<div class="wpem-modal-content">
				<form method="post" id="submit-venue-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
					<h2 class="wpem-form-title wpem-heading-text"><?php _e('Venue Details', 'wp-event-manager'); ?></h2>

					<?php do_action('submit_venue_form_venue_fields_start'); ?>

					<?php foreach ($venue_fields['venue'] as $key => $field) : ?>
						<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
							<label for="<?php esc_attr_e($key); ?>"><?php echo esc_attr($field['label'], 'wp-event-manager') . apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field); ?></label>
							<div class="field <?php echo esc_attr($field['required'] ? 'required-field' : ''); ?>">
								<?php get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
							</div>
						</fieldset>
					<?php endforeach; ?>
					<?php do_action('submit_venue_form_venue_fields_end'); ?>

					<div class="wpem-form-footer">
						<input type="hidden" name="venue_id" value="0">
						<input type="hidden" name="step" value="0">
						<input type="button" name="submit_venue" class="wpem-theme-button wpem_add_venue" value="<?php _e('Add Venue', 'wp-event-manager'); ?>" />
					</div>
				</form>
			</div>
		</div>
		<a href="#">
			<div class="wpem-modal-overlay"></div>
		</a>
	</div>
<?php endif; ?>