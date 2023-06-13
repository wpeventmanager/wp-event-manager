<?php
namespace WPEventManager\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Elementor Single Venue
 *
 * Elementor widget for single Venue.
 *
 */
class Elementor_Single_Venue extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'single-venue';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Single Venue', 'wp-event-manager');
	}
	/**	
	 * Get widget icon.
	 * Retrieve shortcode widget icon.
	 *
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-single-page';		
	}
	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'single-venue', 'code' ];
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
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'wp-event-manager-categories' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_shortcode',
			['label' => __('Single Venue', 'wp-event-manager'),]
		);
		$args = array(
				'post_type'		=> 'event_venue',
				'post_status'	=> 'publish',
				'posts_per_page'=> -1,
				'suppress_filters' => 0,
		);
		$rganizers = get_posts($args);

		$options = [];
		if(!empty($rganizers)){
			foreach ($rganizers as $rganizer) {
				$options[$rganizer->ID] = $rganizer->post_title;
			}
		}else{
			$options[] = __('Not Found Venue', 'wp-event-manager');
		}
		$this->add_control(
			'venue_id',
			[
				'label'     => __('Select Venue', 'wp-event-manager'),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'	=> $options
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
		if($settings['venue_id']>0){
		    $venue_id = 'id='.esc_attr($settings['venue_id']);
		    $settings['venue_id']='id='.esc_attr($settings['venue_id']);
		}else{
		    $venue_id = '';
		    $settings['venue_id']='';
		}
		echo do_shortcode('[event_venue '.$venue_id.' ]');
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