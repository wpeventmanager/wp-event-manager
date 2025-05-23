<?php 
//if user loggedin then display message and logout link else display login link
if (is_user_logged_in()) : ?>
	<div class="wpem-form-group ">
		<label class="wpem-form-label-text"><?php esc_attr_e('Your account', 'wp-event-manager'); ?></label>
		<div class="field account-sign-in wpem-alert wpem-alert-info"> <?php $user = wp_get_current_user();
			// translators: %s is the username of the signed-in user.
			printf(wp_kses(__('You are currently signed in as <strong>%s</strong>.', 'wp-event-manager'), array('strong' => array())), esc_attr($user->user_login)); ?>
			 <a href="<?php echo esc_url(apply_filters('submit_event_form_logout_url', esc_url(wp_logout_url(get_permalink())))); ?>">
			 	<?php esc_html_e('Sign out', 'wp-event-manager'); ?>
			</a>
		</div>
	</div>
<?php else :
	$account_required             = event_manager_user_requires_account();
	$registration_enabled         = event_manager_enable_registration();
	$registration_fields          = wp_event_manager_get_registration_fields();
	$generate_username_from_email = event_manager_generate_username_from_email();	?>
	<div class="wpem-form-group">
		<label class="wpem-form-label-text"><?php esc_html_e('Have an account?', 'wp-event-manager'); ?></label>
		<div class="field account-sign-in wpem-alert wpem-alert-info">
			<a href="<?php echo !empty(get_option('event_manager_login_page_url')) ? esc_url(apply_filters('submit_event_form_login_url', get_option('event_manager_login_page_url'))) : 	esc_url(home_url() . '/wp-login.php'); ?>"><?php esc_html_e('Sign in', 'wp-event-manager'); ?></a>
			<?php if ($registration_enabled) : ?>
				<?php esc_html_e('If you don&rsquo;t have an account with us, just enter your email address and create a new one.  You will receive your password shortly in your email.', 'wp-event-manager');
				 $account_required ? '' : __('(optional)', 'wp-event-manager'); ?>
			<?php elseif ($account_required) : ?>
				<?php echo  wp_kses_post(apply_filters('submit_event_form_login_required_message',  __(' You must sign in to create a new listing.', 'wp-event-manager'))); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php if ($registration_enabled) :
		if (!empty($registration_fields)) {
			foreach ($registration_fields as $key => $field) { ?>
				<div class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
					<label class="wpem-form-label-text" for="<?php echo esc_attr($key); ?>"><?php        echo esc_html($field['label']) . wp_kses_post(apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field)); 
 ?></label>
					<div class="field <?php echo esc_attr($field['required']) ? 'required-field' : ''; ?>">
						<?php get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key'   => $key, 'field' => $field)); ?>
					</div>
				</div>
		<?php	}
			do_action('event_manager_register_form');
		}
	endif;
endif; ?>