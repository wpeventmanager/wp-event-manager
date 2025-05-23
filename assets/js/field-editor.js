var FieldEditor = function() {
    return {
		
        ///<summary>
        ///Initializes the form editor.  
        ///</summary>     
        ///<returns type="initialization settings" />   
        /// <since>1.0.0</since> 
        init: function() {
			
            jQuery('.wp-event-manager-event-form-field-editor').on('init',FieldEditor.actions.initSortable);
			jQuery(	'.wp-event-manager-event-form-field-editor').trigger('init');

			jQuery('.add-field').on('click', FieldEditor.actions.addNewFields); //add new field
			jQuery('body').on('click', '.child-add-field', FieldEditor.actions.addNewChildFields);
			jQuery(	'.wp-event-manager-event-form-field-editor').on('change', '.field-type select', FieldEditor.actions.changeFieldTypeOptions);
			jQuery('.delete-field').on('click', FieldEditor.actions.deleteField); //delete field
			jQuery('.reset').on('click', FieldEditor.actions.resetFields); //reset field
			
			jQuery('.wp-event-manager-organizer-form-field-editor').on('init',FieldEditor.actions.initSortable);
			jQuery(	'.wp-event-manager-organizer-form-field-editor').trigger('init');

			jQuery(	'body').on('change', '.wp-event-manager-event-form-field-editor #bulk-select', FieldEditor.actions.displayRemoveButton);
			jQuery('.remove-button').on('click', FieldEditor.actions.deleteBulkSelectField);
        },
        actions: {
			///<summary>
			///dsiplay remove button
			///</summary>     
			///<returns type="initialization settings" />   
			/// <since>1.0</since> 
			displayRemoveButton :function(e) {
				e.preventDefault();
				var parentContainer = jQuery(this).closest('.wp-event-manager-event-form-field-editor');
				var removeButton = parentContainer.find('.remove-button');
				if (parentContainer.find('#bulk-select:checked').length > 0) {
					removeButton.show();
				} else {
					removeButton.hide();
				}
			},

			///<summary>
			///remove bulk select field
			///</summary>     
			///<returns type="initialization settings" />   
			/// <since>1.0</since> 
			deleteBulkSelectField :function(e) {
					e.preventDefault();
					if (!confirm("Are you sure you want to delete the selected fields?")) {
						return;
					}
					var parentContainer = jQuery(this).closest('.wp-event-manager-event-form-field-editor');
					parentContainer.find('#bulk-select:checked').closest('tr').remove();
					
					jQuery(this).hide();
			},

			///<summary>
			///Initializes sortable.  
			///</summary>     
			///<returns type="initialization settings" />   
			/// <since>1.0</since> 
			initSortable :function() {
				jQuery(this).sortable({
					items:'tr:has(td)',
					cursor:'move',
					axis:'y',
					handle: 'td.sort-column',
					scrollSensitivity:40,
					helper:function(e,ui){
						ui.children().each(function(){
							jQuery(this).width(jQuery(this).width());
						});
						return ui;
					},
					start:function(event,ui){
						ui.item.css('background-color','#FEFEE6');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
					}
				});
				jQuery(this).find('.field-type select').each(FieldEditor.actions.changeFieldTypeOptions);
				jQuery(this).find('.field-rules select:visible').chosen();
			},
			
			///<summary>
			///remove current field
			///</summary>     
			///<returns type="initialization settings" />   
			/// <since>1.0</since> 
			deleteField :function() {
				if (window.confirm(wp_event_manager_form_editor.cofirm_delete_i18n)) {
					var field_type = jQuery(this).closest('tr').data('field-type');
					var field_meta = jQuery(this).closest('tr').data('field-meta');
					jQuery("#"+field_meta+"_visibility").val("0");
					if(field_type === 'group'){
						jQuery(this).closest('tr').next('tr.group').remove();
						jQuery(this).closest('tr').remove();
					} else {
						jQuery(this).closest('tr').css('display', 'none');
					}
				}
				return false;
			},
			
			///<summary>
			///reset all fields
			///</summary>     
			///<returns type="initialization settings" />   
			/// <since>1.0</since> 
			resetFields :function() {
				if (window.confirm(wp_event_manager_form_editor.cofirm_reset_i18n)) {
					return true;
				}
				return false;
			},
			
			///<summary>
			///reset all fields
			///</summary>     
			///<returns type="initialization settings" />   
			/// <since>1.0</since> 
			addNewFields :function() {
				var $tbody = jQuery(this).closest('table').find('tbody#form-fields');
				var row    = $tbody.data('field');
				row = row.replace(/\[-1\]/g, "[" + $tbody.find('tr').size() + "]");
				$tbody.append(row);
				
				jQuery('.wp-event-manager-event-form-field-editor').trigger('init');
				jQuery('.delete-field').on('click', FieldEditor.actions.deleteField); //delete field
				return false;
			},

			///<summary>
			///reset all fields
			///</summary>     
			///<returns type="initialization settings" />   
			/// <since>1.0</since> 
			addNewChildFields :function() {
				var $tbody = jQuery(this).closest('table.child_table').find('tbody.child-form-fields');
				var row    = $tbody.data('field');
				row = row.replace(/\[-1\]/g, "[" + $tbody.find('tr').size() + "]");

				var parnet_name = jQuery(this).closest('tr.group').prev().find('select.field_type').attr('name');
				parnet_name = parnet_name.replace(/\[type\]/g, "");
				row = row.replace(/\[\]/g, parnet_name);

				$tbody.append(row);
				
				jQuery('.wp-event-manager-event-form-field-editor').trigger('init');
				jQuery('.delete-field').on('click', FieldEditor.actions.deleteField); //delete field
				return false;
			},

			///<summary>
			///on change field type 
			///</summary>     
			///<returns type="initialization settings" />   
			/// <since>1.0</since> 
			changeFieldTypeOptions: function(){

				jQuery(this).closest('tr').find('.field-options .placeholder').hide();
				jQuery(this).closest('tr').find('.field-options .options').hide();
				jQuery(this).closest('tr').find('.field-options .na').hide();
				jQuery(this).closest('tr').find('.field-options .file-options').hide();
				jQuery(this).closest('tr').find('.field-options .taxonomy-select').hide();

				var field_type = jQuery(this).closest('tr').data('field-type');
				
				if ('select' === jQuery(this).val() || 'multiselect' === jQuery(this).val() || 'button-options' === jQuery(this).val() || 'radio' === jQuery(this).val()) {
					jQuery(this).closest('tr').find('.field-options .options').show();
				} else if ('file' === jQuery(this).val()) {
					jQuery(this).closest('tr').find('.field-options .file-options').show();
				} else if ('term-select' === jQuery(this).val() || 'term-checklist' === jQuery(this).val() || 'term-multiselect' === jQuery(this).val()) {
					jQuery(this).closest('tr').find('.field-options .taxonomy-select').show();
				} else {
					jQuery(this).closest('tr').find('.field-options .placeholder').show();
				}

				jQuery(this).closest('tr').find('.field-rules .rules').hide();
				jQuery(this).closest('tr').find('.field-rules .na').hide();

				jQuery(this).closest('tr').find('.field-rules .rules').show();
				jQuery(this).closest('tr').find('.field-rules select:visible').chosen();
			}
        }
    }
};
FieldEditor = FieldEditor(), jQuery(document).ready(function(t) {
    FieldEditor.init()
});