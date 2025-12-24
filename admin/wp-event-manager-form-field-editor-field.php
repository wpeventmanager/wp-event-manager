<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if (empty($wpem_field_key)) { 
	$wpem_field_key = $index;
}
$wpem_taxonomies = get_object_taxonomies((object) array('post_type' => 'event_listing')); ?>

<tr data-field-type="text-field" data-field-meta="_<?php echo esc_attr(stripslashes($wpem_field_key)); ?>">
	 <td class="sort-column">&nbsp;</td>
    <td>
        <input <?php if (in_array($wpem_field_key, $disbled_fields)) echo 'disabled'; ?> type="checkbox" id="bulk-select" class="input-checkbox" name="" value="" />
    </td>
    <td>
        <input type="text" class="input-text" name="<?php echo wp_kses_post($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][label]" value="<?php echo esc_attr(stripslashes($wpem_field['label'])); ?>" />
        <input type="hidden" name="_<?php echo esc_attr(stripslashes($wpem_field_key)); ?>_visibility" id="_<?php echo esc_attr(stripslashes($wpem_field_key)); ?>_visibility" value="1" />
    </td>
	<td class="field-type">
		<select name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][type]" class="field_type">
			<?php
			foreach ($field_types as $wpem_key => $type) {

				if ($wpem_field_key !== 'recure_custom_weeks' && $wpem_key === 'multiweek') {
					continue;
				}
				if ($wpem_field_key !== 'recure_custom_dates' && $wpem_key === 'multidate') {
					continue;
				}

				if (in_array($wpem_field_key, $disbled_fields)) {
					if ($wpem_key == $wpem_field['type']) {
						printf('<option value="' . esc_attr($wpem_key) . '" ' . selected($wpem_field['type'], $wpem_key, false) . '>' . esc_html($type) . '</option>');
					}
				}elseif(in_array($wpem_field_key, $taxonomy_fields)){
					if(strpos($wpem_key, 'term') === 0){
						if($wpem_key == $wpem_field['type']) {
							printf('<option value="' . esc_attr(stripslashes($wpem_key)) . '" ' . selected($wpem_field['type'], $wpem_key, false) . '>' . esc_html($type) . '</option>');
						}else{
							printf('<option value="' . esc_attr(stripslashes($wpem_key)) . '" >' . esc_html($type) . '</option>');
						}
					}
				} else {
					printf('<option value="' . esc_attr(stripslashes($wpem_key)) . '" ' . selected($wpem_field['type'], $wpem_key, false) . '>' . esc_html($type) . '</option>');
				}
			} ?>
		</select>
	</td>
	<td>
		<input type="text" class="input-text" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][description]" value="<?php echo esc_attr(isset($wpem_field['description']) ? stripslashes($wpem_field['description']) : ''); ?>" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>" />
	</td>
	<td class="field-options">
		<?php
		if (isset($wpem_field['options']) && is_array($wpem_field['options'])) {
			$wpem_options = implode(
				'|',
				array_map(
					function ($v, $k) {
						return sprintf($k . ' : %s ', $v);
					},
					$wpem_field['options'],
					array_keys($wpem_field['options'])
				)
			);
		} else {
			$wpem_options = '';
		} ?>
		<input type="text" class="input-text placeholder" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][placeholder]" value="<?php if (isset($wpem_field['placeholder'])) {
			echo esc_html(stripslashes($wpem_field['placeholder']));
		}	?>" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>" />
		<input type="text" class="input-text options" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][options]" placeholder="<?php esc_attr_e('Pipe (|) separate options.', 'wp-event-manager'); ?>" value="<?php echo esc_attr($wpem_options); ?>" />
		<div class="file-options">
			<label class="multiple-files"><input type='hidden' value='0' name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][multiple]"><input type="checkbox" class="input-text" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][multiple]" value="1" <?php checked(!empty($wpem_field['multiple']), true); ?> /> <?php esc_attr_e('Multiple Files?', 'wp-event-manager'); ?></label>
		</div>
		<div class="taxonomy-options">
			<label class="taxonomy-option">
				<?php if ($wpem_taxonomies) : ?>
					<select class="input-text taxonomy-select" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][taxonomy]">
						<?php foreach ($wpem_taxonomies  as $taxonomy) : ?>
							<option value="<?php echo esc_attr($taxonomy); ?>" <?php
								if (isset($wpem_field['taxonomy'])) {
									echo selected($wpem_field['taxonomy'], $taxonomy, false);
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
	<td class="field-options">
		<div class="file-options">
		<label class="folder-location">
			<span><?php esc_html_e('Upload Folder:', 'wp-event-manager'); ?></span>
			<input <?php if (in_array($wpem_field_key, $disbled_file_field)) echo 'disabled'; ?> type="text" class="input-text" 
				name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][folder_location]"
				value="<?php echo esc_attr($wpem_field['folder_location'] ?? ''); ?>"
				placeholder="<?php esc_attr_e('uploads', 'wp-event-manager'); ?>" />
		</label>
		</div>
	</td>
	<td> <input type="text" value="_<?php echo esc_attr(stripslashes($wpem_field_key)); ?>" readonly></td>
	<td>
		<?php if (!in_array($wpem_field_key, $disbled_fields)) : ?>
			<input type="checkbox" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][admin_only]" value="1" <?php checked(!empty($wpem_field['admin_only']), true); ?> />
		<?php endif; ?>
	</td>
	<td>
		<input type="text" class="input-text placeholder" name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][priority]" value="<?php
			if (isset($wpem_field['priority'])) {
				echo esc_attr($wpem_field['priority']);
			}
			?>" placeholder="<?php esc_attr_e('N/A', 'wp-event-manager'); ?>" disabled />
	</td>

	<?php if ($group_key == 'event') { ?>
		<td>
			<select <?php if (in_array($wpem_field_key, $disbled_fields_tab_group)) echo 'disabled'; ?> name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][tabgroup]" class="field_type">
				<?php
				$wpem_field['tabgroup'] = isset($wpem_field['tabgroup']) ? $wpem_field['tabgroup'] : 1;
				$wpem_writepanels = WP_Event_Manager_Writepanels::instance();
				foreach ($wpem_writepanels->get_event_data_tabs() as $wpem_key => $tab) {
					$wpem_selected = ($wpem_field['tabgroup'] == $tab['priority']) ? 'selected' : '';
					echo '<option value="' . esc_attr($tab['priority']) . '"' . esc_attr($wpem_selected) . '>' . esc_html($tab['label']) . '</option>';
				}
				?>
			</select>
		</td>
	<?php } ?>

	<td class="field-rules">
		<?php if (!in_array($wpem_field_key, $disbled_fields)) : ?>
			<div class="rules">
				<select name="<?php echo esc_attr($group_key); ?>[<?php echo esc_attr($wpem_field_key); ?>][required]">
					<?php $wpem_field['required'] = (isset($wpem_field['required']) ? $wpem_field['required'] : false); ?>
					<option value="0" <?php
							if ($wpem_field['required'] == false) {
								echo wp_kses_post('selected="selected"');
							} ?>>
						<?php esc_attr_e('Not Required', 'wp-event-manager'); ?>
					</option>
					<option value="1" <?php
							if ($wpem_field['required'] == true) {
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
		<?php if (!in_array($wpem_field_key, $disbled_fields)) : ?>
			<a class="delete-field" href='#'>X</a>
		<?php endif; ?>
	</td>
</tr>
