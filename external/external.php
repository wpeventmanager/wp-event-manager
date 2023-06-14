<?php
/**
 * Load external compatibility tweaks.
 */

// Load external files
require_once('wpml.php');
require_once('polylang.php');
require_once('all-in-one-seo-pack.php');
require_once('jetpack.php');
require_once('yoast.php');

//load file for visual composer custom element of shortcode
require_once('visual-composer/index.php');

//check Elementor Plugin istallation
if(!function_exists('_is_elementor_installed')) {
	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset($installed_plugins[ $file_path ]);
	}
}

if(!function_exists('is_plugin_active')){
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

require_once('elementor.php');