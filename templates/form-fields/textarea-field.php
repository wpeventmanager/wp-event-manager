<textarea cols="20" rows="3" class="input-text" name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" id="<?php echo isset($field['id']) ? esc_attr($field['id']) :  esc_attr($key); ?>" attribute="<?php echo esc_attr(isset($field['attribute']) ? $field['attribute'] : ''); ?>" placeholder="<?php echo esc_attr($field['placeholder']) ?>" maxlength="<?php echo !empty($field['maxlength']) ? $field['maxlength'] : ''; ?>" <?php if (!empty($field['required']))  echo esc_attr('required'); ?>>
<?php echo isset($field['value']) ? esc_textarea($field['value']) : ''; ?>
</textarea>
<?php if (!empty($field['description'])) : ?><small class="description"><?php echo wp_kses( $field['description'], wp_kses_allowed_html($field['description'])); ?></small><?php endif; ?>
