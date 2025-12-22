<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Shows the text field on event listing forms.
 *
 * This template can be overridden by copying it to yourtheme/wp-event-manager/form-fields/text-field.php.
 *
 * @see         https://www.wp-eventmanager.com/documentation/template-files-override/
 * @author      WP Event Manager
 * @package     WP Event Manager
 * @category    Template
 * @version     1.8
 */
?>
<input type="email" class="input-text <?php echo esc_attr(isset($wpem_field['class']) ? $wpem_field['class'] : $key); ?>" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $key); ?>" id="<?php echo isset($wpem_field['id']) ? esc_attr($wpem_field['id']) :  esc_attr($key); ?>" placeholder="<?php echo empty($wpem_field['placeholder']) ? '' : esc_attr($wpem_field['placeholder']); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" value="<?php echo isset($wpem_field['value']) ? esc_attr($wpem_field['value']) : ''; ?>" maxlength="<?php echo !empty($wpem_field['maxlength']) ? esc_attr($wpem_field['maxlength']) : ''; ?>" <?php if (!empty($wpem_field['required'])) echo esc_attr('required'); ?> <?php if (isset($wpem_field['disabled']) && !empty($wpem_field['disabled'])) echo esc_attr('disabled'); ?> />
<?php if (!empty($wpem_field['description'])) : ?>
    <small class="description">
        <?php echo wp_kses_post($wpem_field['description']); ?>
    </small>
<?php endif; ?>