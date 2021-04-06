<input type="button" class="input-button" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" attribute="<?php echo esc_attr( isset( $field['attribute'] ) ? $field['attribute'] : '' ); ?>" value="<?php echo esc_attr( isset( $field['value'] ) ? $field['value'] : $key ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?> />

<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo $field['description']; ?></small><?php endif; ?>
