<div class="controls" style="position: relative">
   <input type="text" class="input-text" name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" id="<?php echo isset($field['id']) ? esc_attr($field['id']) :  esc_attr($key); ?>" attribute="<?php echo esc_attr(isset($field['attribute']) ? $field['attribute'] : ''); ?>" placeholder="<?php _e(esc_attr($field['placeholder']), 'wp-event-manager'); ?>" value="<?php echo isset($field['value']) ? esc_attr($field['value']) : ''; ?>" maxlength="<?php echo !empty($field['maxlength']) ? esc_attr($field['maxlength']) : ''; ?>" <?php if (!empty($field['required'])) echo esc_attr('required'); ?> data-picker="datepicker" />
   <?php if (!empty($field['description'])) : ?>
      <small class="description">
         <?php echo wp_kses_post($field['description']); ?>
      </small>
   <?php endif; ?>
</div>