<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<select name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $wpem_key); ?>" id="<?php echo esc_attr($wpem_key); ?>" <?php if (!empty($field['required'])) echo esc_attr('required'); ?> attribute="<?php echo esc_attr(isset($field['attribute']) ? $field['attribute'] : ''); ?>">

	<?php foreach ($field['options'] as $wpem_key => $wpem_value) :
		if(isset($field['value']) ){
			if(is_array($field['value']))
				$wpem_selected = $field['value'][0];
			else
				$wpem_selected = $field['value'];
		}else{
			if(isset($field['default']))
				$wpem_selected = $field['default'];
			else
				$wpem_selected = '';
		} ?>
		<option value="<?php echo esc_attr($wpem_key); ?>" <?php selected($wpem_selected, $wpem_key); ?>><?php echo esc_attr($wpem_value); ?></option>
	<?php endforeach; ?>

</select>

<?php if (!empty($field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($field['description']); ?>
	</small>
<?php endif; ?>