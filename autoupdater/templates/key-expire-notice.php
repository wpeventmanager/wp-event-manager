<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( get_option( $plugin_slug . '_key_expire_pre' ) ) : 
    $wpem_expire_date = get_option( $plugin_slug . '_licence_expired' );
    $wpem_expire_date = new DateTime( $wpem_expire_date );
    $wpem_expire_date = $wpem_expire_date->format( 'jS F, Y' ); ?>
    <div class="updated">
        <p class="wpem-updater-dismiss" style="float:right;">
            <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'dismiss-key-expire-' . sanitize_title( $plugin_slug ) => '1' ) ), 'wpem_licence_notice_action' ) ); ?>">
                <?php esc_html_e( 'Hide notice', 'wp-event-manager' ); ?>
            </a>
        </p>
        <p>
            <?php printf(
                wp_kses(
                    /* translators: %1$s: plugin name, %2$s: license expiration date */
                    __( 'A licence key for <strong>"%1$s"</strong> will expire on <strong>%2$s</strong>. Please renew your subscription to continue the plugin work.', 'wp-event-manager' ),
                    array(
                        'strong' => array(),
                    )
                ),
                esc_html( $plugin_name ),
                esc_html( $wpem_expire_date )
            ); ?>
        </p>
    </div>

<?php else: ?>
    <div class="updated">
        <?php $wpem_plugin_url = get_admin_url() . 'plugins.php' . '#' . sanitize_title( $plugin_slug . '_licence_key_row' ); ?>
        <p class="wpem-updater-dismiss" style="float:right;">
            <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'dismiss-key-expire-' . sanitize_title( $plugin_slug ) => '1' ) ), 'wpem_licence_notice_action' ) ); ?>">
                <?php esc_html_e( 'Hide notice', 'wp-event-manager' ); ?>
            </a>
        </p>
        <p>
            <?php printf(
                wp_kses(
                    /* translators: %1$s: plugin name */
                    __('A licence key for <strong>"%1$s"</strong> is expired. Please renew your subscription to continue the plugin work.', 'wp-event-manager'),
                    array(
                        'strong' => array(),
                    )
                ),
                esc_html( $wpem_plugin_url )
            ); ?>
        </p>
    </div>
<?php endif; ?>