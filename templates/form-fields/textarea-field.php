<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<textarea cols="20" rows="3" class="input-text" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $wpem_key); ?>" id="<?php echo isset($wpem_field['id']) ? esc_attr($wpem_field['id']) :  esc_attr($wpem_key); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" placeholder="<?php echo isset($wpem_field['placeholder']) ? esc_html($wpem_field['placeholder']) : ''; ?>" maxlength="<?php echo !empty($wpem_field['maxlength']) ? esc_attr($wpem_field['maxlength']) : ''; ?>" <?php if (!empty($wpem_field['required']))  echo esc_attr('required'); ?>><?php echo isset($wpem_field['value']) ? esc_textarea($wpem_field['value']) : ''; ?></textarea>
<?php if (!empty($wpem_field['description'])) : ?>
    <small class="description">
        <?php echo wp_kses( $wpem_field['description'], wp_kses_allowed_html($wpem_field['description'])); ?>
    </small>
<?php endif; ?>
