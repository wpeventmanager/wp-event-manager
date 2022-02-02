<?php if(empty($field_key))
	$field_key = $index;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => 'event_listing' ) );
	?>
<tr data-field-type="<?php echo esc_attr($field['type']); ?>">
	<td class="sort-column">&nbsp;</td>

	<td>
		<input type="text" class="input-text" name="<?php echo $group_key;?>[<?php echo esc_attr($field_key);?>][label]" value="<?php echo esc_attr( stripslashes($field['label']) ); ?>" />
	</td>

	<td class="field-type">
		<select name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][type]" class="field_type" >
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
		<input type="text" class="input-text" name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][description]" value="<?php echo esc_attr( isset( $field['description'] ) ? stripslashes($field['description']) : '' ); ?>" placeholder="<?php _e( 'N/A', 'wp-event-manager' ); ?>" />
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
		<input type="text" class="input-text placeholder" name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][placeholder]" value="<?php if(isset($field['placeholder'])) printf( esc_html__( '%s', 'wp-event-manager' ), esc_attr( stripslashes( $field['placeholder'])) );?>" placeholder="<?php _e( 'N/A', 'wp-event-manager' ); ?>" />
		<input type="text" class="input-text options" name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][options]" placeholder="<?php _e( 'Pipe (|) separate options.', 'wp-event-manager' ); ?>" value="<?php echo esc_attr( $options); ?>" />

		<div class="file-options">
			<label class="multiple-files"><input type='hidden' value='0' name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][multiple]"><input type="checkbox" class="input-text" name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][multiple]" value="1" <?php checked( ! empty( $field['multiple'] ), true ); ?> /> <?php _e( 'Multiple Files?', 'wp-event-manager' ); ?></label>
		</div>
		<div class="taxonomy-options">
			<label class="taxonomy-option">
				<?php if ($taxonomies) : ?>
					<select class="input-text taxonomy-select" name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][taxonomy]">
						<?php foreach ($taxonomies  as $taxonomy ) : ?>
							<option value="<?php echo esc_attr( $taxonomy  ); ?>" <?php if(isset($field['taxonomy'])) echo selected( $field['taxonomy'], $taxonomy, false );?> ><?php echo $taxonomy;?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</label>
		</div>
		<span class="na">&ndash;</span>
	</td>

	<td> <input type="text" value="_<?php echo esc_attr($field_key); ?>" readonly></td>

	<td>
	<?php if( !in_array($field_key, $disbled_fields) ) : ?> 
		<input type="checkbox" name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][admin_only]" value="1" <?php checked( ! empty( $field['admin_only'] ), true ); ?> />
	<?php endif; ?>
	</td>

	<td>
		<input type="text" class="input-text placeholder" name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][priority]" value="<?php if(isset($field['priority'])) printf( esc_html__( '%s', 'wp-event-manager' ),  $field['priority'] );?>" placeholder="<?php _e( 'N/A', 'wp-event-manager' ); ?>"  disabled />
	</td>

	<td class="field-rules">
		<?php if( !in_array($field_key, $disbled_fields) ) : ?> 
			<div class="rules">
				<select name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][required]">
					<?php $field['required'] =  ( isset( $field['required'] ) ? $field['required'] : false );?>
					<option value="0" <?php if($field['required'] == false) echo 'selected="selected"';?> ><?php  _e( 'Not Required', 'wp-event-manager' );?></option>
					<option value="1" <?php if($field['required'] == true) echo 'selected="selected"';?> ><?php  _e( 'Required', 'wp-event-manager' );?></option>
				</select>
			</div>
		<?php endif; ?>
		<span class="na">&ndash;</span>
	</td>

	<td class="field-actions">
		<?php if( !in_array($field_key, $disbled_fields) ) : ?> 
			<a class="delete-field" href='#'>X</a>
		<?php endif; ?>
	</td>

</tr>

<?php
if( isset($field['type']) && $field['type'] == 'group' )
{
	$field_types = apply_filters( 'event_manager_form_group_field_types', array(
		'text'           		=> __( 'Text', 'wp-event-manager' ),
		'time'           		=> __( 'Time', 'wp-event-manager' ),
		'checkbox'       		=> __( 'Checkbox', 'wp-event-manager' ),			
		'date'       			=> __( 'Date', 'wp-event-manager' ),
		'timezone'           	=> __( 'Timezone', 'wp-event-manager' ),			
		'file'       			=> __( 'File', 'wp-event-manager' ),			
		'hidden'       			=> __( 'Hidden', 'wp-event-manager' ),			
		'multiselect'       	=> __( 'Multiselect', 'wp-event-manager' ),			
		'number'       			=> __( 'Number', 'wp-event-manager' ),			
		'password'       		=> __( 'Password', 'wp-event-manager' ),			
		'radio'       			=> __( 'Radio', 'wp-event-manager' ),
		'select'         		=> __( 'Select', 'wp-event-manager' ),
		'textarea'    			=> __( 'Textarea', 'wp-event-manager' ),
	) );

	$child_index = -1;
	?>
	<tr class="group">
		<td colspan="10">
			<table class="widefat child_table" id="<?php echo esc_attr($field_key); ?>">
				<thead>
					<tr>
						<th width="1%">&nbsp;</th>
						<th><?php _e( 'Field Label', 'wp-event-manager' ); ?></th>
						<th width="1%"><?php _e( 'Type', 'wp-event-manager' ); ?></th>
						<th><?php _e( 'Description', 'wp-event-manager' ); ?></th>
						<th><?php _e( 'Placeholder / Options', 'wp-event-manager' ); ?></th>
						<th width="1%"><?php _e( 'Meta Key', 'wp-event-manager' ); ?></th>
						<th width="1%"><?php _e( 'Only For Admin', 'wp-event-manager' ); ?></th>
						<th width="1%"><?php _e( 'Priority', 'wp-event-manager' ); ?></th>
						<th width="1%"><?php _e( 'Validation', 'wp-event-manager' ); ?></th>
						<th width="1%" class="field-actions">&nbsp;</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th colspan="4">
							<a class="button child-add-field" href="javascript:void(0)"><?php _e( 'Add Child field', 'wp-event-manager' ); ?></a>
						</th>			
					</tr>
				</tfoot>
				<tbody class="child-form-fields" data-name="<?php echo esc_attr($group_key);?>[<?php echo esc_attr($field_key);?>][fields]" data-field="<?php
								ob_start();
								
								$child_field_key = '';
								$child_field     = array(
									'type'        => 'text',
									'label'       => '',
									'placeholder' => ''
								);
								include( 'wp-event-manager-form-field-editor-group-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">

					<?php
					if( isset($field['fields']) && !empty($field['fields']) )
					{
						foreach ($field['fields'] as $child_field_key => $child_field) 
						{
							$child_index ++;

							include( 'wp-event-manager-form-field-editor-group-field.php' );
						}	
					}					
					?>
				</tbody>
			</table>
		</td>
	</tr>

	<?php
}
?>
