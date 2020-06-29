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
		require_once( __DIR__ . '/elementor-widgets/elementor-single-event-organizer.php' );
		require_once( __DIR__ . '/elementor-widgets/elementor-single-organizer.php' );
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
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Single_Event_Organizer() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Single_Organizer() );
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
