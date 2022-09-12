<select name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" id="<?php echo esc_attr($key); ?>" <?php if (!empty($field['required'])) echo esc_attr('required'); ?> attribute="<?php echo esc_attr(isset($field['attribute']) ? $field['attribute'] : ''); ?>">


	<?php foreach ($field['options'] as $key => $value) : ?>


		<option value="<?php echo esc_attr($key); ?>" <?php if (isset($field['value']) || isset($field['default'])) selected(isset($field['value']) ? $field['value'] : $field['default'], $key); ?>><?php echo esc_attr($value); ?></option>


	<?php endforeach; ?>


</select>


<?php if (!empty($field['description'])) : ?><small class="description"><?php printf ($field['description']); ?></small><?php endif; ?>