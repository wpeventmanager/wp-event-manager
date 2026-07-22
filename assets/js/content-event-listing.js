var ContentEventListing= function () {
    /// <summary>Constructor function of the event ContentEventListing class.</summary>
    /// <returns type="ContentEventListing" />      
    return {
	    ///<summary>
        ///Initializes the content event listing.  
        ///</summary>     
        ///<returns type="initialization settings" />   
        /// <since>1.0.0</since> 
        init: function() {   
	           Common.logInfo("ContentEventListing.init..."); 
	           
			   jQuery(document).delegate('.wpem-event-list-layout','click', ContentEventListing.actions.lineLayoutIconClick);
			   jQuery(document).delegate('.wpem-event-box-layout','click', ContentEventListing.actions.boxLayoutIconClick);
			   
			   //check if default layout is set or icon are on the page to load according to localstorage
			   //A page can have more than one event listing block (the same shortcode used twice, or
			   //different shortcodes like [events]/[upcoming_events]/[past_events] together), so the
			   //saved layout is looked up and applied separately for each block, not for the page as a whole.
			   jQuery('.wpem-event-listings-instance').each(function() {
				   ContentEventListing.actions.applySavedLayout(jQuery(this));
			   });
			   //Fallback for markup that doesn't have the .wpem-event-listings-instance wrapper
			   //(e.g. a custom/older template override) so layout restoring still works page-wide.
			   if (jQuery('.wpem-event-listings-instance').length === 0 && (jQuery('.wpem-event-list-layout').length > 0 || jQuery('.wpem-event-box-layout').length > 0)) {
				   ContentEventListing.actions.applySavedLayout(jQuery(document));
			   }
			   setTimeout(function(){ 
	                if(jQuery( 'input.date_range_picker' ).length > 0) {
			     		jQuery("input.date_range_picker").daterangepicker({
		                    datepickerOptions : {
		                        numberOfMonths : 2,
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
									clear: function (e, t) {
										jQuery('.comiseo-daterangepicker-triggerbutton').click();
									},
		                    rangeSplitter: ' : ',
		                    presetRanges: [
		                      {
		                        text: event_manager_content_event_listing.i18n_today,
		                        dateStart: function() { return moment() },
		                        dateEnd: function() { return moment() }
		                      }, 
		                      {
		                        text: event_manager_content_event_listing.i18n_tomorrow,
		                        dateStart: function() { return moment().add('days', 1) },
		                        dateEnd: function() { return moment().add('days', 1) }
		                      },
		                      {
		                        text: event_manager_content_event_listing.i18n_thisWeek,
		                        dateStart: function() { return moment().startOf('week') },
		                        dateEnd: function() { return moment().endOf('week') }
		                      }, 
		                      {
		                        text: event_manager_content_event_listing.i18n_nextWeek,
		                        dateStart: function() { return moment().add('weeks', 1).startOf('week') },
		                        dateEnd: function() { return moment().add('weeks', 1).endOf('week') }
		                      },
		                      {
		                        text: event_manager_content_event_listing.i18n_thisMonth,
		                        dateStart: function() { return moment().startOf('month') },
		                        dateEnd: function() { return moment().endOf('month') }
		                      },
		                      {
		                        text: event_manager_content_event_listing.i18n_nextMonth,
		                        dateStart: function() { return moment().add('months', 1).startOf('month') },
		                        dateEnd: function() { return moment().add('months', 1).endOf('month') }
		                      },
		                      {
		                        text: event_manager_content_event_listing.i18n_thisYear,
		                        dateStart: function() { return moment().startOf('year') },
		                        dateEnd: function() { return moment().endOf('year') }
		                      },
		                      {
		                        text: event_manager_content_event_listing.i18n_nextYear,
		                        dateStart: function() { return moment().add('years', 1).startOf('year') },
		                        dateEnd: function() { return moment().add('years', 1).endOf('year') }
		                      },
		                    ],
		                });
			     	}
            	}, 500);
        },
        actions: {
			/// <summary>
			/// Finds the single event listing block (.wpem-event-listings-instance) that an
			/// icon/element belongs to, so layout changes only affect that block and not every
			/// event listing block on the page. Falls back to the whole document for markup
			/// that doesn't have the wrapper (e.g. an old cached/custom template).
			/// </summary>     
			/// <param name="element" type="jQuery" />    
			/// <returns type="jQuery" />    
			/// <since>1.0.0</since>     
			getScope: function ($element) {
				var $scope = $element.closest('.wpem-event-listings-instance');
				return $scope.length ? $scope : jQuery(document);
			},

			/// <summary>
			/// Reads the unique id of an event listing block, used to key its saved layout
			/// separately from any other event listing block on the same page.
			/// </summary>     
			/// <param name="scope" type="jQuery" />    
			/// <returns type="string" />    
			/// <since>1.0.0</since>     
			getInstanceId: function ($scope) {
				return $scope.data('wpem-layout-instance');
			},

			/// <summary>
			/// Applies the layout (box/list/calendar) previously saved for one specific
			/// event listing block, scoping every change to that block only.
			/// </summary>     
			/// <param name="scope" type="jQuery" />    
			/// <since>1.0.0</since>     
			applySavedLayout: function ($scope) {
				if ($scope.find('.wpem-event-list-layout').length === 0 && $scope.find('.wpem-event-box-layout').length === 0) {
					return;
				}

				//With show_pagination attribute in shortcodes. e.g [events per_page="10" show_pagination="true"]
				//Check when user has changed page using pagination and then need to keep current selected layout
				//When layout is box and user changed page using pagination then need to show line layout instead of line layout  
				var instanceId = ContentEventListing.actions.getInstanceId($scope);
				var savedLayout = Common.getLayout(instanceId);

				if (savedLayout == "line-layout") {
					$scope.find(".wpem-event-box-col").show();
					$scope.find('.wpem-event-box-layout').removeClass('wpem-active-layout');
					$scope.find('.wpem-event-list-layout').addClass('wpem-active-layout');

					if ($scope.find(".wpem-event-listings").hasClass('wpem-row'))
						$scope.find(".wpem-event-listings").removeClass('wpem-row');

					$scope.find(".wpem-event-listings").removeClass("wpem-event-listing-box-view");
					$scope.find(".wpem-event-listings").addClass("wpem-event-listing-list-view");

				} else if (savedLayout == "calendar-layout") {
					$scope.find(".wpem-event-box-col").hide();
					$scope.find('.wpem-event-list-layout').removeClass('wpem-active-layout');
					$scope.find('.wpem-event-box-layout').removeClass('wpem-active-layout');
					$scope.find('.wpem-event-calendar-layout').addClass('wpem-active-layout');

					if (!$scope.find(".wpem-event-listings").hasClass('wpem-row'))
						$scope.find(".wpem-event-listings").addClass('wpem-row');

					$scope.find(".wpem-event-listings").removeClass("wpem-event-listing-list-view");
					$scope.find(".wpem-event-listings").addClass("wpem-event-listing-box-view");
					$scope.find(".wpem-event-listings").addClass("wpem-event-listing-calendar-view");
				} else {
					$scope.find(".wpem-event-box-col").show();
					$scope.find('.wpem-event-list-layout').removeClass('wpem-active-layout');
					$scope.find('.wpem-event-box-layout').addClass('wpem-active-layout');

					if (!$scope.find(".wpem-event-listings").hasClass('wpem-row'))
						$scope.find(".wpem-event-listings").addClass('wpem-row');

					$scope.find(".wpem-event-listings").removeClass("wpem-event-listing-list-view");
					$scope.find(".wpem-event-listings").addClass("wpem-event-listing-box-view");
				}
			},

			/// <summary>
			/// Click on line layout.
			/// </summary>     
			/// <returns type="events listing view" />    
			/// <since>1.0.0</since>     
			lineLayoutIconClick: function (event) {   
				var $scope = ContentEventListing.actions.getScope(jQuery(this));
				var instanceId = ContentEventListing.actions.getInstanceId($scope);

				jQuery(this).addClass("wpem-active-layout");
				$scope.find(".wpem-event-box-layout").removeClass("wpem-active-layout");
				$scope.find(".wpem-event-box-col").show();
				$scope.find(".wpem-event-listings").removeClass("wpem-row wpem-event-listing-box-view");
				$scope.find(".wpem-event-listings").addClass("wpem-event-listing-list-view");
								
				Common.setLayout("line-layout", instanceId);
				event.preventDefault();
			},
	            
			/// <summary>
			/// Click on box layout.
			/// </summary>     
			/// <returns type="events listing view" />    
			/// <since>1.0.0</since>     
			boxLayoutIconClick: function (event) {                 	       
				Common.logInfo("ContentEventListing.actions.boxLayoutIconClick...");    
				var $scope = ContentEventListing.actions.getScope(jQuery(this));
				var instanceId = ContentEventListing.actions.getInstanceId($scope);

				jQuery(this).addClass("wpem-active-layout");
				$scope.find(".wpem-event-list-layout").removeClass("wpem-active-layout");

				$scope.find(".wpem-event-box-col").show();
				$scope.find(".wpem-event-listings").removeClass("wpem-event-listing-list-view");
				$scope.find(".wpem-event-listings").addClass('wpem-row wpem-event-listing-box-view');
				
				Common.setLayout("box-layout", instanceId); 
				event.preventDefault();
			}		   
        }
    } //enf of return
}; //end of class

ContentEventListing= ContentEventListing();
jQuery(document).ready(function($) {
   ContentEventListing.init();
});