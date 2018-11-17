<?php 

$twelve_hour =  array( '1:00 AM', '1:30 AM','2:00 AM', '2:30 AM', '3:00 AM', '3:30 AM', '4:00 AM', '4:30 AM', '5:00 AM', '5:30 AM', '6:00 AM', '6:30 AM', '7:00 AM', '7:30 AM', '8:00 AM', '8:30 AM', '9:00 AM', '9:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM','12:00 AM', '12:30 AM','0:00 PM',  '0:30  PM', '1:00 PM', '1:30 PM','2:00 PM', '2:30 PM', '3:00 PM', '3:30 PM', '4:00 PM', '4:30 PM', '5:00 PM', '5:30 PM', '6:00 PM', '6:30 PM', '07:00 PM', '07:30 PM', '08:00 PM', '08:30 PM', '09:00 PM', '09:30 PM', '10:00 PM', '10:30 PM', '11:00 PM', '11:30 PM','12:00 PM', '12:30 PM' );

$twentyfour_hour =  array( '0:00',  '0:30', '1:00', '1:30','2:00', '2:30', '3:00', '3:30', '4:00', '4:30', '5:00', '5:30', '6:00', '6:30', '7:00', '7:30', '8:00', '8:30', '9:00', '9:30', '10:00', '10:30', '11:00', '11:30','12:00', '12:30', '13:00', '13:30', '14:00', '14:30','15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30',);

if(get_event_manager_time_format() == '12'){
?>
<select name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?> attribute="<?php echo esc_attr( isset( $field['attribute'] ) ? $field['attribute'] : '' ); ?>" >
	
	<?php foreach ($twelve_hour as $key => $value) { ?>
		<option value="<?php echo esc_attr( $value ); ?>" <?php if ( isset( $field['value'] ) ) selected( $field['value'] ,  $value ); ?>><?php echo esc_html( $value ); ?></option>
	<?php } ?>
</select>

<?php 
} else{ ?>
<select name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?> attribute="<?php echo esc_attr( isset( $field['attribute'] ) ? $field['attribute'] : '' ); ?>" >

	<?php foreach ($twentyfour_hour as $key => $value) { ?> 
		<option value="<?php echo esc_attr( $value ); ?>" <?php if ( isset( $field['value'] ) ) selected( $field['value'] , $value ); ?>><?php echo esc_html( $value ); ?></option>
	<?php } ?>
</select>
<?php
}
?>

<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo $field['description']; ?></small><?php endif; ?>