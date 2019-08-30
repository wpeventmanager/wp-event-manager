<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Event_Manager_Geocode
 *
 * Obtains Geolocation data for posted events from Google.
 */

class WP_Event_Manager_Geocode {
	
	const GOOGLE_MAPS_GEOCODE_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';
	
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  2.5
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  2.5
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/**
	 * Constructor
	 */
	
	public function __construct() {
		add_filter( 'event_manager_geolocation_endpoint', array( $this, 'add_geolocation_endpoint_query_args' ), 0, 2 );
		add_filter( 'event_manager_geolocation_api_key', array( $this, 'get_google_maps_api_key' ), 0 );
		add_action( 'event_manager_update_event_data', array( $this, 'update_location_data' ), 20, 2 );
		add_action( 'event_manager_event_location_edited', array( $this, 'change_location_data' ), 20, 2 );
	}
	
	/**
	 * Update location data - when submitting a event
	 */
	
	public function update_location_data( $event_id, $values ) {
		
		if ( apply_filters( 'event_manager_geolocation_enabled', true ) && isset( $values['event']['event_location'] ) ) {
			
			$address_data = self::get_location_data( $values['event']['event_location'] );
			
			self::save_location_data( $event_id, $address_data );
		}
	}
	
	/**
	 * Change a events location data upon editing
	 * @param  int $event_id
	 * @param  string $new_location
	 */
	
	public function change_location_data( $event_id, $new_location ) {
		
		if ( apply_filters( 'event_manager_geolocation_enabled', true ) ) {
			
			$address_data = self::get_location_data( $new_location );
			
			self::clear_location_data( $event_id );
			
			self::save_location_data( $event_id, $address_data );
		}
	}
	
	/**
	 * Checks if a event has location data or not
	 * @param  int  $event_id
	 * @return boolean
	 */
	
	public static function has_location_data( $event_id ) {
		
		return get_post_meta( $event_id, 'geolocated', true ) == 1;
	}
	
	/**
	 * Called manually to generate location data and save to a post
	 * @param  int $event_id
	 * @param  string $location
	 */
	
	public static function generate_location_data( $event_id, $location ) {
		
		$address_data = self::get_location_data( $location );
		
		self::save_location_data( $event_id, $address_data );
	}
	
	/**
	 * Delete a event's location data
	 * @param  int $event_id
	 */
	
	public static function clear_location_data( $event_id ) {
		
		delete_post_meta( $event_id, 'geolocated' );
		
		delete_post_meta( $event_id, 'geolocation_city' );
		
		delete_post_meta( $event_id, 'geolocation_country_long' );
		
		delete_post_meta( $event_id, 'geolocation_country_short' );
		
		delete_post_meta( $event_id, 'geolocation_formatted_address' );
		
		delete_post_meta( $event_id, 'geolocation_lat' );
		
		delete_post_meta( $event_id, 'geolocation_long' );
		
		delete_post_meta( $event_id, 'geolocation_state_long' );
		
		delete_post_meta( $event_id, 'geolocation_state_short' );
		
		delete_post_meta( $event_id, 'geolocation_street' );
		
		delete_post_meta( $event_id, 'geolocation_street_number' );
		
		delete_post_meta( $event_id, 'geolocation_zipcode' );
		
		delete_post_meta( $event_id, 'geolocation_postcode' );
	}
	
	/**
	 * Save any returned data to post meta
	 * @param  int $event_id
	 * @param  array $address_data
	 */
	
	public static function save_location_data( $event_id, $address_data ) {
		
		if ( ! is_wp_error( $address_data ) && $address_data ) {
			
			foreach ( $address_data as $key => $value ) {
				
				if ( $value ) {
					
					update_post_meta( $event_id, 'geolocation_' . $key, $value );
				}
			}
			
			update_post_meta( $event_id, 'geolocated', 1 );
		}
	}
	
	/**
	 * Retrieves the Google Maps API key from the plugin's settings.
	 *
	 * @param  string $key
	 * @return string
	 */
	public function get_google_maps_api_key( $key ) {
		return get_option( 'event_manager_google_maps_api_key' );
	}

	/**
	 * Adds the necessary query arguments for a Google Maps Geocode API request.
	 *
	 * @param  string $geocode_endpoint_url
	 * @param  string $raw_address
	 * @return string|bool
	 */
	public function add_geolocation_endpoint_query_args( $geocode_endpoint_url, $raw_address ) {
		// Add an API key if available.
		$api_key = apply_filters( 'event_manager_geolocation_api_key', '', $raw_address );

		if ( '' !== $api_key ) {
			$geocode_endpoint_url = add_query_arg( 'key', rawurlencode( $api_key ), $geocode_endpoint_url );
		}

		$geocode_endpoint_url = add_query_arg( 'address', rawurlencode( $raw_address ), $geocode_endpoint_url );

		$locale = get_locale();
		if ( $locale ) {
			$geocode_endpoint_url = add_query_arg( 'language', substr( $locale, 0, 2 ), $geocode_endpoint_url );
		}

		$region = apply_filters( 'event_manager_geolocation_region_cctld', '', $raw_address );
		if ( '' !== $region ) {
			$geocode_endpoint_url = add_query_arg( 'region', rawurlencode( $region ), $geocode_endpoint_url );
		}

		return $geocode_endpoint_url;
	}

	/**
	 * Get Location Data from Google
	 *
	 * @param string $raw_address
	 * @return array location data
	 */
	
	public static function get_location_data( $raw_address ) {
		
		$invalid_chars = array( " " => "+", "," => "", "?" => "", "&" => "", "=" => "" , "#" => "" );
		
		$raw_address   = trim( strtolower( str_replace( array_keys( $invalid_chars ), array_values( $invalid_chars ), $raw_address ) ) );
		
		if ( empty( $raw_address ) ) {
			
			return false;
		}
		
		$transient_name              = 'em_geocode_' . md5( $raw_address );
		
		$geocoded_address            = get_transient( $transient_name );
		
		$em_geocode_over_query_limit = get_transient( 'em_geocode_over_query_limit' );
		
		// Query limit reached - don't geocode for a while
		
		if ( $em_geocode_over_query_limit && false === $geocoded_address ) {
			
			return false;
		}
		
		try {
			
			if ( false === $geocoded_address || empty( $geocoded_address->results[0] ) ) {
				
				$result = wp_remote_get(
						
						apply_filters( 'event_manager_geolocation_endpoint',  self::GOOGLE_MAPS_GEOCODE_API_URL."?address=" . $raw_address . "&sensor=false&region=" . apply_filters( 'event_manager_geolocation_region_cctld', '', $raw_address ), $raw_address ),
						
						array(
								
								'timeout'     => 5,
								
								'redirection' => 1,
								
								'httpversion' => '1.1',
								
								'user-agent'  => 'WordPress/WP-Event-Manager-' . EVENT_MANAGER_VERSION . '; ' . get_bloginfo( 'url' ),
								
								'sslverify'   => false
						)
						);
				
				$result           = wp_remote_retrieve_body( $result );
				
				$geocoded_address = json_decode( $result );
				
				if ( $geocoded_address->status ) {
					
					switch ( $geocoded_address->status ) {
						
						case 'ZERO_RESULTS' :
							
							throw new Exception( __( "No results found", 'wp-event-manager' ) );
							
							break;
							
						case 'OVER_QUERY_LIMIT' :
							
							set_transient( 'em_geocode_over_query_limit', 1, HOUR_IN_SECONDS );
							
							throw new Exception( __( "Query limit reached", 'wp-event-manager' ) );
							
							break;
						case 'REQUEST_DENIED' :
							throw new Exception( __( "Request denied from google map api key please enable geolocation and gecoding api", 'wp-event-manager' ) );
							break;
							
						case 'OK' :
							
							if ( ! empty( $geocoded_address->results[0] ) ) {
								
								set_transient( $transient_name, $geocoded_address, 24 * HOUR_IN_SECONDS * 365 );
								
							} else {
								
								throw new Exception( __( "Geocoding error", 'wp-event-manager' ) );
							}
							
							break;
							
						default :
							
							throw new Exception( __( "Geocoding error", 'wp-event-manager' ) );
							
							break;
					}
					
				} else {
					
					throw new Exception( __( "Geocoding error", 'wp-event-manager' ) );
				}
			}
			
		} catch ( Exception $e ) {
			
			return new WP_Error( 'error', $e->getMessage() );
		}
		
		$address                      = array();
		
		$address['lat']               = sanitize_text_field( $geocoded_address->results[0]->geometry->location->lat );
		
		$address['long']              = sanitize_text_field( $geocoded_address->results[0]->geometry->location->lng );
		
		$address['formatted_address'] = sanitize_text_field( $geocoded_address->results[0]->formatted_address );
		
		if ( ! empty( $geocoded_address->results[0]->address_components ) ) {
			
			$address_data             = $geocoded_address->results[0]->address_components;
			
			$address['street_number'] = false;
			
			$address['street']        = false;
			
			$address['city']          = false;
			
			$address['state_short']   = false;
			
			$address['state_long']    = false;
			
			$address['postcode']      = false;
			
			$address['country_short'] = false;
			
			$address['country_long']  = false;
			
			foreach ( $address_data as $data ) {
				
				switch ( $data->types[0] ) {
					
					case 'street_number' :
						
						$address['street_number'] = sanitize_text_field( $data->long_name );
						
						break;
						
					case 'route' :
						
						$address['street']        = sanitize_text_field( $data->long_name );
						
						break;
						
					case 'sublocality_level_1' :
						
					case 'locality' :
						
					case 'postal_town' :
						
						$address['city']          = sanitize_text_field( $data->long_name );
						
						break;
						
					case 'administrative_area_level_1' :
						
					case 'administrative_area_level_2' :
						
						$address['state_short']   = sanitize_text_field( $data->short_name );
						
						$address['state_long']    = sanitize_text_field( $data->long_name );
						
						break;
						
					case 'postal_code' :
						
						$address['postcode']      = sanitize_text_field( $data->long_name );
						
						break;
						
					case 'country' :
						
						$address['country_short'] = sanitize_text_field( $data->short_name );
						
						$address['country_long']  = sanitize_text_field( $data->long_name );
						
						break;
				}
			}
		}
		
		return apply_filters( 'event_manager_geolocation_get_location_data', $address, $geocoded_address );
	}
}
WP_Event_Manager_Geocode::instance();