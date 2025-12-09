<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
wp_enqueue_media();
global $post_id;
if(!isset($field['value']) || empty($field['value'])) {
	$field['value'] = get_post_meta($post_id, stripslashes($key), true);
}
if(empty($field['placeholder'])) {
	$field['placeholder'] = 'http://';
}
if(!empty($field['name'])) {
	$name = $field['name'];
} else {
	$name = $key;
} ?>
<p class="form-field <?php echo esc_attr($key); ?>" data-field-name="<?php echo esc_attr($key); ?>">
	<?php
	if(!empty($field['description'])) : ?>
	<span class="tips" data-tip="<?php echo esc_html($field['description'], 'wp-event-manager'); ?>">[?]</span><?php endif; ?></label>
	<span class="wpem-input-field">
		<span class="file_url">
			<?php foreach ((array) $field['value'] as $value) { ?>
				<span class="event-manager-uploaded-file event-manager-uploaded-files multiple-file">
					<input type="hidden" name="<?php echo esc_attr($name); ?>[]" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($value); ?>" />
					<span class="event-manager-uploaded-file-preview">
						<?php if (in_array(pathinfo($value, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'])) : ?>
							<img src="<?php echo esc_attr($value); ?>">
							<a class="event-manager-remove-uploaded-file" href="javascript:void(0);">[remove]</a>
							<?php else :
							if (!wpem_begnWith($value, "http")) {
								$value	= '';
							}
							if (!empty($value)) { ?>
								<span class="wpfm-icon">
									<strong style="display: block; padding-top: 5px;"><?php echo esc_attr(wp_basename($value)); ?></strong>
								</span>
								<a class="event-manager-remove-uploaded-file" href="javascript:void(0);">[remove]</a>
						<?php }
						endif; ?>
					</span>
				</span>
			<?php } ?>
	</span> 
	<?php
	if (!empty($field['multiple'])) { ?>
		<button class="button button-small wp_event_manager_upload_file_button_multiple wpem-theme-button" style="display: block;" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button>
	<?php } else { ?>
		<span class="event-manager-uploaded-file2">
			<button class="button button-small wp_event_manager_upload_file_button wpem-theme-button" style="display: block;" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button>
		</span>
		<?php if (!empty($field['description'])) : ?><small class="description"><?php echo esc_html(trim($field['description'])); ?></small><?php endif; ?>
	<?php }