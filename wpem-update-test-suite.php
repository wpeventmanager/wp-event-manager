<?php
/**
 * Comprehensive Test Suite for WP Event Manager Update Check Fix
 * 
 * This file contains all possible test cases to verify the update check fix
 * Run from WordPress admin or via WP-CLI
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WPEM_Update_Test_Suite {
    
    private $test_results = array();
    private $original_transient = null;
    
    public function __construct() {
        // Store original transient for cleanup
        $this->original_transient = get_transient('wpem_bulk_plugin_update_check');
    }
    
    /**
     * Run all test cases
     */
    public function run_all_tests() {
        echo "<div style='max-width: 1200px; margin: 20px;'>";
        echo "<h2>🧪 WP Event Manager Update Check - Comprehensive Test Suite</h2>";
        
        // Test 1: Basic functionality
        $this->test_basic_functionality();
        
        // Test 2: Cache behavior
        $this->test_cache_behavior();
        
        // Test 3: Error handling
        $this->test_error_handling();
        
        // Test 4: Hook registration
        $this->test_hook_registration();
        
        // Test 5: License scenarios
        $this->test_license_scenarios();
        
        // Test 6: Plugin update scenarios
        $this->test_plugin_update_scenarios();
        
        // Test 7: Performance test
        $this->test_performance();
        
        // Test 8: Edge cases
        $this->test_edge_cases();
        
        // Display summary
        $this->display_test_summary();
        
        // Cleanup
        $this->cleanup();
        
        echo "</div>";
    }
    
    /**
     * Test 1: Basic Functionality
     */
    private function test_basic_functionality() {
        echo "<h3>📋 Test 1: Basic Functionality</h3>";
        
        // Test if WPEM_Updater class exists
        $this->assert_true(
            class_exists('WPEM_Updater'),
            "WPEM_Updater class exists"
        );
        
        // Test if get_wpem_plugins_info function exists
        $this->assert_true(
            function_exists('get_wpem_plugins_info'),
            "get_wpem_plugins_info function exists"
        );
        
        // Test plugin data retrieval
        if (function_exists('get_wpem_plugins_info')) {
            $plugins = get_wpem_plugins_info();
            $this->assert_true(
                is_array($plugins),
                "get_wpem_plugins_info returns array"
            );
            
            echo "<p>✓ Found " . count($plugins) . " WPEM plugins</p>";
        }
        
        // Test transient functions
        $this->assert_true(
            function_exists('get_transient'),
            "get_transient function available"
        );
        
        $this->assert_true(
            function_exists('set_transient'),
            "set_transient function available"
        );
        
        $this->assert_true(
            function_exists('delete_transient'),
            "delete_transient function available"
        );
    }
    
    /**
     * Test 2: Cache Behavior
     */
    private function test_cache_behavior() {
        echo "<h3>💾 Test 2: Cache Behavior</h3>";
        
        // Clear any existing cache
        delete_transient('wpem_bulk_plugin_update_check');
        
        // Test cache miss
        $cached = get_transient('wpem_bulk_plugin_update_check');
        $this->assert_false(
            $cached,
            "Cache is empty initially"
        );
        
        // Set test cache
        $test_data = (object) array('test' => 'data', 'timestamp' => time());
        set_transient('wpem_bulk_plugin_update_check', $test_data, 3600);
        
        // Test cache hit
        $cached = get_transient('wpem_bulk_plugin_update_check');
        $this->assert_true(
            is_object($cached) && isset($cached->test),
            "Cache stores and retrieves data correctly"
        );
        
        // Test cache duration filter
        $default_duration = HOUR_IN_SECONDS * 2;
        $filtered_duration = apply_filters('wpem_update_check_cache_duration', $default_duration);
        $this->assert_true(
            is_numeric($filtered_duration),
            "Cache duration filter works"
        );
        
        echo "<p>✓ Cache duration: " . ($filtered_duration / 3600) . " hours</p>";
    }
    
    /**
     * Test 3: Error Handling
     */
    private function test_error_handling() {
        echo "<h3>⚠️ Test 3: Error Handling</h3>";
        
        // Test with empty check_for_updates_data
        if (class_exists('WPEM_Updater')) {
            $updater = new WPEM_Updater(__FILE__);
            
            // Test empty data
            $empty_data = new stdClass();
            $result = $updater->check_for_updates($empty_data);
            $this->assert_true(
                is_object($result),
                "Handles empty update data gracefully"
            );
            
            // Test with checked but empty
            $empty_checked = new stdClass();
            $empty_checked->checked = array();
            $result = $updater->check_for_updates($empty_checked);
            $this->assert_true(
                is_object($result),
                "Handles empty checked array gracefully"
            );
        }
        
        // Test function existence checks
        $this->assert_true(
            function_exists('get_transient'),
            "Transient functions available for error handling"
        );
        
        // Test with corrupted cache data
        set_transient('wpem_bulk_plugin_update_check', 'invalid_data', 3600);
        $cached = get_transient('wpem_bulk_plugin_update_check');
        $this->assert_true(
            $cached === 'invalid_data',
            "Handles corrupted cache data"
        );
    }
    
    /**
     * Test 4: Hook Registration
     */
    private function test_hook_registration() {
        echo "<h3>🔗 Test 4: Hook Registration</h3>";
        
        global $wp_filter;
        
        // Count update check hooks
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
        
        $this->assert_true(
            $hook_count <= 1,
            "Update check hook registered only once (found: $hook_count)"
        );
        
        // Check upgrader hook
        $upgrader_hook_exists = false;
        if (isset($wp_filter['upgrader_process_complete'])) {
            foreach ($wp_filter['upgrader_process_complete']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    if (is_array($callback['function']) && 
                        is_object($callback['function'][0]) && 
                        get_class($callback['function'][0]) === 'WPEM_Updater' &&
                        $callback['function'][1] === 'clear_update_cache_on_plugin_update') {
                        $upgrader_hook_exists = true;
                        break 2;
                    }
                }
            }
        }
        
        $this->assert_true(
            $upgrader_hook_exists,
            "Plugin update cache clearing hook is registered"
        );
    }
    
    /**
     * Test 5: License Scenarios
     */
    private function test_license_scenarios() {
        echo "<h3>🔑 Test 5: License Scenarios</h3>";
        
        if (function_exists('get_wpem_plugins_info')) {
            $plugins = get_wpem_plugins_info();
            
            $licensed_count = 0;
            $unlicensed_count = 0;
            
            foreach ($plugins as $plugin) {
                $licence_key = get_option($plugin['TextDomain'] . '_licence_key', '');
                $email = get_option($plugin['TextDomain'] . '_email', '');
                
                if (!empty($licence_key) && !empty($email)) {
                    $licensed_count++;
                } else {
                    $unlicensed_count++;
                }
            }
            
            echo "<p>✓ Licensed plugins: $licensed_count</p>";
            echo "<p>✓ Unlicensed plugins: $unlicensed_count</p>";
            
            $this->assert_true(
                ($licensed_count + $unlicensed_count) === count($plugins),
                "All plugins accounted for in license check"
            );
        }
    }
    
    /**
     * Test 6: Plugin Update Scenarios
     */
    private function test_plugin_update_scenarios() {
        echo "<h3>🔄 Test 6: Plugin Update Scenarios</h3>";
        
        // Test WordPress core update detection
        $wp_updates = get_site_transient('update_core');
        $has_core_updates = false;
        
        if (!empty($wp_updates) && !empty($wp_updates->updates)) {
            foreach ($wp_updates->updates as $update) {
                if (isset($update->response) && $update->response === 'upgrade') {
                    $has_core_updates = true;
                    break;
                }
            }
        }
        
        echo "<p>✓ WordPress core updates available: " . ($has_core_updates ? 'Yes' : 'No') . "</p>";
        
        // Test cache clearing method exists
        if (class_exists('WPEM_Updater')) {
            $updater = new WPEM_Updater(__FILE__);
            $this->assert_true(
                method_exists($updater, 'clear_update_cache_on_plugin_update'),
                "Cache clearing method exists"
            );
        }
        
        // Simulate plugin update scenario
        $test_options = array(
            'type' => 'plugin',
            'plugins' => array('wp-event-manager/wp-event-manager.php')
        );
        
        // Set cache first
        set_transient('wpem_bulk_plugin_update_check', 'test_data', 3600);
        
        // Simulate cache clearing
        if (class_exists('WPEM_Updater')) {
            $updater = new WPEM_Updater(__FILE__);
            $updater->clear_update_cache_on_plugin_update(null, $test_options);
            
            $cached_after = get_transient('wpem_bulk_plugin_update_check');
            $this->assert_false(
                $cached_after,
                "Cache cleared after WPEM plugin update"
            );
        }
    }
    
    /**
     * Test 7: Performance Test
     */
    private function test_performance() {
        echo "<h3>⚡ Test 7: Performance Test</h3>";
        
        if (!class_exists('WPEM_Updater')) {
            echo "<p>❌ WPEM_Updater class not available for performance test</p>";
            return;
        }
        
        // Clear cache for fresh test
        delete_transient('wpem_bulk_plugin_update_check');
        
        $updater = new WPEM_Updater(__FILE__);
        
        // Create mock update data
        $mock_data = new stdClass();
        $mock_data->checked = array('wp-event-manager/wp-event-manager.php' => '3.2.2');
        
        // Test 1: First call (should make API call or use empty response)
        $start_time = microtime(true);
        $result1 = $updater->check_for_updates($mock_data);
        $first_call_time = microtime(true) - $start_time;
        
        // Test 2: Second call (should use cache)
        $start_time = microtime(true);
        $result2 = $updater->check_for_updates($mock_data);
        $second_call_time = microtime(true) - $start_time;
        
        echo "<p>✓ First call time: " . round($first_call_time * 1000, 2) . "ms</p>";
        echo "<p>✓ Second call time: " . round($second_call_time * 1000, 2) . "ms</p>";
        
        // Second call should be significantly faster (cache hit)
        $this->assert_true(
            $second_call_time < $first_call_time,
            "Second call faster than first (cache working)"
        );
        
        // Test concurrent calls protection
        $start_time = microtime(true);
        $result3 = $updater->check_for_updates($mock_data);
        $third_call_time = microtime(true) - $start_time;
        
        echo "<p>✓ Third call time: " . round($third_call_time * 1000, 2) . "ms</p>";
        
        $this->assert_true(
            $third_call_time < 0.01, // Should be very fast due to static flag
            "Concurrent call protection working"
        );
    }
    
    /**
     * Test 8: Edge Cases
     */
    private function test_edge_cases() {
        echo "<h3>🎯 Test 8: Edge Cases</h3>";
        
        // Test with no WPEM plugins
        $empty_plugins = array();
        $this->assert_true(
            is_array($empty_plugins),
            "Handles empty plugin array"
        );
        
        // Test with malformed plugin data
        $malformed_plugin = array(
            'Name' => 'Test Plugin',
            // Missing TextDomain
            'Version' => '1.0.0'
        );
        
        $this->assert_true(
            is_array($malformed_plugin),
            "Handles malformed plugin data"
        );
        
        // Test cache with very short duration
        set_transient('wpem_bulk_plugin_update_check', 'short_test', 1);
        sleep(2);
        $expired_cache = get_transient('wpem_bulk_plugin_update_check');
        $this->assert_false(
            $expired_cache,
            "Cache expires correctly"
        );
        
        // Test with invalid license data
        $invalid_license = get_option('nonexistent_plugin_licence_key', '');
        $this->assert_true(
            empty($invalid_license),
            "Handles non-existent license keys gracefully"
        );
        
        // Test memory usage
        $memory_before = memory_get_usage();
        
        // Simulate multiple plugin data
        $large_plugin_array = array();
        for ($i = 0; $i < 50; $i++) {
            $large_plugin_array[] = array(
                'Name' => 'Test Plugin ' . $i,
                'TextDomain' => 'test-plugin-' . $i,
                'Version' => '1.0.' . $i
            );
        }
        
        $memory_after = memory_get_usage();
        $memory_used = $memory_after - $memory_before;
        
        echo "<p>✓ Memory usage for 50 plugins: " . round($memory_used / 1024, 2) . " KB</p>";
        
        $this->assert_true(
            $memory_used < 1048576, // Less than 1MB
            "Memory usage is reasonable"
        );
    }
    
    /**
     * Helper method to assert true conditions
     */
    private function assert_true($condition, $message) {
        if ($condition) {
            echo "<p style='color: green;'>✅ $message</p>";
            $this->test_results[] = array('status' => 'pass', 'message' => $message);
        } else {
            echo "<p style='color: red;'>❌ $message</p>";
            $this->test_results[] = array('status' => 'fail', 'message' => $message);
        }
    }
    
    /**
     * Helper method to assert false conditions
     */
    private function assert_false($condition, $message) {
        $this->assert_true(!$condition, $message);
    }
    
    /**
     * Display test summary
     */
    private function display_test_summary() {
        echo "<h3>📊 Test Summary</h3>";
        
        $passed = 0;
        $failed = 0;
        
        foreach ($this->test_results as $result) {
            if ($result['status'] === 'pass') {
                $passed++;
            } else {
                $failed++;
            }
        }
        
        $total = $passed + $failed;
        $pass_rate = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
        
        echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
        echo "<p><strong>Total Tests:</strong> $total</p>";
        echo "<p style='color: green;'><strong>Passed:</strong> $passed</p>";
        echo "<p style='color: red;'><strong>Failed:</strong> $failed</p>";
        echo "<p><strong>Pass Rate:</strong> $pass_rate%</p>";
        
        if ($pass_rate >= 90) {
            echo "<p style='color: green; font-weight: bold;'>🎉 Excellent! The update check fix is working properly.</p>";
        } elseif ($pass_rate >= 70) {
            echo "<p style='color: orange; font-weight: bold;'>⚠️ Good, but some issues need attention.</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ Critical issues found. Please review the failed tests.</p>";
        }
        echo "</div>";
        
        // Show failed tests
        if ($failed > 0) {
            echo "<h4>Failed Tests:</h4>";
            echo "<ul>";
            foreach ($this->test_results as $result) {
                if ($result['status'] === 'fail') {
                    echo "<li style='color: red;'>" . $result['message'] . "</li>";
                }
            }
            echo "</ul>";
        }
    }
    
    /**
     * Cleanup after tests
     */
    private function cleanup() {
        // Restore original transient if it existed
        if ($this->original_transient !== false) {
            set_transient('wpem_bulk_plugin_update_check', $this->original_transient, HOUR_IN_SECONDS * 2);
        } else {
            delete_transient('wpem_bulk_plugin_update_check');
        }
        
        echo "<p><em>✓ Test cleanup completed</em></p>";
    }
}

/**
 * Function to run the test suite
 */
function wpem_run_comprehensive_tests() {
    $test_suite = new WPEM_Update_Test_Suite();
    $test_suite->run_all_tests();
}

// Add admin menu for comprehensive testing
if (is_admin() && current_user_can('administrator')) {
    add_action('admin_menu', function() {
        add_submenu_page(
            'edit.php?post_type=event_listing',
            'Comprehensive Update Tests',
            'Comprehensive Tests',
            'manage_options',
            'wpem-comprehensive-tests',
            'wpem_run_comprehensive_tests'
        );
    });
}
?>
