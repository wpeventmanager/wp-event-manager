<?php
namespace WPEventManager\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Event Venues
 *
 * Elementor widget for Event Venues.
 *
 */
class Elementor_Event_Venues extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'event-venues';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Event Venues', 'wp-event-manager' );
	}
	/**	
	 * Get widget icon.
	 *
	 * Retrieve shortcode widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-list';		
	}
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'event-venues', 'code' ];
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'wp-event-manager-categories' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_shortcode',
			[
				'label' => __( 'Event Venues', 'wp-event-manager' ),
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC' => __( 'Ascending (ASC)', 'wp-event-manager' ),
					'DESC' => __( 'Descending  (DESC)', 'wp-event-manager' ),
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order By', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => [
					'title' => __( 'Title', 'wp-event-manager' ),
					'ID' => __( 'ID', 'wp-event-manager' ),
					'name' => __( 'Name', 'wp-event-manager' ),
					'modified' => __( 'Modified', 'wp-event-manager' ),
					'rand' => __( 'Rand', 'wp-event-manager' ),
				],
			]
		);

		$this->add_control(
			'show_thumb',
			[
				'label' => __( 'Show Thumb', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'true',
				'options' => [
					'false' => __( 'False', 'wp-event-manager' ),
					'true' => __( 'True', 'wp-event-manager' ),
				],
			]
		);

		$this->add_control(
			'show_count',
			[
				'label' => __( 'Show Count', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'true',
				'options' => [
					'false' => __( 'False', 'wp-event-manager' ),
					'true' => __( 'True', 'wp-event-manager' ),
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		
		echo do_shortcode('[event_venues order="'.$settings['order'].'" orderby="'.$settings['orderby'].'" show_thumb="'.$settings['show_thumb'].'" show_count="'.$settings['show_count'].'"]');
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function _content_template() {}
}
