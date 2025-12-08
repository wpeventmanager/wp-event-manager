<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPEM_Updater_Key_API.
 *
 * @version 1.0
 * @author  WPEM Team
 */
class WPEM_Updater_Key_API {
	private static $activation_endpoint = '?wc-api=wpemstore_licensing_activation_api';
	private static $deactivation_endpoint = '?wc-api=wpemstore_licensing_deactivation_api';
	
	//Attempt to activate a plugin licence.
	public static function activate( $args ) {
		$defaults = array(
			'request'  => 'activate',
			'instance' => site_url(),
		);

		$args = wp_parse_args( $args, $defaults );

		$response = wp_remote_post( esc_url( get_option( 'wp_event_manager_store_url' ) .self::$activation_endpoint), array(
			'body'    => $args,
			'headers' => array(
				'User-Agent' => 'Mozilla/5.0 (WordPress; Licensing Activation)',
				'Accept'     => 'application/json',
			),
			'timeout' => 20,
		) );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
			return false;
		}

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Attempt to deactivate a plugin licence.
	 */
	public static function deactivate( $args ) {

		$defaults = array(
			'request'  => 'deactivate',
			'instance' => site_url(),
		);

		$args = wp_parse_args( $args, $defaults );

		$response = wp_remote_post( esc_url( get_option( 'wp_event_manager_store_url' ) .self::$deactivation_endpoint), array(
			'body'    => $args,
			'headers' => array(
				'User-Agent' => 'Mozilla/5.0 (WordPress; Licensing Activation)',
				'Accept'     => 'application/json',
			),
			'timeout' => 20,
		) );
		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
			return false;
		}
		return wp_remote_retrieve_body($response);
	}
}