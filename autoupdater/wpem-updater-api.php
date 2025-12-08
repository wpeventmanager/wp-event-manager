<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPEM_Updater_API.
 *
 * @version 1.0
 * @author  WPEM Team
 */
class WPEM_Updater_API {
	private static $api_url;

	/**
	 * This functio is used to get wpem api url
	 */
	private static function get_api_url() {
		if ( null === self::$api_url ) {
			self::$api_url = esc_url( get_option( 'wp_event_manager_store_url' ) . '?wc-api=wpemstore_licensing_update_api' );
		}

		return self::$api_url;
	}

	/**
	 * Sends and receives data to and from the server API.
	 * @return object $response
	 */
	public static function plugin_update_check( $args ) {
		$defaults = array(
			'request'        => 'pluginupdatecheck',
			'instance'       => site_url(),
			'plugin_name'    => '',
			'version'        => '',
			'api_product_id' => '',
			'licence_key'    => '',
			'email'          => ''
		);

		$args    = wp_parse_args( $args, $defaults );
		$request = wp_remote_get( self::get_api_url() . '&' . http_build_query( $args, '', '&' ) );
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ),true );
		$response = (object)$response;

		if ( is_object( $response ) ) {
			return $response;
		} else {
			return false;
		}
	}

	/**
	 * Sends and receives data to and from the server API.
	 * @return object $response
	 */
	public static function plugin_information( $args ) {
		$defaults = array(
			'request'        => 'plugininformation',
			'instance'       => site_url(),
			'plugin_name'    => '',
			'version'        => '',
			'api_product_id' => '',
			'licence_key'    => '',
			'email'          => ''
		);

		$args    = wp_parse_args( $args, $defaults );
		$request = wp_remote_get( self::get_api_url() . '&' . http_build_query( $args, '', '&' ) );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ),true );
		$response = (object)$response;

		if ( is_object( $response ) ) {
			return $response;
		} else {
			return false;
		}
	}
}
