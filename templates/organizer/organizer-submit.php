<?php
/**
 * Event Submission Form
 */
if (!defined('ABSPATH'))
    exit;
global $event_manager; 
do_action('wp_event_manager_organizer_submit_before');
$allowed_field_types = array_keys(wpem_get_form_field_types()); ?>

<form action="<?php echo esc_url($action); ?>" method="post" id="submit-organizer-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
    <?php  if (is_user_logged_in()) { ?>

        <h2 class="wpem-form-title wpem-heading-text"><?php esc_html_e('Organizer Details', 'wp-event-manager'); ?></h2>
        <?php if (isset($resume_edit) && $resume_edit) {
            // Translators: %s is a link to create a new organizer.
            printf(
                '<p class="wpem-alert wpem-alert-info"><strong>%s</strong></p>',
                sprintf(
                    esc_html__( 'You are editing an existing organizer. %s', 'wp-event-manager' ),
                    sprintf(
                        '<a href="?new=1&key=%s">%s</a>',
                        esc_attr( $resume_edit ),
                        esc_html__( 'Create a new organizer', 'wp-event-manager' )
                    )
                )
            );
        }        
        do_action('submit_organizer_form_organizer_fields_start'); 
        foreach ($organizer_fields as $key => $field) : 
            if(isset($field['visibility']) && ($field['visibility'] == 0 || $field['visibility'] === false)) :
                continue;
            endif;?>
            <fieldset class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
               <label for="<?php echo esc_attr($key); ?>">
                    <?php echo esc_html($field['label'], 'wp-event-manager'); 
                    echo wp_kses_post(apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field)); ?></label>
                <div class="field <?php echo esc_attr($field['required'] ? 'required-field' : ''); ?>">
                    <?php $field_type = in_array($field['type'], $allowed_field_types, true) ? $field['type'] : 'text';
                    get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
                </div>
            </fieldset>
        <?php endforeach;
        do_action('submit_organizer_form_organizer_fields_end'); ?>

        <div class="wpem-form-footer">
            <input type="hidden" name="event_manager_form" value="<?php echo esc_attr($form); ?>" />
            <input type="hidden" name="organizer_id" value="<?php echo esc_attr($organizer_id); ?>" />
            <input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
            <input type="submit" name="submit_organizer" id="submit-organizer-button" class="wpem-theme-button" value="<?php echo esc_attr($submit_button_text, 'wp-event-manager'); ?>" />
        </div>
    <?php } else {   ?>
        <div class="wpem-form-group">
            <label class="wpem-form-label-text"><?php esc_html_e('Have an account?', 'wp-event-manager'); ?></label>
            <div class="field account-sign-in wpem-alert wpem-alert-info">
                <a href="<?php echo !empty(get_option('event_manager_login_page_url')) ? esc_url(apply_filters('submit_event_form_login_url', get_option('event_manager_login_page_url'))) :esc_url(home_url() . '/wp-login.php'); ?>"><?php esc_html_e('Log In', 'wp-event-manager'); ?></a>
                <?php esc_html_e(" to Submit the List of Organizers from your account.", "wp-event-manager"); ?>
            </div>
        </div>
    <?php } ?>
</form>