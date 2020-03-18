<?php
namespace WPEventManager\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Single Event Summary
 *
 * Elementor widget for single event field.
 *
 */
class Elementor_Event_Field extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'single-event-field';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Single Event Field', 'wp-event-manager' );
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
		return [ 'single-event-fields', 'code' ];
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
				'label' => __( 'Event Field', 'wp-event-manager' ),
			]
		);

		$arrOption = [];

		if(!class_exists('WP_Event_Manager_Form_Submit_Event') ) {
			include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php' );
			include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php' );	
		}
		$form_submit_event_instance = call_user_func( array( 'WP_Event_Manager_Form_Submit_Event', 'instance' ) );
		$fields = $form_submit_event_instance->merge_with_custom_fields('backend');

		foreach($fields  as $group_key => $group_fields){
			foreach ( $group_fields as $field_key => $field ) {
				$arrOption[$field_key] = $field['label'];
			}
		}

		$this->add_control(
			'event_field_before_html',
			[
				'label' => 'Event Field Before HTML',
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Event Field Before HTML', 'elementor' ),
				'show_label' => false,
			]
		);

		$this->add_control(
			'event_field',
			[
				'label'	=> __( 'Event Field', 'wp-event-manager' ),
				'type'  => Controls_Manager::SELECT,
				'options' => $arrOption,
			]
		);

		$this->add_control(
			'event_field_after_html',
			[
				'label' => 'Event Field After HTML',
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Event Field After HTML', 'elementor' ),
				'show_label' => false,
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

		$post_id = get_the_ID();

		$event = get_post($post_id);

		echo $settings['event_field_before_html'];

		if(isset($settings['event_field']) && $settings['event_field'] != '')
		{
			if($settings['event_field'] == 'event_title')
			{
				display_event_title($event);
			}
			else if($settings['event_field'] == 'event_type')
			{
				display_event_type($event, '');
			}
			else if($settings['event_field'] == 'event_category')
			{
				display_event_category($event, '');
			}
			else if($settings['event_field'] == 'event_online')
			{
				$is_event_online = is_event_online($event);

				if(!$is_event_online)
				{
					echo $is_event_online;
				}
			}
			else if($settings['event_field'] == 'event_venue_name')
			{
				display_event_venue_name('', '', true, $event);
			}
			else if($settings['event_field'] == 'event_address')
			{
				display_event_address('', '', true, $event);
			}
			else if($settings['event_field'] == 'event_pincode')
			{
				display_event_address('', '', true, $event);
			}
			else if($settings['event_field'] == 'event_location')
			{
				display_event_location(true, $event);
			}
			else if($settings['event_field'] == 'event_banner')
			{
				display_event_banner('full', '', $event);
			}
			else if($settings['event_field'] == 'event_description')
			{
				echo get_event_description($event);
			}
			else if($settings['event_field'] == 'registration')
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
			else if($settings['event_field'] == 'event_start_date')
			{
				display_event_start_date('', '', true, $event);
			}
			else if($settings['event_field'] == 'event_start_time')
			{
				display_event_start_time('', '', true, $event);
			}
			else if($settings['event_field'] == 'event_end_date')
			{
				display_event_end_date('', '', true, $event);
			}
			else if($settings['event_field'] == 'event_end_time')
			{
				display_event_end_time('', '', true, $event);
			}
			else if($settings['event_field'] == 'event_registration_deadline')
			{
				display_event_registration_end_date('', '', true, $event);
			}
			else if($settings['event_field'] == 'organizer_name')
			{
				display_organizer_name('', '', true, $event);
			}
			else if($settings['event_field'] == 'organizer_logo')
			{
				display_organizer_logo('full', '', $event);
			}
			else if($settings['event_field'] == 'organizer_description')
			{
				echo get_organizer_description($event);
			}
			else if($settings['event_field'] == 'organizer_email')
			{
				display_organizer_email('', '', true, $event);
			}
			else if($settings['event_field'] == 'organizer_website')
			{
				display_organizer_website('', '', true, $event);
			}
			else if($settings['event_field'] == 'organizer_twitter')
			{
				display_organizer_twitter('', '', true, $event);
			}
			else if($settings['event_field'] == 'organizer_youtube')
			{
				display_organizer_youtube('', '', true, $event);
			}
			else if($settings['event_field'] == 'organizer_facebook')
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

		echo $settings['event_field_after_html'];
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
