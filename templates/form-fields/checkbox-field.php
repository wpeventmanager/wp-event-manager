<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<input type="checkbox" class="input-checkbox" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $wpem_key); ?>" id="<?php echo esc_attr($wpem_key); ?>" <?php checked(!empty($wpem_field['value']), true); ?> value="1" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" <?php if (!empty($wpem_field['required'])) echo esc_attr('required'); ?> />

<?php if (!empty($wpem_field['description'])) : ?>
    <small class="description">
        <?php echo wp_kses_post($wpem_field['description']); ?>
    </small>
<?php endif; ?>