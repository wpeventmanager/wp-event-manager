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
	 * @access public
	 * @return void
	 */
	public function __construct() {
        add_action( 'current_screen', function () {
			if ( ! $this->is_plugins_screen() ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', [ $this, 'wpem_enqueue_deactivation_script' ] );
		} );
        
        add_action('wp_ajax_wpem_deactivation_form', array($this, 'wpem_handle_deactivation_form_submission'));
        add_action('wp_ajax_nopriv_wpem_deactivation_form', array($this, 'wpem_handle_deactivation_form_submission'));
	}

    /**
	 * Enqueue deactivation dialog scripts.
	 * @since 3.1.46
	 */
	public function wpem_enqueue_deactivation_script() {
		add_action( 'admin_footer', [ $this, 'wpem_deactivate_feedback_dialog' ] );

		wp_enqueue_script('wpem-deactivation-js', EVENT_MANAGER_PLUGIN_URL . '/assets/js/wpem-deactivation.min.js', ['jquery'], '1.0', true);
        wp_localize_script('wpem-deactivation-js', 'wpem_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
        wp_enqueue_style('wpem-deactivation-css', EVENT_MANAGER_PLUGIN_URL . '/assets/css/wpem-deactivation.min.css');
	}

    /**
	 * This function handles the deactivation form.
	 *@since 3.1.46
	 */
    public function wpem_deactivate_feedback_dialog() {
        ?>
        <div id="wpem-deactivation-popup" style="display:none">
            <div class="popup-content">
                <button id="close-popup" class="close-popup-button">&times;</button>
                <h2 class="popup-heading">Quick Feedback</h2>
                <p class="popup-subheading">If you have a moment, please share why you are deactivating WP Event Manager:</p>
                <form id="wpem-deactivation-form">
                    <div class="radio-options">
                        <label>
                            <input type="radio" name="reason" value="I no longer need the plugin" required>
                            I no longer need the plugin
                        </label>
                        <label>
                            <input type="radio" name="reason" value="I found a better plugin">
                            I found a better plugin
                        </label>
                        <label>
                            <input type="radio" name="reason" value="I could not get the plugin to work">
                            I could not get the plugin to work
                        </label>
                        <label>
                            <input type="radio" name="reason" value="It is a temporary deactivation">
                            It is a temporary deactivation
                        </label>
                        <label>
                            <input type="radio" name="reason" value="Other">
                            Other
                        </label>
                    </div>
                    <div id="other-reason-box" style="display: none; margin-top: 10px;">
                        <textarea id="additional_feedback" name="additional_feedback" placeholder="Tell us more"></textarea>
                    </div>
                    <div class="popup-buttons">
                        <button type="submit" class="btn-submit-deactivate">Submit & Deactivate</button>
                        <button type="button" id="skip-deactivate" class="btn-skip-deactivate">Skip & Deactivate</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
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
	 * This function handles current plugin screen.
	 *@since 3.1.46
	 */
	public function is_plugins_screen() {
		return in_array( get_current_screen()->id, [ 'plugins', 'plugins-network' ] );
	}

}

new WP_Event_Manager_Deactivation();