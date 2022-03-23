var ContentEventListing= function () {
    /// <summary>Constructor function of the event ContentEventListing class.</summary>
    /// <returns type="ContentEventListing" />      
    return {
	    ///<summary>
        ///Initializes the content event listing.  
        ///</summary>     
        ///<returns type="initialization settings" />   
        /// <since>1.0.0</since> 
        init: function() 
        {   
	           Common.logInfo("ContentEventListing.init..."); 
	           
			   jQuery(document).delegate('#wpem-event-list-layout','click', ContentEventListing.actions.lineLayoutIconClick);
			   jQuery(document).delegate('#wpem-event-box-layout','click', ContentEventListing.actions.boxLayoutIconClick);
			   
			   //check if default layout is set or icon are on the page to load according to localstorage
			   if(jQuery('.wpem-event-list-layout').length > 0 || jQuery('.wpem-event-box-layout').length > 0) {
				   //With show_pagination attribute in shortcodes. e.g [events per_page="10" show_pagination="true"]
				   //Check when user has changed page using pagination and then need to keep current selected layout
		           //When layout is box and user changed page using pagination then need to show line layout instead of line layout  
		           if(localStorage.getItem("layout")=="line-layout" ){    
		                    jQuery(".wpem-event-box-col").show();
		                    jQuery('.wpem-event-box-layout').removeClass('wpem-active-layout');
		                    jQuery('.wpem-event-list-layout').addClass('wpem-active-layout');
		                    
		                    if(jQuery(".wpem-event-listings").hasClass('wpem-row'))
		                       jQuery(".wpem-event-listings").removeClass('wpem-row');
	
		                   jQuery(".wpem-event-listings").removeClass("wpem-event-listing-box-view");
		                   jQuery(".wpem-event-listings").addClass("wpem-event-listing-list-view");
		   
		              } 
		              else if(localStorage.getItem("layout")=="calendar-layout" ){      
		                jQuery(".wpem-event-box-col").hide();
		                jQuery('.wpem-event-list-layout').removeClass('wpem-active-layout');
		                jQuery('.wpem-event-box-layout').removeClass('wpem-active-layout');
		                jQuery('.wpem-event-calendar-layout').addClass('wpem-active-layout');
	
		                if(!jQuery(".wpem-event-listings").hasClass('wpem-row'))
		                   jQuery(".wpem-event-listings").addClass('wpem-row');
	
		               jQuery(".wpem-event-listings").removeClass("wpem-event-listing-list-view");
		               jQuery(".wpem-event-listings").removeClass("wpem-event-listing-box-view");      
		               jQuery(".wpem-event-listings").addClass("wpem-event-listing-calendar-view");      	                 
		              }  
		              else {   
		                jQuery(".wpem-event-box-col").show();
		                jQuery('.wpem-event-list-layout').removeClass('wpem-active-layout');
		                jQuery('.wpem-event-box-layout').addClass('wpem-active-layout');
	
		                if(!jQuery(".wpem-event-listings").hasClass('wpem-row'))
		                   jQuery(".wpem-event-listings").addClass('wpem-row');
	
		               jQuery(".wpem-event-listings").removeClass("wpem-event-listing-list-view");
		               jQuery(".wpem-event-listings").addClass("wpem-event-listing-box-view"); 
		              }
			   	}

			   	setTimeout(function(){ 
	                if(jQuery( 'input.date_range_picker' ).length > 0)
			     	{
			     		jQuery("input.date_range_picker").daterangepicker({
		                    datepickerOptions : {
		                        numberOfMonths : 2,
		                        minDate: null,
						        maxDate: null
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
        actions: 
        {
		        /// <summary>
	            /// Click on line layout.
	            /// </summary>     
	            /// <returns type="events listing view" />    
	            /// <since>1.0.0</since>     
	            lineLayoutIconClick: function (event)
	            {   
	                      Common.logInfo("ContentEventListing.actions.lineLayoutIconClick...");   

                jQuery(this).addClass("wpem-active-layout");
                jQuery("#wpem-event-box-layout").removeClass("wpem-active-layout");
                
                
                jQuery(".wpem-event-box-col").show();
                

                jQuery(".wpem-event-listings").removeClass("wpem-row wpem-event-listing-box-view");
            
                jQuery(".wpem-event-listings").addClass("wpem-event-listing-list-view");
                    	            
    		      localStorage.setItem("layout", "line-layout");
    		      event.preventDefault();
	            },
	            
	            /// <summary>
	            /// Click on box layout.
	            /// </summary>     
	            /// <returns type="events listing view" />    
	            /// <since>1.0.0</since>     
	            boxLayoutIconClick: function (event)
	            {                 	       
	                Common.logInfo("ContentEventListing.actions.boxLayoutIconClick...");    
                    jQuery(this).addClass("wpem-active-layout");

                    if(jQuery("#wpem-event-list-layout").hasClass("wpem-active-layout"))
                        jQuery("#wpem-event-list-layout").removeClass("wpem-active-layout");
                        
                    jQuery(".wpem-event-box-col").show();
                    //jQuery("#calendar-layout-view-container").hide();

                    jQuery(".wpem-event-listings").removeClass("wpem-event-listing-list-view");
                   // jQuery(".wpem-event-listings").addClass("wpem-row wpem-event-listing-box-view");
                    
                     jQuery(".wpem-event-listings").addClass('wpem-row wpem-event-listing-box-view');
                    // jQuery(".wpem-event-listings").addClass('wpem-event-listing-box-view');
                    
    		       localStorage.setItem("layout", "box-layout"); 
    		       event.preventDefault();
	            }		   
        }

    } //enf of return

}; //end of class

ContentEventListing= ContentEventListing();
jQuery(document).ready(function($) 
{
   ContentEventListing.init();
});
