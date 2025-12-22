<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Radio Field. Example definition:
 *
 * 'test_radio' => array(
 * 		'label'    => __( 'Test Radio', 'wp-event-manager' ),
 * 		'type'     => 'radio',
 * 		'required' => false,
 * 		'default'  => 'option2',
 * 		'priority' => 1,
 * 		'options'  => array(
 * 			'option1' => 'This is option 1',
 * 		 	'option2' => 'This is option 2'
 * 		)
 * 	)
 */
$wpem_field['default'] = empty($wpem_field['default']) ? current(array_keys($wpem_field['options'])) : $wpem_field['default'];
$wpem_default = !empty($wpem_field['value']) ? $wpem_field['value'] : $wpem_field['default'];

foreach ($wpem_field['options'] as $wpem_option_key => $wpem_value) : ?>
	<label><input type="radio" name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $key); ?>" id="<?php echo esc_attr($key); ?>" attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>" value="<?php echo esc_attr($wpem_option_key); ?>" <?php checked($wpem_default, $wpem_option_key); ?> /> <?php echo esc_html($wpem_value, 'wp-event-manager'); ?></label>
<?php endforeach; 

if (!empty($wpem_field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($wpem_field['description']); ?>
	</small>
<?php endif; ?>