var EventRegistration= function () {
    /// <summary>Constructor function of the event EventRegistration class.</summary>
    /// <returns type="EventRegistration" />  
    return {
	    ///<summary>
        ///Initializes the EventRegistration.  
        ///</summary>     
        ///<returns type="initialization EventRegistration" />   
        /// <since>1.0.0</since> 
        init: function() 
        {						
		    // Slide toggle
	        jQuery( '.registration_details' ).hide();

			jQuery(".registration_button").on('click',EventRegistration.actions.toggleDetails);			
	    },
    	actions :
    	{
    	    	   /// <summary>
    			   /// Click on registration details.     
    			   /// </summary>
    			   /// <param name="parent" type="Event"></param>    
    			   /// <returns type="actions" />
    			   /// <since>1.0.0</since>    
    			   toggleDetails: function(event) 
    			   {                   
    			   	     event.preventDefault();

                         jQuery( event.target ).toggleClass('wpem-active-button');
    			   	     jQuery( '.registration_details' ).slideToggle();
    			   }    			  
    	}

    } //enf of return
}; //end of class

EventRegistration = EventRegistration();
jQuery(document).ready(function($) 
{
  EventRegistration.init();
});