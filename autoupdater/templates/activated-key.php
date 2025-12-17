<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="updated">
	<p>
		<?php printf(
			wp_kses(
				/* translators: %s: plugin name */
				__( 'Your licence for <strong>%s</strong> has been activated. Thanks!', 'wp-event-manager' ),
				array(
					'strong' => array(),
				)
			),
			esc_html( $plugin_name )
		); ?>
	</p>
</div>