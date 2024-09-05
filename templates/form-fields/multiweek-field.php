<?php
global $post_id;

    $weeks = [];
    if (!isset($field['value']) || empty($field['value'])) {
        $saved_weeks = esc_attr(get_post_meta($post_id, stripslashes($key), true));
        if (!empty($saved_weeks)) {
            $weeks = explode(',', $saved_weeks);
        }
    } else {
        $weeks = is_array($field['value']) ? $field['value'] : explode(',', $field['value']);
    }

    $name = isset($field['name']) ? $field['name'] : $key;

    ?>
    <div id="recure_custom_weeks_field" class="controls form-field" style="display:none;">
        <div id="custom_weeks_container">
            <?php if (!empty($weeks)) : ?>
                <?php foreach ($weeks as $week) : ?>
                    <select name="<?php echo esc_attr($name); ?>[]" class="input-select">
                        <?php foreach ($field['options'] as $week_key => $week_label) : ?>
                            <option value="<?php echo esc_attr($week_key); ?>" <?php selected($week, $week_key); ?>>
                                <?php echo esc_html($week_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endforeach; ?>
            <?php else : ?>
                <select name="<?php echo esc_attr($name); ?>[]" class="input-select">
                    <?php foreach ($field['options'] as $week_key => $week_label) : ?>
                        <option value="<?php echo esc_attr($week_key); ?>">
                            <?php echo esc_html($week_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
        <button type="button" id="add_custom_week" class="button wpem-theme-button">
            <?php _e('Add Another Week Day', 'wp-event-manager-recurring-events'); ?>
        </button>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#add_custom_week').on('click', function(e) {
            e.preventDefault();
            var newWeekField = $('<select name="<?php echo esc_attr($name); ?>[]" class="input-select"><?php foreach ($field['options'] as $week_key => $week_label) : ?><option value="<?php echo esc_attr($week_key); ?>"><?php echo esc_html($week_label); ?></option><?php endforeach; ?></select>');
            $('#custom_weeks_container').append(newWeekField);
        });

    });
    </script>
    <?php