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

                jQuery('body').append(`
                    <div id="wpem-deactivation-popup">
                        <div class="popup-content">
                            <button id="close-popup" class="close-popup-button">&times;</button>
                            <h2 class="popup-heading">Quick Feedback</h2>
                            <p class="popup-subheading">If you have a moment, please share why you are deactivating WP Event Manager:</p>
                            <form id="wpem-deactivation-form">
                                <div class="radio-options">
                                    <label>
                                        <input type="radio" name="reason" value="I no longer need the plugin" required>
                                        I no longer need the plugin
                                    </label>
                                    <label>
                                        <input type="radio" name="reason" value="I found a better plugin">
                                        I found a better plugin
                                    </label>
                                    <label>
                                        <input type="radio" name="reason" value="I couldn't get the plugin to work">
                                        I couldn't get the plugin to work
                                    </label>
                                    <label>
                                        <input type="radio" name="reason" value="It's a temporary deactivation">
                                        It's a temporary deactivation
                                    </label>
                                    <label>
                                        <input type="radio" name="reason" value="Other">
                                        Other
                                    </label>
                                </div>
                                <div id="other-reason-box" style="display: none; margin-top: 10px;">
                                    <textarea id="additional_feedback" name="additional_feedback" placeholder="Tell us more"></textarea>
                                </div>
                                <div class="popup-buttons">
                                    <button type="submit" class="btn-submit-deactivate">Submit & Deactivate</button>
                                    <button type="button" id="skip-deactivate" class="btn-skip-deactivate">Skip & Deactivate</button>
                                </div>
                            </form>
                        </div>
                    </div>
                `);

                Deactivation.actions.bindPopupEvents();
            },

            bindPopupEvents: function () {
                jQuery('#close-popup').on('click', function () {
                    jQuery('#wpem-deactivation-popup').remove();
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

                    jQuery.post(wpem_ajax.ajax_url, formData + '&action=wpem_deactivation_form', function (response) {
                        if (response.success) {
                            alert(response.data);
                            Deactivation.actions.deactivatePlugin();
                        } else {
                            alert(response.data);
                        }
                    });
                });

                jQuery('#skip-deactivate').on('click', function () {
                    Deactivation.actions.deactivatePlugin();
                });
            },

            deactivatePlugin: function () {
                jQuery('#wpem-deactivation-popup .popup-content').html(`
                    <div class="deactivating-message">
                        Deactivating...
                    </div>
                `);
                jQuery.post(wpem_ajax.ajax_url, {
                    action: 'wpem_deactivate_plugin' },
                    function () {
                    window.location.reload();
                });
            }
        }
    };
};

Deactivation = Deactivation();
jQuery(document).ready(function ($) {
    Deactivation.init();
});
