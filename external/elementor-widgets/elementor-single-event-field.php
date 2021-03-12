<?php

namespace WPEventManager\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

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
        return __('Single Event Field', 'wp-event-manager');
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
        return ['single-event-fields', 'code'];
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
        return ['wp-event-manager-categories'];
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
                    'label' => __('Event Field', 'wp-event-manager'),
                ]
        );

        $arrOption = [];

        if (!class_exists('WP_Event_Manager_Form_Submit_Event')) {
            include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php' );
            include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php' );
        }
        $form_submit_event_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Event', 'instance'));
        $fields = $form_submit_event_instance->merge_with_custom_fields('backend');
        foreach ($fields as $group_key => $group_fields) {
            foreach ($group_fields as $field_key => $field) {
                $arrOption[$field_key] = $field['label'];
            }
        }

        $arrOption['view_count'] = __('View Count', 'wp-event-manager');
        $arrOption['event_ticket_type'] = __('Ticket Type', 'wp-event-manager');
        $arrOption['event_share'] = __('Share Event', 'wp-event-manager');

        //unset field
        if (isset($arrOption['paid_tickets']))
            unset($arrOption['paid_tickets']);

        if (isset($arrOption['free_tickets']))
            unset($arrOption['free_tickets']);

        if (isset($arrOption['donation_tickets']))
            unset($arrOption['donation_tickets']);

        $this->add_control(
                'event_field_before_html',
                [
                    'label' => 'Event Field Before HTML',
                    'type' => Controls_Manager::TEXTAREA,
                    'placeholder' => __('Event Field Before HTML', 'elementor'),
                    'show_label' => false,
                ]
        );

        $this->add_control(
                'event_field',
                [
                    'label' => __('Event Field', 'wp-event-manager'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $arrOption,
                ]
        );

        $this->add_control(
                'event_field_after_html',
                [
                    'label' => 'Event Field After HTML',
                    'type' => Controls_Manager::TEXTAREA,
                    'placeholder' => __('Event Field After HTML', 'elementor'),
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

        if (isset($settings['event_field']) && $settings['event_field'] != '') {
            if ($settings['event_field'] == 'event_title') {
                display_event_title($event);
            } else if ($settings['event_field'] == 'event_type') {
                display_event_type($event, '');
            } else if ($settings['event_field'] == 'event_category') {
                display_event_category($event, '');
            } else if ($settings['event_field'] == 'event_online') {
                $is_event_online = is_event_online($event);

                if (!$is_event_online) {
                    echo get_event_location();
                } else {
                    echo __('Online Event', 'wp-event-manager');
                }
            } else if ($settings['event_field'] == 'event_venue_name') {
                display_event_venue_name('', '', true, $event);
            } else if ($settings['event_field'] == 'event_address') {
                display_event_address('', '', true, $event);
            } else if ($settings['event_field'] == 'event_pincode') {
                display_event_pincode('', '', true, $event);
            } else if ($settings['event_field'] == 'event_location') {
                display_event_location(true, $event);
            } else if ($settings['event_field'] == 'event_banner') {
                display_event_banner('full', '', $event);
            } else if ($settings['event_field'] == 'event_description') {
                echo get_event_description($event);
            } else if ($settings['event_field'] == 'registration') {

                $registration_end_date = get_event_registration_end_date($event);
                                $registration_end_date = !empty($registration_end_date) ? $registration_end_date.' 23:59:59' : '';
             // check if timezone settings is enabled as each event then set current time stamp according to the timezone
            // for eg. if each event selected then Berlin timezone will be different then current site timezone.
            $current_timestamp = strtotime(current_time('Y-m-d H:i:s'));


            if (!empty($registration_end_date) && strtotime($registration_end_date) < $current_timestamp)
            {
                echo '<div class="wpem-alert wpem-alert-warning">' . __('Event registration closed.', 'wp-event-manager') . '</div>';
            }
            else
                get_event_manager_template('event-registration.php');
            } else if ($settings['event_field'] == 'event_start_date') {
                display_event_start_date('', '', true, $event);
            } else if ($settings['event_field'] == 'event_start_time') {
                display_event_start_time('', '', true, $event);
            } else if ($settings['event_field'] == 'event_end_date') {
                display_event_end_date('', '', true, $event);
            } else if ($settings['event_field'] == 'event_end_time') {
                display_event_end_time('', '', true, $event);
            } else if ($settings['event_field'] == 'event_timezone') {
                display_event_timezone('', '', true, $event);
            } else if ($settings['event_field'] == 'event_registration_deadline') {
                display_event_registration_end_date('', '', true, $event);
            } else if ($settings['event_field'] == 'organizer_name') {
                display_organizer_name('', '', true, $event);
            } else if ($settings['event_field'] == 'organizer_logo') {
                display_organizer_logo('full', '', $event);
            } else if ($settings['event_field'] == 'organizer_description') {
                echo get_organizer_description($event);
            } else if ($settings['event_field'] == 'organizer_email') {
                display_organizer_email('', '', true, $event);
            } else if ($settings['event_field'] == 'event_organizer_ids') {
                echo get_organizer_name($event, true);
            } else if ($settings['event_field'] == 'organizer_website') {
                display_organizer_website('', '', true, $event);
            } else if ($settings['event_field'] == 'organizer_twitter') {
                display_organizer_twitter('', '', true, $event);
            } else if ($settings['event_field'] == 'organizer_youtube') {
                display_organizer_youtube('', '', true, $event);
            } else if ($settings['event_field'] == 'event_video_url') {
                ?>
                <?php if (get_organizer_youtube($event)) : ?>
                    <div class="clearfix">&nbsp;</div>
                    <button id="event-youtube-button" data-modal-id="wpem-youtube-modal-popup" class="wpem-theme-button wpem-modal-button"><?php _e('Watch video', 'wp-event-manager'); ?></button>
                    <div id="wpem-youtube-modal-popup" class="wpem-modal" role="dialog" aria-labelledby="<?php _e('Watch video', 'wp-event-manager'); ?>">
                        <div class="wpem-modal-content-wrapper">
                            <div class="wpem-modal-header">
                                <div class="wpem-modal-header-title"><h3 class="wpem-modal-header-title-text"><?php _e('Watch video', 'wp-event-manager'); ?></h3></div>
                                <div class="wpem-modal-header-close"><a href="javascript:void(0)" class="wpem-modal-close" id="wpem-modal-close">x</a></div>
                            </div>
                            <div class="wpem-modal-content">
                                <?php echo wp_oembed_get(get_organizer_youtube(), array('autoplay' => 1, 'rel' => 0)); ?>
                            </div>
                        </div>
                        <a href="#"><div class="wpem-modal-overlay"></div></a>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                <?php endif; ?>
                <?php
            } else if ($settings['event_field'] == 'organizer_facebook') {
                display_organizer_facebook('', '', true, $event);
            } else if ($settings['event_field'] == 'view_count') {
                $view_count = get_post_views_count($event);

                if ($view_count) :
                    ?>
                    <i class="wpem-icon-eye"></i> <?php echo $view_count; ?>
                    <?php
                endif;
            } else if ($settings['event_field'] == 'event_ticket_type') {
                if (get_event_ticket_option($event)) :
                    ?>
                    <div class="wpem-event-ticket-type" class="wpem-event-ticket-type-text">
                        <span class="wpem-event-ticket-type-text"><?php display_event_ticket_option('', '', true, $event); ?></span>
                    </div>
                    <?php
                endif;
            } else if ($settings['event_field'] == 'event_category') {
                display_event_category($event);
            } else if ($settings['event_field'] == 'event_registration_deadline') {
                if (get_event_registration_end_date($event)) {
                    display_event_registration_end_date($event);
                }
            } else if ($settings['event_field'] == 'event_share') {
                ?>
                <div class="wpem-share-this-event">
                    <div class="wpem-event-share-lists">
                        <?php do_action('single_event_listing_social_share_start'); ?>
                        <div class="wpem-social-icon wpem-facebook">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php display_event_permalink($event); ?>"
                               title="Share this page on Facebook">Facebook</a>
                        </div>
                        <div class="wpem-social-icon wpem-twitter">
                            <a href="https://twitter.com/share?text=twitter&url=<?php display_event_permalink($event); ?>"
                               title="Share this page on Twitter">Twitter</a>
                        </div>
                        <div class="wpem-social-icon wpem-linkedin">
                            <a href="https://www.linkedin.com/sharing/share-offsite/?&url=<?php display_event_permalink($event); ?>"
                               title="Share this page on Linkedin">Linkedin</a>
                        </div>
                        <div class="wpem-social-icon wpem-xing">
                            <a href="https://www.xing.com/spi/shares/new?url=<?php display_event_permalink($event); ?>"
                               title="Share this page on Xing">Xing</a>
                        </div>
                        <div class="wpem-social-icon wpem-pinterest">
                            <a href="https://pinterest.com/pin/create/button/?url=<?php display_event_permalink($event); ?>"
                               title="Share this page on Pinterest">Pinterest</a>
                        </div>
                        <?php do_action('single_event_listing_social_share_end'); ?>
                    </div>
                </div>
                <?php
            } else if ($settings['event_field'] == 'event_venue_ids') {
                echo get_event_venue_name($event, true);
            } else {

                $event_field = get_post_meta($post_id, '_' . $settings['event_field'], true);

                if (!empty($event_field)) {
                    if (is_array($event_field)) {
                        foreach ($event_field as $key => $value) {
                            $file_type = wp_check_filetype($event_field[$key]);
                            $allowed_file_type = event_manager_get_allowed_mime_types();
                            $img_types = array('image/jpeg', 'image/gif', 'image/png');
                            $file_types = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                            ?>
                            <div class="wpem-additional-info-block-details-content-items wpem-additional-file-slider">
                                <?php
                                if (in_array($file_type['type'], $allowed_file_type)) {
                                    if (in_array($file_type['type'], $img_types)) {
                                        ?>
                                        <div>
                                            <img src="<?php echo esc_url($event_field[$key]); ?>" alt="">
                                        </div>
                                    <?php } elseif (in_array($file_type['type'], $file_types)) {
                                        ?>
                                        <div class="wpem-icon">
                                            <p class="wpem-additional-info-block-title"><strong><?php echo esc_attr(wp_basename($event_field[$key])); ?></strong></p>
                                            <a target="_blank" class="wpem-icon-download3" href="<?php echo esc_url($event_field[$key]); ?>">
                                                <?php echo _e('Download', 'wp-event-manager'); ?>
                                            </a>
                                        </div>
                                        <?php
                                    } else {
                                        echo $event_field[$key];
                                    }
                                } else {
                                    echo $event_field[$key];
                                }
                                ?>
                            </div>
                            <?php
                        }
                    } else {
                        echo $event_field;
                    }
                }
            }
        } else {
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
    protected function _content_template() {
        
    }

}
