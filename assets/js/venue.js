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
            
            jQuery('#show_ALL').show();
            jQuery('.venue-letters a').on('click', this.actions.showVenueInfo);
			jQuery("#upcoming-past-tabs a").on('click',this.actions.tabClick); 		
            
            if(localStorage.getItem("layout")=="calendar-layout"){
                localStorage.setItem("layout", "box-layout");
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
            showVenueInfo: function (event) {
                Common.logInfo("Venues.actions.showVenueInfo...");

                var currentClickedLetterId = jQuery(this).attr('id');
                var showAllLetterId = 'ALL';
                //first, hide all venue info 	
                jQuery('.show-venue-info').hide();

                //checks condition if selected id is \show_All\ then it will show all venue name,else it will show only slected alphabet letter venue name.
                if (currentClickedLetterId == showAllLetterId) {
                    //show all venue block which has clas show-venue-info
                    jQuery('.show-venue-info').show();
                    jQuery('.no-venue').addClass('wpem-d-none');
                } else if(jQuery('#show_' + currentClickedLetterId).length) {	//show clicked letter venue only       
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
                      
                    if(localStorage.getItem("layout")=="box-layout") {                       
        	            jQuery("#past #line-layout-icon").addClass("lightgray-layout-icon");
        		        jQuery("#past #box-layout-icon").removeClass("lightgray-layout-icon");
		            } else {
		                jQuery("#past #line-layout-icon").removeClass("lightgray-layout-icon");
        		        jQuery("#past #box-layout-icon").addClass("lightgray-layout-icon");
		            }
                }else if(jQuery(event.target).attr('href')=='#current') {   
                    if(jQuery(".normal-section-title").length >0)
                        jQuery(".normal-section-title").html(event_manager_venue.i18n_currentEventsTitle);  
                     
                    if(localStorage.getItem("layout")=="box-layout") {                       
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