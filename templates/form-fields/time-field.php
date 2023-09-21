<?php

/**
 * 
 * @since 3.0
 * 
 */
?>
<div class="controls" style="position: relative">
   <input type="text" class="input-text" name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" id="<?php echo isset($field['id']) ? esc_attr($field['id']) :  esc_attr($key); ?>" attribute="<?php echo esc_attr(isset($field['attribute']) ? $field['attribute'] : ''); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo isset($field['value']) ? esc_attr($field['value']) : ''; ?>" maxlength="<?php echo !empty($field['maxlength']) ? $field['maxlength'] : ''; ?>" <?php if (!empty($field['required'])) echo esc_attr('required'); ?> data-picker="timepicker" />
   <?php if (!empty($field['description'])) : ?><small class="description"><?php echo esc_textarea($field['description']); ?></small><?php endif; ?>
</div>