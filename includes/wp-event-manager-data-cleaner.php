<?php
/**
 * Defines a class with methods for cleaning up plugin data. To be used when
 * the plugin is deleted.
 *
 * @package Core
 */

if(!defined('ABSPATH')) {
	// Exit if accessed directly.
	exit;
}

/**
 * Methods for cleaning up all plugin data.
 *
 * @since 2.5
 */
class WP_Event_Manager_Data_Cleaner {

	/**
	 * Custom post types to be deleted.
	 *
	 * @var $custom_post_types
	 */
	private static $custom_post_types = array(
		'event_listing',
		'event_organizer',
		'event_venue',
	);

	/**
	 * Taxonomies to be deleted.
	 *
	 * @var $taxonomies
	 */
	private static $taxonomies = array(
		'event_listing_category',
		'event_listing_type',
	);

	/** Cron jobs to be unscheduled.
	 *
	 * @var $cron_jobs
	 */
	private static $cron_jobs = array(
		'event_manager_check_for_expired_events',
		'event_manager_delete_old_previews',
		'event_manager_clear_expired_transients',
		'event_manager_email_daily_notices',
		'event_manager_usage_tracking_send_usage_data',
	);

	/**
	 * Options to be deleted.
	 *
	 * @var $options
	 */
	private static $options = array();

	/**
	 * Site options to be deleted.
	 *
	 * @var $site_options
	 */
	private static $site_options = array(
		'event_manager_helper',
	);

	/**
	 * Transient names (as MySQL regexes) to be deleted. The prefixes
	 * "_transient_" and "_transient_timeout_" will be prepended.
	 *
	 * @var $transients
	 */
	private static $transients = array(
		'_event_manager_activation_redirect',
		'get_event_listings-transient-version',
		'em_.*',
	);

	/**
	 * Role to be removed.
	 *
	 * @var $role
	 */
	private static $role = 'organizer';

	/**
	 * Capabilities to be deleted.
	 *
	 * @var $caps
	 */
	private static $caps = array(
		'manage_event_listings',
		'edit_event_listing',
		'read_event_listing',
		'delete_event_listing',
		'edit_event_listings',
		'edit_others_event_listings',
		'publish_event_listings',
		'read_private_event_listings',
		'delete_event_listings',
		'delete_private_event_listings',
		'delete_published_event_listings',
		'delete_others_event_listings',
		'edit_private_event_listings',
		'edit_published_event_listings',
		'manage_event_listing_terms',
		'edit_event_listing_terms',
		'delete_event_listing_terms',
		'assign_event_listing_terms',
	);

	/**
	 * User meta key names to be deleted.
	 *
	 * @var array $user_meta_keys
	 */
	private static $user_meta_keys = array(
		'_organizer_logo',
		'_organizer_name',
		'_organizer_website',
		'_organizer_tagline',
		'_organizer_twitter',
		'_organizer_video',
	);

	/**
	 * Cleanup all data.
	 *
	 * @access public
	 */
	public static function cleanup_all() {
		self::cleanup_custom_post_types();
		self::cleanup_taxonomies();
		self::cleanup_pages();
		self::cleanup_cron_jobs();
		self::cleanup_roles_and_caps();
		self::cleanup_transients();
		self::cleanup_user_meta();
		self::cleanup_options();
		self::cleanup_site_options();
	}

	/**
	 * Cleanup data for custom post types.
	 *
	 * @access private
	 */
	private static function cleanup_custom_post_types() {
		foreach (self::$custom_post_types as $post_type) {
			$items = get_posts(
				array(
					'post_type'   => $post_type,
					'post_status' => 'any',
					'numberposts' => -1,
					'fields'      => 'ids',
				)
			);

			foreach ($items as $item) {
				self::delete_event_with_attachment($item);
				wp_delete_post($item);
			}
		}
	}

	/**
	 * wpem_delete_event_with_attachment function.
	 *
	 * @param $post_id
	 * @return void
	 */
	private static function delete_event_with_attachment($post_id) {
		if(!in_array(get_post_type($post_id), ['event_listing', 'event_organizer']))
			return;
		
 		$event_banner = esc_url(get_post_meta($post_id, '_event_banner', true));
		if(!empty($event_banner)){
			$wp_upload_dir = wp_get_upload_dir();
			$baseurl = $wp_upload_dir['baseurl'] . '/';

			if(is_array($event_banner)){
				foreach ($event_banner as $banner) {
					$wp_attached_file = str_replace($baseurl, '', $banner);

					$args = array(
				        'meta_key'         	=> '_wp_attached_file',
				        'meta_value'       	=> $wp_attached_file,
				        'post_type'        	=> 'attachment',
				        'posts_per_page'	=> 1,
				   );
					$attachments = get_posts($args);
					if(!empty($attachments)){
						foreach ($attachments as $attachment) {
							wp_delete_attachment($attachment->ID, true);
						}
					}
				}
			} else {
				$wp_attached_file = str_replace($baseurl, '', $event_banner);
				$args = array(
			        'meta_key'         	=> '_wp_attached_file',
			        'meta_value'       	=> $wp_attached_file,
			        'post_type'        	=> 'attachment',
			        'posts_per_page'	=> 1,
			   );
				$attachments = get_posts($args);
				if(!empty($attachments)){
					foreach ($attachments as $attachment) {
						wp_delete_attachment($attachment->ID, true);
					}
				}
			}
		}
		$thumbnail_id = get_post_thumbnail_id($post_id);
		if(!empty($thumbnail_id)) {
			wp_delete_attachment($thumbnail_id, true);
		}
	}

	/**
	 * Cleanup data for taxonomies.
	 *
	 * @access private
	 */
	private static function cleanup_taxonomies() {
		global $wpdb;
		foreach (self::$taxonomies as $taxonomy) {
			$terms = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT term_id, term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = %s",
					$taxonomy
				)
			);
			// Delete all data for each term.
			foreach ($terms as $term) {
				$wpdb->delete($wpdb->term_relationships, array('term_taxonomy_id' => $term->term_taxonomy_id));
				$wpdb->delete($wpdb->term_taxonomy, array('term_taxonomy_id' => $term->term_taxonomy_id));
				$wpdb->delete($wpdb->terms, array('term_id' => $term->term_id));
				$wpdb->delete($wpdb->termmeta, array('term_id' => $term->term_id));
			}
			if(function_exists('clean_taxonomy_cache')) {
				clean_taxonomy_cache($taxonomy);
			}
		}
	}

	/**
	 * Cleanup data for pages.
	 *
	 * @access private
	 */
	private static function cleanup_pages() {
		// Trash the Submit Event page.
		$submit_event_form_page_id = get_option('event_manager_submit_event_form_page_id');
		if($submit_event_form_page_id) {
			wp_delete_post($submit_event_form_page_id, true);
		}

		// Trash the Event Dashboard page.
		$event_dashboard_page_id = get_option('event_manager_event_dashboard_page_id');
		if($event_dashboard_page_id) {
			wp_delete_post($event_dashboard_page_id, true);
		}

		// Trash the Events page.
		$events_page_id = get_option('event_manager_events_page_id');
		if($events_page_id) {
			wp_delete_post($events_page_id, true);
		}

		// Trash the submit organizer page.
		$submit_organizer_form_page_id = get_option('event_manager_submit_organizer_form_page_id');
		if($submit_organizer_form_page_id) {
			wp_delete_post($submit_organizer_form_page_id, true);
		}

		// Trash the organizer dashboard page.
		$organizer_dashboard_page_id = get_option('event_manager_organizer_dashboard_page_id');
		if($organizer_dashboard_page_id) {
			wp_delete_post($organizer_dashboard_page_id, true);
		}

		// Trash the event organizer page.
		$event_organizers_page_id = get_option('event_manager_event_organizers_page_id');
		if($event_organizers_page_id) {
			wp_delete_post($event_organizers_page_id, true);
		}

		// Trash the submit venue page.
		$submit_venue_form_page_id = get_option('event_manager_submit_venue_form_page_id');
		if($submit_venue_form_page_id) {
			wp_delete_post($submit_venue_form_page_id, true);
		}

		// Trash the venue dashboard page.
		$venue_dashboard_page_id = get_option('event_manager_venue_dashboard_page_id');
		if($venue_dashboard_page_id) {
			wp_delete_post($venue_dashboard_page_id, true);
		}

		// Trash the event venue page.
		$event_venues_page_id = get_option('event_manager_event_venues_page_id');
		if($event_venues_page_id) {
			wp_delete_post($event_venues_page_id, true);
		}
	}

	/**
	 * Cleanup data for options.
	 *
	 * @access private
	 */
	private static function cleanup_options() {
		foreach (self::$options as $option) {
			delete_option($option);
		}
	}

	/**
	 * Cleanup data for site options.
	 *
	 * @access private
	 */
	private static function cleanup_site_options() {
		foreach (self::$site_options as $option) {
			delete_site_option($option);
		}
	}

	/**
	 * Cleanup transients from the database.
	 *
	 * @access private
	 */
	private static function cleanup_transients() {
		global $wpdb;

		foreach (array('_transient_', '_transient_timeout_') as $prefix) {
			foreach (self::$transients as $transient) {
				$wpdb->query(
					$wpdb->prepare(
						"DELETE FROM $wpdb->options WHERE option_name RLIKE %s",
						$prefix . $transient
					)
				);
			}
		}
	}

	/**
	 * Cleanup data for roles and caps.
	 *
	 * @access private
	 */
	private static function cleanup_roles_and_caps() {
		global $wp_roles;

		// Remove caps from roles.
		$role_names = array_keys($wp_roles->roles);
		foreach ($role_names as $role_name) {
			$role = get_role($role_name);
			self::remove_all_event_manager_caps($role);
		}

		// Remove caps and role from users.
		$users = get_users(array());
		foreach ($users as $user) {
			self::remove_all_event_manager_caps($user);
			$user->remove_role(self::$role);
		}

		// Remove role.
		remove_role(self::$role);
	}

	/**
	 * Helper method to remove WPEM caps from a user or role object.
	 *
	 * @param (WP_User|WP_Role) $object the user or role object.
	 */
	private static function remove_all_event_manager_caps($object) {
		foreach (self::$caps as $cap) {
			$object->remove_cap($cap);
		}
	}

	/**
	 * Cleanup user meta from the database.
	 *
	 * @access private
	 */
	private static function cleanup_user_meta() {
		global $wpdb;

		foreach (self::$user_meta_keys as $meta_key) {
			$wpdb->delete($wpdb->usermeta, array('meta_key' => $meta_key));
		}
	}

	/**
	 * Cleanup cron jobs. Note that this should be done on deactivation, but
	 * doing it here as well for safety.
	 *
	 * @access private
	 */
	private static function cleanup_cron_jobs() {
		foreach (self::$cron_jobs as $job) {
			wp_clear_scheduled_hook($job);
		}
	}
}