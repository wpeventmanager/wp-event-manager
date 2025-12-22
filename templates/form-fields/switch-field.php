<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $post_id;

// Fetch saved values
$wpem_saved_values = get_post_meta($post_id, stripslashes($key), true);
$wpem_saved_values = !empty($wpem_saved_values) ? (array) $wpem_saved_values : [];

// Get options
$wpem_options = isset($wpem_field['options']) ? $wpem_field['options'] : [];?>

<div class="health-guidelines-switches">
	<?php foreach ($wpem_options as $wpem_option_key => $wpem_option_label) : ?>
		<p class="form-field">
			<label class="wpem-input-switch">
				<input type="checkbox" 
					name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($wpem_option_key); ?>]" 
					id="<?php echo esc_attr($key . '_' . $wpem_option_key); ?>" class="<?php echo esc_attr($key); ?>"
					value="1"
					<?php echo isset($wpem_field['value'][$wpem_option_key]) ? 'checked' : ''; ?>>
				<span class="wpem-input-slider round"></span>
			</label>
			<label for="<?php echo esc_attr($key . '_' . $wpem_option_key); ?>">
				<?php echo esc_html($wpem_option_label); ?>
			</label>
		</p>

		<?php if ($wpem_option_key === 'custom_guidelines') : ?>
			<input type="text" 
				name="<?php echo esc_attr($key); ?>[custom_text]" 
				id="<?php echo esc_attr($key . '_custom_text'); ?>"
				placeholder="<?php esc_attr_e('Enter custom health guideline', 'wp-event-manager'); ?>"
				value="<?php echo isset($wpem_saved_values['custom_text']) ? esc_attr($wpem_saved_values['custom_text']) : ''; ?>">
		<?php endif; ?>

	<?php endforeach; ?>
</div>