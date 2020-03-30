<?php

namespace WPEventManager\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Single Event Summary
 *
 * Elementor tag for single event tag.
 * https://developers.elementor.com/dynamic-tags/
 */
class Elementor_Event_Tag extends Tag {

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
		return 'single-event-tag';
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
		return __( 'Single Event Tag', 'wp-event-manager' );
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
		return [ Module::TEXT_CATEGORY ];
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
				if(in_array($field['type'], ['text', 'term-select', 'radio', 'wp-editor', 'date', 'time']))
				{
					$arrOption[$field_key] = $field['label'];	
				}
			}
		}

		$this->add_control(
			'event_tag',
			[
				'label' => __( 'Event Tag', 'wp-event-manager' ),
				'type' => Controls_Manager::SELECT,
				'options' => $arrOption,
			]
		);
	}

	/**
	* Render
	*
	* Prints out the value of the Dynamic tag
	*
	* @since 3.1.12
	* @access public
	*
	* @return void
	*/
	public function render() {
		$event_tag = $this->get_settings( 'event_tag' );

        $post_id = get_the_ID();

		$event = get_post($post_id);

		if(isset($event_tag) && $event_tag != '')
		{
			if($event_tag == 'event_title')
			{
				display_event_title($event);
			}
			else if($event_tag == 'event_type')
			{
				display_event_type($event, '');
			}
			else if($event_tag == 'event_category')
			{
				display_event_category($event, '');
			}
			else if($event_tag == 'event_online')
			{
				$is_event_online = is_event_online($event);

				if(!$is_event_online)
				{
					echo $is_event_online;
				}
			}
			else if($event_tag == 'event_venue_name')
			{
				display_event_venue_name('', '', true, $event);
			}
			else if($event_tag == 'event_address')
			{
				display_event_address('', '', true, $event);
			}
			else if($event_tag == 'event_pincode')
			{
				display_event_address('', '', true, $event);
			}
			else if($event_tag == 'event_location')
			{
				display_event_location(true, $event);
			}
			else if($event_tag == 'event_description')
			{
				echo get_event_description($event);
			}
			else if($event_tag == 'registration')
			{
				$register = get_event_registration_method($event);
				wp_enqueue_script( 'wp-event-manager-event-registration' );

				if(isset($register->url) || isset($register->raw_email)):
				
					if($register->type == 'email')
					{
						$register_data = $register->raw_email;
					}
					else
					{
						$register_data = $register->url;
					}

					if($register_data != ''):

					?>
					<div class="event_registration registration">
						<?php do_action( 'event_registration_start', $register ); ?>
						<div class="wpem-event-sidebar-button wpem-registration-event-button">
						<input type="button" class="registration_button wpem-theme-button" value="<?php _e( 'Register for event', 'wp-event-manager' ); ?>" />
						</div>
						<div class="registration_details wpem-register-event-form" style="display: none;">
							<?php
								/**
								 * event_manager_registration_details_email or event_manager_registration_details_url hook
								 */
								do_action( 'event_manager_registration_details_' . $register->type, $register );
							?>
						</div>
						<?php do_action( 'event_registration_end', $register ); ?>
					</div>

					<?php endif; ?>

				<?php endif;
			}
			else if($event_tag == 'event_start_date')
			{
				display_event_start_date('', '', true, $event);
			}
			else if($event_tag == 'event_start_time')
			{
				display_event_start_time('', '', true, $event);
			}
			else if($event_tag == 'event_end_date')
			{
				display_event_end_date('', '', true, $event);
			}
			else if($event_tag == 'event_end_time')
			{
				display_event_end_time('', '', true, $event);
			}
			else if($event_tag == 'event_registration_deadline')
			{
				display_event_registration_end_date('', '', true, $event);
			}
			else if($event_tag == 'organizer_name')
			{
				display_organizer_name('', '', true, $event);
			}
			else if($event_tag == 'organizer_description')
			{
				echo get_organizer_description($event);
			}
			else if($event_tag == 'organizer_email')
			{
				display_organizer_email('', '', true, $event);
			}
			else if($event_tag == 'organizer_website')
			{
				display_organizer_website('', '', true, $event);
			}
			else if($event_tag == 'organizer_twitter')
			{
				display_organizer_twitter('', '', true, $event);
			}
			else if($event_tag == 'organizer_youtube')
			{
				display_organizer_youtube('', '', true, $event);
			}
			else if($event_tag == 'organizer_facebook')
			{
				display_organizer_facebook('', '', true, $event);
			}
			else
			{
				echo get_post_meta($post_id, $settings['event_field'], true);
			}
		}
		else
		{
			display_event_title($event);
		}
	}

	
}
