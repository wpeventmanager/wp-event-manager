<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Event Submission Form
 */
if(!defined('ABSPATH')) exit;
global $event_manager; 
do_action('wp_event_manager_event_submit_before');
$wpem_allowed_field_types = array_keys(wpem_get_form_field_types()); ?>

<form action="<?php echo esc_url($action); ?>" method="post" id="submit-event-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
	<?php if(apply_filters('submit_event_form_show_signin', true)) : 
		wpem_get_event_manager_template('account-signin.php'); 
	 endif; 
	if(event_manager_user_can_post_event() || event_manager_user_can_edit_event($event_id)) : ?>
		<!-- Event Information Fields -->
		<h2 class="wpem-form-title wpem-heading-text"><?php esc_html_e('Event Details', 'wp-event-manager'); ?></h2>
		<?php
		if ( isset( $resume_edit ) && $resume_edit ) {
			/* translators: %1$s is the static text "You are editing an existing event.", %2$s is the link to create a new event */
			printf(
				'<p class="wpem-alert wpem-alert-info"><strong>%1$s %2$s</strong></p>',
				esc_html__( 'You are editing an existing event.', 'wp-event-manager' ),
				sprintf(
					'<a href="?new=1&key=%1$s&_wpnonce=%3$s">%2$s</a>',
					esc_attr( $resume_edit ),
					esc_html__( 'Create A New Event', 'wp-event-manager' ),
					esc_attr( wp_create_nonce( 'wpem_reset_submission_cookies' ) )
				)
			);
		}

		do_action('submit_event_form_event_fields_start'); 

		//Show Hide event thumbnail field on front end
		$wpem_thumbnail_key = 'event_thumbnail'; 
		$wpem_show_thumbnail_field = get_option('event_manager_upload_custom_thumbnail', false); 
		foreach($event_fields as $wpem_key => $wpem_field) :
			if(isset($wpem_field['visibility']) && ($wpem_field['visibility'] == 0 || $wpem_field['visibility'] = false)) :
				continue;
			endif; 
			if (isset($wpem_field['type']) && $wpem_field['type'] === 'media-library-image' && !is_user_logged_in()) {
				continue;
			}
			if ($wpem_key === $wpem_thumbnail_key && $wpem_show_thumbnail_field != 1) {
				continue;
			} ?>
			<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($wpem_key); ?>">
				<label for="<?php echo esc_attr($wpem_key); ?>">
					<?php echo esc_html(isset($wpem_field['label']) ? $wpem_field['label'] : '', 'wp-event-manager');
					echo wp_kses_post(apply_filters('submit_event_form_required_label', isset($wpem_field['required']) && $wpem_field['required'] == 1 ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', isset($wpem_field['required']) ? $wpem_field['required'] : '')); ?>
				</label>
				<div class="field <?php echo esc_attr(isset($wpem_field['required']) && $wpem_field['required'] == 1 ? 'required-field' : ''); ?>">
					<?php if(isset($wpem_field['addon']) && !empty($wpem_field['addon'])) : 
						do_action('wpem_submit_event_form_addon_before', $wpem_field, $wpem_key);	
					else : 
						$wpem_field_type = in_array($wpem_field['type'], $wpem_allowed_field_types, true) ? $wpem_field['type'] : 'text';
						wpem_get_event_manager_template('form-fields/' . $wpem_field_type . '-field.php', array('wpem_key' => $wpem_key, 'wpem_field' => $wpem_field)); 
					endif; ?>
				</div>
			</fieldset>
		<?php endforeach; 
		do_action('submit_event_form_event_fields_end'); ?>

		<!-- Organizer Information Fields -->
		<?php if(!get_option('event_manager_hide_frontend_organizer')) :
			if(get_option('enable_event_organizer')) :
				if($wpem_organizer_fields) :
					do_action('submit_event_form_organizer_fields_start');
					foreach($wpem_organizer_fields as $wpem_key => $wpem_field) : 
						if(isset($wpem_field['visibility']) && ($wpem_field['visibility'] == 0 || $wpem_field['visibility'] == false)) :
							continue;
						endif;?>
						<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($wpem_key); ?>">
							<h2 class="wpem-form-title wpem-heading-text"><?php esc_html_e('Organizer Details', 'wp-event-manager'); ?></h2>
							<label for="<?php echo esc_attr($wpem_key); ?>">
								<?php echo esc_html(isset($wpem_field['label']) ? $wpem_field['label'] : '', 'wp-event-manager');
								echo wp_kses_post(apply_filters('submit_event_form_required_label', isset($wpem_field['required']) && $wpem_field['required'] == 1 ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', isset($wpem_field['required']) ? $wpem_field['required'] : '')); ?>
							</label>
							<div class="field <?php echo (isset($wpem_field['required']) && $wpem_field['required'] == 1 ? 'required-field' : ''); ?>">
								<?php $wpem_field_type = in_array($wpem_field['type'], $wpem_allowed_field_types, true) ? $wpem_field['type'] : 'text';
								wpem_get_event_manager_template('form-fields/' . $wpem_field_type . '-field.php', array('wpem_key' => $wpem_key, 'wpem_field' => $wpem_field)); ?>
							</div>
						</fieldset>
					<?php endforeach;
					do_action('submit_event_form_organizer_fields_end'); 
				endif;
			endif;
		endif; ?>

		<!-- Venue Information Fields -->
		<?php if(!get_option('event_manager_hide_frontend_venue')) :
			if(get_option('enable_event_venue')) :
				if($wpem_venue_fields) :
					do_action('submit_event_form_venue_fields_start'); 
					foreach($wpem_venue_fields as $wpem_key => $wpem_field) : 
						if(isset($wpem_field['visibility']) && ($wpem_field['visibility'] == 0 || $wpem_field['visibility'] == false)) :
							continue;
						endif;?>
						<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($wpem_key); ?>">
							<h2 class="wpem-form-title wpem-heading-text"><?php esc_html_e('Venue Details', 'wp-event-manager'); ?></h2>
							<label for="<?php echo esc_attr($wpem_key); ?>">
								<?php echo esc_html(isset($wpem_field['label']) ? $wpem_field['label'] : '', 'wp-event-manager');
								echo wp_kses_post(apply_filters('submit_event_form_required_label', isset($wpem_field['required']) && $wpem_field['required'] == 1 ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', isset($wpem_field['required']) ? $wpem_field['required'] : '')); ?>
							</label>
							<div class="field <?php echo (isset($wpem_field['required']) && $wpem_field['required'] == 1 ? 'required-field' : ''); ?>">
								<?php $wpem_field_type = in_array($wpem_field['type'], $wpem_allowed_field_types, true) ? $wpem_field['type'] : 'text';
								wpem_get_event_manager_template('form-fields/' . $wpem_field_type . '-field.php', array('wpem_key' => $wpem_key, 'wpem_field' => $wpem_field)); ?>
							</div>
						</fieldset>
					<?php endforeach;
					do_action('submit_event_form_venue_fields_end');
				endif;
			endif;
		endif; ?>

		<div class="wpem-form-footer">
			<input type="hidden" name="event_manager_form" value="<?php echo esc_attr($form); ?>" />
			<input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>" />
			<input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
			<?php wp_nonce_field('edit-event_' . $event_id, '_wpnonce'); ?>
			<input type="submit" name="submit_event" class="wpem-theme-button" value="<?php echo esc_attr($submit_button_text); ?>" />
		</div>
	<?php else :
		do_action('submit_event_form_disabled');
	endif; ?>
</form>

<?php if(get_option('enable_event_organizer')) : 
	$wpem_organizer_fields =	$GLOBALS['event_manager']->forms->get_fields('submit-organizer');
	if(is_user_logged_in()) {
		$current_user = wp_get_current_user();
		if(isset($wpem_organizer_fields['organizer']['organizer_name']))
			$wpem_organizer_fields['organizer']['organizer_name']['value'] =  $current_user->display_name;
		if(isset($wpem_organizer_fields['organizer']['organizer_email']))
			$wpem_organizer_fields['organizer']['organizer_email']['value'] =  $current_user->user_email;
	} ?>

	<div id="wpem_add_organizer_popup" class="wpem-modal" role="dialog" aria-labelledby="<?php echo esc_attr__('Add Organizer', 'wp-event-manager'); ?>">
		<div class="wpem-modal-content-wrapper">
			<div class="wpem-modal-header">
				<div class="wpem-modal-header-title">
					<h3 class="wpem-modal-header-title-text"><?php esc_html_e('Add Organizer', 'wp-event-manager'); ?></h3>
				</div>
				<div class="wpem-modal-header-close"><a href="javascript:void(0)" class="wpem-modal-close" id="wpem-modal-close">x</a></div>
			</div>
			<div class="wpem-modal-content">
				<form method="post" id="submit-organizer-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
					<h2 class="wpem-form-title wpem-heading-text"><?php esc_html_e('Organizer Details', 'wp-event-manager'); ?></h2>

					<?php do_action('submit_organizer_form_organizer_fields_start'); ?>

					<?php foreach($wpem_organizer_fields['organizer'] as $wpem_key => $wpem_field) : 
						if(isset($wpem_field['visibility']) && ($wpem_field['visibility'] == 0 || $wpem_field['visibility'] == false)) :
							continue;
						endif;?>
						<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($wpem_key); ?>">
							<label for="<?php echo esc_attr($wpem_key, 'wp-event-manager'); ?>">
							<?php echo esc_html($wpem_field['label'], 'wp-event-manager');
							 	echo wp_kses_post(apply_filters('submit_event_form_required_label', $wpem_field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $wpem_field)); ?>
							</label>
							<div class="field <?php echo esc_attr($wpem_field['required'] ? 'required-field' : ''); ?>">
								<?php $wpem_field_type = in_array($wpem_field['type'], $wpem_allowed_field_types, true) ? $wpem_field['type'] : 'text';
								wpem_get_event_manager_template('form-fields/' . $wpem_field_type . '-field.php', array('wpem_key' => $wpem_key, 'wpem_field' => $wpem_field)); ?>
							</div>
						</fieldset>
					<?php endforeach;
					do_action('wpem_submit_organizer_form_organizer_fields_end'); ?>

					<div class="wpem-form-footer">
						<?php wp_nonce_field( 'wpem_add_organizer_action', 'wpem_add_organizer_nonce' ); ?>
						<input type="hidden" name="organizer_id" value="0">
						<input type="hidden" name="step" value="0">
						<input type="button" name="submit_organizer" class="wpem-theme-button wpem_add_organizer" value="<?php esc_html_e('Add Organizer', 'wp-event-manager'); ?>" />
						<div id="oragnizer_message"></div>
					</div>
				</form>
			</div>
		</div>
		<a href="#">
			<div class="wpem-modal-overlay"></div>
		</a>
	</div>
<?php endif;

if(get_option('enable_event_venue')) :

	$GLOBALS['event_manager']->forms->get_form('submit-venue', array());
	$wpem_form_submit_venue_instance = call_user_func(array('WPEM_Event_Manager_Form_Submit_Venue', 'instance'));
	$wpem_venue_fields =	$wpem_form_submit_venue_instance->wpem_merge_with_custom_fields('backend'); ?>

	<div id="wpem_add_venue_popup" class="wpem-modal" role="dialog" aria-labelledby="<?php echo esc_attr__('Add Venue', 'wp-event-manager'); ?>">
		<div class="wpem-modal-content-wrapper">
			<div class="wpem-modal-header">
				<div class="wpem-modal-header-title">
					<h3 class="wpem-modal-header-title-text">
						<?php esc_html_e('Add Venue', 'wp-event-manager'); ?>
					</h3>
				</div>
				<div class="wpem-modal-header-close">
					<a href="javascript:void(0)" class="wpem-modal-close" id="wpem-modal-close">x</a>
				</div>
			</div>
			<div class="wpem-modal-content">
				<form method="post" id="submit-venue-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
					<h2 class="wpem-form-title wpem-heading-text">
						<?php esc_html_e('Venue Details', 'wp-event-manager'); ?>
					</h2>

					<?php do_action('submit_venue_form_venue_fields_start');
					foreach($wpem_venue_fields['venue'] as $wpem_key => $wpem_field) : 
						if(isset($wpem_field['visibility']) && ($wpem_field['visibility'] == 0 || $wpem_field['visibility'] = false)) :
							continue;
						endif; ?>
						<fieldset class="wpem-form-group fieldset-<?php echo esc_attr($wpem_key); ?>">
							<label for="<?php echo esc_attr($wpem_key, 'wp-event-manager'); ?>">
								<?php echo esc_html($wpem_field['label'], 'wp-event-manager');
								echo wp_kses_post(apply_filters('submit_event_form_required_label', $wpem_field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $wpem_field)); ?>
							</label>
							
							<div class="field <?php echo esc_attr($wpem_field['required'] ? 'required-field' : ''); ?>">
								<?php $wpem_field_type = in_array($wpem_field['type'], $wpem_allowed_field_types, true) ? $wpem_field['type'] : 'text';
								wpem_get_event_manager_template('form-fields/' . $wpem_field_type . '-field.php', array('wpem_key' => $wpem_key, 'wpem_field' => $wpem_field)); ?>
							</div>
						</fieldset>
					<?php endforeach;
					do_action('submit_venue_form_venue_fields_end'); ?>

					<div class="wpem-form-footer">
						<?php wp_nonce_field( 'wpem_add_venue_action', 'wpem_add_venue_nonce' ); ?>
						<input type="hidden" name="venue_id" value="0">
						<input type="hidden" name="step" value="0">
						<input type="button" name="submit_venue" class="wpem-theme-button wpem_add_venue" value="<?php esc_html_e('Add Venue', 'wp-event-manager'); ?>" />
						<div id="venue_message"></div>
					</div>
				</form>
			</div>
		</div>
		<a href="#">
			<div class="wpem-modal-overlay"></div>
		</a>
	</div>
<?php endif; ?>