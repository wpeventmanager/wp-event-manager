<?php
/**
 * Listing instance stack and markup helpers (pairs event-listings-start / event-listings-end).
 *
 * @package wp-event-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reference to the static stack for the current request.
 *
 * @return array
 */
function &wpem_listing_instance_stack_ref() {
	static $stack = array();
	return $stack;
}

/**
 * Push when opening the listing wrapper (event-listings-start).
 *
 * @param string $listing_instance_id Sanitized instance key.
 */
function wpem_listing_instance_stack_push( $listing_instance_id ) {
	$stack = &wpem_listing_instance_stack_ref();
	$stack[] = $listing_instance_id;
}

/**
 * Pop when closing the listing wrapper (event-listings-end).
 */
function wpem_listing_instance_stack_pop() {
	$stack = &wpem_listing_instance_stack_ref();
	if ( ! empty( $stack ) ) {
		array_pop( $stack );
	}
}

/**
 * Output opening wrapper div for one listing instance.
 *
 * @param string $listing_instance_id Sanitized instance key.
 */
function wpem_echo_listing_instance_wrapper_open( $listing_instance_id ) {
	echo '<div class="wpem-event-listing-instance" data-wpem-listing-instance="' . esc_attr( $listing_instance_id ) . '">';
}

/**
 * Output the main event listings container opening tag.
 *
 * @param string $listing_instance_id Sanitized instance key.
 * @param string $wpem_list_type_class  Layout CSS classes.
 * @param string $layout_type           Layout type attribute value.
 */
function wpem_echo_listing_view_container_open( $listing_instance_id, $wpem_list_type_class, $layout_type ) {
	echo '<div id="wpem-el-view-' . esc_attr( $listing_instance_id ) . '" class="wpem-main wpem-event-listings event_listings wpem-event-listing-view-root ' . esc_attr( $wpem_list_type_class ) . '" data-id="' . esc_attr( $layout_type ) . '">';
}

/**
 * Output a listing section title (escaped).
 *
 * @param string $title Section heading text.
 */
function wpem_echo_listing_section_title( $title ) {
	echo esc_html( $title );
}
