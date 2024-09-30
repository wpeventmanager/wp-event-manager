<?php wp_enqueue_script('wp-event-manager-multiselect'); 

wp_register_script( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
wp_register_script( 'wp-event-manager-multiselect', EVENT_MANAGER_PLUGIN_URL . '/assets/js/multiselect.min.js', array( 'jquery', 'chosen' ), EVENT_MANAGER_VERSION, true );
wp_enqueue_style( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/css/chosen.css' ); ?>

<select multiple="multiple" name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>[]" id="<?php echo esc_attr($key); ?>" class="event-manager-multiselect" data-no_results_text="<?php esc_attr_e('No results match', 'wp-event-manager'); ?>" attribute="<?php echo esc_attr(isset($field['attribute']) ? $field['attribute'] : ''); ?>" data-multiple_text="<?php esc_attr_e('Select Some Options', 'wp-event-manager'); ?>">
    <?php 
    // Get the default value (array of default organizers)
    $default_value = isset($field['default']) ? (array)$field['default'] : []; // Ensure it's an array

    foreach ($field['options'] as $option_key => $option_value) : ?>
        <option value="<?php echo esc_attr($option_key); ?>" <?php echo in_array($option_key, $default_value) || (!empty($field['value']) && is_array($field['value']) && in_array($option_key, $field['value'])) ? 'selected' : ''; ?>>
            <?php echo esc_html($option_value); ?>
        </option>
    <?php endforeach; ?>
</select>

<?php if (!empty($field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($field['description']); ?>
	</small>
<?php endif; ?>