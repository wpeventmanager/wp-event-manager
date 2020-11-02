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
            
            jQuery('#show_ALL').show();
            jQuery('.organizer-letters a').on('click', this.actions.showOrganizerInfo);
			jQuery("#upcoming-past-tabs a").on('click',this.actions.tabClick); 		
            
            if(localStorage.getItem("layout")=="calendar-layout"){
                localStorage.setItem("layout", "box-layout");
            }
			if(jQuery(".normal-section-title").length >0)
			   jQuery(".normal-section-title").html(event_manager_organizer.i18n_upcomingEventsTitle);
    	},

        actions:
        {
            /// <summary>
            /// This function is use to show organizer name by based on how alphabet letters are clicked 	  
            /// </summary>
            /// <param name="parent" type="Event"></param>           
            /// <returns type="actions" />     
            /// <since>1.0.0</since>       
            showOrganizerInfo: function (event)
            {
                Common.logInfo("Organizers.actions.showOrganizerInfo...");

                var currentClickedLetterId = jQuery(this).attr('id');
                var showAllLetterId = 'ALL';
                //first, hide all organizer info 	
                jQuery('.show-organizer-info').hide();

                //checks condition if selected id is \show_All\ then it will show all organizer name,else it will show only slected alphabet letter organizer name.
                if (currentClickedLetterId == showAllLetterId) {
                    //show all organizer block which has clas show-organizer-info
                    jQuery('.show-organizer-info').show();
                    jQuery('.no-organizer').addClass('wpem-d-none');
                }
                else if(jQuery('#show_' + currentClickedLetterId).length ) {	//show clicked letter organizer only       
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
            tabClick: function (event)
            {
                Common.logInfo("Organizers.actions.showtab...");   
                
        		if(jQuery(event.target).attr('href')=='#past')
                {   
                    if(jQuery(".normal-section-title").length >0)
                      jQuery(".normal-section-title").html(event_manager_organizer.i18n_pastEventsTitle);  
                      
                    if(localStorage.getItem("layout")=="box-layout")
		            {                       
        	            jQuery( "#past #line-layout-icon" ).addClass( "lightgray-layout-icon" );
        		        jQuery( "#past #box-layout-icon" ).removeClass( "lightgray-layout-icon" );
		            } 
		            else
		            {
		                jQuery( "#past #line-layout-icon" ).removeClass( "lightgray-layout-icon" );
        		        jQuery( "#past #box-layout-icon" ).addClass( "lightgray-layout-icon" );
		            }
                }else if(jQuery(event.target).attr('href')=='#current')
                {   
                    if(jQuery(".normal-section-title").length >0)
                      jQuery(".normal-section-title").html(event_manager_organizer.i18n_currentEventsTitle);  
                      
                     
                    if(localStorage.getItem("layout")=="box-layout")
		            {                       
        	            jQuery( "#current #line-layout-icon" ).addClass( "lightgray-layout-icon" );
        		        jQuery( "#current #box-layout-icon" ).removeClass( "lightgray-layout-icon" );
		            } 
		            else
		            {
		                jQuery( "#current #line-layout-icon" ).removeClass( "lightgray-layout-icon" );
        		        jQuery( "#current #box-layout-icon" ).addClass( "lightgray-layout-icon" );
		            }
                }
                else
                {
                    if(jQuery(".normal-section-title").length >0)
                       jQuery(".normal-section-title").html(event_manager_organizer.i18n_upcomingEventsTitle);
                }
               	//jQuery(this).tab('show');
                event.preventDefault();
            }                        
        }
    }
};
Organizers = Organizers();
jQuery(document).ready(function ($) {
    Organizers.init();
});