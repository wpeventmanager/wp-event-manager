<?php
/**
 * Shortcodes Page
*/

if (!defined('ABSPATH')){
	 exit;// Exit if accessed directly
} 

if (!class_exists('WP_Event_Manager_Shortcode_List')) :

	/**
	 * WP_Event_Manager_Shortcode_List Class
	*/
	class WP_Event_Manager_Shortcode_List {
		/**
		 * Handles output of the reports page in admin.
		 */
		public function shortcode_list() { ?>
			<div class="wrap wp_event_manager wp_event_manager_shortcodes_wrap">
				<h2><?php _e('WP Event Manager shortcodes', 'wp-event-manager'); ?></h2>
			
				<div class="wpem-shortcode-page">
					<div class="wpem-shortcode-filters">
						<select name="category" id="category">
							<option value="" selected="selected">Select category & Add-ons</option>
							<option class="level-0" value="business-professional">Business &amp; Professional&nbsp;(2)</option>
							<option class="level-0" value="charity-causes">Charity &amp; Causes&nbsp;(0)</option>
							<option class="level-0" value="community-culture">Community &amp; Culture&nbsp;(0)</option>
							<option class="level-0" value="family-education">Family &amp; Education&nbsp;(0)</option>
							<option class="level-0" value="fashion-beauty">Fashion &amp; Beauty&nbsp;(0)</option>
							<option class="level-0" value="film-media-entertainment">Film, Media &amp; Entertainment&nbsp;(0)</option>
							<option class="level-0" value="food-drink">Food &amp; Drink&nbsp;(0)</option>
							<option class="level-0" value="game-or-competition">Game or Competition&nbsp;(0)</option>
							<option class="level-0" value="other">Other&nbsp;(0)</option>
							<option class="level-0" value="performing-visual-arts">Performing &amp; Visual Arts&nbsp;(0)</option>
							<option class="level-0" value="science-technology">Science &amp; Technology&nbsp;(0)</option>
							<option class="level-0" value="sports-fitness">Sports &amp; Fitness&nbsp;(0)</option>
						</select>
						<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
					</div>
				
					<div class="wpem-shortcode-table">
						<table>
							<thead>
								<tr>
									<th>Shortcode</th>
									<th>Title</th>
									<th>Description</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="wpem-shortcode-td">[events]</td>
									<td>The event listings</td>
									<td>To display all the event listings, users need to create a new page from the Pages menu at the Admin Panel and add the shortcode  [events] or can add the shortcode in the Template file that is attached to the page created.</td>
									<td><a class="button add-field" href="javascript:void(0)">View Details</a></td>
								</tr>
								<tr>
									<td class="wpem-shortcode-td">[my_registrations]</td>
									<td>My Registrations</td>
									<td>My registration is the page that displays the list of all the events for which a particular user has registered.</td>
									<td><a class="button add-field" href="javascript:void(0)">View Details</a></td>
								</tr>
								<tr>
									<td class="wpem-shortcode-td">[submit_organizer_form]</td>
									<td>Organizer Shortcodes</td>
									<td>To set up the Submit Organizer Page, paste the shortcode [submit_organizer_form], in the content area of  the Submit Organizer Page.</td>
									<td><a class="button add-field" href="javascript:void(0)">View Details</a></td>
								</tr>
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