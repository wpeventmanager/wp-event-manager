<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<input type="button" class="input-button" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $key); ?>" id="<?php echo esc_attr($key); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" value="<?php echo esc_attr(isset($wpem_field['value']) ? $wpem_field['value'] : $key); ?>" <?php if (!empty($wpem_field['required'])) echo wp_kses_post('required'); ?> />

<?php if (!empty($wpem_field['description'])) : ?>
    <small class="description">
        <?php echo wp_kses_post($wpem_field['description']); ?>
    </small>
<?php endif; ?> 