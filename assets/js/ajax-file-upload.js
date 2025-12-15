var AjaxFileUpload= function () {
    /// <summary>Constructor function of the event AjaxFileUpload class.</summary>
    /// <returns type="Home" />      
    return {
		///<summary>
        ///Initializes the ajax file upload.  
        ///</summary>     
        ///<returns type="initialization settings" />   
       	/// <since>1.0.0</since>
        
        init: function() {
			jQuery('.wp-event-manager-file-upload').each(function(){
				jQuery(this).fileupload({
					dataType: 'json',
					dropZone: jQuery(this),
					url: event_manager_ajax_file_upload.ajax_upload_url,
					maxNumberOfFiles: 1,
					formData: {
						script: true
					},

					add: function (e, data) {
						console.log("img upload");
						var $file_field     = jQuery(this);
						var $form           = $file_field.closest('form');
						var $uploaded_files = $file_field.parent().find('.event-manager-uploaded-files');
						var uploadErrors    = [];
						// Validate type
						var allowed_types = jQuery(this).data('file_types');
						if(allowed_types) {
							var acceptFileTypes = new RegExp("(\.|\/)(" + allowed_types + ")$", "i");
							if(data.originalFiles[0]['name'].length && !acceptFileTypes.test(data.originalFiles[0]['name'])) {
								uploadErrors.push(event_manager_ajax_file_upload.i18n_invalid_file_type + ' ' + allowed_types.replace(/\|/g, ', '));
							}
						}
						if(uploadErrors.length > 0) {
							alert(uploadErrors.join("\n"));
						} else {
							$form.find(':input[type="submit"]').attr('disabled', 'disabled');
							data.context = jQuery('<progress value="" max="100"></progress>').appendTo($uploaded_files);
							data.submit();
						}
					},

					progress: function (e, data) {
						var $file_field     = jQuery(this);
						var $uploaded_files = $file_field.parent().find('.event-manager-uploaded-files');
						var progress        = parseInt(data.loaded / data.total * 100, 10);
						data.context.val(progress);
					},

					fail: function (e, data) {
						console.log("fail");
						var $file_field     = jQuery(this);
						var $form           = $file_field.closest('form');
						
						// Remove progress indicator
						if (data.context) {
							data.context.remove();
						}
						
						// Get error details
						var errorMessage = 'Upload failed';
						if (data.errorThrown) {
							errorMessage = data.errorThrown;
						} else if (data.jqXHR && data.jqXHR.responseJSON && data.jqXHR.responseJSON.message) {
							errorMessage = data.jqXHR.responseJSON.message;
						} else if (data.textStatus) {
							errorMessage = 'Upload failed: ' + data.textStatus;
						}
						
						console.error('Upload error:', data);
						alert(errorMessage);
						
						$form.find(':input[type="submit"]').removeAttr('disabled');
					},

					done: function (e, data) {
						console.log("done");
						var $file_field     = jQuery(this);
						var $form           = $file_field.closest('form');
						var $uploaded_files = $file_field.parent().find('.event-manager-uploaded-files');
						var multiple        = $file_field.attr('multiple') ? 1 : 0;
						var image_types     = [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ];
						data.context.remove();
						
						// Handle the response properly
						var response = data.result;
						if (typeof response === 'string') {
							try {
								response = JSON.parse(response);
							} catch (e) {
								console.error('Invalid JSON response:', response);
								alert('Upload failed: Invalid server response');
								$form.find(':input[type="submit"]').removeAttr('disabled');
								return;
							}
						}
						
						jQuery.each(response.files, function(index, file) {
							if(file.error || !file.success) {
								alert(file.error || 'Upload failed');
							} else {
								if(jQuery.inArray(file.extension, image_types) >= 0) {
									var html = jQuery.parseHTML(event_manager_ajax_file_upload.js_field_html_img);
									jQuery(html).find('.event-manager-uploaded-file-preview img').attr('src', file.url);
								} else {
									var html = jQuery.parseHTML(event_manager_ajax_file_upload.js_field_html);
									jQuery(html).find('.event-manager-uploaded-file-name code').text(file.name);
								}
								jQuery(html).find('.input-text').val(file.url);
								jQuery(html).find('.input-text').attr('name', 'current_' + $file_field.attr('name'));
								if(multiple) {
									$uploaded_files.append(html);
								} else {
									$uploaded_files.html(html);
								}
							}
						});
						$form.find(':input[type="submit"]').removeAttr('disabled');
					}
				});
			});		
		} 		
  	} //enf of return
}; //end of class

AjaxFileUpload= AjaxFileUpload();
jQuery(document).ready(function($) {
   AjaxFileUpload.init();
});