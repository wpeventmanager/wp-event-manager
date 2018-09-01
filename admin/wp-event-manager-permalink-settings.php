<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles front admin page for WP Event Manager.
 *
 * @since 2.5
 */
class WP_Event_Manager_Permalink_Settings {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  2.5
	 */
	private static $_instance = null;

	/**
	 * Permalink settings.
	 *
	 * @var array
	 * @since 2.5
	 */
	private $permalinks = array();

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  2.5
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->setup_fields();
		$this->settings_save();
		$this->permalinks = WP_Event_Manager_Post_Types::get_permalink_structure();
	}

	/**
	 * Add setting fields related to permalinks.
	 */
	public function setup_fields() {
		add_settings_field(
			'wpem_event_base_slug',
			__( 'Event base', 'wp-event-manager' ),
			array( $this, 'event_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wpem_event_category_slug',
			__( 'Event category base', 'wp-event-manager' ),
			array( $this, 'event_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wpem_event_type_slug',
			__( 'Event type base', 'wp-event-manager' ),
			array( $this, 'event_type_slug_input' ),
			'permalink',
			'optional'
		);
	}

	/**
	 * Show a slug input box for event post type slug.
	 */
	public function event_base_slug_input() {
		?>
		<input name="wpem_event_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['event_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'event', 'Event permalink - resave permalinks after changing this', 'wp-event-manager' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box for event category slug.
	 */
	public function event_category_slug_input() {
		?>
		<input name="wpem_event_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['category_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'event-category', 'Event category slug - resave permalinks after changing this', 'wp-event-manager' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box for event type slug.
	 */
	public function event_type_slug_input() {
		?>
		<input name="wpem_event_type_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['type_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'event-type', 'Event type slug - resave permalinks after changing this', 'wp-event-manager' ); ?>" />
		<?php
	}

	/**
	 * Save the settings.
	 */
	public function settings_save() {
		if ( ! is_admin() ) {
			return;
		}

		if ( isset( $_POST['permalink_structure'] ) ) {
			if ( function_exists( 'switch_to_locale' ) ) {
				switch_to_locale( get_locale() );
			}

			$permalinks                  = (array) get_option( 'wpem_permalinks', array() );
			$permalinks['event_base']      = sanitize_title_with_dashes( $_POST['wpem_event_base_slug'] );
			$permalinks['category_base'] = sanitize_title_with_dashes( $_POST['wpem_event_category_slug'] );
			$permalinks['type_base']     = sanitize_title_with_dashes( $_POST['wpem_event_type_slug'] );

			update_option( 'wpem_permalinks', $permalinks );

			if ( function_exists( 'restore_current_locale' ) ) {
				restore_current_locale();
			}
		}
	}
}

WP_Event_Manager_Permalink_Settings::instance();
