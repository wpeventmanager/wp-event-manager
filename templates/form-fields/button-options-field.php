<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Button Options Field. Example definition:
 *
 * 'test_options' => array(
 * 		'label'    => __( 'Test Button Options', 'wp-event-manager'),
 * 		'type'     => 'button-options',
 * 		'required' => false, * 		
 * 		'priority' => 1,
 * 		'options'  => array(
 * 			'option1' => 'This is option 1',
 * 		 	'option2' => 'This is option 2'
 * 		)
 * 	)
 */

foreach($field['options'] as $wpem_option_key => $wpem_value) : ?>
    <input type="button" name="<?php echo esc_attr( isset( $field['name']) ? $field['name'] : $key); ?>" id="<?php echo esc_attr( $key); ?>" attribute="<?php echo esc_attr( isset( $field['attribute']) ? $field['attribute'] : ''); ?>" value="<?php echo esc_attr( $wpem_option_key); ?>" /> <?php echo esc_html( $wpem_value); ?>
<?php endforeach; 

if(!empty( $field['description'])) : ?>
    <small class="description">
        <?php echo wp_kses_post($field['description']); ?>
    </small>
<?php endif; ?>
