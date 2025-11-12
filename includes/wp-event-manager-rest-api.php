<?php
/**
 * WP Event Manager REST API
 * 
 * Provides REST API endpoints for events, organizers, and venues
 * 
 * @package WP Event Manager
 * @since 3.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WP_Event_Manager_Default_REST_API class
 */
class WP_Event_Manager_Default_REST_API {
    
    /**
     * Namespace for REST API
     */
    const NAMESPACE = 'wpem/v1';
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // License activation endpoint (called from store)
        register_rest_route( self::NAMESPACE, '/license/activate', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'activate_license' ),
            'permission_callback' => '__return_true', // No auth needed - validated by store
            'args'                => array(
                'licence_key'   => array(
                    'description' => __( 'License key', 'wp-event-manager' ),
                    'type'        => 'string',
                    'required'    => true,
                ),
                'email'         => array(
                    'description' => __( 'Customer email', 'wp-event-manager' ),
                    'type'        => 'string',
                    'required'    => true,
                ),
                'text_domain'   => array(
                    'description' => __( 'Plugin text domain', 'wp-event-manager' ),
                    'type'        => 'string',
                    'required'    => true,
                ),
            ),
        ) );
        
        // License deactivation endpoint (called from store)
        register_rest_route( self::NAMESPACE, '/license/deactivate', array(
            'methods'             => array( WP_REST_Server::READABLE, WP_REST_Server::CREATABLE ),
            'callback'            => array( $this, 'deactivate_license' ),
            'permission_callback' => '__return_true', // No auth needed - validated by store
            'args'                => array(
                'licence_key'   => array(
                    'description' => __( 'License key', 'wp-event-manager' ),
                    'type'        => 'string',
                    'required'    => true,
                ),
                'text_domain'   => array(
                    'description' => __( 'Plugin text domain', 'wp-event-manager' ),
                    'type'        => 'string',
                    'required'    => false,
                ),
                'store_url'     => array(
                    'description' => __( 'Store URL', 'wp-event-manager' ),
                    'type'        => 'string',
                    'required'    => false,
                ),
            ),
        ) );
    }
    
    /**
     * Get collection parameters
     */
    private function get_collection_params() {
        return array(
            'page'     => array(
                'description' => __( 'Current page of the collection.', 'wp-event-manager' ),
                'type'        => 'integer',
                'default'     => 1,
            ),
            'per_page' => array(
                'description' => __( 'Maximum number of items to be returned.', 'wp-event-manager' ),
                'type'        => 'integer',
                'default'     => 10,
            ),
            'orderby'  => array(
                'description' => __( 'Sort by attribute.', 'wp-event-manager' ),
                'type'        => 'string',
                'default'     => 'date',
                'enum'        => array( 'date', 'title', 'id' ),
            ),
            'order'    => array(
                'description' => __( 'Order sort attribute.', 'wp-event-manager' ),
                'type'        => 'string',
                'default'     => 'DESC',
                'enum'        => array( 'ASC', 'DESC' ),
            ),
            'status'   => array(
                'description' => __( 'Post status.', 'wp-event-manager' ),
                'type'        => 'string',
                'default'     => 'publish',
            ),
        );
    }
      
    /**
     * Activate license (called from store)
     * Saves license key and email to wp_options
     */
    public function activate_license( $request ) {
        $params = $request->get_json_params();
        
        $licence_key = sanitize_text_field( $params['licence_key'] ?? '' );
        $email = sanitize_email( $params['email'] ?? '' );
        $text_domain = sanitize_text_field( $params['text_domain'] ?? 'wp-event-manager' );
        $store_url = esc_url_raw( $params['store_url'] ?? '' );
        
        // Validate required fields
        if ( empty( $licence_key ) || empty( $email ) ) {
            return rest_ensure_response( array(
                'success' => false,
                'message' => __( 'License key and email are required.', 'wp-event-manager' ),
            ) );
        }
        
        // Save license key to wp_options
        $licence_key_option = $text_domain . '_licence_key';
        $email_option = $text_domain . '_email';
        $status_option = $text_domain . '_licence_status';
        $store_url_option = $text_domain . '_store_url';
        
        update_option( $licence_key_option, $licence_key );
        update_option( $email_option, $email );
        update_option( $status_option, 'active' );
        update_option( $store_url_option, $store_url );
        
        // Log activation
        do_action( 'wpem_license_activated', $licence_key, $email, $text_domain );
        
        return rest_ensure_response( array(
            'success' => true,
            'message' => sprintf( 
                __( 'License activated successfully for %s', 'wp-event-manager' ), 
                $email 
            ),
            'data'    => array(
                'licence_key' => $licence_key,
                'email'       => $email,
                'status'      => 'active',
                'text_domain' => $text_domain,
            ),
        ) );
    }
    
    /**
     * Deactivate license (called from store)
     * Updates license status to inactive or deletes from wp_options
     * Handles both GET query parameters and POST JSON requests
     */
    public function deactivate_license( $request ) {
        // Get parameters from both JSON body and query string
        $params = $request->get_json_params();
        if ( empty( $params ) ) {
            $params = $request->get_query_params();
        }
        
        $licence_key = sanitize_text_field( $params['licence_key'] ?? '' );
        $text_domain = sanitize_text_field( $params['text_domain'] ?? 'wp-event-manager' );
        
        // Log deactivation request for debugging
        error_log( sprintf(
            'WP Event Manager License Deactivation: licence_key=%s, text_domain=%s',
            $licence_key,
            $text_domain
        ) );
        
        // Validate required fields
        if ( empty( $licence_key ) ) {
            error_log( 'License deactivation failed: missing licence_key' );
            return rest_ensure_response( array(
                'success' => false,
                'message' => __( 'License key is required.', 'wp-event-manager' ),
            ) );
        }
        
        // Option names
        $licence_key_option = $text_domain . '_licence_key';
        $email_option = $text_domain . '_email';
        $status_option = $text_domain . '_licence_status';
        $store_url_option = $text_domain . '_store_url';
        
        // Verify the license key matches (if stored)
        $stored_licence_key = get_option( $licence_key_option );
        
        if ( ! empty( $stored_licence_key ) && $stored_licence_key !== $licence_key ) {
            error_log( sprintf(
                'License deactivation failed: key mismatch. Stored: %s, Provided: %s',
                $stored_licence_key,
                $licence_key
            ) );
            return rest_ensure_response( array(
                'success' => false,
                'message' => __( 'License key does not match.', 'wp-event-manager' ),
            ) );
        }
        
        // Update status to inactive and delete the options
        update_option( $status_option, 'inactive' );
        delete_option( $licence_key_option );
        delete_option( $email_option );
        delete_option( $status_option );
        delete_option( $store_url_option );
        
        error_log( sprintf(
            'License deactivated successfully: %s',
            $licence_key
        ) );
        
        // Log deactivation
        do_action( 'wpem_license_deactivated', $licence_key, $text_domain );
        
        return rest_ensure_response( array(
            'success' => true,
            'message' => __( 'License deactivated successfully.', 'wp-event-manager' ),
            'data'    => array(
                'licence_key' => $licence_key,
                'status'      => 'inactive',
                'text_domain' => $text_domain,
            ),
        ) );
    }
    
}

// Initialize REST API
function wpem_default_rest_api_init() {
    new WP_Event_Manager_Default_REST_API();
}
add_action( 'plugins_loaded', 'wpem_default_rest_api_init' );
