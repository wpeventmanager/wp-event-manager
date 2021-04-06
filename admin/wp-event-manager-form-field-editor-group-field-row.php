<tr class="group">
	<td colspan="10">
		<table class="widefat child_table">
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
			<tbody class="child-form-fields" data-field="<?php
							ob_start();
							$child_index     = -1;
							$child_field_key = '';
							$child_field     = array(
								'type'        => 'text',
								'label'       => '',
								'placeholder' => ''
							);
							include( 'wp-event-manager-form-field-editor-group-field.php' );
							echo esc_attr( ob_get_clean() );
						?>">
			</tbody>
		</table>
	</td>
</tr>