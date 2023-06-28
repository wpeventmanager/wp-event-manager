<?php
/**
 * Only load these if Polylang plugin is installed and active.
 *
 * Load routines only if Polylang is loaded.
 *
 * @since 1.6
 */
function polylang_event_manager_init() {
	add_filter('wpem_lang', 'polylang_event_manager_get_event_listings_lang');
	add_filter('event_manager_page_id', 'polylang_event_manager_page_id');
}
add_action('pll_init', 'polylang_event_manager_init');

/**
 * Returns Polylang's current language.
 *
 * @since 1.6
 * @param string $lang
 * @return string
 */
function polylang_event_manager_get_event_listings_lang($lang) {
	if (function_exists('pll_current_language')
	     && function_exists('pll_is_translated_post_type')
	     && pll_is_translated_post_type('event_listing')) {
		return pll_current_language();
	}
	return $lang;
}

/**
 * Returns the page ID for the current language.
 *
 * @since 1.6
 *
 * @param int $page_id
 * @return int
 */
function polylang_event_manager_page_id($page_id) {
	if (function_exists('pll_get_post')) {
		$page_id = pll_get_post($page_id);
	}
	return absint($page_id);
}