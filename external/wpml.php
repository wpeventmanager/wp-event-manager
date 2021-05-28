<?php
/**
 * Only load these if WPML plugin is installed and active.
 */

/**
 * Load routines only if WPML is loaded.
 *
 * @since 1.6
 */
function wpml_event_manager_init() {
	add_action( 'get_event_listings_init', 'wpml_event_manager_set_language' );
	add_filter( 'wpem_lang', 'wpml_event_manager_get_event_listings_lang' );
	add_filter( 'event_manager_page_id', 'wpml_event_manager_page_id' );
}
add_action( 'wpml_loaded', 'wpml_event_manager_init' );
add_action( 'wpml_loaded', 'wpml_event_manager_set_language' );

/**
 * Sets Event Manager's language if it is sent in the Ajax request.
 *
 * @since 1.6
 */
function wpml_event_manager_set_language() {

	$input_lang = isset( $_POST['lang'] ) ? sanitize_text_field( wp_unslash( $_POST['lang'] ) ) : false;

	if ( isset( $_SERVER['REQUEST_URI'] ) && ( strstr( $_SERVER['REQUEST_URI'], '/em-ajax/' ) || ! empty( $_GET['em-ajax'] ) ) && $input_lang)  {
		do_action( 'wpml_switch_language', sanitize_text_field( $_POST['lang'] ) );
	}
}


/**
 * Returns WPML's current language.
 *
 * @since 1.6
 *
 * @param string $lang
 * @return string
 */
function wpml_event_manager_get_event_listings_lang( $lang ) {
	return apply_filters( 'wpml_current_language', $lang );
}

/**
 * Returns the page ID for the current language.
 *
 * @param int $page_id
 * @return int
 */
function wpml_event_manager_page_id( $page_id ) {
	return apply_filters( 'wpml_object_id', $page_id, 'page', true );
}
