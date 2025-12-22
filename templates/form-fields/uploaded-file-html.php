<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<div class="event-manager-uploaded-file">
	<?php
	if(is_numeric($value)) {
		$wpem_image_src = wp_get_attachment_image_src(absint($value));
		$wpem_image_src = $wpem_image_src ? $wpem_image_src[0] : '';
	} else {
		$wpem_image_src = $value;
	}
	$wpem_extension = !empty($wpem_extension) ? $wpem_extension : substr(strrchr($wpem_image_src, '.'), 1);
	//check for file extension/type
	if(3 !== strlen($wpem_extension) || in_array($wpem_extension, array('jpg', 'gif', 'png', 'jpeg', 'jpe'))) : ?>
		<span class="event-manager-uploaded-file-preview">
			<img src="<?php echo esc_url($wpem_image_src); ?>" /> 
			<a class="event-manager-remove-uploaded-file" href="#">[<?php esc_attr_e('remove', 'wp-event-manager'); ?>]</a>
		</span>
	<?php else : ?>
		<span class="event-manager-uploaded-file-name">
			<code><?php echo esc_html(basename($wpem_image_src)); ?></code> 
			<a class="event-manager-remove-uploaded-file" href="#">[<?php esc_attr_e('remove', 'wp-event-manager'); ?>]</a>
		</span>
	<?php endif; ?>
	<input type="hidden" class="input-text" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" />
</div>