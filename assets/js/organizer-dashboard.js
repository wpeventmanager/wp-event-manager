var OrganizerDashboard= function () {
    /// <summary>Constructor function of the event OrganizerDashboard class.</summary>
    /// <returns type="Home" />      
    return {
	    ///<summary>
        ///Initializes the event dashboard.  
        ///</summary>     
        ///<returns type="initialization settings" />   
        /// <since>1.0.0</since> 
        init: function() 
        {
          
	  	    Common.logInfo("OrganizerDashboard.init...");  
	  	    if(jQuery('.event-dashboard-action-delete').length >0)
		    {

				jQuery('.event-dashboard-action-delete').css({'cursor':'pointer'});  					
				//for delete event confirmation dialog / tooltip 
				jQuery('.event-dashboard-action-delete').on('click', OrganizerDashboard.confirmation.showDialog);	

	        }

	        if(jQuery('.event-organizer-count').length >0)
		    {				
				//show event list dialog / tooltip 
				jQuery('.event-organizer-count').on('click', function(){
					jQuery(this).next('.organizer-events-list').slideToggle();
				});	

	        }


 	 }, 

	confirmation:{	    
             /// <summary>
	        /// Show bootstrap third party confirmation dialog when click on 'Delete' options on event dashboard page where show delete event option.	     
	        /// </summary>
	        /// <param name="parent" type="assign"></param>           
	        /// <returns type="actions" />     
	        /// <since>1.0.0</since>       
	        showDialog: function(event) 
	        {
	        	Common.logInfo("OrganizerDashboard.confirmation.showDialog...");	            
	           	return confirm(event_manager_organizer_dashboard.i18n_confirm_delete);
	           	event.preventDefault(); 
	        },
	
	    }		 //end of comfirmation	 
    } //enf of return	
}; //end of class

OrganizerDashboard= OrganizerDashboard();
jQuery(document).ready(function($) 
{
   OrganizerDashboard.init();
});