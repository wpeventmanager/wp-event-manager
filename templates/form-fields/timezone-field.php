<?php
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
 <select name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) :  esc_attr( $key ); ?>" class="input-select <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : $key ); ?>">
 			<?php
 			$field['default'] = isset($field['default']) ? $field['default'] : '';
 			$value = isset($field['value']) ? $field['value'] : $field['default'];	
 			echo WP_Event_Manager_Date_Time::wp_event_manager_timezone_choice($value);
 			?>
 </select>