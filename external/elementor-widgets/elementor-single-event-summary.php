<?php
namespace WPEventManager\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Single Event Summary
 *
 * Elementor widget for single event summary.
 *
 */
class Elementor_Event_Summary extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'single-event-summary';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Single Event Summary', 'wp-event-manager' );
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
		return 'eicon-archive-title';
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
		return [ 'single-event-summary', 'code' ];
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
	protected function register_controls() {
		$this->start_controls_section(
			'section_shortcode',
			[
				'label' => __( 'Event Summary', 'wp-event-manager' ),
			]
		);

		$args = array(
				'post_type'		=> 'event_listing',
				'post_status'	=> 'publish',
				'posts_per_page'=> -1,
				'suppress_filters' => 0,
		);

		$events = get_posts( $args );

		$options = [];
		if(!empty($events))
		{
			foreach ($events as $event) {
				$options[$event->ID] = $event->post_title;
			}
		}
		else
		{
			$options[] = __( 'Not Found Event', 'wp-event-manager' );
		}

		$this->add_control(
			'event_id',
			[
				'label'     => __( 'Select Event', 'wp-event-manager' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'	=> $options
			]
		);

		$this->add_control(
			'width',
			[
				'default' 	=> '250px',
				'label'     => __( 'Width', 'wp-event-manager' ),
				'type'      => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'align',
			[
				'label' => __( 'Align', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __( 'Left', 'wp-event-manager' ),
					'center' => __( 'Center', 'wp-event-manager' ),
					'right' => __( 'Right', 'wp-event-manager' ),
				],
			]
		);

		$this->add_control(
			'featured',
			[
				'label' => __( 'Featured', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'false',
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
		
		if($settings['event_id']>0)
		    $event_id = 'id="'.esc_attr($settings['event_id']).'"';
	    else
	        $event_id = '';

	    if($settings['width']>0)
		    $width = 'width="'.esc_attr($settings['width']).'"';
	    else
	        $width = '';

	    if($settings['align'] != '')
		    $align = 'align="'.esc_attr($settings['align']).'"';
	    else
	        $align = '';

	    if($settings['featured'] != '')
		    $featured = 'featured="'.esc_attr($settings['featured']).'"';
	    else
	        $featured = '';

	    echo do_shortcode('[event_summary '.$event_id.' '.$width.' '.$align.' '.$featured.' ]');
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {}
}
