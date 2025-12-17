<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} ?>
<div class="notice notice-error">
    <p>
        <?php 
        printf(
            wp_kses(
                // translators: %s: plugin name
                __('Your licence for <strong>%s</strong> has been deactivated.', 'wp-event-manager'),
                array(
                    'strong' => array(), // Allow only <strong> tags
                )
            ),
            esc_html( $plugin_name )
        ); ?>
    </p>
</div>