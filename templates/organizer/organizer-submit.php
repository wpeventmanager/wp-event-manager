<?php

/**
 * Event Submission Form
 */
if (!defined('ABSPATH'))
    exit;

global $event_manager;
?>

<form action="<?php echo esc_url($action); ?>" method="post" id="submit-organizer-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
    <?php
    if (is_user_logged_in()) {

        // if field value is not set apply current user details
        $user = wp_get_current_user();
        $username = !empty($user->display_name) ? $user->display_name : $user->user_login;

        if (!isset($organizer_fields['organizer_name']['value']) && empty($organizer_fields['organizer_name']['value']) && (isset($organizer_fields['organizer_name']['visibility']) && $organizer_fields['organizer_name']['visibility'] != 0)) {
            $organizer_fields['organizer_name']['value'] = $username;
        }
        if (!isset($organizer_fields['organizer_email']['value']) && empty($organizer_fields['organizer_email']['value']) && (isset($organizer_fields['organizer_email']['visibility']) && $organizer_fields['organizer_email']['visibility'] != 0)) {
            $organizer_fields['organizer_email']['value'] = $user->user_email;
        }
    ?>
        <h2 class="wpem-form-title wpem-heading-text"><?php _e('Organizer Details', 'wp-event-manager'); ?></h2>
        <?php
        if (isset($resume_edit) && $resume_edit) {
            printf('<p class="wpem-alert wpem-alert-info"><strong>' . __("You are editing an existing organizer. %s", "wp-event-manager") . '</strong></p>', '<a href="?new=1&key=' . $resume_edit . '">' . __('Create A New organizer', 'wp-event-manager') . '</a>');
        }
        ?>

        <?php do_action('submit_organizer_form_organizer_fields_start'); ?>
        <?php foreach ($organizer_fields as $key => $field) : ?>
            <fieldset class="wpem-form-group fieldset-<?php echo esc_attr($key); ?>">
                <label for="<?php esc_attr_e($key); ?>"><?php echo __($field['label'], 'wp-event-manager') . apply_filters('submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $field); ?></label>
                <div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
                    <?php get_event_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
                </div>
            </fieldset>
        <?php endforeach; ?>
        <?php do_action('submit_organizer_form_organizer_fields_end'); ?>

        <div class="wpem-form-footer">
            <input type="hidden" name="event_manager_form" value="<?php echo $form; ?>" />
            <input type="hidden" name="organizer_id" value="<?php echo esc_attr($organizer_id); ?>" />
            <input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
            <input type="submit" name="submit_organizer" id="submit-organizer-button" class="wpem-theme-button" value="<?php esc_attr_e($submit_button_text); ?>" />
        </div>

    <?php
    } else {
    ?>
        <div class="wpem-form-group">
            <label class="wpem-form-label-text"><?php _e('Have an account?', 'wp-event-manager'); ?></label>
            <div class="field account-sign-in wpem-alert wpem-alert-info">
                <a href="<?php echo !empty(get_option('event_manager_login_page_url')) ? apply_filters('submit_event_form_login_url', get_option('event_manager_login_page_url')) : home_url() . '/wp-login.php'; ?>"><?php _e('Log In', 'wp-event-manager'); ?></a>
                <?php echo __(" to Submit the List of Organizers from your account.", "wp-event-manager"); ?>
            </div>
        </div>
    <?php }
    ?>
</form>