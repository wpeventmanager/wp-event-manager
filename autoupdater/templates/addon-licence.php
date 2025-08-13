<div class="licence-row">
	<div class="plugin-info"><?php echo esc_html( $plugin['Title'] ); ?>
		<div class="plugin-author">
			<a target="_blank" href="https://wp-eventmanager.com/"><?php echo esc_html($plugin['Author']); ?></a>				
		</div>
	</div>

	<div class="plugin-licence">
		<form method="post">
			<label for="<?php echo esc_attr( $plugin['TextDomain'] ); ?>_licence_key"><?php esc_html_e('License', 'wp-event-manager'); ?>
				<input <?php echo esc_attr( $disabled ); ?> type="text" id="<?php echo esc_attr( $plugin['TextDomain'] ); ?>_licence_key" name="<?php echo esc_attr( $plugin['TextDomain'] ); ?>_licence_key" placeholder="XXXX-XXXX-XXXX-XXXX" value="<?php echo esc_attr( $licence_key ); ?>">
			</label>

			<label for="<?php echo esc_attr( $plugin['TextDomain'] ); ?>_email"><?php esc_html_e('Email', 'wp-event-manager'); ?>
				<input <?php echo esc_attr($disabled); ?> type="email" id="<?php echo esc_attr( $plugin['TextDomain'] ); ?>_email" name="<?php echo esc_attr( $plugin['TextDomain'] ); ?>_email" placeholder="<?php esc_html_e('Email address', 'wp-event-manager'); ?>" value="<?php echo esc_attr( $email ); ?>">
			</label>

			<?php if(!empty($licence_key) ) : ?>
				<a href="<?php echo esc_url( remove_query_arg( array( 'deactivated_licence', 'activated_licence' ), add_query_arg( $plugin['TextDomain'] . '_deactivate_licence', 1 ) ) ); ?>" class="button"><?php esc_html_e('Deactivate License', 'wp-event-manager'); ?></a>
			<?php else : ?>
				<input type="submit" class="button" id="submit_wpem_licence_key" name="submit_wpem_licence_key" value="<?php esc_html_e('Activate License', 'wp-event-manager'); ?>">
			<?php endif ; ?>
		</form>
	</div>
</div>