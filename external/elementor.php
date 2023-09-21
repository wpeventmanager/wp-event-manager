<?php
namespace WPEventManager;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Plugin {

	/**
	 * Instance
	 *
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @access public
	 */
	public function __construct() {
		// Register Categories
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_categories' ]);

		// Register Scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

		// Register tags
		add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'register_tags' ] );
	}

	/**
	 * Register new category for WP-event-manager core widget
	 * @param $elementsManager
	 */
	public function register_categories($elementsManager) {
		$elementsManager =\Elementor\Plugin::instance()->elements_manager;
		
		$elementsManager->add_category(
			'wp-event-manager-categories',
			array(
				'title' => 'WP Event Manager',
				'icon'  => 'fonts',
			), 0 // 0 to TOP
		);
	}

	/**
	 * Widget scripts
	 *
	 * widget_scripts
	 *
	 * @access private
	 */
	public function widget_scripts() {

		$ajax_url         = get_site_url() . '/em-ajax/get_listings/';
		$ajax_filter_deps = array( 'jquery', 'jquery-deserialize' );

		$chosen_shortcodes   = array( 'submit_event_form', 'event_dashboard', 'events' );
		$chosen_used_on_page = has_wpem_shortcode( null, $chosen_shortcodes );


		//jQuery Chosen - vendor
		if ( apply_filters( 'event_manager_chosen_enabled', $chosen_used_on_page ) ) {

			wp_enqueue_style( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/css/chosen.css' );

			wp_register_script( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
			wp_register_script( 'wp-event-manager-term-multiselect', EVENT_MANAGER_PLUGIN_URL . '/assets/js/term-multiselect.min.js', array( 'jquery', 'chosen' ), EVENT_MANAGER_VERSION, true );
			wp_register_script( 'wp-event-manager-multiselect', EVENT_MANAGER_PLUGIN_URL . '/assets/js/multiselect.min.js', array( 'jquery', 'chosen' ), EVENT_MANAGER_VERSION, true );
			if (!wp_script_is( 'chosen', 'enqueued' ))
				wp_enqueue_script( 'chosen');
			//wp_enqueue_script( 'wp-event-manager-term-multiselect');
			if (!wp_script_is( 'wp-event-manager-multiselect', 'enqueued' ))
				wp_enqueue_script( 'wp-event-manager-multiselect');

			$ajax_filter_deps[] = 'chosen';
		}
	
		wp_enqueue_style( 'wp-event-manager-frontend', EVENT_MANAGER_PLUGIN_URL . '/assets/css/frontend.min.css');

		//common js
		wp_register_script('wp-event-manager-common', EVENT_MANAGER_PLUGIN_URL . '/assets/js/common.min.js', array('jquery'), EVENT_MANAGER_VERSION, true);	

		if (!wp_script_is( 'wp-event-manager-common', 'enqueued' ))
			wp_enqueue_script('wp-event-manager-common'); 

		//jQuery UI date rang picker
		wp_enqueue_style( 'wp-event-manager-jquery-ui-daterangepicker', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-ui-daterangepicker/jquery.comiseo.daterangepicker.css');
		wp_enqueue_style( 'wp-event-manager-jquery-ui-daterangepicker-style', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-ui-daterangepicker/styles.css');
		wp_register_script( 'wp-event-manager-jquery-ui-daterangepicker', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-ui-daterangepicker/jquery.comiseo.daterangepicker.js', array('jquery-ui-core', 'jquery-ui-button', 'jquery-ui-datepicker', 'jquery-ui-menu', 'jquery-ui-widget', 'moment') , EVENT_MANAGER_VERSION, true );

		if (!wp_script_is( 'wp-event-manager-jquery-ui-daterangepicker', 'enqueued' ))
			wp_enqueue_script( 'wp-event-manager-jquery-ui-daterangepicker');

		
		wp_register_script( 'wp-event-manager-content-event-listing', EVENT_MANAGER_PLUGIN_URL . '/assets/js/content-event-listing.min.js', array('jquery','wp-event-manager-common'), EVENT_MANAGER_VERSION, true );					
		wp_localize_script( 'wp-event-manager-content-event-listing', 'event_manager_content_event_listing', array(
				
				'i18n_initialText' => __( 'Select date range', 'wp-event-manager' ),
				'i18n_applyButtonText' => __( 'Apply', 'wp-event-manager' ),
				'i18n_clearButtonText' => __( 'Clear', 'wp-event-manager' ),
				'i18n_cancelButtonText' => __( 'Cancel', 'wp-event-manager' ),
				
				'i18n_today' => __( 'Today', 'wp-event-manager' ),
				'i18n_tomorrow' => __( 'Tomorrow', 'wp-event-manager' ),
				'i18n_thisWeek' => __( 'This Week', 'wp-event-manager' ),
				'i18n_nextWeek' => __( 'Next Week', 'wp-event-manager' ),
				'i18n_thisMonth' => __( 'This Month', 'wp-event-manager' ),
				'i18n_nextMonth' => __( 'Next Month', 'wp-event-manager' ),
				'i18n_thisYear' => __( 'This Year', 'wp-event-manager' ),
				'i18n_nextYear' => __( 'Next Month', 'wp-event-manager' )
		) );

		if (!wp_script_is( 'wp-event-manager-content-event-listing', 'enqueued' ))
			wp_enqueue_script( 'wp-event-manager-content-event-listing');

		//ajax filters js
		wp_register_script( 'wp-event-manager-ajax-filters', EVENT_MANAGER_PLUGIN_URL . '/assets/js/event-ajax-filters.min.js', $ajax_filter_deps, EVENT_MANAGER_VERSION, true );
		wp_localize_script( 'wp-event-manager-ajax-filters', 'event_manager_ajax_filters', array(
			'ajax_url'                => $ajax_url,
			'is_rtl'                  => is_rtl() ? 1 : 0,
			'lang'                    => apply_filters( 'wpem_lang', null ) //defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '', // WPML workaround until this is standardized			
		) );

		if (!wp_script_is( 'wp-event-manager-ajax-filters', 'enqueued' ))
			wp_enqueue_script( 'wp-event-manager-ajax-filters');


		wp_enqueue_style( 'wp-event-manager-jquery-ui-css', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-ui/jquery-ui.css');
		
		wp_enqueue_style( 'wp-event-manager-grid-style', EVENT_MANAGER_PLUGIN_URL . '/assets/css/wpem-grid.min.css');
		wp_enqueue_style( 'wp-event-manager-font-style', EVENT_MANAGER_PLUGIN_URL . '/assets/fonts/style.css');
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @access private
	 */
	private function include_widgets_files() {
		require_once( __DIR__ . '/elementor-widgets/elementor-submit-event-form.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-event-listing.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-single-event.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-single-event-summary.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-event-dashboard.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-past-event-listing.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-single-event-field.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-upcoming-event-listing.php' );

		require_once( __DIR__ . '/elementor-widgets/elementor-submit-organizer-form.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-organizer-dashboard.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-single-event-organizer.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-single-organizer.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-event-organizers.php' );

		require_once( __DIR__ . '/elementor-widgets/elementor-submit-venue-form.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-venue-dashboard.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-single-event-venue.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-single-venue.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-event-venues.php' );
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @access public
	 */
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Submit_Event_Form() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Event_Listing() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Single_Event() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Event_Summary() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Event_Dashboard() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Past_Event_Listing() );				
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Event_Field() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Upcoming_Event_Listing() );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Organizer_Dashboard() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Submit_Organizer_Form() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Single_Event_Organizer() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Single_Organizer() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Event_Organizers() );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Venue_Dashboard() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Submit_Venue_Form() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Single_Event_Venue() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Single_Venue() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Event_Venues() );
	}

	/**
	 * Register new group for WP-event-manager core tag
	 * @param $elementsManager
	 */
	public function register_groups() {
		$elementsManager = \Elementor\Plugin::$instance->dynamic_tags;
		
		$elementsManager->register_group(
			'wp-event-manager-groups', [
				'title' => 'WP Event Manager'
			]
		);
	}

	/**
	 * Include Tags files
	 *
	 * Load tags files
	 *
	 * @access private
	 */
	private function include_tags_files() {		
		require_once( __DIR__ . '/elementor-tags/elementor-single-event-tag.php' );
		require_once( __DIR__ . '/elementor-tags/elementor-single-event-image-tag.php' );
		require_once( __DIR__ . '/elementor-tags/elementor-single-event-gallery-tag.php' );
	}

	/**
	 * Register Tags
	 *
	 * Register new Elementor widgets.
	 *
	 * @access public
	 */
	public function register_tags($dynamic_tags) {
		// Register Groups
		$this->register_groups();

		// Its is now safe to include Tags files
		$this->include_tags_files();

		// Register Tags
		$dynamic_tags->register_tag( new Tags\Elementor_Event_Tag() );
		$dynamic_tags->register_tag( new Tags\Elementor_Event_Image_Tag() );
		$dynamic_tags->register_tag( new Tags\Elementor_Event_Gallery_Tag() );
	}

}

// Instantiate Plugin Class
Plugin::instance();
