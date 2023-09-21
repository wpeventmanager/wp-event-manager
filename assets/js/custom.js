var ajaxurl = event_custom.ajax_url;

jQuery(document).ready(function ($) {
	// Handler for the "Load More" button click
	$("#load-more-events").on("click", function () {
		var $button = $(this);
		var page = $button.data("page");

		// Make an AJAX request to load more events
		$.ajax({
			url: ajaxurl, // WordPress AJAX URL
			type: "POST",
			data: {
				action: "load_more_events", // Custom AJAX action name
				page: page,
			},
			success: function (response) {
				if (response) {
					// Append the loaded events to the container
					$(".event-list-container").append(response);

					// Update the data-page attribute for the next load
					$button.data("page", page + 1);
				} else {
					// No more events to load, hide the button
					$button.hide();
				}
			},
		});
	});
});
