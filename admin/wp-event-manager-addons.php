<?php
/**
 * Addons Page
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WP_Event_Manager_Addons' ) ) :

/**
 * WP_Event_Manager_Addons Class
*/

class WP_Event_Manager_Addons {

	/**
	 * Handles output of the reports page in admin.
	 */

	public function output() {

		if ( false === ( $addons = get_transient( 'wp_event_manager_addons_html' ) ) ) {

			$raw_addons = wp_remote_get(
					'http://www.wp-eventmanager.com/plugins',
					array(
							'timeout'     => 10,
							'redirection' => 5,
							'sslverify'   => false
					)
					);

			if ( ! is_wp_error( $raw_addons ) ) {

				$raw_addons = wp_remote_retrieve_body( $raw_addons );

				// Get Products
				$dom = new DOMDocument();
				libxml_use_internal_errors(true);
				$dom->loadHTML( $raw_addons );

				$xpath  = new DOMXPath( $dom );
				$tags   = $xpath->query('//ul[@class="products columns-4"]');
				foreach ( $tags as $tag ) {
					$addons = $tag->ownerDocument->saveXML( $tag );
					break;
				}

				$addons = wp_kses_post( $addons );

				if ( $addons ) {
					set_transient( 'wp_event_manager_addons_html1', $addons, 60*60*24*7 ); // Cached for a week
				}
			}
		}

		?>
		<div class="wrap wp_event_manager wp_event_manager_addons_wrap">
				<h2><?php _e( 'WP Event Manager Add-ons', 'wp-event-manager' ); ?></h2>

			<?php echo $addons; ?>
		</div>
		<?php
	}
}
endif;
return new WP_Event_Manager_Addons();