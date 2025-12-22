<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
wp_enqueue_media();
global $post_id;
if(!isset($wpem_field['value']) || empty($wpem_field['value'])) {
	$wpem_field['value'] = get_post_meta($post_id, stripslashes($key), true);
}
if(empty($wpem_field['placeholder'])) {
	$wpem_field['placeholder'] = 'http://';
}
if(!empty($wpem_field['name'])) {
	$wpem_name = $wpem_field['name'];
} else {
	$wpem_name = $key;
} ?>
<p class="form-field <?php echo esc_attr($key); ?>" data-field-name="<?php echo esc_attr($key); ?>">
	<?php
	if(!empty($wpem_field['description'])) : ?>
	<span class="tips" data-tip="<?php echo esc_html($wpem_field['description'], 'wp-event-manager'); ?>">[?]</span><?php endif; ?></label>
	<span class="wpem-input-field">
		<span class="file_url">
			<?php foreach ((array) $wpem_field['value'] as $wpem_value) { ?>
				<span class="event-manager-uploaded-file event-manager-uploaded-files multiple-file">
					<input type="hidden" name="<?php echo esc_attr($wpem_name); ?>[]" placeholder="<?php echo esc_attr($wpem_field['placeholder']); ?>" value="<?php echo esc_attr($wpem_value); ?>" />
					<span class="event-manager-uploaded-file-preview">
						<?php if (in_array(pathinfo($wpem_value, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'])) : ?>
							<img src="<?php echo esc_attr($wpem_value); ?>">
							<a class="event-manager-remove-uploaded-file" href="javascript:void(0);">[remove]</a>
							<?php else :
							if (!wpem_begnWith($wpem_value, "http")) {
								$wpem_value	= '';
							}
							if (!empty($wpem_value)) { ?>
								<span class="wpfm-icon">
									<strong style="display: block; padding-top: 5px;"><?php echo esc_attr(wp_basename($wpem_value)); ?></strong>
								</span>
								<a class="event-manager-remove-uploaded-file" href="javascript:void(0);">[remove]</a>
						<?php }
						endif; ?>
					</span>
				</span>
			<?php } ?>
	</span> 
	<?php
	if (!empty($wpem_field['multiple'])) { ?>
		<button class="button button-small wp_event_manager_upload_file_button_multiple wpem-theme-button" style="display: block;" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button>
	<?php } else { ?>
		<span class="event-manager-uploaded-file2">
			<button class="button button-small wp_event_manager_upload_file_button wpem-theme-button" style="display: block;" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button>
		</span>
		<?php if (!empty($wpem_field['description'])) : ?><small class="description"><?php echo esc_html(trim($wpem_field['description'])); ?></small><?php endif; ?>
	<?php }