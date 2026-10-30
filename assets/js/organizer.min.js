var Organizers = function () {

    /// <summary>Constructor function of the Organizers class.</summary>
    /// <since>1.0.0</since>
    /// <returns type="Organizers" />  
    return {
        /// <summary>
        /// Initializes the Organizers.       
        /// </summary>                 
        /// <returns type="initialization settings" />     
        /// <since>1.0.0</since>  
                init: function () {           
            Common.logInfo("Organizers.init...");
            
            this.actions.applyPageFilter();
            jQuery('.organizer-letters a').on('click', this.actions.showOrganizerInfo);
			jQuery("#upcoming-past-tabs a").on('click',this.actions.tabClick); 		
            
            if(Common.getLayout()=="calendar-layout"){
                Common.setLayout("box-layout");
            }
			if(jQuery(".normal-section-title").length >0)
			   jQuery(".normal-section-title").html(event_manager_organizer.i18n_upcomingEventsTitle);
    	},

        actions: {
            /// <summary>
            /// This function is use to show organizer name by based on how alphabet letters are clicked 	  
            /// </summary>
            /// <param name="parent" type="Event"></param>           
            /// <returns type="actions" />     
            /// <since>1.0.0</since>       
                        getCurrentPage: function () {
                var match = window.location.search.match(/[?&]organizer_page=(\d+)/);
                var page = match ? parseInt(match[1], 10) : 1;
                return (page && page > 0) ? page : 1;
            },

            applyPageFilter: function ( forcePage ) {
                var currentPage = forcePage ? forcePage : Organizers.actions.getCurrentPage();
                var anyGroupVisible = false;
                jQuery('.show-organizer-info').each(function () {
                    var $group = jQuery(this);
                    var visibleCount = 0;
                    $group.find('.organizer-list-items').each(function () {
                        var itemPage = parseInt(jQuery(this).data('page'), 10) || 1;
                        var isMatch = (itemPage === currentPage);
                        jQuery(this).toggle(isMatch);
                        if (isMatch) { visibleCount++; }
                    });
                    $group.toggle(visibleCount > 0);
                    if (visibleCount > 0) { anyGroupVisible = true; }
                });
                jQuery('.no-organizer').toggleClass('wpem-d-none', anyGroupVisible);
            },

            showOrganizerInfo: function (event) {
                Common.logInfo("Organizers.actions.showOrganizerInfo...");

                var currentClickedLetterId = jQuery(this).attr('id');
                var showAllLetterId = 'ALL';
                jQuery('.show-organizer-info').hide();
                jQuery('.organizer-list-items').show();

                if (currentClickedLetterId == showAllLetterId) {
                    // Always send "All" back to page 1 so the organizer
                    // list and the pagination widget below stay in
                    // sync, instead of just visually filtering.
                    if (Organizers.actions.getCurrentPage() !== 1) {
                        var url = new URL(window.location.href);
                        url.searchParams.delete('organizer_page');
                        window.location.href = url.toString();
                        return;
                    }
                    Organizers.actions.applyPageFilter( 1 );
                } else if(jQuery('#show_' + currentClickedLetterId).length) {
                    jQuery('#show_' + currentClickedLetterId).css({ "display": "block" });
                    jQuery('.no-organizer').addClass('wpem-d-none');
                }else{
                    jQuery('.no-organizer').removeClass('wpem-d-none');
                }
                event.preventDefault();
            },
			
			/// <summary>
            /// This function is use to show tabes of past and upcoming event onsingle organizer.	  
            /// </summary>
            /// <param name="parent" type="Event"></param>           
            /// <returns type="actions" />     
            /// <since>1.0.0</since>       
            tabClick: function (event) {
                Common.logInfo("Organizers.actions.showtab...");   
                
        		if(jQuery(event.target).attr('href')=='#past') {   
                    if(jQuery(".normal-section-title").length >0)
                      jQuery(".normal-section-title").html(event_manager_organizer.i18n_pastEventsTitle);  
                      
                    if(Common.getLayout()=="box-layout") {                       
        	            jQuery("#past #line-layout-icon").addClass("lightgray-layout-icon");
        		        jQuery("#past #box-layout-icon").removeClass("lightgray-layout-icon");
		            } else  {
		                jQuery("#past #line-layout-icon").removeClass("lightgray-layout-icon");
        		        jQuery("#past #box-layout-icon").addClass("lightgray-layout-icon");
		            }
                }else if(jQuery(event.target).attr('href')=='#current') {   
                    if(jQuery(".normal-section-title").length >0)
                        jQuery(".normal-section-title").html(event_manager_organizer.i18n_currentEventsTitle);  
                     
                    if(Common.getLayout()=="box-layout") {                       
        	            jQuery("#current #line-layout-icon").addClass("lightgray-layout-icon");
        		        jQuery("#current #box-layout-icon").removeClass("lightgray-layout-icon");
		            } else {
		                jQuery("#current #line-layout-icon").removeClass("lightgray-layout-icon");
        		        jQuery("#current #box-layout-icon").addClass("lightgray-layout-icon");
		            }
                } else {
                    if(jQuery(".normal-section-title").length >0)
                       jQuery(".normal-section-title").html(event_manager_organizer.i18n_upcomingEventsTitle);
                }
                event.preventDefault();
            }                        
        }
    }
};
Organizers = Organizers();
jQuery(document).ready(function ($) {
    Organizers.init();
});