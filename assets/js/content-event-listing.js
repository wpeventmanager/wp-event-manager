var ContentEventListing = function () {
	var STORAGE_PREFIX = 'wpem_layout_inst_';

	function getStoredLayout(instanceId) {
		if (!instanceId) {
			return localStorage.getItem('layout');
		}
		var scoped = localStorage.getItem(STORAGE_PREFIX + instanceId);
		if (scoped !== null && scoped !== '') {
			return scoped;
		}
		return localStorage.getItem('layout');
	}

	function setStoredLayout(instanceId, layout) {
		if (instanceId) {
			localStorage.setItem(STORAGE_PREFIX + instanceId, layout);
		}
	}

	function getInstanceRoot(el) {
		return jQuery(el).closest('.wpem-event-listing-instance');
	}

	function applyLayoutToInstance($root, layoutKey) {
		if (!$root.length) {
			return;
		}
		var $listings = $root.find('.wpem-event-listings');
		var $boxCols = $root.find('.wpem-event-box-col');

		if (layoutKey === 'line-layout') {
			$boxCols.show();
			$root.find('.wpem-event-box-layout').removeClass('wpem-active-layout');
			$root.find('.wpem-event-list-layout').addClass('wpem-active-layout');
			if ($listings.hasClass('wpem-row')) {
				$listings.removeClass('wpem-row');
			}
			$listings.removeClass('wpem-event-listing-box-view');
			$listings.addClass('wpem-event-listing-list-view');
		} else if (layoutKey === 'calendar-layout') {
			$boxCols.hide();
			$root.find('.wpem-event-list-layout').removeClass('wpem-active-layout');
			$root.find('.wpem-event-box-layout').removeClass('wpem-active-layout');
			$root.find('.wpem-event-calendar-layout').addClass('wpem-active-layout');
			if (!$listings.hasClass('wpem-row')) {
				$listings.addClass('wpem-row');
			}
			$listings.removeClass('wpem-event-listing-list-view');
			$listings.addClass('wpem-event-listing-box-view');
			$listings.addClass('wpem-event-listing-calendar-view');
		} else {
			$boxCols.show();
			$root.find('.wpem-event-list-layout').removeClass('wpem-active-layout');
			$root.find('.wpem-event-box-layout').addClass('wpem-active-layout');
			if (!$listings.hasClass('wpem-row')) {
				$listings.addClass('wpem-row');
			}
			$listings.removeClass('wpem-event-listing-list-view');
			$listings.addClass('wpem-event-listing-box-view');
		}
	}

	return {
		init: function () {
			Common.logInfo('ContentEventListing.init...');

			jQuery(document).on('click', '.wpem-event-list-layout', ContentEventListing.actions.lineLayoutIconClick);
			jQuery(document).on('click', '.wpem-event-box-layout', ContentEventListing.actions.boxLayoutIconClick);

			jQuery('.wpem-event-listing-instance').each(function () {
				var $root = jQuery(this);
				var instanceId = $root.attr('data-wpem-listing-instance') || '';
				if ($root.find('.wpem-event-list-layout').length > 0 || $root.find('.wpem-event-box-layout').length > 0) {
					var layout = getStoredLayout(instanceId);
					if (layout === 'line-layout') {
						applyLayoutToInstance($root, 'line-layout');
					} else if (layout === 'calendar-layout') {
						applyLayoutToInstance($root, 'calendar-layout');
					} else {
						applyLayoutToInstance($root, 'box-layout');
					}
				}
			});

			setTimeout(function () {
				if (jQuery('input.date_range_picker').length > 0) {
					jQuery('input.date_range_picker').daterangepicker({
						datepickerOptions: {
							numberOfMonths: 2,
							minDate: null,
							maxDate: null,
							monthNames: event_manager_content_event_listing.i18n_monthNames,
							dayNames: event_manager_content_event_listing.i18n_dayNames,
							dayNamesMin: event_manager_content_event_listing.i18n_dayNamesMin
						},
						initialText: event_manager_content_event_listing.i18n_initialText,
						applyButtonText: event_manager_content_event_listing.i18n_applyButtonText,
						clearButtonText: event_manager_content_event_listing.i18n_clearButtonText,
						cancelButtonText: event_manager_content_event_listing.i18n_cancelButtonText,
						dateFormat: event_manager_content_event_listing.i18n_datepicker_format,
						altFormat: event_manager_content_event_listing.i18n_datepicker_format,
						clear: function () {
							jQuery('.comiseo-daterangepicker-triggerbutton').click();
						},
						rangeSplitter: ' : ',
						presetRanges: [
							{
								text: event_manager_content_event_listing.i18n_today,
								dateStart: function () { return moment(); },
								dateEnd: function () { return moment(); }
							},
							{
								text: event_manager_content_event_listing.i18n_tomorrow,
								dateStart: function () { return moment().add('days', 1); },
								dateEnd: function () { return moment().add('days', 1); }
							},
							{
								text: event_manager_content_event_listing.i18n_thisWeek,
								dateStart: function () { return moment().startOf('week'); },
								dateEnd: function () { return moment().endOf('week'); }
							},
							{
								text: event_manager_content_event_listing.i18n_nextWeek,
								dateStart: function () { return moment().add('weeks', 1).startOf('week'); },
								dateEnd: function () { return moment().add('weeks', 1).endOf('week'); }
							},
							{
								text: event_manager_content_event_listing.i18n_thisMonth,
								dateStart: function () { return moment().startOf('month'); },
								dateEnd: function () { return moment().endOf('month'); }
							},
							{
								text: event_manager_content_event_listing.i18n_nextMonth,
								dateStart: function () { return moment().add('months', 1).startOf('month'); },
								dateEnd: function () { return moment().add('months', 1).endOf('month'); }
							},
							{
								text: event_manager_content_event_listing.i18n_thisYear,
								dateStart: function () { return moment().startOf('year'); },
								dateEnd: function () { return moment().endOf('year'); }
							},
							{
								text: event_manager_content_event_listing.i18n_nextYear,
								dateStart: function () { return moment().add('years', 1).startOf('year'); },
								dateEnd: function () { return moment().add('years', 1).endOf('year'); }
							}
						]
					});
				}
			}, 500);
		},
		actions: {
			lineLayoutIconClick: function (event) {
				var $root = getInstanceRoot(this);
				var instanceId = $root.attr('data-wpem-listing-instance') || '';
				jQuery(this).addClass('wpem-active-layout');
				$root.find('.wpem-event-box-layout').removeClass('wpem-active-layout');
				$root.find('.wpem-event-box-col').show();
				var $listings = $root.find('.wpem-event-listings');
				$listings.removeClass('wpem-row wpem-event-listing-box-view');
				$listings.addClass('wpem-event-listing-list-view');
				setStoredLayout(instanceId, 'line-layout');
				event.preventDefault();
			},

			boxLayoutIconClick: function (event) {
				Common.logInfo('ContentEventListing.actions.boxLayoutIconClick...');
				var $root = getInstanceRoot(this);
				var instanceId = $root.attr('data-wpem-listing-instance') || '';
				jQuery(this).addClass('wpem-active-layout');
				if ($root.find('.wpem-event-list-layout').hasClass('wpem-active-layout')) {
					$root.find('.wpem-event-list-layout').removeClass('wpem-active-layout');
				}
				$root.find('.wpem-event-box-col').show();
				var $listings = $root.find('.wpem-event-listings');
				$listings.removeClass('wpem-event-listing-list-view');
				$listings.addClass('wpem-row wpem-event-listing-box-view');
				setStoredLayout(instanceId, 'box-layout');
				event.preventDefault();
			}
		}
	};
};

ContentEventListing = ContentEventListing();
jQuery(document).ready(function () {
	ContentEventListing.init();
});
