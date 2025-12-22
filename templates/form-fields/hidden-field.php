<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<input type="hidden" class="input-text" name="<?php echo esc_attr( isset( $wpem_field['name'] ) ? $wpem_field['name'] : $key ); ?>"  id="<?php echo isset( $wpem_field['id'] ) ? esc_attr( $wpem_field['id'] ) :  esc_attr( $key ); ?>" attribute="<?php echo esc_attr( isset( $wpem_field['attribute'] ) ? $wpem_field['attribute'] : '' ); ?>"  value="<?php echo esc_attr(isset( $wpem_field['value'] ) ? esc_attr( $wpem_field['value'] ) : ''); ?>" />