<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
wp_enqueue_script('wp-event-manager-multiselect'); 

wp_register_script( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
wp_localize_script('chosen', 'wpem_chosen', array(
    'multiple_text' => __('Select Some Options', 'wp-event-manager'),
    'single_text' => __('Select an Option', 'wp-event-manager'),
    'no_result_text' => __('No results match', 'wp-event-manager'),
));
wp_enqueue_script('chosen');
wp_register_script( 'wp-event-manager-multiselect', EVENT_MANAGER_PLUGIN_URL . '/assets/js/multiselect.min.js', array( 'jquery', 'chosen' ), EVENT_MANAGER_VERSION, true );
wp_enqueue_style( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/css/chosen.css', array(), '1.0.0' );
wp_enqueue_script('wpem-dompurify', EVENT_MANAGER_PLUGIN_URL . '/assets/js/dom-purify/dompurify.min.js', [], '3.0.5', true); ?>

<select multiple="multiple" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $key); ?>[]" id="<?php echo esc_attr($key); ?>" class="event-manager-multiselect" data-no_results_text="<?php esc_attr_e('No results match', 'wp-event-manager'); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" data-multiple_text="<?php esc_attr_e('Select Some Options', 'wp-event-manager'); ?>">
    <?php 
    // Get the default value (array of default organizers)
    $wpem_default_value = isset($wpem_field['default']) ? (array)$wpem_field['default'] : []; // Ensure it's an array

    foreach ($wpem_field['options'] as $wpem_option_key => $wpem_option_value) : ?>
        <option value="<?php echo esc_attr($wpem_option_key); ?>" <?php echo in_array($wpem_option_key, $wpem_default_value) || (!empty($wpem_field['value']) && is_array($wpem_field['value']) && in_array($wpem_option_key, $wpem_field['value'])) ? 'selected' : ''; ?>>
            <?php echo esc_html($wpem_option_value); ?>
        </option>
    <?php endforeach; ?>
</select>

<?php if (!empty($wpem_field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($wpem_field['description']); ?>
	</small>
<?php endif; ?>