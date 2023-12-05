<select name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" id="<?php echo esc_attr($key); ?>" <?php if (!empty($field['required'])) echo esc_attr('required'); ?> attribute="<?php echo esc_attr(isset($field['attribute']) ? $field['attribute'] : ''); ?>">

	<?php foreach ($field['options'] as $key => $value) :
		if(isset($field['value']) ){
			if(is_array($field['value']))
				$selected = $field['value'][0];
			else
				$selected = $field['value'];
		}else{
			if(isset($field['default']))
				$selected = $field['default'];
			else
				$selected = '';
		} ?>
		<option value="<?php echo esc_attr($key); ?>" <?php selected($selected, $key); ?>><?php echo esc_attr($value); ?></option>
	<?php endforeach; ?>

</select>

<?php if (!empty($field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($field['description']); ?>
	</small>
<?php endif; ?>