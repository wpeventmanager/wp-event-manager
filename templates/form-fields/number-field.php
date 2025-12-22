<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<input type="number" class="input-text <?php echo esc_attr(isset($wpem_field['class']) ? $wpem_field['class'] : $wpem_key); ?>" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $wpem_key); ?>" id="<?php echo isset($wpem_field['id']) ? esc_attr($wpem_field['id']) :  esc_attr($wpem_key); ?>" placeholder="<?php echo esc_attr($wpem_field['placeholder']); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" value="<?php echo isset($wpem_field['value']) ? esc_attr($wpem_field['value']) : ''; ?>" min="<?php echo isset($wpem_field['min']) ? esc_attr($wpem_field['min']) : esc_attr('0'); ?>" max="<?php echo isset($wpem_field['max']) ? esc_attr($wpem_field['max']) : ''; ?>" maxlength="<?php echo !empty($wpem_field['maxlength']) ? esc_attr($wpem_field['maxlength']) : ''; ?>" <?php if (!empty($wpem_field['required'])) echo esc_attr('required'); ?> <?php echo esc_attr(isset($wpem_field['step']) ? 'step=any': ''); ?> />

<?php if (!empty($wpem_field['description'])) : ?>
    <small class="description">
        <?php echo wp_kses_post($wpem_field['description']); ?>
    </small>
<?php endif; ?>