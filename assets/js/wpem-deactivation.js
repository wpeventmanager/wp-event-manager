var Deactivation = function () {
    /// <summary>Constructor function of the Deactivation</summary>
    /// <since>3.1.46</since>
    /// <returns type="Deactivation" /> 
    return {
        /// <summary>
        /// Initializes the Deactivation.       
        /// </summary>                 
        /// <returns type="initialization settings" />     
        /// <since>3.1.46</since> 
        init: function () {
            jQuery('#deactivate-wp-event-manager').on('click', Deactivation.actions.showDeactivationPopup);
        },

        actions: {

            showDeactivationPopup: function (e) {
                e.preventDefault();
                jQuery("#wpem-deactivation-popup").css("display", "flex");
                Deactivation.actions.bindPopupEvents();
            },

            bindPopupEvents: function () {
                jQuery('#close-popup').on('click', function () {
                    jQuery("#wpem-deactivation-popup").css("display", "none");
                });

                jQuery('input[name="reason"]').on('change', function () {
                    if (jQuery(this).val() === 'Other') {
                        jQuery('#other-reason-box').slideDown();
                    } else {
                        jQuery('#other-reason-box').slideUp();
                    }
                });

                jQuery('#wpem-deactivation-form').on('submit', function (e) {
                    e.preventDefault();
                    jQuery('#wpem-deactivation-popup .popup-content').html(`
                        <div class="deactivating-message">
                            Deactivating...
                        </div>
                    `);
                    const formData = jQuery(this).serialize();

                    jQuery.post(wpem_ajax.ajax_url, formData + '&action=wpem_deactivation_form'+'&ajax_nonce=' + wpem_ajax.nonce, function (response) {
                        if (response.success) {
                            Deactivation.actions.deactivatePlugin();
                        }
                    });
                });

                jQuery('#skip-deactivate').on('click', function () {
                    Deactivation.actions.deactivatePlugin();
                });
            },

            deactivatePlugin: function () {
                jQuery('#wpem-deactivation-popup .popup-content').html(`
                    <div class="deactivating-message">Deactivating...</div>
                `);
                $deactivateLink =  jQuery('#the-list').find('[data-slug="wp-event-manager"] span.deactivate a');
                location.href = $deactivateLink.attr('href');
            }
        }
    };
};

Deactivation = Deactivation();
jQuery(document).ready(function ($) {
    Deactivation.init();
});