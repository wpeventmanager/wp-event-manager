<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$wpem_classes            = array('input-text');
$wpem_allowed_mime_types = array_keys(!empty($wpem_field['allowed_mime_types']) ? $wpem_field['allowed_mime_types'] : get_allowed_mime_types());
$wpem_field_name         = isset($wpem_field['name']) ? $wpem_field['name'] : $key;
$wpem_field_name         .= !empty($wpem_field['multiple']) ? '[]' : '';

if (!empty($wpem_field['ajax']) && event_manager_user_can_upload_file_via_ajax()) {
	wp_enqueue_script('wp-event-manager-ajax-file-upload');
	$wpem_classes[] = 'wp-event-manager-file-upload';
} ?>

<div class="event-manager-uploaded-files">
	<?php if (!empty($wpem_field['value'])) : 
		if (is_array($wpem_field['value'])) : 
			foreach ($wpem_field['value'] as $wpem_value) : 
				wpem_get_event_manager_template('form-fields/uploaded-file-html.php', array('key' => $key, 'name' => 'current_' . $wpem_field_name, 'value' => $wpem_value, 'field' => $wpem_field)); 
			endforeach; 
		elseif ($wpem_value = $wpem_field['value']) :
			wpem_get_event_manager_template('form-fields/uploaded-file-html.php', array('key' => $key, 'name' => 'current_' . $wpem_field_name, 'value' => $wpem_value, 'field' => $wpem_field));
		endif;
	endif; ?>
</div>

<input type="file" class="<?php echo esc_attr(implode(' ', $wpem_classes)); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" data-file_types="<?php echo esc_attr(implode('|', $wpem_allowed_mime_types)); ?>" <?php if (!empty($wpem_field['multiple'])) echo esc_attr('multiple'); ?> name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $key); ?><?php if (!empty($wpem_field['multiple'])) echo esc_attr('[]'); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo empty($wpem_field['placeholder']) ? '' : esc_attr($wpem_field['placeholder']); ?>" />

<small class="description">
	<?php if (!empty($wpem_field['description'])) { 
		echo esc_attr($wpem_field['description']);
	} else { 
		// translators: %s is the maximum file size allowed for uploads.
		printf(esc_attr('Maximum file size: %s.', 'wp-event-manager'), esc_attr(size_format(wp_max_upload_size())));
	} ?>
</small>