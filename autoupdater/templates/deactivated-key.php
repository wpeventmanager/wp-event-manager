<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<div class="notice notice-error">
    <?php // translators: %s: plugin name ?>
    <p>
        <?php 
        printf(
            wp_kses(
                __('Your licence for <strong>%s</strong> has been deactivated.', 'wp-event-manager'),
                array(
                    'strong' => array(), // Allow only <strong> tags
                )
            ),
            esc_html( $plugin_name )
        );
        ?>
    </p>
</div>