<?php if(empty($field_key))
	$field_key = $index;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => 'event_listing' ) );
	?>
<tr>
	<td class="sort-column">&nbsp;</td>
	<td>
		<input type="text" class="input-text" name="<?php echo $group_key;?>[<?php echo $field_key;?>][label]" value="<?php echo esc_attr( stripslashes($field['label']) ); ?>" />
	</td>
	<td class="field-type">
		<select name="<?php echo $group_key;?>[<?php echo $field_key;?>][type]" class="field_type" >
			<?php
			foreach ( $field_types as $key => $type ) {
			    if( in_array($field_key, $disbled_fields) ){
			        if($key==$field['type'])
			            echo '<option value="' . esc_attr( $key ) . '" ' . selected( $field['type'], $key, false ) . '>' . esc_html( $type ) . '</option>';
			    }else{
			        echo '<option value="' . esc_attr( $key ) . '" ' . selected( $field['type'], $key, false ) . '>' . esc_html( $type ) . '</option>';
			    }
			}
			?>
		</select>
	</td>
	<td>
		<input type="text" class="input-text" name="<?php echo $group_key;?>[<?php echo $field_key;?>][description]" value="<?php echo esc_attr( isset( $field['description'] ) ? stripslashes($field['description']) : '' ); ?>" placeholder="<?php _e( 'N/A', 'wp-event-manager' ); ?>" />
	</td>
	<td class="field-options">
	<?php
			if(isset($field['options'])){
				 $options = implode('|', array_map(
				 	function ($v, $k) { return sprintf(__( $k." : %s ",'wp-event-manager'), $v); },
				 		$field['options'],
				 		array_keys($field['options'])
				 	));
			}
			else 
				$options = '';	
		?>
		<input type="text" class="input-text placeholder" name="<?php echo $group_key;?>[<?php echo $field_key;?>][placeholder]" value="<?php if(isset($field['placeholder'])) printf( esc_html__( '%s', 'wp-event-manager' ),  $field['placeholder'] );?>" placeholder="<?php _e( 'N/A', 'wp-event-manager' ); ?>" />
		<input type="text" class="input-text options" name="<?php echo $group_key;?>[<?php echo $field_key;?>][options]" placeholder="<?php _e( 'Pipe (|) separate options.', 'wp-event-manager' ); ?>" value="<?php echo esc_attr( $options); ?>" />

		<div class="file-options">
			<label class="multiple-files"><input type="checkbox" class="input-text" name="<?php echo $group_key;?>[<?php echo $field_key;?>][multiple]" value="1" <?php checked( ! empty( $field['multiple'] ), true ); ?> /> <?php _e( 'Multiple Files?', 'wp-event-manager' ); ?></label>
		</div>
		<div class="taxonomy-options">
			<label class="taxonomy-option">
			<?php if  ($taxonomies) { ?>
					<select class="input-text taxonomy-select" name="<?php echo $group_key;?>[<?php echo $field_key;?>][taxonomy]">
					<?php  foreach ($taxonomies  as $taxonomy ) { ?>
						<option value="<?php echo esc_attr( $taxonomy  ); ?>" <?php if(isset($field['taxonomy'])) echo selected( $field['taxonomy'], $taxonomy, false );?> ><?php echo $taxonomy;?></option>
					<?php } ?>
					</select>
					<?php		
					}
					?>
			</label>
		</div>
		<span class="na">&ndash;</span>
	</td>
	<td> <input type="text" value="_<?php echo $field_key; ?>" readonly></td>
	<td>
	<?php if( !in_array($field_key, $disbled_fields) ){ ?> 
	<input type="checkbox" name="<?php echo $group_key;?>[<?php echo $field_key;?>][admin_only]" value="1" <?php checked( ! empty( $field['admin_only'] ), true ); ?> /></td>
	<?php } ?>
	<td>
		<input type="text" class="input-text placeholder" name="<?php echo $group_key;?>[<?php echo $field_key;?>][priority]" value="<?php if(isset($field['priority'])) printf( esc_html__( '%s', 'wp-event-manager' ),  $field['priority'] );?>" placeholder="<?php _e( 'N/A', 'wp-event-manager' ); ?>"  disabled />
	</td>
	<td class="field-rules">
	<?php if( !in_array($field_key, $disbled_fields) ){ ?> 
		<div class="rules">
			<select name="<?php echo $group_key;?>[<?php echo $field_key;?>][required]">
				<?php $field['required'] =  ( isset( $field['required'] ) ? $field['required'] : false );?>
				<option value="0" <?php if($field['required'] == false) echo 'selected="selected"';?> ><?php  _e( 'Not Required', 'wp-event-manager' );?></option>
				<option value="1" <?php if($field['required'] == true) echo 'selected="selected"';?> ><?php  _e( 'Required', 'wp-event-manager' );?></option>
			</select>
		</div>
		<?php } ?>
		<span class="na">&ndash;</span>
	</td>
	<td class="field-actions">
	<?php if( !in_array($field_key, $disbled_fields) ){ ?> 
		<a class="delete-field" href='#'>X</a>
		<?php } ?>
	</td>
</tr>