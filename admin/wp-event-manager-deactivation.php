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
            'nonce'    => wp_create_nonce('wpem_deactivation_nonce'),
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
                <h2 class="popup-heading"><?php esc_html_e('Quick Feedback', 'wp-event-manager'); ?></h2>
                <p class="popup-subheading"><?php esc_html_e('If you have a moment, please share why you are deactivating WP Event Manager:', 'wp-event-manager');?></p>
                <form id="wpem-deactivation-form">
                    <div class="radio-options">
                        <label>
                            <input type="radio" name="reason" value="I no longer need the plugin" required>
                            <?php esc_html_e('I no longer need the plugin', 'wp-event-manager'); ?>
                        </label>
                        <label>
                            <input type="radio" name="reason" value="I found a better plugin">
                            <?php esc_html_e('I found a better plugin', 'wp-event-manager'); ?>
                        </label>
                        <label>
                            <input type="radio" name="reason" value="I could not get the plugin to work">
                            <?php esc_html_e('I could not get the plugin to work', 'wp-event-manager'); ?>
                        </label>
                        <label>
                            <input type="radio" name="reason" value="It is a temporary deactivation">
                            <?php esc_html_e('It is a temporary deactivation', 'wp-event-manager'); ?>
                        </label>
                        <label>
                            <input type="radio" name="reason" value="Other">
                            <?php esc_html_e('Other', 'wp-event-manager'); ?>
                        </label>
                    </div>
                    <div id="other-reason-box" style="display: none; margin-top: 10px;">
                        <textarea id="additional_feedback" name="additional_feedback" placeholder="<?php esc_html_e('Tell us more', 'wp-event-manager'); ?>"></textarea>
                    </div>
                    <div class="popup-buttons">
                        <button type="submit" class="btn-submit-deactivate"><?php esc_html_e('Submit & Deactivate', 'wp-event-manager'); ?></button>
                        <button type="button" id="skip-deactivate" class="btn-skip-deactivate"><?php esc_html_e('Skip & Deactivate', 'wp-event-manager'); ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
	 * This function is used to get the user ip.
	 *@since 3.1.46
	 */
    public function wpem_get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
	 * This function get the location from ip.
	 *@since 3.1.46
	 */
    function wpem_get_location_by_ip($ip) {
        $url = "http://ip-api.com/json/{$ip}";

        $response = wp_remote_get( $url, array(
            'timeout' => 10,
            'redirection' => 5,
        ) );

        // Error handling
        if ( is_wp_error( $response ) ) {
            return null;
        }

        $body = wp_remote_retrieve_body( $response );
        if ( empty( $body ) ) {
            return null;
        }

        $data = json_decode( $body, true );

        if ( isset( $data['status'] ) && $data['status'] === 'success' ) {
            return [
                'city'    => $data['city'] ?? '',
                'country' => $data['country'] ?? '',
            ];
        }

        return null;
    }

    /**
	 * This function handles the deactivation form submission.
	 *@since 3.1.46
	 */
    public function wpem_handle_deactivation_form_submission() {
        check_ajax_referer('wpem_deactivation_nonce');

        if (isset($_POST['reason'])) {
            $reason = sanitize_text_field(wp_unslash($_POST['reason']));
            $additional_feedback = sanitize_text_field(wp_unslash($_POST['additional_feedback']));

            $current_user = wp_get_current_user();
            $user_first_name = get_user_meta($current_user->ID, 'first_name', true);
            $user_last_name = get_user_meta($current_user->ID, 'last_name', true);
            $user_name = $current_user->user_login;
            $ip = $this->wpem_get_user_ip();
            $location = $this->wpem_get_location_by_ip($ip);

            if ($reason == 'Other') {
                $reason = 'Other (' . $additional_feedback . ')';
            }		

            $api_url = esc_url(get_option('wp_event_manager_store_url').'?wc-api=wpem_plugin_deactivation_review');
            $data = array(
                'request' => 'deactivationreview',
                'email' => get_option('admin_email'),
                'username' => $user_name,
                'first_name' => $user_first_name,
                'last_name' => $user_last_name,
                'city' => $location['city'],
                'country' => $location['country'],
                'ip_address' => $ip,
                'instance' => site_url(),
                'version' => EVENT_MANAGER_VERSION,
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