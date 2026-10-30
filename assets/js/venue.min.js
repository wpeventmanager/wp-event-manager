var Venues = function () {

    /// <summary>Constructor function of the Venues class.</summary>
    /// <since>1.0.0</since>
    /// <returns type="Venues" />  
    return {
        /// <summary>
        /// Initializes the Venues.       
        /// </summary>                 
        /// <returns type="initialization settings" />     
        /// <since>1.0.0</since>  
               init: function () {           
            Common.logInfo("Venues.init...");
            
            this.actions.applyPageFilter();
            jQuery('.venue-letters a').on('click', this.actions.showVenueInfo);
			jQuery("#upcoming-past-tabs a").on('click',this.actions.tabClick); 		
            
            if(Common.getLayout()=="calendar-layout"){
                Common.setLayout("box-layout");
            }
			if(jQuery(".normal-section-title").length >0)
			   jQuery(".normal-section-title").html(event_manager_venue.i18n_upcomingEventsTitle);
    	},

        actions: {
            /// <summary>
            /// This function is use to show venue name by based on how alphabet letters are clicked 	  
            /// </summary>
            /// <param name="parent" type="Event"></param>           
            /// <returns type="actions" />     
            /// <since>1.0.0</since>       
                        getCurrentPage: function () {
                var match = window.location.search.match(/[?&]venue_page=(\d+)/);
                var page = match ? parseInt(match[1], 10) : 1;
                return (page && page > 0) ? page : 1;
            },

                       applyPageFilter: function ( forcePage ) {
                var currentPage = forcePage ? forcePage : Venues.actions.getCurrentPage();
                var anyGroupVisible = false;
                jQuery('.show-venue-info').each(function () {
                    var $group = jQuery(this);
                    var visibleCount = 0;
                    $group.find('.venue-list-items').each(function () {
                        var itemPage = parseInt(jQuery(this).data('page'), 10) || 1;
                        var isMatch = (itemPage === currentPage);
                        jQuery(this).toggle(isMatch);
                        if (isMatch) { visibleCount++; }
                    });
                    $group.toggle(visibleCount > 0);
                    if (visibleCount > 0) { anyGroupVisible = true; }
                });
                jQuery('.no-venue').toggleClass('wpem-d-none', anyGroupVisible);
            },
            showVenueInfo: function (event) {
                Common.logInfo("Venues.actions.showVenueInfo...");

                var currentClickedLetterId = jQuery(this).attr('id');
                var showAllLetterId = 'ALL';
                jQuery('.show-venue-info').hide();
                jQuery('.venue-list-items').show();

                if (currentClickedLetterId == showAllLetterId) {
                    // Always send "All" back to page 1 so the venue
                    // list and the pagination widget below stay in
                    // sync, instead of just visually filtering.
                    if (Venues.actions.getCurrentPage() !== 1) {
                        var url = new URL(window.location.href);
                        url.searchParams.delete('venue_page');
                        window.location.href = url.toString();
                        return;
                    }
                    Venues.actions.applyPageFilter( 1 );
                } else if(jQuery('#show_' + currentClickedLetterId).length) {
                    jQuery('#show_' + currentClickedLetterId).css({ "display": "block" });
                    jQuery('.no-venue').addClass('wpem-d-none');
                }else{
                    jQuery('.no-venue').removeClass('wpem-d-none');
                }
                event.preventDefault();
            },
			
			/// <summary>
            /// This function is use to show tabes of past and upcoming event onsingle venue.	  
            /// </summary>
            /// <param name="parent" type="Event"></param>           
            /// <returns type="actions" />     
            /// <since>1.0.0</since>       
            tabClick: function (event) {
                Common.logInfo("Venues.actions.showtab...");   
                
        		if(jQuery(event.target).attr('href')=='#past') {   
                    if(jQuery(".normal-section-title").length >0)
                      jQuery(".normal-section-title").html(event_manager_venue.i18n_pastEventsTitle);  
                      
                    if(Common.getLayout()=="box-layout") {                       
        	            jQuery("#past #line-layout-icon").addClass("lightgray-layout-icon");
        		        jQuery("#past #box-layout-icon").removeClass("lightgray-layout-icon");
		            } else {
		                jQuery("#past #line-layout-icon").removeClass("lightgray-layout-icon");
        		        jQuery("#past #box-layout-icon").addClass("lightgray-layout-icon");
		            }
                }else if(jQuery(event.target).attr('href')=='#current') {   
                    if(jQuery(".normal-section-title").length >0)
                        jQuery(".normal-section-title").html(event_manager_venue.i18n_currentEventsTitle);  
                     
                    if(Common.getLayout()=="box-layout") {                       
        	            jQuery("#current #line-layout-icon").addClass("lightgray-layout-icon");
        		        jQuery("#current #box-layout-icon").removeClass("lightgray-layout-icon");
		            } else {
		                jQuery("#current #line-layout-icon").removeClass("lightgray-layout-icon");
        		        jQuery("#current #box-layout-icon").addClass("lightgray-layout-icon");
		            }
                } else {
                    if(jQuery(".normal-section-title").length >0)
                       jQuery(".normal-section-title").html(event_manager_venue.i18n_upcomingEventsTitle);
                }
                event.preventDefault();
            }                        
        }
    }
};
Venues = Venues();
jQuery(document).ready(function ($) {
    Venues.init();
});