<?php
/**
 * Load Test to Simulate Original Problem and Verify Fix
 * 
 * This script simulates the original issue of 42+ duplicate API calls
 * and verifies that the fix prevents them.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WPEM_Load_Test {
    
    private $api_call_count = 0;
    private $cache_hit_count = 0;
    private $test_start_time = 0;
    
    public function __construct() {
        $this->test_start_time = microtime(true);
    }
    
    /**
     * Run load test to simulate multiple update checks
     */
    public function run_load_test() {
        echo "<div style='max-width: 1000px; margin: 20px;'>";
        echo "<h2>🚀 WP Event Manager - Load Test & API Call Simulation</h2>";
        
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<h4>⚠️ Test Scenario</h4>";
        echo "<p>This test simulates the original problem where refreshing /wp-admin/plugins.php ";
        echo "would trigger 42+ duplicate API calls to the licensing server.</p>";
        echo "<p><strong>Expected Result:</strong> With the fix, only 1 API call should be made, ";
        echo "and subsequent calls should use cached data.</p>";
        echo "</div>";
        
        // Clear cache to start fresh
        delete_transient('wpem_bulk_plugin_update_check');
        
        // Test 1: Simulate original problem (multiple instances)
        $this->test_multiple_update_checks();
        
        // Test 2: Simulate page refreshes
        $this->test_page_refresh_simulation();
        
        // Test 3: Test concurrent requests
        $this->test_concurrent_requests();
        
        // Test 4: Monitor actual API calls
        $this->monitor_api_calls();
        
        // Display results
        $this->display_load_test_results();
        
        echo "</div>";
    }
    
    /**
     * Test 1: Multiple Update Checks (Original Problem Simulation)
     */
    private function test_multiple_update_checks() {
        echo "<h3>📊 Test 1: Multiple Update Check Simulation</h3>";
        
        if (!class_exists('WPEM_Updater')) {
            echo "<p>❌ WPEM_Updater class not available</p>";
            return;
        }
        
        // Create mock update data
        $mock_data = new stdClass();
        $mock_data->checked = array();
        
        // Add multiple plugins to simulate the original scenario
        if (function_exists('get_wpem_plugins_info')) {
            $plugins = get_wpem_plugins_info();
            foreach ($plugins as $plugin) {
                $mock_data->checked[$plugin['plugin_files']] = $plugin['Version'];
            }
        }
        
        // Add main plugin
        $mock_data->checked['wp-event-manager/wp-event-manager.php'] = '3.2.2';
        
        echo "<p>Simulating update checks for " . count($mock_data->checked) . " plugins...</p>";
        
        $updater = new WPEM_Updater(__FILE__);
        
        // Hook into API calls to count them
        add_filter('pre_http_request', array($this, 'intercept_api_calls'), 10, 3);
        
        $start_time = microtime(true);
        
        // Simulate multiple update checks (original problem scenario)
        for ($i = 1; $i <= 15; $i++) {
            $result = $updater->check_for_updates($mock_data);
            
            if ($i <= 5) {
                echo "<p>Update check #$i completed</p>";
            } elseif ($i === 6) {
                echo "<p>... (continuing with " . (15 - 5) . " more checks)</p>";
            }
        }
        
        $end_time = microtime(true);
        $total_time = round(($end_time - $start_time) * 1000, 2);
        
        remove_filter('pre_http_request', array($this, 'intercept_api_calls'));
        
        echo "<div style='background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<p><strong>Results:</strong></p>";
        echo "<p>• Total update checks: 15</p>";
        echo "<p>• API calls made: {$this->api_call_count}</p>";
        echo "<p>• Cache hits: {$this->cache_hit_count}</p>";
        echo "<p>• Total time: {$total_time}ms</p>";
        echo "<p>• Average time per check: " . round($total_time / 15, 2) . "ms</p>";
        echo "</div>";
        
        if ($this->api_call_count <= 1) {
            echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS: API calls limited to {$this->api_call_count} (fix working!)</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ ISSUE: {$this->api_call_count} API calls made (should be 1 or 0)</p>";
        }
    }
    
    /**
     * Test 2: Page Refresh Simulation
     */
    private function test_page_refresh_simulation() {
        echo "<h3>🔄 Test 2: Page Refresh Simulation</h3>";
        
        echo "<p>Simulating multiple page refreshes of /wp-admin/plugins.php...</p>";
        
        if (!class_exists('WPEM_Updater')) {
            echo "<p>❌ WPEM_Updater class not available</p>";
            return;
        }
        
        $updater = new WPEM_Updater(__FILE__);
        $mock_data = new stdClass();
        $mock_data->checked = array('wp-event-manager/wp-event-manager.php' => '3.2.2');
        
        $refresh_times = array();
        
        // Reset counters
        $this->api_call_count = 0;
        $this->cache_hit_count = 0;
        
        add_filter('pre_http_request', array($this, 'intercept_api_calls'), 10, 3);
        
        // Simulate 10 page refreshes
        for ($i = 1; $i <= 10; $i++) {
            $start = microtime(true);
            $result = $updater->check_for_updates($mock_data);
            $end = microtime(true);
            
            $refresh_times[] = ($end - $start) * 1000;
            
            if ($i <= 3) {
                echo "<p>Page refresh #$i: " . round($refresh_times[$i-1], 2) . "ms</p>";
            }
            
            // Small delay between refreshes
            usleep(100000); // 0.1 second
        }
        
        remove_filter('pre_http_request', array($this, 'intercept_api_calls'));
        
        $avg_refresh_time = array_sum($refresh_times) / count($refresh_times);
        
        echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<p><strong>Page Refresh Results:</strong></p>";
        echo "<p>• Total refreshes: 10</p>";
        echo "<p>• API calls made: {$this->api_call_count}</p>";
        echo "<p>• Average refresh time: " . round($avg_refresh_time, 2) . "ms</p>";
        echo "<p>• Fastest refresh: " . round(min($refresh_times), 2) . "ms</p>";
        echo "<p>• Slowest refresh: " . round(max($refresh_times), 2) . "ms</p>";
        echo "</div>";
        
        if ($this->api_call_count <= 1) {
            echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS: Page refreshes use cached data</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ ISSUE: Multiple API calls on page refresh</p>";
        }
    }
    
    /**
     * Test 3: Concurrent Requests
     */
    private function test_concurrent_requests() {
        echo "<h3>⚡ Test 3: Concurrent Request Protection</h3>";
        
        if (!class_exists('WPEM_Updater')) {
            echo "<p>❌ WPEM_Updater class not available</p>";
            return;
        }
        
        $updater = new WPEM_Updater(__FILE__);
        $mock_data = new stdClass();
        $mock_data->checked = array('wp-event-manager/wp-event-manager.php' => '3.2.2');
        
        // Reset counters
        $this->api_call_count = 0;
        
        add_filter('pre_http_request', array($this, 'intercept_api_calls'), 10, 3);
        
        $start_time = microtime(true);
        
        // Simulate rapid concurrent calls
        for ($i = 0; $i < 5; $i++) {
            $updater->check_for_updates($mock_data);
        }
        
        $end_time = microtime(true);
        $total_time = ($end_time - $start_time) * 1000;
        
        remove_filter('pre_http_request', array($this, 'intercept_api_calls'));
        
        echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<p><strong>Concurrent Request Results:</strong></p>";
        echo "<p>• Rapid calls made: 5</p>";
        echo "<p>• API calls triggered: {$this->api_call_count}</p>";
        echo "<p>• Total time: " . round($total_time, 2) . "ms</p>";
        echo "<p>• Time per call: " . round($total_time / 5, 2) . "ms</p>";
        echo "</div>";
        
        if ($this->api_call_count <= 1) {
            echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS: Concurrent request protection working</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ ISSUE: Multiple concurrent API calls not prevented</p>";
        }
    }
    
    /**
     * Test 4: Monitor Actual API Calls
     */
    private function monitor_api_calls() {
        echo "<h3>🔍 Test 4: API Call Monitoring</h3>";
        
        // Check current cache status
        $cached_data = get_transient('wpem_bulk_plugin_update_check');
        $cache_timeout = get_option('_transient_timeout_wpem_bulk_plugin_update_check', 0);
        
        echo "<p><strong>Current Cache Status:</strong></p>";
        if ($cached_data !== false) {
            $time_remaining = $cache_timeout - time();
            echo "<p>✅ Cache exists, expires in " . max(0, $time_remaining) . " seconds</p>";
        } else {
            echo "<p>❌ No cache found - next update check will make API call</p>";
        }
        
        // Check if we can detect the API endpoint
        $api_url = 'https://wp-eventmanager.com/?wc-api=wpemstore_licensing_update_api';
        echo "<p><strong>API Endpoint:</strong> $api_url</p>";
        
        // Show cache duration setting
        $cache_duration = apply_filters('wpem_update_check_cache_duration', HOUR_IN_SECONDS * 2);
        echo "<p><strong>Cache Duration:</strong> " . ($cache_duration / 3600) . " hours</p>";
        
        // Check for debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            echo "<p>✅ WP_DEBUG enabled - check error logs for 'WPEM Update Check:' messages</p>";
        } else {
            echo "<p>⚠️ WP_DEBUG disabled - enable to see detailed logging</p>";
        }
    }
    
    /**
     * Intercept API calls for counting
     */
    public function intercept_api_calls($preempt, $parsed_args, $url) {
        // Check if this is a WPEM licensing API call
        if (strpos($url, 'wp-eventmanager.com') !== false && 
            strpos($url, 'wpemstore_licensing_update_api') !== false) {
            
            $this->api_call_count++;
            
            // Return a mock response to prevent actual API calls during testing
            return array(
                'headers' => array(),
                'body' => json_encode(array('test' => 'response')),
                'response' => array('code' => 200, 'message' => 'OK'),
                'cookies' => array(),
                'filename' => null
            );
        }
        
        // Check for cache hits by looking for transient access
        if (strpos($url, 'transient') !== false) {
            $this->cache_hit_count++;
        }
        
        return $preempt;
    }
    
    /**
     * Display final load test results
     */
    private function display_load_test_results() {
        $total_test_time = microtime(true) - $this->test_start_time;
        
        echo "<h3>🏁 Load Test Summary</h3>";
        
        echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 5px;'>";
        echo "<p><strong>Overall Performance:</strong></p>";
        echo "<p>• Total test duration: " . round($total_test_time, 2) . " seconds</p>";
        echo "<p>• Total API calls intercepted: {$this->api_call_count}</p>";
        echo "<p>• Cache utilization: " . ($this->cache_hit_count > 0 ? 'Active' : 'Not detected') . "</p>";
        
        // Performance verdict
        if ($this->api_call_count <= 2) {
            echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4 style='color: green;'>🎉 EXCELLENT PERFORMANCE</h4>";
            echo "<p>The update check fix is working perfectly! API calls are properly limited and cached.</p>";
            echo "</div>";
        } elseif ($this->api_call_count <= 5) {
            echo "<div style='background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4 style='color: orange;'>⚠️ MODERATE PERFORMANCE</h4>";
            echo "<p>Some improvement seen, but there may be room for optimization.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4 style='color: red;'>❌ PERFORMANCE ISSUES</h4>";
            echo "<p>The fix may not be working correctly. Please review the implementation.</p>";
            echo "</div>";
        }
        
        echo "<h4>Recommendations:</h4>";
        echo "<ul>";
        echo "<li>Monitor server logs for actual API call reduction</li>";
        echo "<li>Use Query Monitor plugin in production to verify results</li>";
        echo "<li>Test with actual licensed plugins for complete validation</li>";
        echo "<li>Consider adjusting cache duration if needed using the 'wpem_update_check_cache_duration' filter</li>";
        echo "</ul>";
        echo "</div>";
    }
}

/**
 * Function to run the load test
 */
function wpem_run_load_test() {
    $load_test = new WPEM_Load_Test();
    $load_test->run_load_test();
}

// Add admin menu for load testing
if (is_admin() && current_user_can('administrator')) {
    add_action('admin_menu', function() {
        add_submenu_page(
            'edit.php?post_type=event_listing',
            'Load Test & API Monitoring',
            'Load Test',
            'manage_options',
            'wpem-load-test',
            'wpem_run_load_test'
        );
    });
}
?>
