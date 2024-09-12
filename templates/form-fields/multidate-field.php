    <?php
    global $post_id;
    $datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
    $php_date_format        = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

    $dates = [];
    if (!isset($field['value']) || empty($field['value'])) {
        $saved_dates = esc_attr(get_post_meta($post_id, stripslashes($key), true));
        if (!empty($saved_dates)) {
            $dates = explode(',', $saved_dates);
            $dates = array_map(function($date) use ($php_date_format) {
                return date($php_date_format, strtotime($date));
            }, $dates);
        }
    } else {
        $dates = is_array($field['value']) ? $field['value'] : explode(',', $field['value']);
    }

    $name = isset($field['name']) ? $field['name'] : $key;

    ?>
    <div id="recure_custom_dates_field" class="controls form-field" position: relative; style="display:none;">
        <div id="custom_dates_container">
            <?php if (!empty($dates)) : ?>
                <?php foreach ($dates as $date) : ?>
                    <input type="text" class="input-text" name="<?php echo esc_attr($name); ?>[]" value="<?php echo esc_attr($date); ?>" placeholder="<?php _e('Select Date', 'wp-event-manager-recurring-events'); ?>" data-picker="datepicker" />
                <?php endforeach; ?>
            <?php else : ?>
                <input type="text" class="input-text" name="<?php echo esc_attr($name); ?>[]" placeholder="<?php _e('Select Date', 'wp-event-manager-recurring-events'); ?>" data-picker="datepicker" />
            <?php endif; ?>
        </div>
        <button type="button" id="add_custom_date" class="button wpem-theme-button">
            <?php _e('Add Another Date', 'wp-event-manager-recurring-events'); ?>
        </button>
			</div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {

        // Initialize date pickers for existing inputs
        $('input[data-picker="datepicker"]').datepicker({
            dateFormat: '<?php echo esc_js($datepicker_date_format); ?>'
        });

        // Add new date input when "Add Another Date" button is clicked
        $('#add_custom_date').on('click', function(e) {
            e.preventDefault();
            var newDateField = $('<input type="text" class="input-text" name="<?php echo esc_attr($name); ?>[]" placeholder="<?php _e('Select Date', 'wp-event-manager-recurring-events'); ?>" data-picker="datepicker" />');
            $('#custom_dates_container').append(newDateField);

            // Initialize date picker for the newly added input
            newDateField.datepicker({
                dateFormat: '<?php echo esc_js($datepicker_date_format); ?>'
            });
        });

    });
    </script>
    <?php