<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $post_id;

// Fetch saved values
$saved_values = get_post_meta($post_id, stripslashes($key), true);
$saved_values = !empty($saved_values) ? (array) $saved_values : [];

// Get options
$options = isset($field['options']) ? $field['options'] : [];?>

<div class="health-guidelines-switches">
	<?php foreach ($options as $option_key => $option_label) : ?>
		<p class="form-field">
			<label class="wpem-input-switch">
				<input type="checkbox" 
					name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($option_key); ?>]" 
					id="<?php echo esc_attr($key . '_' . $option_key); ?>" class="<?php echo esc_attr($key); ?>"
					value="1"
					<?php echo isset($field['value'][$option_key]) ? 'checked' : ''; ?>>
				<span class="wpem-input-slider round"></span>
			</label>
			<label for="<?php echo esc_attr($key . '_' . $option_key); ?>">
				<?php echo esc_html($option_label); ?>
			</label>
		</p>

		<?php if ($option_key === 'custom_guidelines') : ?>
			<input type="text" 
				name="<?php echo esc_attr($key); ?>[custom_text]" 
				id="<?php echo esc_attr($key . '_custom_text'); ?>"
				placeholder="<?php esc_attr_e('Enter custom health guideline', 'wp-event-manager'); ?>"
				value="<?php echo isset($saved_values['custom_text']) ? esc_attr($saved_values['custom_text']) : ''; ?>">
		<?php endif; ?>

	<?php endforeach; ?>
</div>