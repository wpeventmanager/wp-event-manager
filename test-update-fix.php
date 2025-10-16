<?php
/**
 * Test script to verify the update check fix
 * 
 * This script can be used to test if the excessive update check issue is resolved.
 * Run this from WordPress admin or via WP-CLI to monitor update check behavior.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Test function to monitor update check behavior
 */
function wpem_test_update_check_fix() {
    // Enable debug logging temporarily
    $original_debug = defined('WP_DEBUG') ? WP_DEBUG : false;
    if (!defined('WP_DEBUG')) {
        define('WP_DEBUG', true);
    }
    
    echo "<h3>WP Event Manager Update Check Test</h3>";
    
    // Check if transient exists
    $cached_response = get_transient('wpem_bulk_plugin_update_check');
    if ($cached_response !== false) {
        echo "<p><strong>✓ Cache Status:</strong> Update check cache exists (good - prevents duplicate API calls)</p>";
        echo "<p><strong>Cache expires in:</strong> " . human_time_diff(time(), time() + get_option('_transient_timeout_wpem_bulk_plugin_update_check', 0)) . "</p>";
    } else {
        echo "<p><strong>⚠ Cache Status:</strong> No cache found - next update check will make API call</p>";
    }
    
    // Get plugin info
    if (function_exists('get_wpem_plugins_info')) {
        $plugins = get_wpem_plugins_info();
        echo "<p><strong>WPEM Plugins Found:</strong> " . count($plugins) . "</p>";
        
        $licensed_count = 0;
        foreach ($plugins as $plugin) {
            $licence_key = get_option($plugin['TextDomain'] . '_licence_key', '');
            $email = get_option($plugin['TextDomain'] . '_email', '');
            if (!empty($licence_key) && !empty($email)) {
                $licensed_count++;
            }
        }
        echo "<p><strong>Licensed Plugins:</strong> $licensed_count (only these will trigger update checks)</p>";
    }
    
    // Test the hook registration
    global $wp_filter;
    $hook_count = 0;
    if (isset($wp_filter['site_transient_update_plugins'])) {
        foreach ($wp_filter['site_transient_update_plugins']->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                if (is_array($callback['function']) && 
                    is_object($callback['function'][0]) && 
                    get_class($callback['function'][0]) === 'WPEM_Updater' &&
                    $callback['function'][1] === 'check_for_updates') {
                    $hook_count++;
                }
            }
        }
    }
    
    if ($hook_count <= 1) {
        echo "<p><strong>✓ Hook Registration:</strong> Update check hook registered $hook_count time(s) (good)</p>";
    } else {
        echo "<p><strong>⚠ Hook Registration:</strong> Update check hook registered $hook_count times (may cause duplicates)</p>";
    }
    
    // Check cache duration
    $cache_duration = apply_filters( 'wpem_update_check_cache_duration', HOUR_IN_SECONDS * 2 );
    echo "<p><strong>Cache Duration:</strong> " . ($cache_duration / 3600) . " hours (can be modified with 'wpem_update_check_cache_duration' filter)</p>";
    
    echo "<h4>Improvements Made:</h4>";
    echo "<ul>";
    echo "<li><strong>✓ Error Handling:</strong> Added function_exists() checks for transient functions</li>";
    echo "<li><strong>✓ Update Conflict Prevention:</strong> Cache is cleared when WP core or WPEM plugins are updated</li>";
    echo "<li><strong>✓ Shorter Cache Duration:</strong> Reduced from 6 hours to 2 hours for faster paid addon detection</li>";
    echo "<li><strong>✓ Force Fresh Check:</strong> Bypasses cache when WordPress core updates are available</li>";
    echo "<li><strong>✓ Empty Response Handling:</strong> Gracefully handles cases with no licensed plugins</li>";
    echo "</ul>";
    
    echo "<h4>Monitoring & Testing:</h4>";
    echo "<ul>";
    echo "<li>Enable WP_DEBUG to see 'WPEM Update Check:' log messages</li>";
    echo "<li>Use Query Monitor plugin to verify API calls are reduced to ~1 every 2 hours</li>";
    echo "<li>Test plugin updates to ensure cache is properly cleared</li>";
    echo "<li>Manual cache clear: <code>delete_transient('wpem_bulk_plugin_update_check');</code></li>";
    echo "</ul>";
}

// Add admin menu item for testing (only for administrators)
if (is_admin() && current_user_can('administrator')) {
    add_action('admin_menu', function() {
        add_submenu_page(
            'edit.php?post_type=event_listing',
            'Update Check Test',
            'Update Check Test',
            'manage_options',
            'wpem-update-test',
            'wpem_test_update_check_fix'
        );
    });
}
?>
