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
 * media/data/kishan/site/wpem/wp-content/plugins/elementor-pro/modules/dynamic-tags/acf/tags/acf-gallery.php
 */
class Elementor_Event_Gallery_Tag extends Data_Tag {

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
		return 'single-event-gallery-tag';
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
		return __( 'Single Event Gallery Tag', 'wp-event-manager' );
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
		return [ Module::GALLERY_CATEGORY ];
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
		return 'event_gallery_tag';
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
		$event_gallery_tag = $this->get_settings( 'event_gallery_tag' );

        $post_id = get_the_ID();

		$event = get_post($post_id);

		$images = [];

		if(isset($event_gallery_tag) && $event_gallery_tag != '')
		{
			if($event_gallery_tag == 'event_banner')
			{
				$arrImage = get_event_banner($event);

				if(!empty($arrImage))
				{
					if(is_array($arrImage))
					{
						foreach ($arrImage as $key => $url) 
						{
							$id = attachment_url_to_postid($url);
							$images[] = [
								'id' => $id,
							];
						}
					}
					else
					{
						$id = attachment_url_to_postid($arrImage);

						$images[] = [
							'id' => $id,
						];
					}
				}
			}
		}

		return $images;
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
	protected function register_controls() {

		$arrOption = [];

		if(!class_exists('WP_Event_Manager_Form_Submit_Event') ) {
			include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php' );
			include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php' );	
		}
		$form_submit_event_instance = call_user_func( array( 'WP_Event_Manager_Form_Submit_Event', 'instance' ) );
		$fields = $form_submit_event_instance->merge_with_custom_fields('backend');

		foreach($fields  as $group_key => $group_fields)
		{
			if(in_array($group_key, ['event']))
			{
				foreach ( $group_fields as $field_key => $field )
				{
					if(in_array($field['type'], ['file']))
					{
						$arrOption[$field_key] = $field['label'];	
					}
				}
			}			
		}

		$this->add_control(
			'event_gallery_tag',
			[
				'label' => __( 'Event Gallery Tag', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'options' => $arrOption
			]
		);
	}

	

	protected function get_supported_fields() {
		return [
			'gallery',
		];
	}

	
}
