<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<select name="<?php echo esc_attr(isset($wpem_field['name']) ? $wpem_field['name'] : $wpem_key); ?>" id="<?php echo esc_attr($wpem_key); ?>" <?php if (!empty($wpem_field['required'])) echo esc_attr('required'); ?> attribute="<?php echo esc_attr(isset($wpem_field['attribute']) ? $wpem_field['attribute'] : ''); ?>">

	<?php foreach ($wpem_field['options'] as $wpem_key => $wpem_value) :
		if(isset($wpem_field['value']) ){
			if(is_array($wpem_field['value']))
				$wpem_selected = $wpem_field['value'][0];
			else
				$wpem_selected = $wpem_field['value'];
		}else{
			if(isset($wpem_field['default']))
				$wpem_selected = $wpem_field['default'];
			else
				$wpem_selected = '';
		} ?>
		<option value="<?php echo esc_attr($wpem_key); ?>" <?php selected($wpem_selected, $wpem_key); ?>><?php echo esc_attr($wpem_value); ?></option>
	<?php endforeach; ?>

</select>

<?php if (!empty($wpem_field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($wpem_field['description']); ?>
	</small>
<?php endif; ?>