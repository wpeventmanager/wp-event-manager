<?php
/*
* This file use for settings at admin site for wp event manager plugin.
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * WP_Event_Manager_Settings class.
 */
class WP_Event_Manager_Settings
{

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->settings_group = 'event_manager';
		add_action('admin_init', array($this, 'register_settings'));
	}

	/**
	 * init_settings function.
	 *
	 * @access protected
	 * @return void
	 */
	protected function init_settings()
	{

		// Prepare roles option
		$roles = get_editable_roles();

		$account_roles = array();
		foreach ($roles as $key => $role) {

			if ($key == 'administrator') {
				continue;
			}

			$account_roles[$key] = $role['name'];
		}

		$this->settings = apply_filters(
			'event_manager_settings',
			array(
				'general_settings'     => array(

					__('General', 'wp-event-manager'),

					array(

						array(
							'name'       => 'enable_event_organizer',
							'std'        => '1',
							'label'      => __('Enable organizer', 'wp-event-manager'),
							'cb_label'   => __('Disable this to remove the functionality of Organizers on Your Event Website.', 'wp-event-manager'),
							'desc'       => '',
							'type'       => 'checkbox',
							'attributes' => array(),
						),

						array(
							'name'       => 'enable_event_venue',
							'std'        => '1',
							'label'      => __('Enable venue', 'wp-event-manager'),
							'cb_label'   => __('Disable this to remove the functionality of Venue on your Event Website.', 'wp-event-manager'),
							'desc'       => '',
							'type'       => 'checkbox',
							'attributes' => array(),
						),

						array(
							'name'       => 'event_manager_delete_data_on_uninstall',
							'std'        => '0',
							'label'      => __('Delete Data On Uninstall', 'wp-event-manager'),
							'cb_label'   => __('Delete WP Event Manager data when the plugin is deleted. Once removed, this data cannot be restored.', 'wp-event-manager'),
							'desc'       => '',
							'type'       => 'checkbox',
							'attributes' => array(),
						),
						array(
							'name'       => 'event_manager_google_maps_api_key',
							'std'        => '',
							'label'      => __('Google API Key', 'wp-event-manager'),
							'desc'       => __('If you are going to deal with google map or location then you need Google API key to retrieve location information for event listings. Also this Google API key require when you will use <a href="https://www.wp-eventmanager.com/product/wp-event-manager-google-maps/" target="__blank">Google Map Addon</a>.  Acquire an API key from the <a href="https://developers.google.com/maps/documentation/geocoding/get-api-key" target="__blank">Google Maps API developer site</a>. ', 'wp-event-manager'),
							'attributes' => array(),
						),
					),
				),
				'event_listings'       => array(

					__('Event Listings', 'wp-event-manager'),

					array(

						array(

							'name'        => 'event_manager_per_page',

							'std'         => '10',

							'placeholder' => '',

							'label'       => __('Listings Per Page', 'wp-event-manager'),

							'desc'        => __('How many listings should be shown per page by default?', 'wp-event-manager'),

							'type'        => 'number',

							'attributes'  => array(),
						),

						array(

							'name'       => 'event_manager_hide_cancelled_events',

							'std'        => '0',

							'label'      => __('Cancelled Events', 'wp-event-manager'),

							'cb_label'   => __('Hide cancelled events.', 'wp-event-manager'),

							'desc'       => __('If enabled, cancelled events will be hidden from archives.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),
						array(
							'name'       => 'event_manager_hide_expired',

							'std'        => get_option('event_manager_hide_expired_content') ? '1' : '0', // back compat
							'label'      => __('Hide Expired Listings', 'wp-event-manager'),

							'cb_label'   => __('Hide expired listings in event archive/search.', 'wp-event-manager'),

							'desc'       => __('If enabled, expired event listing is not searchable.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_hide_expired_content',

							'std'        => '1',

							'label'      => __('Hide Expired Listings Content', 'wp-event-manager'),

							'cb_label'   => __('Hide expired listing content in single event listing (singular).', 'wp-event-manager'),

							'desc'       => __('If enabled, the content within expired listings will be hidden. Otherwise, expired listings will be displayed as normal (without the event registration area).', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_enable_default_category_multiselect',

							'std'        => '0',

							'label'      => __('Multi-select Categories', 'wp-event-manager'),

							'cb_label'   => __('Enable category multiselect by default.', 'wp-event-manager'),

							'desc'       => __('If enabled, the category select box will default to a multi select on the [events] shortcode.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_enable_default_event_type_multiselect',

							'std'        => '0',

							'label'      => __('Multi-select Event Types', 'wp-event-manager'),

							'cb_label'   => __('Enable event type multiselect by default.', 'wp-event-manager'),

							'desc'       => __('If enabled, the event type select box will default to a multi select on the [events] shortcode.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'    => 'event_manager_category_filter_type',

							'std'     => 'any',

							'label'   => __('Category Filter', 'wp-event-manager'),

							'desc'    => __('If enabled, the category select box will default to a multi select on the [events] shortcode.', 'wp-event-manager'),

							'type'    => 'radio',

							'options' => array(

								'any' => __('Events will be shown if within ANY selected category.', 'wp-event-manager'),

								'all' => __('Events will be shown if within ALL selected categories.', 'wp-event-manager'),
							),
						),

						array(

							'name'    => 'event_manager_event_type_filter_type',

							'std'     => 'any',

							'label'   => __('Event Type Filter', 'wp-event-manager'),

							'desc'    => __('If enabled, the event type select box will default to a multi select on the [events] shortcode.', 'wp-event-manager'),

							'type'    => 'select',

							'options' => array(

								'any' => __('Events will be shown if within ANY selected event type.', 'wp-event-manager'),

								'all' => __('Events will be shown if within ALL selected event types.', 'wp-event-manager'),
							),
						),
					),
				),

				'event_submission'     => array(

					__('Event Submission', 'wp-event-manager'),

					array(

						array(

							'name'       => 'event_manager_user_requires_account',

							'std'        => '1',

							'label'      => __('Account Required', 'wp-event-manager'),

							'cb_label'   => __('Submitting listings requires an account.', 'wp-event-manager'),

							'desc'       => __('If disabled, non-logged in users will be able to submit listings without creating an account.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_enable_registration',

							'std'        => '1',

							'label'      => __('Account Creation', 'wp-event-manager'),

							'cb_label'   => __('Allow account creation.', 'wp-event-manager'),

							'desc'       => __('If enabled, non-logged in users will be able to create an account by entering their email address on the submission form.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_generate_username_from_email',

							'std'        => '1',

							'label'      => __('Account Username', 'wp-event-manager'),

							'cb_label'   => __('Automatically Generate Username from Email Address.', 'wp-event-manager'),

							'desc'       => __('If enabled, a username will be generated from the first part of the user email address. Otherwise, a username field will be shown.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),
						array(
							'name'       => 'event_manager_use_standard_password_setup_email',
							'std'        => '1',
							'label'      => __('Account Password', 'wp-event-manager'),
							'cb_label'   => __('Use WordPress\' default behavior and email new users link to set a password.', 'wp-event-manager'),
							'desc'       => __('If enabled, an email will be sent to the user with their username and a link to set their password. Otherwise, a password field will be shown and their email address won\'t be verified.', 'wp-event-manager'),
							'type'       => 'checkbox',
							'attributes' => array(),
						),

						array(

							'name'    => 'event_manager_registration_role',

							'std'     => 'organizer',

							'label'   => __('Account Role', 'wp-event-manager'),

							'desc'    => __('If you enable user registration on your submission form, choose a role for the new user.', 'wp-event-manager'),

							'type'    => 'select',

							'options' => $account_roles,
						),

						array(

							'name'       => 'event_manager_submission_requires_approval',

							'std'        => '1',

							'label'      => __('Moderate New Listings', 'wp-event-manager'),

							'cb_label'   => __('New listing submissions require admin approval.', 'wp-event-manager'),

							'desc'       => __('If enabled, new submissions will be inactive, pending admin approval.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_user_can_edit_pending_submissions',

							'std'        => '0',

							'label'      => __('Allow Pending Edits', 'wp-event-manager'),

							'cb_label'   => __('Submissions awaiting approval can be edited.', 'wp-event-manager'),

							'desc'       => __('If enabled, submissions awaiting admin approval can be edited by the user.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),
						array(
							'name'       => 'event_manager_user_can_add_multiple_banner',

							'std'        => '0',

							'label'      => __('Allow Multiple Banners', 'wp-event-manager'),

							'cb_label'   => __('User can submit multiple banner.', 'wp-event-manager'),

							'desc'       => __('If enabled, Multiple banner can add at frontend by user and backend side by admin.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),
						array(

							'name'       => 'event_manager_delete_events_after_finished',

							'std'        => '0',

							'label'      => __('Delete listings after finished', 'wp-event-manager'),

							'cb_label'   => __('Delete listings after finished.', 'wp-event-manager'),

							'desc'       => __('If enabled, expired listings will automatically deleted after finished.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),
						array(

							'name'       => 'event_manager_delete_expired_events',

							'std'        => '0',

							'label'      => __('Delete Expired listings', 'wp-event-manager'),

							'cb_label'   => __('Expired listings are deleted after 30 days.', 'wp-event-manager'),

							'desc'       => __('If enabled, expired listings will automatically deleted after 30 days.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'    => 'event_manager_submission_expire_options',

							'std'     => 'event_end_date',

							'label'   => __('Listing Expire', 'wp-event-manager'),

							'desc'    => __('You can set event submission expiry time either event end date or specific days.', 'wp-event-manager'),

							'type'    => 'select',

							'options' => array(

								'event_end_date' => __('Listing expire on Event End Date', 'wp-event-manager'),

								'days'           => __('Listing expire on Specified Below Days', 'wp-event-manager'),
							),
						),

						array(

							'name'       => 'event_manager_submission_duration',

							'std'        => '30',

							'label'      => __('Listing Duration', 'wp-event-manager'),

							'desc'       => __('How many <strong>days</strong> listings are live before expiring. Can be left blank to never expire.', 'wp-event-manager'),

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_enable_categories',

							'std'        => '1',

							'label'      => __('Categories', 'wp-event-manager'),

							'cb_label'   => __('Enable Categories for listing and submission form.', 'wp-event-manager'),

							'desc'       => __('Choose whether to enable categories on the event listing and submission page. If enabled, it needs to be prior created at the backend.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_enable_event_types',

							'std'        => '1',

							'label'      => __('Event Types', 'wp-event-manager'),

							'cb_label'   => __('Enable Event types for listing and submission form.', 'wp-event-manager'),

							'desc'       => __('Choose whether to enable event types on the event listing and submission page. If enabled, it needs to be prior created at the backend.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),

						array(

							'name'       => 'event_manager_enable_event_ticket_prices',

							'std'        => '0',

							'label'      => __('Ticket prices', 'wp-event-manager'),

							'cb_label'   => __('Enable Ticket prices for listing and submission form.', 'wp-event-manager'),

							'desc'       => __('Choose whether to enable ticket prices on the event listing and submission page.', 'wp-event-manager'),

							'type'       => 'checkbox',

							'attributes' => array(),
						),
					),
				),

				'event_pages'          => array(

					__('Pages', 'wp-event-manager'),

					array(

						array(

							'name'  => 'event_manager_submit_event_form_page_id',

							'std'   => '',

							'label' => __('Submit Event Form Page', 'wp-event-manager'),

							'desc'  => __('Select the page where you have placed the [submit_event_form] shortcode. This lets the plugin know where the form is located.', 'wp-event-manager'),

							'type'  => 'page',
						),

						array(

							'name'  => 'event_manager_event_dashboard_page_id',

							'std'   => '',

							'label' => __('Event Dashboard Page', 'wp-event-manager'),

							'desc'  => __('Select the page where you have placed the [event_dashboard] shortcode. This lets the plugin know where the dashboard is located.', 'wp-event-manager'),

							'type'  => 'page',
						),

						array(

							'name'  => 'event_manager_events_page_id',

							'std'   => '',

							'label' => __('Event Listings Page', 'wp-event-manager'),

							'desc'  => __('Select the page where you have placed the [events] shortcode. This lets the plugin know where the event listings page is located.', 'wp-event-manager'),

							'type'  => 'page',
						),
						array(

							'name'  => 'event_manager_login_page_url',

							'std'   => wp_login_url(),

							'label' => __('Login Page URL', 'wp-event-manager'),

							'desc'  => __('Enter the Login page URL.', 'wp-event-manager'),

							'type'  => 'text',
						),

						array(

							'name'  => 'event_manager_submit_organizer_form_page_id',

							'std'   => '',

							'label' => __('Submit Organizer Form Page', 'wp-event-manager'),

							'desc'  => __('Select the page where you have placed the [submit_organizer_form] shortcode. This lets the plugin know where the form is located.', 'wp-event-manager'),

							'type'  => 'page',
						),
						array(

							'name'  => 'event_manager_organizer_dashboard_page_id',

							'std'   => '',

							'label' => __('Organizer Dashboard Page', 'wp-event-manager'),

							'desc'  => __('Select the page where you have placed the [organizer_dashboard] shortcode. This lets the plugin know where the dashboard is located.', 'wp-event-manager'),

							'type'  => 'page',
						),

						array(

							'name'  => 'event_manager_submit_venue_form_page_id',

							'std'   => '',

							'label' => __('Submit Venue Form Page', 'wp-event-manager'),

							'desc'  => __('Select the page where you have placed the [submit_venue_form] shortcode. This lets the plugin know where the form is located.', 'wp-event-manager'),

							'type'  => 'page',
						),
						array(

							'name'  => 'event_manager_venue_dashboard_page_id',

							'std'   => '',

							'label' => __('Venue Dashboard Page', 'wp-event-manager'),

							'desc'  => __('Select the page where you have placed the [venue_dashboard] shortcode. This lets the plugin know where the dashboard is located.', 'wp-event-manager'),

							'type'  => 'page',
						),

					),
				),
				'date_time_formatting' => array(
					__('Date & Time Format', 'wp-event-manager'),

					array(
						array(

							'name'    => 'event_manager_datepicker_format',

							'std'     => '',

							'label'   => __('Datepicker Date Format', 'wp-event-manager'),

							'desc'    => __('Select the date format to use in datepickers.', 'wp-event-manager'),

							'type'    => 'select',

							'options' => WP_Event_Manager_Date_Time::get_event_manager_date_admin_settings(),
						),

						array(
							'name'    => 'event_manager_timepicker_format',
							'std'     => '12',
							'label'   => __('Timepicker Format', 'wp-event-manager'),

							'desc'    => __('Select the time format to use in timepickers.', 'wp-event-manager'),

							'type'    => 'radio',

							'options' => array(
								'12' => __('12 Hours', 'wp-event-manager'),
								'24' => __('24 Hours', 'wp-event-manager'),
							),
						),
						array(
							'name'  => 'event_manager_timepicker_step',

							'std'   => '30',

							'label' => __('Timepicker Step', 'wp-event-manager'),

							'desc'  => __('Select the time step to use in timepickers. Time step must have to be in between 1 to 60.', 'wp-event-manager'),

							'type'  => 'number',
						),
						array(
							'name'  => 'event_manager_view_date_format',

							'std'   => 'Y-m-d',

							'label' => __('Date Format', 'wp-event-manager'),

							'desc'  => __('This date format will be used at the frontend date display. <a href="https://wordpress.org/support/article/formatting-date-and-time/" target="__blank">For more information click here</a>.', 'wp-event-manager'),

							'type'  => 'text',
						),
						array(
							'name'  => 'event_manager_date_time_format_separator',

							'std'   => '@',

							'label' => __('Date And Time Separator', 'wp-event-manager'),

							'desc'  => __('Add date and time separator.', 'wp-event-manager'),

							'type'  => 'text',
						),
						array(
							'name'    => 'event_manager_timezone_setting',

							'std'     => 'site_timezone',

							'label'   => __('Event Timezone', 'wp-event-manager'),

							'desc'    => __('In case while submitting an event, if the timezone for each event is not selected, the WP Event Manager will take the default WordPress (website) timezone.', 'wp-event-manager'),

							'type'    => 'radio',
							'options' => array(
								'site_timezone' => __('Use website timezone.', 'wp-event-manager'),
								'each_event'    => __('Select a timezone on each event.', 'wp-event-manager'),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings()
	{		$this->init_settings();
		foreach ($this->settings as $section) {
			foreach ($section[1] as $option) {
				if (isset($option['std'])) {
					add_option($option['name'], $option['std']);
				}
				register_setting($this->settings_group, $option['name']);
			}
		}
	}

	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */

	public function output()
	{
		$this->init_settings(); ?>

		<div class="wrap event-manager-settings-wrap">

			<h1 class="wp-heading-inline">
				<?php esc_attr_e('Settings', 'wp-event-manager');?>
			</h1>

			<div class="wpem-wrap event-manager-settings-wrap">

				<form method="post" name="event-manager-settings-form" action="options.php">

					<?php settings_fields($this->settings_group); ?>

					<h2 class="nav-tab-wrapper">

						<?php

						foreach ($this->settings as $key => $section) {

							echo wp_kses_post('<a href="#settings-' . sanitize_title($key) . '" class="nav-tab">' . esc_html($section[0]) . '</a>');
						}
						?>
					</h2>

					<div class="admin-setting-left">

						<div class="white-background">

							<?php if (!empty($_GET['settings-updated'])) {
								flush_rewrite_rules();
								echo wp_kses_post('<div class="updated fade event-manager-updated"><p>' . esc_attr__('Settings successfully saved', 'wp-event-manager') . '</p></div>');
							}

							foreach ($this->settings as $key => $section) {

								echo wp_kses_post('<div id="settings-' . sanitize_title($key) . '" class="settings_panel">');

								echo wp_kses_post('<table class="form-table">');

								foreach ($section[1] as $option) {

									$placeholder = (!empty($option['placeholder'])) ? 'placeholder="' . $option['placeholder'] . '"' : '';

									$class = !empty($option['class']) ? $option['class'] : '';

									$value = get_option($option['name']);

									$option['type'] = !empty($option['type']) ? $option['type'] : '';

									$attributes = array();

									if (!empty($option['attributes']) && is_array($option['attributes'])) {

										foreach ($option['attributes'] as $attribute_name => $attribute_value) {

											$attributes[] = esc_attr($attribute_name) . '="' . esc_attr($attribute_value) . '"';
										}
									}

									echo wp_kses_post('<tr valign="top" class="' . esc_attr($class) . '"><th scope="row"><label for="setting-' . esc_attr($option['name']) . '">' . esc_attr($option['label']) . '</a></th><td>');

									switch ($option['type']) {

										case 'checkbox':?>
											<label><input id="setting-<?php echo esc_attr($option['name']); ?>" name="<?php echo esc_attr($option['name']); ?>" type="checkbox" value="1" <?php echo implode(' ', $attributes); ?> <?php checked('1', $value); ?> /> <?php echo esc_attr($option['cb_label']); ?></label>
											<?php

											if ($option['desc']) {
												echo wp_kses_post(' <p class="description">' . $option['desc'] . '</p>');
											}
											break;

										case 'textarea': ?>
											<textarea id="setting-<?php echo esc_attr($option['name']); ?>" class="large-text" cols="50" rows="<?php echo isset($option['row']) ? esc_attr($option['row']) : 3; ?>" name="<?php echo esc_attr($option['name']); ?>" <?php echo implode(' ', $attributes); ?> <?php echo esc_attr($placeholder); ?>><?php echo esc_textarea($value); ?></textarea>
											<?php

											if ($option['desc']) {?>
												<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
											<?php }
											break;

										case 'select': ?>
											<select id="setting-<?php echo esc_attr($option['name']); ?>" class="regular-text" name="<?php echo esc_attr($option['name']); ?>" <?php echo implode(' ', $attributes); ?>>
												<?php

												foreach ($option['options'] as $key => $name) {
													printf('<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_html($name) . '</option>');
												}
												?>
											</select>
											<?php
											if ($option['desc']) {?>
												<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
										<?php	}
											break;

										case 'multiselect': ?>
											<select id="setting-<?php echo esc_attr($option['name']); ?>" multiple="multiple" class="regular-text" name="<?php echo esc_attr($option['name']); ?>[]" <?php echo implode(' ', $attributes); ?>>
												<?php
												foreach ($option['options'] as $key => $name) {
													$selected = '';
													if (!empty($value)) {
														if (in_array($key, $value)) {
															$selected = ' selected ';
														}
													}
													printf('<option value="' . esc_attr($key) . '" ' . $selected . ' >' . esc_html($name) . '</option>');
												}
												?>
											</select>
											<?php
											if ($option['desc']) {?>
												<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
											<?php }
											break;

										case 'radio':  ?>
											<fieldset>
												<legend class="screen-reader-text">
													<span><?php echo esc_html($option['label']); ?></span>
												</legend>
												<?php

												foreach ($option['options'] as $key => $name) {
													echo '<label><input name="' . esc_attr($option['name']) . '" type="radio" value="' . esc_attr($key) . '" ' . checked($value, $key, false) . ' />' . esc_html($name) . '</label><br>';
												}

												if ($option['desc']) {?>
													<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
												<?php }
												?>
											</fieldset>
										<?php
											break;

										case 'page':
											$args = array(

												'name'        => $option['name'],

												'id'          => $option['name'],

												'sort_column' => 'menu_order',

												'sort_order'  => 'ASC',

												'show_option_none' => __('--no page--', 'wp-event-manager'),

												'echo'        => false,

												'selected'    => absint($value),

											);

											echo str_replace(' id=', " data-placeholder='" . __('Select a page&hellip;', 'wp-event-manager') . "' id=", wp_dropdown_pages($args));

											if ($option['desc']) {?>
												<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
											<?php }
											break;

										case 'password': ?>
											<input id="setting-<?php echo esc_attr($option['name']); ?>" class="regular-text" type="password" name="<?php echo esc_attr($option['name']); ?>" value="<?php esc_attr_e($value); ?>" <?php echo implode(' ', $attributes); ?> <?php echo esc_attr($placeholder); ?> />
											<?php

											if ($option['desc']) { ?>
												<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
											<?php }
											break;

										case '':
										case 'input':
										case 'text': ?>
											<input id="setting-<?php echo esc_attr($option['name']); ?>" class="regular-text" type="text" name="<?php echo esc_attr($option['name']); ?>" value="<?php esc_attr_e($value); ?>" <?php echo implode(' ', $attributes); ?> <?php echo esc_attr($placeholder); ?> />
											<?php

											if ($option['desc']) { ?>
												<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
											<?php }
											break;

										case 'number': ?>
											<input id="setting-<?php echo esc_attr($option['name']); ?>" class="regular-text" type="number" min="0" name="<?php echo esc_attr($option['name']); ?>" value="<?php esc_attr_e($value); ?>" <?php echo implode(' ', $attributes); ?> <?php echo esc_attr($placeholder); ?> />
											<?php

											if ($option['desc']) { ?>
												<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
											<?php }
											break;

										case 'button': ?>
											<button class="button" id="setting-<?php echo esc_attr($option['name']); ?>" class="regular-text" type="button" name="<?php echo esc_attr($option['name']); ?>" <?php echo implode(' ', $attributes); ?> <?php echo esc_attr($placeholder); ?>><?php echo esc_attr($option['cb_label']); ?></button>
											<?php

											if ($option['desc']) { ?>
												<p class="description"><?php echo wp_kses_post($option['desc']);?></p>
											<?php }
											break;

										case 'multi-select-checkbox':
											$this->create_multi_select_checkbox($option);
											break;

										default:
											do_action('wp_event_manager_admin_field_' . $option['type'], $option, $attributes, $value, $placeholder);
											break;
									}?>
									</td></tr>
								<?php } ?>
								</table></div>
							<?php }?>
						</div> <!-- .white-background- -->
						<p class="submit">
							<input type="submit" class="button-primary" id="save-changes" value="<?php esc_attr_e('Save Changes', 'wp-event-manager'); ?>" />
						</p>
					</div> <!-- .admin-setting-left -->

				</form>
				<?php do_action('wpem_admin_seting_side_box_start'); ?>
				<div id="plugin_info" class="box-info">

					<h3><span><?php esc_attr_e('Helpful Resources', 'wp-event-manager'); ?></span></h3>

					<div class="wpem-plugin_info-inside">
						<div class="wpem-setup-help-center-block-wrap">
							<div class="wpem-setup-help-center-block">
								<div class="wpem-setup-help-center-block-icon">
									<span class="wpem-setup-help-center-knowledge-base-icon"></span>
								</div>
								<div class="wpem-setup-help-center-block-content">
									<div class="wpem-setup-help-center-block-heading"><?php esc_attr_e('Knowledge Base', 'wp-event-manager'); ?></div>
									<div class="wpem-setup-help-center-block-desc"><?php esc_attr_e('Solve your queries by browsing our documentation.', 'wp-event-manager'); ?></div>
									<a href="https://wp-eventmanager.com/knowledge-base" target="_blank" class="wpem-setup-help-center-block-link"><span class="wpem-setup-help-center-box-target-text"><?php esc_attr_e('Browse More', 'wp-event-manager'); ?> »</span></a>
								</div>
							</div>
							<div class="wpem-setup-help-center-block">
								<div class="wpem-setup-help-center-block-icon">
									<span class="wpem-setup-help-center-faqs-icon"></span>
								</div>
								<div class="wpem-setup-help-center-block-content">
									<div class="wpem-setup-help-center-block-heading"><?php esc_attr_e('FAQs', 'wp-event-manager'); ?></div>
									<div class="wpem-setup-help-center-block-desc"><?php esc_attr_e('Explore through the frequently asked questions.', 'wp-event-manager'); ?></div>
									<a href="https://wp-eventmanager.com/faqs" target="_blank" class="wpem-setup-help-center-block-link"><span class="wpem-setup-help-center-box-target-text"><?php esc_attr_e('Get Answers', 'wp-event-manager'); ?> »</span></a>
								</div>
							</div>
							<div class="wpem-setup-help-center-block">
								<div class="wpem-setup-help-center-block-icon">
									<span class="wpem-setup-help-center-video-tutorial-icon"></span>
								</div>
								<div class="wpem-setup-help-center-block-content">
									<div class="wpem-setup-help-center-block-heading"><?php esc_attr_e('Video Tutorials', 'wp-event-manager'); ?></div>
									<div class="wpem-setup-help-center-block-desc"><?php esc_attr_e('Learn different skills by examining attractive video tutorials.', 'wp-event-manager'); ?></div>
									<a href="https://www.youtube.com/channel/UCnfYxg-fegS_n9MaPNU61bg" target="_blank" class="wpem-setup-help-center-block-link"><span class="wpem-setup-help-center-box-target-text"><?php esc_attr_e('Watch all', 'wp-event-manager'); ?> »</span></a>
								</div>
							</div>
						</div>
						<span class="light-grey"><?php esc_attr_e('Powered By', 'wp-event-manager'); ?></span> <a href="https://wp-eventmanager.com/" target="_blank"><img src="<?php echo EVENT_MANAGER_PLUGIN_URL; ?>/assets/images/wpem-logo.svg" alt="WP Event Manager"></a>
					</div>
				</div>
				<?php do_action('wpem_admin_seting_side_box_end'); ?>
			</div>
		</div>
<?php
		wp_enqueue_script('wp-event-manager-admin-settings');
	}

	/**
	 * Creates Multiselect checkbox.
	 * This function generate multiselect
	 *
	 * @param $value
	 * @return void
	 */
	public function create_multi_select_checkbox($value)
	{ ?>
		<ul class="mnt-checklist" id="<?php echo esc_attr($value['name']);?>" >
		<?php foreach ($value['options'] as $option_value => $option_list) {
			$checked = ' ';
			if (get_option($value['name'])) {

				$all_country  = get_option($value['name']);
				$start_string = strpos($option_list['name'], '[');
				$country_code = substr($option_list['name'], $start_string + 1, 2);
				$coutry_exist = array_key_exists($country_code, $all_country);
				if ($coutry_exist) {
					$checked = " checked='checked' ";
				}
			}?>
			<li>

			<input id="setting-<?php echo esc_attr($option_list['name']);?>" name="<?php echo esc_attr($option_list['name']);?>" type="checkbox" <?php echo esc_attr($checked);?> />
				<?php echo esc_attr($option_list['cb_label']); ?>
			</li>
		<?php } ?>
		</ul>
	<?php }
}