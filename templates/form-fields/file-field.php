<?php
$classes            = array('input-text');
$allowed_mime_types = array_keys(!empty($field['allowed_mime_types']) ? $field['allowed_mime_types'] : get_allowed_mime_types());
$field_name         = isset($field['name']) ? $field['name'] : $key;
$field_name         .= !empty($field['multiple']) ? '[]' : '';

if (!empty($field['ajax']) && event_manager_user_can_upload_file_via_ajax()) {
	wp_enqueue_script('wp-event-manager-ajax-file-upload');
	$classes[] = 'wp-event-manager-file-upload';
} ?>

<div class="event-manager-uploaded-files">
	<?php if (!empty($field['value'])) : 
		if (is_array($field['value'])) : 
			foreach ($field['value'] as $value) : 
				get_event_manager_template('form-fields/uploaded-file-html.php', array('key' => $key, 'name' => 'current_' . $field_name, 'value' => $value, 'field' => $field)); 
			endforeach; 
		elseif ($value = $field['value']) :
			get_event_manager_template('form-fields/uploaded-file-html.php', array('key' => $key, 'name' => 'current_' . $field_name, 'value' => $value, 'field' => $field));
		endif;
	endif; ?>
</div>

<input type="file" class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-file_types="<?php echo esc_attr(implode('|', $allowed_mime_types)); ?>" <?php if (!empty($field['multiple'])) echo esc_attr('multiple'); ?> name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?><?php if (!empty($field['multiple'])) echo esc_attr('[]'); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo empty($field['placeholder']) ? '' : esc_attr($field['placeholder']); ?>" />

<small class="description">
	<?php if (!empty($field['description'])) { 
		echo esc_attr($field['description']);
	} else { 
		printf(__('Maximum file size: %s.', 'wp-event-manager'), size_format(wp_max_upload_size()));
	} ?>
</small>