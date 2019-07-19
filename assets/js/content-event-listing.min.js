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
			   
    		  //With show_pagination attribute in shortcodes. e.g [events per_page="10" show_pagination="true"]
    		  //Check when user has changed page using pagination and then need to keep current selected layout
    		  //When layout is box and user changed page using pagination then need to show line layout instead of line layout  
    		  if(localStorage.getItem("layout")=="line-layout" )
    		  {            		      
                  jQuery(".wpem-event-box-col").show();
                  jQuery('#line-layout-icon').addClass("active");

                  jQuery( "#wpem-event-box-layout" ).removeClass( "active" ); 
    		      jQuery( "#wpem-event-calendar-layout" ).removeClass( "active" );
                  
    		  } 
    		  else if(localStorage.getItem("layout")=="box-layout" )
    		  {   
                 jQuery(".wpem-event-box-col").show();
                 
	              jQuery( "#wpem-event-box-layout" ).addClass( "active" );                
	              jQuery( "#wpem-event-list-layout" ).removeClass( "active" );  
	              jQuery( "#calendar-calendar-icon" ).removeClass( "active" );	                  
	            
        	     
    		  }
    		  else if(localStorage.getItem("layout")=="calendar-layout" )
    		  {   
                     jQuery('#load_more_events').hide();
    			     jQuery( "#wpem-event-calendar-layout" ).addClass( "active" );
		             jQuery( "#wpem-event-list-layout" ).removeClass( "active" );	         
		             jQuery( "#wpem-event-box-layout" ).removeClass( "active" );	         
        	     
    		  }   
        },
        
        ///<summary>
        ///Bind event for layout selection icon click.  
        ///</summary>     
        ///<returns type="initialization settings" />   
        /// <since>1.0.0</since> 
        layoutSelectionEventBind: function() 
        {     
            jQuery(document).delegate('#load_more_events','click', ContentEventListing.actions.loadMoreEventsClick);
		    jQuery(document).delegate('#wpem-event-list-layout','click', ContentEventListing.actions.lineLayoutIconClick);
		    jQuery(document).delegate('#wpem-event-box-layout','click', ContentEventListing.actions.boxLayoutIconClick);		    
        },
        
        actions: 
        {
                /// <summary>
	            /// Check when user has changed page using pagination and then need to keep current selected layout.
	            /// </summary>     
	            /// <returns type="events listing view" />    
	            /// <since>1.0.0</since>     
	            loadMoreEventsClick: function (event)
	            {	                          
	                 Common.logInfo("ContentEventListing.actions.loadMoreEventsClick...");
	                 //Check when user has changed page using pagination and then need to keep current selected layout
        			//When layout is box and user changed page using pagination then need to show line layout instead of line layout
        			if(localStorage.getItem("layout")=="line-layout")
        			{  
        			    jQuery('.line-layout').show();  
        			    jQuery('.box-layout').hide();   
        			}
        			else
        			{           
        			    jQuery('.line-layout').hide();  
        			    jQuery('.box-layout').show();   
        			}   			
        			event.preventDefault();
		       },
		   
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
   ContentEventListing.layoutSelectionEventBind();
});
