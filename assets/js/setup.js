var Setup = function () {
    /// <summary>Constructor function of the Setup</summary>
    /// <since>3.1.46</since>
    /// <returns type="Setup" /> 
    return {
        /// <summary>
        /// Initializes the Setup.       
        /// </summary>                 
        /// <returns type="initialization settings" />     
        /// <since>3.1.46</since> 
        init: function () {
            jQuery('#wpem_save_installation_settings').on('click', Setup.actions.saveSettings);
        },

        actions: {

            saveSettings: function (e) {
                e.preventDefault();
                var dateFormat = jQuery('select[name="wpem_date_format"]').val();
                var timeFormat = jQuery('select[name="wpem-time-format"]').val();
        
                var data = {
                    action: 'wpem_save_installation_settings',
                    date_format: dateFormat,
                    time_format: timeFormat,
                    security: wpem_ajax.nonce
                };
                jQuery.post(wpem_ajax.ajax_url, data, function(response) {
                    if (response.success) {
                        window.location.href = response.data.redirect_url;
                    }
                });
            },
        }
    };
};
Setup = Setup();
jQuery(document).ready(function ($) {
    Setup.init();
});
