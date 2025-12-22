<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<input type="password" class="input-text" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $key); ?>" id="<?php echo esc_attr($key); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" placeholder="<?php echo esc_attr($wpem_field['placeholder']); ?>" value="<?php echo isset($wpem_field['value']) ? esc_attr($wpem_field['value']) : ''; ?>" maxlength="<?php echo !empty($wpem_field['maxlength']) ? esc_attr($wpem_field['maxlength']) : ''; ?>" <?php if (!empty($wpem_field['required'])) echo esc_attr('required'); ?> />

<?php if (!empty($wpem_field['description'])) : ?>
    <small class="description">
        <?php echo wp_kses_post($wpem_field['description']); ?>
    </small>
<?php endif; ?>