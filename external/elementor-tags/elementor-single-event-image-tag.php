<?php

namespace WPEventManager\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Single Event Summary
 *
 * Elementor tag for single event tag.
 * https://developers.elementor.com/dynamic-tags/
 * media/data/kishan/site/wpem/wp-content/plugins/elementor-pro/modules/dynamic-tags/acf/tags/acf-image.php
 */
class Elementor_Event_Image_Tag extends Data_Tag {

	/**
	* Get Name
	*
	* Returns the Name of the tag
	*
	* @since 3.1.12
	* @access public
	*
	* @return string
	*/
	public function get_name() {
		return 'single-event-image-tag';
	}

	/**
	* Get Title
	*
	* Returns the title of the Tag
	*
	* @since 3.1.12
	* @access public
	*
	* @return string
	*/
	public function get_title() {
		return __( 'Single Event Image Tag', 'wp-event-manager' );
	}
   
	/**
	* Get Group
	*
	* Returns the Group of the tag
	*
	* @since 3.1.12
	* @access public
	*
	* @return string
	*/
	public function get_group() {
		return 'wp-event-manager-groups';
	}

	/**
	* Get Categories
	*
	* Returns an array of tag categories
	*
	* @since 3.1.12
	* @access public
	*
	* @return array
	*/
	public function get_categories() {
		return [ Module::IMAGE_CATEGORY ];
	}

	/**
	* Get Setting Key
	*
	* Returns the Group of the tag
	*
	* @since 3.1.12
	* @access public
	*
	* @return string
	*/	
	public function get_panel_template_setting_key() {
		return 'event_image_tag';
	}


	/**
	* Get Value
	*
	* Prints out the value of the Dynamic tag
	*
	* @since 3.1.12
	* @access public
	*
	* @return void
	*/
	public function get_value( array $options = [] ) {
		$event_image_tag = $this->get_settings( 'event_image_tag' );

        $post_id = get_the_ID();

		$event = get_post($post_id);

		$image_data = [
			'id' => null,
			'url' => '',
		];

		if(isset($event_image_tag) && $event_image_tag != '')
		{
			if($event_image_tag == 'event_banner')
			{
				$value['id']  	= 0;
				$value['url'] 	= get_event_banner($event);
			}
			else if($event_image_tag == 'organizer_logo')
			{
				$value['id'] 	= 0;
				$value['url'] 	= get_organizer_logo( $event, 'full' );
			}
		}

		if ( empty( $value ) && $this->get_settings( 'fallback_image' ) ) {
			$value = $this->get_settings( 'fallback_image' );
		}

		if ( ! empty( $value ) ) {
			$image_data['id'] = $value['id'];
			$image_data['url'] = $value['url'];
		}

		return $image_data;
	}



	/**
	* Register Controls
	*
	* Registers the Dynamic tag controls
	*
	* @since 3.1.12
	* @access protected
	*
	* @return void
	*/
	protected function _register_controls() {

		$arrOption = [];

		if(!class_exists('WP_Event_Manager_Form_Submit_Event') ) {
			include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php' );
			include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php' );	
		}
		$form_submit_event_instance = call_user_func( array( 'WP_Event_Manager_Form_Submit_Event', 'instance' ) );
		$fields = $form_submit_event_instance->merge_with_custom_fields('backend');

		foreach($fields  as $group_key => $group_fields)
		{
			foreach ( $group_fields as $field_key => $field )
			{
				if(in_array($field['type'], ['file']))
				{
					$arrOption[$field_key] = $field['label'];	
				}
			}
		}

		$this->add_control(
			'event_image_tag',
			[
				'label' => __( 'Event Image Tag', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'options' => $arrOption,
			]
		);

		$this->add_control(
			'fallback_image',
			[
				'label' => __( 'Fallback', 'wp-event-manager' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
	}

	

	protected function get_supported_fields() {
		return [
			'image',
		];
	}

	
}
