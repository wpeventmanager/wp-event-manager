<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Shows the timezone field on event listing forms.
 *
 * This template can be overridden by copying it to yourtheme/wp-event-manager/form-fields/timezone-field.php.
 *
 * @see         https://www.wp-eventmanager.com/documentation/template-files-override/
 * @author      WP Event Manager
 * @package     WP Event Manager
 * @category    Template
 * @since 		3.0
 * @version     3.0
 */
 ?>
 <select name="<?php echo esc_attr( isset( $wpem_field['name'] ) ? $wpem_field['name'] : $wpem_key ); ?>" id="<?php echo isset( $wpem_field['id'] ) ? esc_attr( $wpem_field['id'] ) :  esc_attr( $wpem_key ); ?>" class="input-select <?php echo esc_attr( isset( $wpem_field['class'] ) ? $wpem_field['class'] : $wpem_key ); ?>">
 	<?php
		$wpem_field['default'] = isset($wpem_field['default']) ? $wpem_field['default'] : '';
		$wpem_value = isset($wpem_field['value']) ? $wpem_field['value'] : $wpem_field['default'];	
		echo wp_kses_post(
			WP_Event_Manager_Date_Time::wpem_timezone_choice(
				esc_attr( $wpem_value )
			)
		); ?>
 </select>