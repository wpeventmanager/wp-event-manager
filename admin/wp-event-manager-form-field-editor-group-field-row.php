<tr class="group">
	<td colspan="10">
		<table class="widefat child_table">
			<thead>
				<tr>
					<th width="1%">&nbsp;</th>
					<th><?php esc_attr_e( 'Field Label', 'wp-event-manager' ); ?></th>
					<th width="1%"><?php esc_attr_e( 'Type', 'wp-event-manager' ); ?></th>
					<th><?php esc_attr_e( 'Description', 'wp-event-manager' ); ?></th>
					<th><?php esc_attr_e( 'Placeholder / Options', 'wp-event-manager' ); ?></th>
					<th width="1%"><?php esc_attr_e( 'Meta Key', 'wp-event-manager' ); ?></th>
					<th width="1%"><?php esc_attr_e( 'Only For Admin', 'wp-event-manager' ); ?></th>
					<th width="1%"><?php esc_attr_e( 'Priority', 'wp-event-manager' ); ?></th>
					<th width="1%"><?php esc_attr_e( 'Validation', 'wp-event-manager' ); ?></th>
					<th width="1%" class="field-actions">&nbsp;</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="4">
						<a class="button child-add-field" href="javascript:void(0)"><?php esc_attr_e( 'Add Child field', 'wp-event-manager' ); ?></a>
					</th>			
				</tr>
			</tfoot>
			<tbody class="child-form-fields" data-field="
				<?php
					ob_start();
					$child_index     = -1;
					$child_field_key = '';
					$child_field     = array(
						'type'        => 'text',
						'label'       => '',
						'placeholder' => '',
					);
					require esc_html('wp-event-manager-form-field-editor-group-field.php');
					echo wp_kses_post(ob_get_clean());
					?>	">
			</tbody>
		</table>
	</td>
</tr>