<?php

/**
 * group fields is generated from this page .
 * group fields for the paid and free group.
 * This field is used in submit event form.
 * */
?>

<?php
//get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
$datepicker_date_format     = WP_Event_Manager_Date_Time::get_datepicker_format();

//covert datepicker format  into php date() function date format
$php_date_format        = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
?>

<?php if (!empty($field['value']) && is_array($field['value'])) : ?>

    <?php foreach ($field['value'] as $index => $value) : ?>

        <div class="group-row-<?php echo esc_attr($index); ?>">
            <input type="hidden" class="group-row" name="repeated-row-<?php echo esc_attr($key); ?>[]" value="<?php echo esc_attr(absint($index)); ?>" />

            <div class="wpem-tabs-wrapper wpem-add-group-tab-wrapper">

                <div class="wpem-tabs-action-buttons">
                    <div class="wpem-group-counter-wrapper">
                        <div class="wpem-group-counter"><?php echo  esc_attr(absint($index + 1)); ?></div>
                    </div>
                    <div class="wpem-group-close-button"><a href="javascript:void(0)" class="remove-group-row" title="<?php _e('Remove', 'wp-event-manager'); ?>" id="group-row-<?php echo esc_attr($index); ?>"><i class="wpem-icon-cross"></i></a></div>
                </div>

                <div class="wpem-tab-content current">
                    <div class="wpem-tab-pane active">
                        <?php
                        foreach ($field['fields'] as $subkey => $subfield) : ?>
                            <fieldset class="wpem-form-group fieldset-<?php esc_attr_e($subkey); ?>">
                                <?php if (!empty($subfield['label'])) : ?>
                                    <label for="<?php esc_attr_e($subkey); ?>"><?php echo esc_attr($subfield['label'], 'wp-event-manager') . apply_filters('submit_event_form_required_label', $subfield['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $subfield); ?></label>
                                <?php endif; ?>

                                <div class="field">
                                    <?php
                                    $subfield['name']  = $key . '_' . $subkey . '_' . $index;
                                    $subfield['id']    = $key . '_' . $subkey . '_' . $index;
                                    $subfield['value'] = isset($value[$subkey]) ? $value[$subkey] : '';

                                    if ($subfield['type'] === 'date') {
                                        $subfield['value'] = !empty($subfield['value']) ? date($php_date_format, strtotime($subfield['value'])) : $subfield['value'];
                                    }

                                    get_event_manager_template('form-fields/' . $subfield['type'] . '-field.php', array('key' => $subkey, 'field' => $subfield));
                                    ?>
                                </div>
                            </fieldset>
                        <?php endforeach; ?>
                    </div>
                </div><!-- / wpemtab wraper  -->

            </div>

        </div>

    <?php endforeach; ?>

<?php endif; ?>


<a href="javascript:void(0)" class="wpem-theme-text-button add-group-row add-group-<?php echo esc_attr($key); ?>" data-row="<?php
                                                                                                                            ob_start();
                                                                                                                            ?>
   <div class=" group-row-<?php echo esc_attr($key . '_%%group-row-index%%'); ?>">

    <input type="hidden" class="group-row" name="repeated-row-<?php echo esc_attr($key); ?>[]" value="%%group-row-index%%" />

    <div class="wpem-tabs-wrapper wpem-add-group-tab-wrapper">

        <div class="wpem-tabs-action-buttons">

            <div class="wpem-group-counter-wrapper">
                <div class="wpem-group-counter"><?php echo wp_kses_post('%%group-row-index%%'); ?></div>
            </div>

            <div class="wpem-group-close-button"><a href="javascript:void(0)" class="remove-group-row" title="<?php _e('Remove', 'wp-event-manager'); ?>" id="group-row-<?php echo esc_attr($key . '_%%group-row-index%%'); ?>"><i class="wpem-icon-cross"></i></a></div>
        </div>

        <div class="wpem-tab-content current">
            <div class="wpem-tab-pane active">
                <?php
                foreach ($field['fields'] as $subkey => $subfield) : ?>
                    <fieldset class="wpem-form-group fieldset-<?php esc_attr_e($subkey); ?>">
                        <?php if (!empty($subfield['label'])) : ?>
                            <label for="<?php esc_attr_e($subkey); ?>"><?php echo esc_attr($subfield['label'], 'wp-event-manager') . apply_filters('submit_event_form_required_label', $subfield['required'] ? '<span class="require-field">*</span>' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>', $subfield); ?></label>
                        <?php endif; ?>

                        <div class="field">
                            <?php
                            $subfield['name'] = $key . '_' . $subkey . '_%%group-row-index%%';
                            $subfield['id']   = $key . '_' . $subkey . '_%%group-row-index%%';
                            get_event_manager_template('form-fields/' . $subfield['type'] . '-field.php', array('key' => $subkey, 'field' => $subfield));
                            ?>
                        </div>
                    </fieldset>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        echo esc_attr(ob_get_clean());
        ?>">+ <?php
                if (!empty($field['label'])) {
                    echo esc_attr($field['label']);
                };
                ?>
</a>
<?php if (!empty($field['description'])) : ?><small class="description"><?php echo esc_attr($field['description']); ?></small><?php endif; ?>