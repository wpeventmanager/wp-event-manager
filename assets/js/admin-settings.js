var AdminSettings= function () {
    /// <summary>Constructor function of the event settings class.</summary>
    /// <returns type="Settings" />   
    return {
	    ///<summary>
        ///Initializes the AdminSettings.  
        ///</summary>     
        ///<returns type="initialization AdminSettings" />   
        /// <since>1.0.0</since> 
        init: function() {			
		     //Bind on click event of the settings section
			jQuery(".nav-tab-wrapper a").on('click',AdminSettings.actions.tabClick);
		  	 //show by default first Event Listings Settings Tab
            jQuery('.nav-tab-wrapper a:first').click();		
			jQuery("#setting-event_manager_enable_registration").on('change',AdminSettings.actions.allowAccountCreation);
			jQuery("#setting-event_manager_submission_expire_options").on('change',AdminSettings.actions.selectEventExpiryOption);						
	   },

	actions :{
	    	   /// <summary>
			   /// Click on tab either Event Listings, Event Submission or Pages.     
			   /// </summary>
			   /// <param name="parent" type="Event"></param>    
			   /// <returns type="actions" />
			   /// <since>1.0.0</since>    
			   tabClick: function(event) {                   
					event.preventDefault();
					jQuery('.settings_panel').hide();
					jQuery('.nav-tab-active').removeClass('nav-tab-active');
					jQuery(jQuery(this).attr('href')).show();
					jQuery(this).addClass('nav-tab-active');				
					var option= jQuery("#setting-event_manager_submission_expire_options:last option:selected").val();	
					if (option =='days') 
					jQuery('#setting-event_manager_submission_duration').closest('tr').show();
					else
					jQuery('#setting-event_manager_submission_duration').closest('tr').hide();
					return false;
			   },

			   /// <summary>
			   /// If enabled, non-logged in users will be able to create an account by entering their email address on the submission form.  
			   /// </summary>
			   /// <param name="parent" type="Event"></param> 
			   /// <returns type="actions" />     
			   /// <since>1.0.0</since>      
			   allowAccountCreation: function(event) {
			   	if (jQuery(this).is(':checked')) {
					jQuery('#setting-event_manager_registration_role').closest('tr').show();
					jQuery('#setting-event_manager_registration_username_from_email').closest('tr').show();
				} else {
					jQuery('#setting-event_manager_registration_role').closest('tr').hide();
					jQuery('#setting-event_manager_registration_username_from_email').closest('tr').hide();
				}
				event.preventDefault();
			}			  
		}
    } //enf of return
}; //end of class

AdminSettings = AdminSettings();
jQuery(document).ready(function($) {
  AdminSettings.init();
});