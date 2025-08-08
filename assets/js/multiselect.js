var MultiSelect= function () {
    /// <summary>Constructor function of the event MultiSelect class.</summary>
    /// <returns type="Home" />      
    return {
     	///<summary>
        ///Initializes the multiselect.  
        ///</summary>     
        ///<returns type="initialization settings" />   
        /// <since>1.0.0</since>         
        init: function() 
        {
			jQuery(".event-manager-multiselect").chosen({search_contains:!0});
            const $allSelects = jQuery('select.event-manager-multiselect[multiple]');

            $allSelects.each(function() {
                const $select = jQuery(this);
                const $form = $select.closest('form');
                $form.on('submit', function(e) {
                    let selectedValues = $select.val();
                    const $submitButton = jQuery(this).find('[type="submit"]');
                    $select.closest('.field').find('.error-msg').remove();

                    if (!selectedValues || selectedValues.length === 0) {
                        e.preventDefault();
                        $select.closest('.field').append('<div class="error-msg" style="color:red; font-size:13px; margin-top:5px;">Please fill this field</div>');
                        $select.next('.chosen-container').css('border', '1px solid red');
                        $submitButton.css('pointer-events', 'none');
                    } else {
                        $select.next('.chosen-container').css('border', '');
                        $submitButton.css('pointer-events', 'auto');
                    }
                });
                $select.on('change', function() {
                    const selectedValues = jQuery(this).val();
                    const $submitButton = $form.find('[type="submit"]');

                    if (selectedValues && selectedValues.length > 0) {
                        jQuery(this).closest('.field').find('.error-msg').remove();
                        jQuery(this).next('.chosen-container').css('border', '');
                        $submitButton.css('pointer-events', 'auto');
                    }
                });
            });
		}   
    } //enf of returnmultiselect
}; //end of class

MultiSelect= MultiSelect();
jQuery(document).ready(function($) {
   MultiSelect.init();
});