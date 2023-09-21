<?php
/**
 * Adds additional compatibility with Yoast SEO.
 */
// Yoast SEO will by default include the `event_listing` post type because it is flagged as public.
/**
 * Skip filled event listings.
 *
 * @param array  $url  Array of URL parts.
 * @param string $type URL type.
 * @param object $user Data object for the URL.
 * @return string|bool False if we're skipping
 */
function event_manager_yoast_skip_cancelled_event_listings( $url, $type, $post ) {
	if ( 'event_listing' !== $post->post_type ) {
		return $url;
	}
	if ( is_event_cancelled( $post ) ) {
		return false;
	}
	return $url;
}
add_action( 'wpseo_sitemap_entry', 'event_manager_yoast_skip_cancelled_event_listings', 10, 3 );