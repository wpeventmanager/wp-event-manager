jQuery(document).ready(function ($) {
    $('#deactivate-wp-event-manager').on('click', function (e) {
        e.preventDefault();

        // Append the popup
        $('body').append(`
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

        // Close the popup
        $('#close-popup').on('click', function () {
            $('#wpem-deactivation-popup').remove();
        });

        // Show/hide "Other" text area
        $('input[name="reason"]').on('change', function () {
            if ($(this).val() === 'Other') {
                $('#other-reason-box').slideDown();
            } else {
                $('#other-reason-box').slideUp();
            }
        });

        // Submit feedback and deactivate
        $('#wpem-deactivation-form').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.post(wpemAjax.ajaxUrl, formData + '&action=wpem_deactivation_form', function (response) {
                if (response.success) {
                    alert(response.data); // Feedback success message
                    deactivatePlugin();
                } else {
                    alert(response.data); // Error message
                }
            });
        });

        // Skip feedback and deactivate
        $('#skip-deactivate').on('click', function () {
            deactivatePlugin();
        });

        // Deactivate plugin
        function deactivatePlugin() {
            $.post(wpemAjax.ajaxUrl, { action: 'wpem_deactivate_plugin' }, function () {
                window.location.reload(); // Reload the page to finalize deactivation
            });
        }
    });
});
