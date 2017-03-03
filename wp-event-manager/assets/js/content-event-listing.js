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
    		      jQuery('.line-layout').show();   
    		      jQuery('.box-layout').hide();   	   
    		      jQuery( "#line-layout-icon" ).removeClass( "lightgray-layout-icon" );
    		      jQuery( "#box-layout-icon" ).addClass( "lightgray-layout-icon" );  
    		      
    		      jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings-table-bordered-change" ); 
                  jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings-table-bordered" );
                  jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings-change" ); 
                  jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings" );       
                  
    		  } 
    		  else if(localStorage.getItem("layout")=="box-layout" )
    		  {   
                  jQuery('.box-layout').show();
        		  jQuery('.line-layout').hide(); 
	              jQuery( "#line-layout-icon" ).addClass( "lightgray-layout-icon" );                
	              jQuery( "#box-layout-icon" ).removeClass( "lightgray-layout-icon" );  
	                       
	              jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings-table-bordered" ); 
        	      jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings-table-bordered-change" );
        	      jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings" ); 
        	      jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings-change" );  
        	     
    		  }
    		  else if(localStorage.getItem("layout")=="calendar-layout" )
    		  {   
                     jQuery('.box-layout').hide(); 
    			     jQuery('.line-layout').hide();    			    
    			     jQuery('#load_more_events').hide();
    			     jQuery( "#line-layout-icon" ).addClass( "lightgray-layout-icon" );
		             jQuery( "#box-layout-icon" ).addClass( "lightgray-layout-icon" );
		             jQuery( "#calendar-layout-icon" ).removeClass( "lightgray-layout-icon" );	         
        	     
    		  }   
    		  else 
    		  {
    		        //initially line layout hidden    		       
        		   jQuery('.line-layout').hide(); 
    		       jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings-table-bordered" ); 
                   jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings-table-bordered-change" );
                   jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings" ); 
                   jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings-change" ); 
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
		    jQuery(document).delegate('#line-layout-icon','click', ContentEventListing.actions.lineLayoutIconClick);
		    jQuery(document).delegate('#box-layout-icon','click', ContentEventListing.actions.boxLayoutIconClick);		    
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
	               	      jQuery('.box-layout').hide(); 
        			      jQuery('.line-layout').show();      
	                      jQuery(this).removeClass( "lightgray-layout-icon" );
	                      jQuery(this).closest('i').prev().addClass( "lightgray-layout-icon" );
	                      
	                      jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings-table-bordered-change" ); 
        	              jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings-table-bordered" );
        	              jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings-change" ); 
        	              jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings" ); 
        	            
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
	                       jQuery('.box-layout').show();
        			       jQuery('.line-layout').hide(); 
	                       jQuery(this).removeClass( "lightgray-layout-icon" );	                       
	                       jQuery(this).closest('i').next().addClass( "lightgray-layout-icon" );
	                       
	                       jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings-table-bordered" ); 
        	               jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings-table-bordered-change" );
        	               jQuery( ".event-listings-view-header" ).parent().find('ul').removeClass( "event-listings" ); 
        	               jQuery( ".event-listings-view-header" ).parent().find('ul').addClass( "event-listings-change" );  
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
