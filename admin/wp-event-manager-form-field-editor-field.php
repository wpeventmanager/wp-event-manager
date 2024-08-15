<?php
if (empty($field_key)) {
	$field_key = $index;
}
$taxonomies = get_object_taxonomies((object) array('post_type' => 'event_listing')); ?>

<tr data-field-type="text-field" data-field-meta="_<?php echo esc_attr(stripslashes($field_key)); ?>">
	<td class="sort-column">&nbsp;</td>
	<td>
		<input type="text" class="input-text" name="<?php echo wp_kses_post($group_key); ?>[<?php echo esc_attr($field_key); ?>][label]" value="<?php echo esc_attr(stripslashes($field['label'])); ?>" />
		<input type="hidden" name="_<?php echo esc_attr(stripslashes($field_key)); ?>_visibility" id="_<?php echo esc_attr(stripslashes($field_key)); ?>_visibility" value="1" />
	</td>
	<td class="field-type">
		<select name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][type]" class="field_type">
			<?php
			foreach ($field_types as $key => $type) {
				if (in_array($field_key, $disbled_fields)) {
					if ($key == $field['type']) {
						printf('<option value="' . esc_attr($key) . '" ' . selected($field['type'], $key, false) . '>' . esc_html($type) . '</option>');
					}
				}elseif(in_array($field_key, $taxonomy_fields)){
					if(strpos($key, 'term') === 0){
						if($key == $field['type']) {
							printf('<option value="' . esc_attr(stripslashes($key)) . '" ' . selected($field['type'], $key, false) . '>' . esc_html($type) . '</option>');
						}else{
							printf('<option value="' . esc_attr(stripslashes($key)) . '" >' . esc_html($type) . '</option>');
						}
					}
				} else {
					printf('<option value="' . esc_attr(stripslashes($key)) . '" ' . selected($field['type'], $key, false) . '>' . esc_html($type) . '</option>');
				}
			} ?>
		</select>
	</td>
	<td>
		<input type="text" class="input-text" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][description]" value="<?php echo esc_attr(isset($field['description']) ? stripslashes($field['description']) : ''); ?>" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>" />
	</td>
	<td class="field-options">
		<?php
		if (isset($field['options']) && is_array($field['options'])) {
			$options = implode(
				'|',
				array_map(
					function ($v, $k) {
						return sprintf($k . ' : %s ', $v);
					},
					$field['options'],
					array_keys($field['options'])
				)
			);
		} else {
			$options = '';
		} ?>
		<input type="text" class="input-text placeholder" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][placeholder]" value="<?php if (isset($field['placeholder'])) {
			printf(esc_html__('%s', 'wp-event-manager'), esc_attr(stripslashes($field['placeholder'])));
		}	?>" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>" />
		<input type="text" class="input-text options" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][options]" placeholder="<?php esc_attr_e('Pipe (|) separate options.', 'wp-event-manager'); ?>" value="<?php echo esc_attr($options); ?>" />
		<div class="file-options">
			<label class="multiple-files"><input type='hidden' value='0' name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][multiple]"><input type="checkbox" class="input-text" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][multiple]" value="1" <?php checked(!empty($field['multiple']), true); ?> /> <?php esc_attr_e('Multiple Files?', 'wp-event-manager'); ?></label>
		</div>
		<div class="taxonomy-options">
			<label class="taxonomy-option">
				<?php if ($taxonomies) : ?>
					<select class="input-text taxonomy-select" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][taxonomy]">
						<?php foreach ($taxonomies  as $taxonomy) : ?>
							<option value="<?php echo esc_attr($taxonomy); ?>" <?php
								if (isset($field['taxonomy'])) {
									echo selected($field['taxonomy'], $taxonomy, false);
								}
								?>>
								<?php echo esc_html($taxonomy); ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</label>
		</div>
		<span class="na">&ndash;</span>
	</td>
	<td> <input type="text" value="_<?php echo esc_attr(stripslashes($field_key)); ?>" readonly></td>
	<td>
		<?php if (!in_array($field_key, $disbled_fields)) : ?>
			<input type="checkbox" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][admin_only]" value="1" <?php checked(!empty($field['admin_only']), true); ?> />
		<?php endif; ?>
	</td>
	<td>
		<input type="text" class="input-text placeholder" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][priority]" value="<?php
			if (isset($field['priority'])) {
				echo esc_attr($field['priority']);
			}
			?>" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>" disabled />
	</td>

	<td class="field-rules">
		<?php if (!in_array($field_key, $disbled_fields)) : ?>
			<div class="rules">
				<select name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($field_key); ?>][required]">
					<?php $field['required'] = (isset($field['required']) ? $field['required'] : false); ?>
					<option value="0" <?php
							if ($field['required'] == false) {
								echo wp_kses_post('selected="selected"');
							} ?>>
						<?php esc_attr_e('Not Required', 'wp-event-manager'); ?>
					</option>
					<option value="1" <?php
							if ($field['required'] == true) {
								echo wp_kses_post('selected="selected"');
							} ?>>
						<?php esc_attr_e('Required', 'wp-event-manager'); ?>
					</option>
				</select>
			</div>
		<?php endif; ?>
		<span class="na">&ndash;</span>
	</td>

	<td class="field-actions">
		<?php if (!in_array($field_key, $disbled_fields)) : ?>
			<a class="delete-field" href='#'>X</a>
		<?php endif; ?>
	</td>
</tr>
