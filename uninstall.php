<?phpif ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {	exit();}// Cleanup all data.require 'core/wp-event-manager-data-cleaner.php';if ( ! is_multisite() ) {	// Only do deletion if the setting is true.	$do_deletion = get_option( 'event_manager_delete_data_on_uninstall' );	if ( $do_deletion ) {		WP_Event_Manager_Data_Cleaner::cleanup_all();	}} else {	global $wpdb;	$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );	$original_blog_id = get_current_blog_id();	foreach ( $blog_ids as $blog_id ) {		switch_to_blog( $blog_id );		// Only do deletion if the setting is true.		$do_deletion = get_option( 'event_manager_delete_data_on_uninstall' );		if ( $do_deletion ) {			WP_Event_Manager_Data_Cleaner::cleanup_all();		}	}	switch_to_blog( $original_blog_id );}$options = array(
		'event_manager_enqueue_boostrap_frontend',
		'event_manager_enqueue_boostrap_backend',
		'event_manager_delete_data_on_uninstall',
		'event_manager_google_maps_api_key',
		'event_manager_per_page',
		'event_manager_hide_cancelled_events',
		'event_manager_hide_expired',
		'event_manager_hide_expired_content',
		'event_manager_enable_categories',
		'event_manager_enable_event_types',
		'event_manager_enable_event_ticket_prices',
		'event_manager_enable_default_category_multiselect',
		'event_manager_enable_default_event_type_multiselect',
		'event_manager_category_filter_type',
		'event_manager_event_type_filter_type',
		'event_manager_user_requires_account',
		'event_manager_enable_registration',
		'event_manager_generate_username_from_email',
		'event_manager_use_standard_password_setup_email',
		'event_manager_registration_role',
		'event_manager_submission_requires_approval',
		'event_manager_user_can_edit_pending_submissions',
		'event_manager_user_can_add_multiple_banner',
		'event_manager_delete_expired_events',
		'event_manager_submission_expire_options',
		'event_manager_submission_duration',
		'event_manager_allowed_registration_method',
		'event_manager_multiselect_event_type',
		'event_manager_multiselect_event_category',
		'event_manager_time_format',
		'event_manager_submit_event_form_page_id',
		'event_manager_event_dashboard_page_id',
		'event_manager_events_page_id',
);foreach ( $options as $option ) {
	delete_option( $option );
}