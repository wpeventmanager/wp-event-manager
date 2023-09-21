<?php
/**
 * Adds additional compatibility with All in One SEO Pack.
 */
/**
 * Skip expired event listings.
 *
 * @param WP_Post[] $posts
 * @return WP_Post[]
 */
function event_manager_aiosp_sitemap_filter_expired_events( $posts ) {
	foreach ( $posts as $index => $post ) {
		if ( $post instanceof WP_Post && 'event_listing' !== $post->post_type ) {
			continue;
		}
		if ( is_event_cancelled( $post ) ) {
			unset( $posts[ $index ] );
		}
	}
	return $posts;
}
add_action( 'aiosp_sitemap_post_filter', 'event_manager_aiosp_sitemap_filter_expired_events', 10, 3 );