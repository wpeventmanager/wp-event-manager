<?php
/**
 * Listing instance stack and markup helpers (pairs event-listings-start / event-listings-end).
 *
 * @package wp-event-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
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
 * Opening wrapper div markup for one listing instance (escaped).
 *
 * @param string $listing_instance_id Sanitized instance key.
 * @return string Safe HTML fragment.
 */
function wpem_listing_instance_wrapper_html( $listing_instance_id ) {
	return '<div class="wpem-event-listing-instance" data-wpem-listing-instance="' . esc_attr( $listing_instance_id ) . '">';
}

/**
 * Main event listings container opening tag (escaped).
 *
 * @param string $listing_instance_id Sanitized instance key.
 * @param string $wpem_list_type_class  Layout CSS classes.
 * @param string $layout_type           Layout type attribute value.
 * @return string Safe HTML fragment.
 */
function wpem_listing_view_container_open_html( $listing_instance_id, $wpem_list_type_class, $layout_type ) {
	return '<div id="wpem-el-view-' . esc_attr( $listing_instance_id ) . '" class="wpem-main wpem-event-listings event_listings wpem-event-listing-view-root ' . esc_attr( $wpem_list_type_class ) . '" data-id="' . esc_attr( $layout_type ) . '">';
}

/**
 * Escaped listing section title text (for use inside heading elements).
 *
 * @param string $title Section heading text.
 * @return string Escaped HTML text.
 */
function wpem_listing_section_title_html( $title ) {
	return esc_html( $title );
}

/**
 * Allowed HTML for listing instance fragments (wp_kses).
 *
 * @return array
 */
function wpem_listing_instance_markup_kses_allowed() {
	return array(
		'div' => array(
			'id'                         => true,
			'class'                      => true,
			'data-wpem-listing-instance' => true,
			'data-id'                    => true,
		),
	);
}
