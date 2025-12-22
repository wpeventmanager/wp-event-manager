<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * this is used for time selection field
 * @since 3.0
 */
?>
<div class="controls" style="position: relative">
   <input type="text" class="input-text" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $wpem_key); ?>" id="<?php echo isset($wpem_field['id']) ? esc_attr($wpem_field['id']) :  esc_attr($wpem_key); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" placeholder="<?php echo esc_attr($wpem_field['placeholder'], 'wp-event-manager'); ?>" value="<?php echo isset($wpem_field['value']) ? esc_attr($wpem_field['value']) : ''; ?>" maxlength="<?php echo !empty($wpem_field['maxlength']) ? esc_attr($wpem_field['maxlength']) : ''; ?>" <?php if (!empty($wpem_field['required'])) echo esc_attr('required'); ?> data-picker="timepicker" />
   <?php if (!empty($wpem_field['description'])) : ?>
      <small class="description">
         <?php echo wp_kses_post($wpem_field['description']); ?>
      </small>
   <?php endif; ?>
</div>