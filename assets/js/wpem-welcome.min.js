
jQuery(document).ready(function ($) {

    
$('.wpem-welcome-dismiss').on('click', function (e) {

    e.preventDefault();

    var notice = $(this).closest('.wpem-welcome-notice');

    // Hide notification immediately.
    notice.stop(true, true).fadeOut(150, function () {
        $(this).remove();
    });

    // Save dismissal in the background.
    $.ajax({
        url: wpemWelcome.ajax_url,
        type: 'POST',
        timeout: 15000,
        data: {
            action: 'wpem_dismiss_welcome_notice',
            nonce: wpemWelcome.nonce
        },
        success: function (response) {
            if (!response.success) {
                console.error('Welcome notice dismissal was not saved.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Welcome notice AJAX error:', error);
        }
    });

});



    // Open Zoho form popup.
    $('.wpem-open-zoho-form').on('click', function () {

        var modal = $('#wpem-zoho-modal');
        var iframe = document.getElementById('ziframe_830693');

        if (!modal.length || !iframe) {
            return;
        }

        modal.fadeIn(200);
        $('body').addClass('wpem-zoho-modal-open');

        // Load Zoho form with the current page URL as referrer.
        var formUrl = 'https://forms.zohopublic.com/wpem/form/testform/formperma/c6zXw52sN_RnBx4eh__X--6S5HMU7NfvIW-spBm6L_I';

        var referrer = window.location.href;

        try {
            if (window.self !== window.top) {
                referrer = window.top.location.href;
            }
        } catch (e) {}

        if (referrer && referrer.length > 1800) {
            var queryIndex = referrer.indexOf('?');

            if (queryIndex > -1) {
                referrer = referrer.substring(0, queryIndex);
            }

            if (referrer.length > 1800) {
                referrer = referrer.substring(0, 1800);
            }
        }

        formUrl += '?referrername=' + encodeURIComponent(referrer);

        iframe.src = formUrl;
    });

    // Close Zoho popup.
    function closeZohoModal() {
        $('#wpem-zoho-modal').fadeOut(200);
        $('body').removeClass('wpem-zoho-modal-open');

        // Stop iframe when popup closes.
        $('#ziframe_830693').attr('src', 'about:blank');
    }

    $('.wpem-zoho-close, .wpem-zoho-modal-overlay').on('click', function () {
        closeZohoModal();
    });

    // Close popup using Escape key.
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#wpem-zoho-modal').is(':visible')) {
            closeZohoModal();
        }
    });


});
