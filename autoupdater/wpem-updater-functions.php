<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/** 
* Get all WP Event Manager plugin info
*
* @since 3.2.1
*/
if (!function_exists('get_wpem_plugins_info')) {
	function get_wpem_plugins_info() {
		$plugins_info = array();
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins(); 
		
		foreach ($plugins as $filename => $plugin) {
			if ($plugin['AuthorName'] == 'WP Event Manager' && is_plugin_active($filename) && !in_array($plugin['TextDomain'], ["wp-event-manager", "wpem-rest-api"])) {
				$plugin_info = array();
				$plugin_info['Name'] = $plugin['Name'];
				$plugin_info['TextDomain'] = $plugin['TextDomain'];
				$plugin_info['plugin_files'] = $filename;
				$plugin_info['Version'] = $plugin['Version'];
				$plugin_info['Title'] = $plugin['Title'];
				$plugin_info['AuthorName'] = $plugin['AuthorName'];
				array_push($plugins_info, $plugin_info);
			}
		}
		return $plugins_info;
	}
} ?>