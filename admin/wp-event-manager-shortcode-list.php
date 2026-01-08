<?php
if(!defined('ABSPATH')){
	 exit;// Exit if accessed directly
} 

if(!class_exists('WP_Event_Manager_Shortcode_List')) :
	/**
	 * WP_Event_Manager_Shortcode_List Class
	*/
	class WP_Event_Manager_Shortcode_List {
		/**
		 * Handles output of the reports page in admin.
		 */
		public function shortcode_list() { 
			wp_register_script('wp-event-manager-admin-js', EVENT_MANAGER_PLUGIN_URL . '/assets/js/admin.min.js', array('jquery', 'jquery-tiptip', 'jquery-ui-core', 'jquery-ui-datepicker'), EVENT_MANAGER_VERSION, true);
			
			wp_enqueue_script('wp-event-manager-admin-js');
			
			$detail_link = esc_url("https://wp-eventmanager.com/knowledge-base/");

			$shortcode_plugins = apply_filters('wpem_event_manager_shortcode_plugin', 
				array(
					'wp-event-manager' => __('WP Event Manager', 'wp-event-manager')
				)
			);	
			$plugin_slug                 = 'wp-event-manager';
			$shortcode_list_nonce_action = 'wpem_shortcode_list_filter';
			$shortcode_list_nonce_name   = 'wpem_shortcode_list_nonce';

			if (
				isset( $_GET['plugin'], $_GET[ '_wpnonce' ] )
				&& ! empty( $_GET['plugin'] )
				&& wp_verify_nonce(
					wp_unslash( $_GET[ '_wpnonce' ] ),
					$shortcode_list_nonce_action
				)
			) {
				$plugin_slug = sanitize_text_field( wp_unslash( $_GET['plugin'] ) );
			}
			$shortcode_list_nonce = wp_create_nonce( $shortcode_list_nonce_action );
			?>
			<style>
				.<?php echo esc_attr($plugin_slug);?>{display:table-row;}
			</style>
			<div class="wrap wp_event_manager wp_event_manager_shortcodes_wrap">
				<h2><?php esc_attr_e('WP Event Manager shortcodes', 'wp-event-manager'); ?></h2>
				<div class="wpem-shortcode-page">

					<div class="wpem-shortcode-filters">
						<input type="hidden" id="wpem_shortcode_list_nonce" name="<?php echo esc_attr( $shortcode_list_nonce_name ); ?>" value="<?php echo esc_attr( $shortcode_list_nonce ); ?>" />
						<select name="wpem_shortcode_filter" id="wpem_shortcode_filter">
							<option value=""><?php esc_attr_e('Select Plugin', 'wp-event-manager');?></option> 
							<?php 
							foreach ($shortcode_plugins as $key => $value) { 
								if($key == $plugin_slug) 
									$selected = 'selected="selected"';
								else
									$selected = ''; 
								echo '<option class="level-0" value="'.esc_attr($key).'" '.esc_attr($selected).'>'.esc_attr($value).'</option>';
							 } ?>
						</select>
						<input type="button" name="shortcode_list_filter_action" id="shortcode_list_filter_action" class="button" value="<?php esc_attr_e('Filter', 'wp-event-manager');?>">
					</div>

					<div class="wpem-shortcode-table">
						<table>
							<thead>
								<tr>
									<th><?php esc_attr_e('Shortcode', 'wp-event-manager');?></th>
									<th><?php esc_attr_e('Title', 'wp-event-manager');?></th>
									<th><?php esc_attr_e('Description', 'wp-event-manager');?></th>
									<th><?php esc_attr_e('Action', 'wp-event-manager');?></th>
								</tr>
							</thead>
							<tbody>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[events]</td>
									<td><?php esc_attr_e('The event listings', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('To display all the event listings, users need to create a new page from the Pages menu at the Admin Panel and add the shortcode  [events] or can add the shortcode in the Template file that is attached to the page created.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'the-event-listings/';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[submit_event_form]</td>
									<td><?php esc_attr_e('The event submission form', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('To display the Event Submission Form, a user needs to create a new page from the Pages menu at the Admin Panel and then add the shortcode [submit_event_form].', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'the-event-submission-form/';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[event_dashboard]</td>
									<td><?php esc_attr_e('The Event Dashboard', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('You can add an Event Dashboard to a new page by pasting the appropriate shortcode on the HTML editor.To display an Event Dashboard, users need to create a page from the pages menu at the Admin Panel and add the shortcode [event_dashboard].', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'the-event-dashboard/';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[event id="event_id"]</td>
									<td><?php esc_attr_e('The Single Event Listing', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The brand new feature of WP Event Manager gives users the right to access their events on a single page. In case, users want to view their events on a single page, a new page can be created from the admin panel for the single event listing.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'the-single-event-listing/';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[past_events]</td>
									<td><?php esc_attr_e('Past Events', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The Past Events Page is dedicated to the users who want to access the details of all their past events. It allows users to view the list of events and their details that have taken place in the past.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'past-events/';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[event_summary id="event_id"]</td>
									<td><?php esc_attr_e('The Event Summary', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The brand new feature of WP Event Manager allows users to get a complete summary of their events. A new page needs to be created from the Admin Panel to access the event summaries.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'the-event-summary/';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td"> [submit_organizer_form]</td>
									<td><?php esc_attr_e('Submit Organizer Page', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The Submit Organizers Page contains a form in which a user needs to fill in the details of the event organizers. To set up the Submit Organizer Page, paste the shortcode [submit_organizer_form], in the content area of  the Submit Organizer Page.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'organizer-shortcode/#articleTOC_0';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[organizer_dashboard]</td>
									<td><?php esc_attr_e('Organizer Dashboard', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The dashboard displays the list of all the organizers created. A user can add, delete, duplicate organizers from the Organizer Dashboard.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'organizer-shortcode/#articleTOC_1';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[event_organizers]</td>
									<td><?php esc_attr_e('Event Organizers', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The event Organizer page displays event organizers list in alphabetical order with an alphabetic filter option. To set up the Event Organizer page, paste the shortcode [event_organizers] in the content area of the Event Organizer page.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'organizer-shortcode/#articleTOC_2';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[event_organizer id="organizer_id"]</td>
									<td><?php esc_attr_e('Event Organizer', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('In order to display a particular organizer on the page, a user can add this shortcode.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'organizer-shortcode/#articleTOC_4';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[single_event_organizer id="event_id"]</td>
									<td><?php esc_attr_e('Single Event Organizers', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('In order to display a particular event’s Organizer, a user can add this shortcode.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'organizer-shortcode/#articleTOC_5';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>

								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td"> [submit_venue_form]</td>
									<td><?php esc_attr_e('Submit Venue Page', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The “Submit Venue” Page contains a form in which a user needs to fill in the details of the event venues.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'venue-shortcode/#articleTOC_0';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[venue_dashboard]</td>
									<td><?php esc_attr_e('Venue Dashboard', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The dashboard displays the list of all the Venues created by the logged in users. A user can add, edit, delete, create, duplicate Venues and view a specific venue’s event list from the Venue Dashboard.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'venue-shortcode/#articleTOC_1';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[event_venues]</td>
									<td><?php esc_attr_e('Event Venues', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('The Event Venue  page displays the list of event venues in alphanumerical order with an alphanumeric filter option.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'venue-shortcode/#articleTOC_2';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[event_venue id="venue_id"]</td>
									<td><?php esc_attr_e('Event Venue', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('In order to display a particular Venue on the page, a user can add this shortcode.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'venue-shortcode/#articleTOC_4';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<tr class="shortcode_list wp-event-manager">
									<td class="wpem-shortcode-td">[single_event_venue id="event_id"]</td>
									<td><?php esc_attr_e('Single Event Venues', 'wp-event-manager');?></td>
									<td><?php esc_attr_e('In order to display a particular event’s Venue, a user can add this shortcode.', 'wp-event-manager');?></td>
									<td><a class="button add-field" href="<?php echo esc_attr($detail_link).'venue-shortcode/#articleTOC_5';?>" target="_blank"><?php esc_attr_e('View Details', 'wp-event-manager');?></a></td>
								</tr>
								<?php do_action('wpem_event_manager_shortcode_list', $detail_link); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php
		} 
	}
endif;
return new WP_Event_Manager_Shortcode_List();