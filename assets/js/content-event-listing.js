/* global Common, moment */
var ContentEventListing = function () {
	var STORAGE_PREFIX = "wpem_layout_inst_";

	function getStoredLayout(instanceId) {
		if (!instanceId) {
			return localStorage.getItem("layout");
		}
		var scoped = localStorage.getItem(STORAGE_PREFIX + instanceId);
		if (scoped !== null && scoped !== "") {
			return scoped;
		}
		return localStorage.getItem("layout");
	}

	function setStoredLayout(instanceId, layout) {
		if (instanceId) {
			localStorage.setItem(STORAGE_PREFIX + instanceId, layout);
		}
	}

	function getInstanceRoot(el) {
		return jQuery(el).closest(".wpem-event-listing-instance");
	}

	function applyLineLayout($root) {
		var $listings = $root.find(".wpem-event-listings");
		var $boxCols = $root.find(".wpem-event-box-col");
		$boxCols.show();
		$root.find(".wpem-event-box-layout").removeClass("wpem-active-layout");
		$root.find(".wpem-event-list-layout").addClass("wpem-active-layout");
		if ($listings.hasClass("wpem-row")) {
			$listings.removeClass("wpem-row");
		}
		$listings.removeClass("wpem-event-listing-box-view");
		$listings.addClass("wpem-event-listing-list-view");
	}

	function applyCalendarLayout($root) {
		var $listings = $root.find(".wpem-event-listings");
		var $boxCols = $root.find(".wpem-event-box-col");
		$boxCols.hide();
		$root.find(".wpem-event-list-layout").removeClass("wpem-active-layout");
		$root.find(".wpem-event-box-layout").removeClass("wpem-active-layout");
		$root.find(".wpem-event-calendar-layout").addClass("wpem-active-layout");
		if (!$listings.hasClass("wpem-row")) {
			$listings.addClass("wpem-row");
		}
		$listings.removeClass("wpem-event-listing-list-view");
		$listings.addClass("wpem-event-listing-box-view");
		$listings.addClass("wpem-event-listing-calendar-view");
	}

	function applyBoxLayout($root) {
		var $listings = $root.find(".wpem-event-listings");
		var $boxCols = $root.find(".wpem-event-box-col");
		$boxCols.show();
		$root.find(".wpem-event-list-layout").removeClass("wpem-active-layout");
		$root.find(".wpem-event-box-layout").addClass("wpem-active-layout");
		if (!$listings.hasClass("wpem-row")) {
			$listings.addClass("wpem-row");
		}
		$listings.removeClass("wpem-event-listing-list-view");
		$listings.addClass("wpem-event-listing-box-view");
	}

	function applyLayoutToInstance($root, layoutKey) {
		if (!$root.length) {
			return;
		}
		if (layoutKey === "line-layout") {
			applyLineLayout($root);
			return;
		}
		if (layoutKey === "calendar-layout") {
			applyCalendarLayout($root);
			return;
		}
		applyBoxLayout($root);
	}

	function resolveLayoutKey(layout) {
		if (layout === "line-layout") {
			return "line-layout";
		}
		if (layout === "calendar-layout") {
			return "calendar-layout";
		}
		return "box-layout";
	}

	function applyLayoutToSingleInstance() {
		var $root = jQuery(this);
		if ($root.find(".wpem-event-list-layout").length === 0 && $root.find(".wpem-event-box-layout").length === 0) {
			return;
		}
		var instanceId = $root.attr("data-wpem-listing-instance") || "";
		var layoutKey = resolveLayoutKey(getStoredLayout(instanceId));
		applyLayoutToInstance($root, layoutKey);
	}

	function buildDateRangePickerOptions() {
		var labels = window.event_manager_content_event_listing;
		return {
			datepickerOptions: {
				numberOfMonths: 2,
				minDate: null,
				maxDate: null,
				monthNames: labels.i18n_monthNames,
				dayNames: labels.i18n_dayNames,
				dayNamesMin: labels.i18n_dayNamesMin
			},
			initialText: labels.i18n_initialText,
			applyButtonText: labels.i18n_applyButtonText,
			clearButtonText: labels.i18n_clearButtonText,
			cancelButtonText: labels.i18n_cancelButtonText,
			dateFormat: labels.i18n_datepicker_format,
			altFormat: labels.i18n_datepicker_format,
			clear() {
				jQuery(".comiseo-daterangepicker-triggerbutton").click();
			},
			rangeSplitter: " : ",
			presetRanges: [
				{
					text: labels.i18n_today,
					dateStart() { return moment(); },
					dateEnd() { return moment(); }
				},
				{
					text: labels.i18n_tomorrow,
					dateStart() { return moment().add("days", 1); },
					dateEnd() { return moment().add("days", 1); }
				},
				{
					text: labels.i18n_thisWeek,
					dateStart() { return moment().startOf("week"); },
					dateEnd() { return moment().endOf("week"); }
				},
				{
					text: labels.i18n_nextWeek,
					dateStart() { return moment().add("weeks", 1).startOf("week"); },
					dateEnd() { return moment().add("weeks", 1).endOf("week"); }
				},
				{
					text: labels.i18n_thisMonth,
					dateStart() { return moment().startOf("month"); },
					dateEnd() { return moment().endOf("month"); }
				},
				{
					text: labels.i18n_nextMonth,
					dateStart() { return moment().add("months", 1).startOf("month"); },
					dateEnd() { return moment().add("months", 1).endOf("month"); }
				},
				{
					text: labels.i18n_thisYear,
					dateStart() { return moment().startOf("year"); },
					dateEnd() { return moment().endOf("year"); }
				},
				{
					text: labels.i18n_nextYear,
					dateStart() { return moment().add("years", 1).startOf("year"); },
					dateEnd() { return moment().add("years", 1).endOf("year"); }
				}
			]
		};
	}

	function initDateRangePicker() {
		if (jQuery("input.date_range_picker").length === 0) {
			return;
		}
		jQuery("input.date_range_picker").daterangepicker(buildDateRangePickerOptions());
	}

	return {
		init() {
			Common.logInfo("ContentEventListing.init...");

			jQuery(document).on("click", ".wpem-event-list-layout", ContentEventListing.actions.lineLayoutIconClick);
			jQuery(document).on("click", ".wpem-event-box-layout", ContentEventListing.actions.boxLayoutIconClick);

			jQuery(".wpem-event-listing-instance").each(applyLayoutToSingleInstance);

			setTimeout(initDateRangePicker, 500);
		},
		actions: {
			lineLayoutIconClick(evt) {
				var $root = getInstanceRoot(this);
				var instanceId = $root.attr("data-wpem-listing-instance") || "";
				jQuery(this).addClass("wpem-active-layout");
				$root.find(".wpem-event-box-layout").removeClass("wpem-active-layout");
				$root.find(".wpem-event-box-col").show();
				var $listings = $root.find(".wpem-event-listings");
				$listings.removeClass("wpem-row wpem-event-listing-box-view");
				$listings.addClass("wpem-event-listing-list-view");
				setStoredLayout(instanceId, "line-layout");
				evt.preventDefault();
			},

			boxLayoutIconClick(evt) {
				Common.logInfo("ContentEventListing.actions.boxLayoutIconClick...");
				var $root = getInstanceRoot(this);
				var instanceId = $root.attr("data-wpem-listing-instance") || "";
				jQuery(this).addClass("wpem-active-layout");
				if ($root.find(".wpem-event-list-layout").hasClass("wpem-active-layout")) {
					$root.find(".wpem-event-list-layout").removeClass("wpem-active-layout");
				}
				$root.find(".wpem-event-box-col").show();
				var $listings = $root.find(".wpem-event-listings");
				$listings.removeClass("wpem-event-listing-list-view");
				$listings.addClass("wpem-row wpem-event-listing-box-view");
				setStoredLayout(instanceId, "box-layout");
				evt.preventDefault();
			}
		}
	};
};

ContentEventListing = ContentEventListing();
jQuery(document).ready(function () {
	ContentEventListing.init();
});
