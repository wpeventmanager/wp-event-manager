<?php if(empty($child_field_key)) {
	$child_field_key = $child_index;
}
$taxonomies = get_object_taxonomies((object) array('post_type' => 'event_listing')); ?>
<tr>
	<td class="sort-column">&nbsp;</td>
	<td>
		<input type="text" class="input-text" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][label]" value="<?php echo esc_attr(stripslashes($child_field['label'])); ?>" />
	</td>
	<td class="field-type">
		<select name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][type]" class="field_type" >
			<?php
			foreach ($field_types as $key => $type) {
				if(!in_array($key, array('group'))) {
					if(in_array($child_field_key, $disbled_fields)) {
						if($key == $child_field['type']) {
							echo wp_kses_post('<option value="' . esc_attr($key) . '" ' . selected($child_field['type'], $key, false) . '>' . esc_html($type) . '</option>');
						}
					} else {
						echo wp_kses_post('<option value="' . esc_attr($key) . '" ' . selected($child_field['type'], $key, false) . '>' . esc_html($type) . '</option>');
					}
				}
			} ?>
		</select>
	</td>
	<td>
		<input type="text" class="input-text" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][description]" value="<?php echo esc_attr(isset($child_field['description']) ? stripslashes($child_field['description']) : ''); ?>" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>" />
	</td>
	<td class="field-options">
		<?php
		if(isset($child_field['options']) && !empty($child_field['options'])) {
			$child_options = implode(
				'|',
				array_map(
					function ($child_v, $child_k) {
						return sprintf($child_k . ' : %s ', 'wp-event-manager' , $child_v) ; },
					$child_field['options'],
					array_keys($child_field['options'])
				)
			);
		} else {
				$child_options = '';
		} ?>
		<input type="text" class="input-text placeholder" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][placeholder]" value="
			<?php
				if(isset($child_field['placeholder'])) {
					printf(esc_html__('%s', 'wp-event-manager'), esc_attr(stripslashes($child_field['placeholder'])));}
				?>
		" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>" />
		<input type="text" class="input-text options" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][options]" placeholder="<?php esc_attr_e('Pipe (|) separate options.', 'wp-event-manager'); ?>" value="<?php echo esc_attr($child_options); ?>" />

		<div class="file-options">
			<label class="multiple-files"><input type="checkbox" class="input-text" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][multiple]" value="1" <?php checked(!empty($child_field['multiple']), true); ?> /> <?php esc_attr_e('Multiple Files?', 'wp-event-manager'); ?></label>
		</div>
		<div class="taxonomy-options">
			<label class="taxonomy-option">
				<?php if($taxonomies) : ?>
					<select class="input-text taxonomy-select" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][taxonomy]">
						<?php foreach ($taxonomies  as $taxonomy) : ?>
							<option value="<?php echo esc_attr($taxonomy); ?>" 
								<?php
								if(isset($child_field['taxonomy'])) {
									echo esc_attr(selected($child_field['taxonomy'], $taxonomy, false));}
								?>
							><?php echo esc_attr($taxonomy); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</label>
		</div>
		<span class="na">&ndash;</span>
	</td>
	<td> <input type="text" value="_<?php echo esc_attr($child_field_key); ?>" readonly></td>
	<td>
		<?php if(!in_array($child_field_key, $disbled_fields)) : ?> 
			<input type="checkbox" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][admin_only]" value="1" <?php checked(!empty($child_field['admin_only']), true); ?> />
		<?php endif; ?>
	</td>
	<td>
		<input type="text" class="input-text placeholder" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][priority]" value="
			<?php
			if(isset($child_field['priority'])) {
				printf(esc_html__('%s', 'wp-event-manager'), esc_attr(stripslashes($child_field['priority'])));}
			?>
		" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>"  disabled />
	</td>
	<td class="field-rules">
		<?php if(!in_array($child_field_key, $disbled_fields)) : ?> 
			<div class="rules">
				<select name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][fields][<?php echo esc_attr($child_field_key); ?>][required]">
					<?php $child_field['required'] = (isset($child_field['required']) ? $child_field['required'] : false); ?>
					<option value="0" 
					<?php
					if($child_field['required'] == false) {
						echo esc_attr('selected="selected"');
					}
					?>
					><?php esc_attr_e('Not Required', 'wp-event-manager'); ?></option>
					<option value="1" 
					<?php
					if($child_field['required'] == true) {
						echo esc_attr('selected="selected"');
					}
					?>
					><?php esc_attr_e('Required', 'wp-event-manager'); ?></option>
				</select>
			</div>
		<?php endif; ?>
		<span class="na">&ndash;</span>
	</td>
	<td class="field-actions">
		<?php if(!in_array($child_field_key, $disbled_fields)) : ?> 
			<a class="delete-field" href="javascript:void(0)">X</a>
		<?php endif; ?>
	</td>
</tr>