<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * group fields is generated from this page .
 * group fields for the paid and free group.
 * This field is used in submit event form.
 * */

//get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
$wpem_datepicker_date_format     = WP_Event_Manager_Date_Time::get_datepicker_format();

//covert datepicker format  into php date() function date format
$wpem_php_date_format        = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($wpem_datepicker_date_format);

if (!empty($wpem_field['value']) && is_array($wpem_field['value'])) : 
    foreach ($wpem_field['value'] as $wpem_index => $wpem_value) : ?>
        <div class="group-row-<?php echo esc_attr($wpem_index); ?>">
            <input type="hidden" class="group-row" name="repeated-row-<?php echo esc_attr($wpem_key); ?>[]" value="<?php echo esc_attr(absint($wpem_index)); ?>" />

            <div class="wpem-tabs-wrapper wpem-add-group-tab-wrapper">

                <div class="wpem-tabs-action-buttons">
                    <div class="wpem-group-counter-wrapper">
                        <div class="wpem-group-counter"><?php echo  esc_attr(absint($wpem_index + 1)); ?></div>
                    </div>
                    <div class="wpem-group-close-button"><a href="javascript:void(0)" class="remove-group-row" title="<?php esc_attr_e('Remove', 'wp-event-manager'); ?>" id="group-row-<?php echo esc_attr($wpem_index); ?>"><i class="wpem-icon-cross"></i></a></div>
                </div>

                <div class="wpem-tab-content current">
                    <div class="wpem-tab-pane active">
                        <?php
                        foreach ($wpem_field['fields'] as $wpem_subkey => $wpem_subfield) : ?>
                            <fieldset class="wpem-form-group fieldset-<?php esc_attr($wpem_subkey, 'wp-event-manager'); ?>">
                                <?php if (!empty($wpem_subfield['label'])) : ?>
                                    <label for="<?php esc_attr($wpem_subkey, 'wp-event-manager'); ?>"><?php echo esc_attr($wpem_subfield['label'], 'wp-event-manager') . wp_kses_post(apply_filters('submit_event_form_required_label', $wpem_subfield['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional123)', 'wp-event-manager') . '</small>', $wpem_subfield)); ?></label>
                                <?php endif; ?>

                                <div class="field">
                                    <?php
                                    $wpem_subfield['name']  = $wpem_key . '_' . $wpem_subkey . '_' . $wpem_index;
                                    $wpem_subfield['id']    = $wpem_key . '_' . $wpem_subkey . '_' . $wpem_index;
                                    $wpem_subfield['value'] = isset($wpem_value[$wpem_subkey]) ? $wpem_value[$wpem_subkey] : '';

                                    if ($wpem_subfield['type'] === 'date') {
                                        $wpem_subfield['value'] = !empty($wpem_subfield['value']) ? gmdate($wpem_php_date_format, strtotime($wpem_subfield['value'])) : $wpem_subfield['value'];
                                    }

                                    wpem_get_event_manager_template('form-fields/' . $wpem_subfield['type'] . '-field.php', array('key' => $wpem_subkey, 'field' => $wpem_subfield));
                                    ?>
                                </div>
                            </fieldset>
                        <?php endforeach; ?>
                    </div>
                </div><!-- / wpemtab wraper  -->
            </div>
        </div>
    <?php endforeach;
endif; ?>

<a href="javascript:void(0)" class="wpem-theme-text-button add-group-row add-group-<?php echo esc_attr($wpem_key); ?>" data-row="<?php
                                                                                                                            ob_start();
                                                                                                                            ?>
   <div class=" group-row-<?php echo esc_attr($wpem_key . '_%%group-row-index%%'); ?>">

    <input type="hidden" class="group-row" name="repeated-row-<?php echo esc_attr($wpem_key); ?>[]" value="%%group-row-index%%" />

    <div class="wpem-tabs-wrapper wpem-add-group-tab-wrapper">

        <div class="wpem-tabs-action-buttons">

            <div class="wpem-group-counter-wrapper">
                <div class="wpem-group-counter"><?php echo wp_kses_post('%%group-row-index%%'); ?></div>
            </div>

            <div class="wpem-group-close-button"><a href="javascript:void(0)" class="remove-group-row" title="<?php esc_attr_e('Remove', 'wp-event-manager'); ?>" id="group-row-<?php echo esc_attr($wpem_key . '_%%group-row-index%%'); ?>"><i class="wpem-icon-cross"></i></a></div>
        </div>

        <div class="wpem-tab-content current">
            <div class="wpem-tab-pane active">
                <?php
                foreach ($wpem_field['fields'] as $wpem_subkey => $wpem_subfield) : ?>
                    <fieldset class="wpem-form-group fieldset-<?php esc_attr($wpem_subkey, 'wp-event-manager'); ?>">
                        <?php if (!empty($wpem_subfield['label'])) : ?>
                            <label for="<?php esc_attr($wpem_subkey, 'wp-event-manager'); ?>"><?php echo esc_attr($wpem_subfield['label'], 'wp-event-manager') . wp_kses_post(apply_filters('submit_event_form_required_label', $wpem_subfield['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $wpem_subfield)); ?></label>
                        <?php endif; ?>

                        <div class="field">
                            <?php
                            $wpem_subfield['name'] = $wpem_key . '_' . $wpem_subkey . '_%%group-row-index%%';
                            $wpem_subfield['id']   = $wpem_key . '_' . $wpem_subkey . '_%%group-row-index%%';
                            wpem_get_event_manager_template('form-fields/' . $wpem_subfield['type'] . '-field.php', array('key' => $wpem_subkey, 'field' => $wpem_subfield));
                            ?>
                        </div>
                    </fieldset>
                <?php endforeach; ?>
            </div>
        </div>
        <?php echo esc_attr(ob_get_clean());
        ?>">+ <?php
                if (!empty($wpem_field['label'])) {
                    echo esc_attr($wpem_field['label']);
                };
                ?>
</a>
<?php if (!empty($wpem_field['description'])) : ?>
    <small class="description">
        <?php echo esc_attr($wpem_field['description']); ?>
    </small>
<?php endif; ?>