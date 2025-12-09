<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<div class="updated">
    <?php // translators: %s: plugin name ?>
    <p>
        <?php 
        printf(
            wp_kses(
                __('Your licence for <strong>%s</strong> has been activated. Thanks!', 'wp-event-manager'),
                array(
                    'strong' => array(), // Allow <strong> tags
                )
            ),
            esc_html( $plugin_name )
        );
        ?>
    </p>
</div>