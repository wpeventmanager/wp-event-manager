<?php

namespace WPEventManager\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

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
        return __('Single Event Tag', 'wp-event-manager');
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
        return [Module::TEXT_CATEGORY];
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

        if (!class_exists('WP_Event_Manager_Form_Submit_Event')) {
            include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php' );
            include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php' );
        }
        $form_submit_event_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Event', 'instance'));
        $fields = $form_submit_event_instance->merge_with_custom_fields('backend');

        foreach ($fields as $group_key => $group_fields) {
            foreach ($group_fields as $field_key => $field) {
                //if(in_array($field['type'], ['text', 'term-select', 'radio', 'wp-editor', 'date', 'time', 'select', 'multiselect']))
                if (!in_array($field['type'], ['file', 'hidden'])) {
                    $arrOption[$field_key] = $field['label'];
                }
            }
        }

        $arrOption['view_count'] = __('View Count', 'wp-event-manager');
        $arrOption['event_ticket_type'] = __('Ticket Type', 'wp-event-manager');
        $arrOption['event_share'] = __('Share Event', 'wp-event-manager');

        $this->add_control(
                'event_tag',
                [
                    'label' => __('Event Tag', 'wp-event-manager'),
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
        $event_tag = $this->get_settings('event_tag');

        $post_id = get_the_ID();

        $event = get_post($post_id);

        if (isset($event_tag) && $event_tag != '') {
            if ($event_tag == 'event_title') {
                display_event_title($event);
            } else if ($event_tag == 'event_type') {
                display_event_type($event, '');
            } else if ($event_tag == 'event_category') {
                display_event_category($event, '');
            } else if ($event_tag == 'event_online') {
                $is_event_online = is_event_online($event);

                if (!$is_event_online) {
                    echo $is_event_online;
                }
            } else if ($event_tag == 'event_venue_name') {
                display_event_venue_name('', '', true, $event);
            } else if ($event_tag == 'event_address') {
                display_event_address('', '', true, $event);
            } else if ($event_tag == 'event_pincode') {
                display_event_address('', '', true, $event);
            } else if ($event_tag == 'event_location') {
                display_event_location(true, $event);
            } else if ($event_tag == 'event_description') {
                echo get_event_description($event);
            } else if ($event_tag == 'registration') {
                get_event_manager_template('event-registration.php');
            } else if ($event_tag == 'event_start_date') {
                display_event_start_date('', '', true, $event);
            } else if ($event_tag == 'event_start_time') {
                display_event_start_time('', '', true, $event);
            } else if ($event_tag == 'event_end_date') {
                display_event_end_date('', '', true, $event);
            } else if ($event_tag == 'event_end_time') {
                display_event_end_time('', '', true, $event);
            } else if ($event_tag == 'event_registration_deadline') {
                display_event_registration_end_date('', '', true, $event);
            } else if ($event_tag == 'organizer_name') {
                display_organizer_name('', '', true, $event);
            } else if ($event_tag == 'organizer_logo') {
                display_organizer_logo('full', '', $event);
            } else if ($event_tag == 'organizer_description') {
                echo get_organizer_description($event);
            } else if ($event_tag == 'organizer_email') {
                display_organizer_email('', '', true, $event);
            } else if ($event_tag == 'event_organizer_ids') {
                echo get_organizer_name($event, true);
            } else if ($event_tag == 'organizer_website') {
                display_organizer_website('', '', true, $event);
            } else if ($event_tag == 'organizer_twitter') {
                display_organizer_twitter('', '', true, $event);
            } else if ($event_tag == 'organizer_youtube') {
                display_organizer_youtube('', '', true, $event);
            } else if ($event_tag == 'organizer_facebook') {
                display_organizer_facebook('', '', true, $event);
            } else if ($event_tag == 'view_count') {
                $view_count = get_post_views_count($event);

                if ($view_count) :
                    ?>
                    <i class="wpem-icon-eye"></i> <?php echo $view_count; ?>
                    <?php
                endif;
            } else if ($event_tag == 'event_ticket_type') {
                if (get_event_ticket_option($event)) :
                    ?>
                    <div class="wpem-event-ticket-type" class="wpem-event-ticket-type-text">
                        <span class="wpem-event-ticket-type-text"><?php display_event_ticket_option('', '', true, $event); ?></span>
                    </div>
                    <?php
                endif;
            } else if ($event_tag == 'event_category') {
                display_event_category($event);
            } else if ($event_tag == 'event_registration_deadline') {
                if (get_event_registration_end_date($event)) {
                    display_event_registration_end_date($event);
                }
            } else if ($event_tag == 'event_share') {
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
            } else {
                $field_value = get_post_meta($post_id, '_' . $event_tag, true);
                if (!class_exists('WP_Event_Manager_Form_Submit_Event')) {
                    include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php' );
                    include_once( EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php' );
                }
                $form_submit_event_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Event', 'instance'));
                $fields = $form_submit_event_instance->merge_with_custom_fields('backend');

                foreach ($fields as $group_key => $group_fields) {
                    foreach ($group_fields as $field_key => $field) {
                        //if(in_array($field['type'], ['text', 'term-select', 'radio', 'wp-editor', 'date', 'time', 'select', 'multiselect']))
                        
                        if (in_array($field['type'], ['select', 'multiselect']) && $event_tag == $field_key) {
                            if (is_array($field_value) && !empty($field_value)) {
                                foreach ($field_value as $key => $value) {
                                    if (isset($field['options'][$value]))
                                        echo __($field['options'][$value], 'wp-event-manager');
                                }
                            } elseif (isset($field['options'][$field_value]))
                                echo __($field['options'][$field_value], 'wp-event-manager');
                        } elseif ($event_tag == $field_key)
                            echo __($field_value, 'wp-event-manager');
                    }
                }
            }
        } else {
            display_event_title($event);
        }
    }

}
