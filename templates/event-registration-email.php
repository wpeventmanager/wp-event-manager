<p>
	<?php printf(__('To register for this event <strong>email your details to</strong> <a class="event_registration_email" href="mailto:%1$s%2$s">%1$s</a>', 'wp-event-manager'), esc_attr($register->email), '?subject=' . rawurlencode($register->subject)); ?>
</p>

<p>
	<?php esc_html_e('Register using webmail: ', 'wp-event-manager'); ?>
	<a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo esc_attr($register->email); ?>&su=<?php echo urlencode($register->subject); ?>" target="_blank" class="event_registration_email">
		<?php esc_html_e('Gmail', 'wp-event-manager'); ?>
	</a> /
	<a href="http://webmail.aol.com/Mail/ComposeMessage.aspx?to=<?php echo esc_attr($register->email); ?>&subject=<?php echo urlencode($register->subject); ?>" target="_blank" class="event_registration_email">
		<?php esc_html_e('AOL', 'wp-event-manager'); ?>
	</a> /
	<a href="http://compose.mail.yahoo.com/?to=<?php echo esc_attr($register->email); ?>&subject=<?php echo urlencode($register->subject); ?>" target="_blank" class="event_registration_email">
		<?php esc_html_e('Yahoo', 'wp-event-manager'); ?>
	</a> /
	<a href="https://outlook.live.com/mail/0/deeplink/compose?to=<?php echo esc_attr($register->email); ?>&subject=<?php echo urlencode($register->subject); ?>" target="_blank" class="event_registration_email">
		<?php esc_html_e('Outlook', 'wp-event-manager'); ?>
	</a>
</p>  