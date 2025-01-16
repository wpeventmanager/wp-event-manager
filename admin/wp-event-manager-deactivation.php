<?php
/*
* This file use to create fields of wp event manager at admin side.
*/
if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 *  Class with details of plugin deactivation.
 */
class WP_Event_Manager_Deactivation {

    /**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('wp_ajax_wpem_deactivation_form', array($this, 'wpem_handle_deactivation_form_submission'));
		add_action('wp_ajax_wpem_deactivate_plugin', array($this, 'wpem_handle_plugin_deactivation'));
	}

    /**
	 * This function handles the deactivation form submission.
	 *@since 3.1.46
	 */
    public function wpem_handle_deactivation_form_submission() {
        if (isset($_POST['reason'])) {
            $reason = sanitize_text_field(wp_unslash($_POST['reason']));
            $additional_feedback = sanitize_text_field(wp_unslash($_POST['additional_feedback']));

            $current_user = wp_get_current_user();

            if ($reason == 'Other') {
                $reason = 'Other (' . $additional_feedback . ')';
            }		

            $api_url = 'https://wp-eventmanager.com/?wc-api=wpem_plugin_deactivation_review';
            $data = array(
                'request' => 'deactivationreview',
                'instance' => site_url(),
                'version' => EVENT_MANAGER_VERSION,
                'email' => get_option('admin_email'),
                'reason' => $reason,
            );
        
            $args=array();
            $args    = wp_parse_args($args, $data);
            $request = wp_remote_get($api_url . '&' . http_build_query($args, '', '&'));
            error_log(print_r($request,true));
            if(is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
                return false;
            }
            $response = json_decode(wp_remote_retrieve_body($request),true);
            $response = (object)$response;

            if (is_wp_error($response)) {
                wp_send_json_error('Failed to send feedback. Please try again.');
            } else {
                wp_send_json_success('Thank you for your feedback!');
            }
        } else {
            wp_send_json_error('Please provide a reason for deactivation.');
        }
    }

    /**
     * This function handle plugin
     * @since 3.1.46
     */
    public function wpem_handle_plugin_deactivation() {
        if (!current_user_can('activate_plugins')) {
            wp_send_json_error('You do not have permission to deactivate plugins.');
        }

        deactivate_plugins(EVENT_MANAGER_PLUGIN_DIR);

        wp_send_json_success('Plugin deactivated successfully.');
    }

}

new WP_Event_Manager_Deactivation();