var EventDashboard= function () {
    /// <summary>Constructor function of the event EventDashboard class.</summary>
    /// <returns type="Home" />      
    return {
	    ///<summary>
        ///Initializes the event dashboard.  
        ///</summary>     
        ///<returns type="initialization settings" />   
        /// <since>1.0.0</since> 
        init: function(){
          
			if (jQuery('.event-dashboard-action-delete').length > 0 && jQuery('td .wpem-dboard-event-action').length == 0){

				jQuery('.event-dashboard-action-delete').css({'cursor':'pointer'});  					
				//for delete event confirmation dialog / tooltip 
				jQuery('.event-dashboard-action-delete').on('click', EventDashboard.confirmation.showDialog);	
	        }

	        // For Dashboard Menu Toggle
	        if(jQuery('.wpem-main-vmenu-dashboard-sub-menu .wpem-main-vmenu-dashboard-link').length >0){
		    	jQuery('.wpem-main-vmenu-dashboard-sub-menu .wpem-main-vmenu-dashboard-submenu-ul').hide();
		    	jQuery('.wpem-main-vmenu-dashboard-sub-menu .wpem-main-vmenu-dashboard-link').on('click', EventDashboard.actions.openSubmenu);	
		    }

		    // For Active Dashboard Menu Open
	        if(jQuery('.wpem-main-vmenu-dashboard-sub-menu .wpem-main-vmenu-dashboard-link-active').length >0){
		    	jQuery('.wpem-main-vmenu-dashboard-sub-menu .wpem-main-vmenu-dashboard-link-active').trigger('click');	
		    }

		    // For Event List Toggle
		    if(jQuery('#wpem-dashboard-event-list-wrapper .wpem-event-dashboard-information-toggle').length >0) {
		    	jQuery('#wpem-dashboard-event-list-wrapper .wpem-event-dashboard-information-toggle').hide();
				jQuery('#wpem-dashboard-event-list-wrapper').on('click', '.event-dashboard-action-details', function() {
					jQuery(this).closest('div.wpem-dashboard-event-list').find('section:not(.wpem-event-dashboard-information-toggle)').slideUp();
					jQuery(this).closest('div.wpem-dashboard-event-list').find('section.wpem-event-dashboard-information-toggle').slideToggle();
					return false;
				})
				.on('click', 'a.hide_section', function() {
					jQuery(this).closest('section').slideUp();
					return false;
				});
			}
            jQuery("#reset_dashboard").on('click', function(){
                window.location.href = window.location.href.split('?')[0];
            });
			// For Event Filter Toggle
			if(jQuery('.wpem-dashboard-main-header .wpem-event-dashboard-filter-toggle').length >0){
				jQuery('.wpem-dashboard-main-header .wpem-event-dashboard-filter-toggle').hide();
				jQuery('.wpem-dashboard-main-header .wpem-dashboard-main-filter').on('click', '.wpem-dashboard-event-filter', function() {
					jQuery(this).closest('div.wpem-dashboard-main-header').find('form:not(.wpem-event-dashboard-filter-toggle)').slideUp();
					jQuery(this).closest('div.wpem-dashboard-main-header').find('form.wpem-event-dashboard-filter-toggle').slideToggle();

					if(jQuery('.wpem-dashboard-main-header .wpem-event-dashboard-filter-toggle').hasClass('wpem-d-block')){
						jQuery('.wpem-dashboard-main-header .wpem-event-dashboard-filter-toggle').removeClass('wpem-d-block');
					}
					return false;
				})
		    }
 	 	}, 

		confirmation:{
            /// <summary>
	        /// Show bootstrap third party confirmation dialog when click on 'Delete' options on event dashboard page where show delete event option.	     
	        /// </summary>
	        /// <param name="parent" type="assign"></param>           
	        /// <returns type="actions" />     
	        /// <since>1.0.0</since>       
	        showDialog: function(event) {
	        	Common.logInfo("EventDashboard.confirmation.showDialog...");	            
	           	return confirm(event_manager_event_dashboard.i18n_confirm_delete);
	           	event.preventDefault(); 
	        },
	    },//end of comfirmation
	    
	    actions:{
	    	openSubmenu: function(event) {
	        	event.stopPropagation();

				var parentLI = jQuery(this).closest("li");
				var other = parentLI.siblings();
				var myUL = parentLI.find("ul");
				var myToggle = jQuery(this).find(".wpem-main-vmenu-caret");

				other.find("ul").slideUp("100");
				other.find("a i.wpem-main-vmenu-caret").removeClass("wpem-main-vmenu-caret-down").addClass("wpem-main-vmenu-caret-up");

				myUL.slideToggle("100");
				myToggle.toggleClass("wpem-main-vmenu-caret-up wpem-main-vmenu-caret-down");
	        },
	    }, //end of actions
    } //enf of return	
}; //end of class

EventDashboard= EventDashboard();
jQuery(document).ready(function($) {
   EventDashboard.init();
});