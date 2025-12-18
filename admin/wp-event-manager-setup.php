<?php
/*
* From admin panel, setuping post event page, event dashboard page and event listings page.
*
*/
if(!defined('ABSPATH')) {
	exit;
}

/**
 * WP_Event_Manager_Setup class.
 */
class WP_Event_Manager_Setup {

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('admin_menu', array($this, 'admin_menu'), 12);
		add_action('admin_head', array($this, 'admin_head'));
		add_action('admin_init', array($this, 'redirect'));
		if(isset($_GET['page']) && 'event-manager-setup' === sanitize_text_field( wp_unslash($_GET['page']))) {
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 12);
		}
		add_action('wp_ajax_wpem_save_installation_settings', array($this,'wpem_save_installation_settings'));
	}

	/**
	 * Admin menu.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_dashboard_page(__('Setup', 'wp-event-manager'), __('Setup', 'wp-event-manager'), 'manage_options', 'event-manager-setup', array($this, 'output'));
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page('index.php', 'event-manager-setup');
	}

	/**
	 * Sends user to the setup page on first activation.
	 */
	public function redirect() {
		global $pagenow;

		if(isset($_GET['page']) && sanitize_text_field( wp_unslash($_GET['page'])) === 'event-manager-setup') {
			if(get_option('wpem_installation', false)) {
				wp_safe_redirect( admin_url( 'index.php' ) );
				exit;
			}
		}
		// Bail if no activation redirect transient is set
		if(!get_transient('_event_manager_activation_redirect')) {
			return;
		}
		if(!current_user_can('manage_options')) {
			return;
		}
		// Delete the redirect transient
		delete_transient('_event_manager_activation_redirect');
		// Bail if activating from network, or bulk, or within an iFrame
		if(is_network_admin() || isset($_GET['activate-multi']) || defined('IFRAME_REQUEST')) {
			return;
		}
		if((isset($_GET['action']) && 'upgrade-plugin' == sanitize_text_field( wp_unslash($_GET['action']))) && (isset($_GET['plugin']) && strstr(sanitize_text_field( wp_unslash($_GET['plugin'])), 'wp-event-manager.php'))) {
			return;
		}
		wp_safe_redirect(admin_url('index.php?page=event-manager-setup'));
		exit;
	}

	/**
	 * Enqueue scripts for setup page.
	 */
	public function admin_enqueue_scripts()	{
		wp_enqueue_style('event_manager_setup_css', EVENT_MANAGER_PLUGIN_URL . '/assets/css/setup.min.css', array('dashicons'));
		wp_enqueue_script('event_manager_setup_js', EVENT_MANAGER_PLUGIN_URL . '/assets//js/setup.min.js', array('jquery'), null, true);

		wp_localize_script('event_manager_setup_js', 'wpem_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'   => wp_create_nonce('wpem_save_installation_settings_nonce')
		));
	}

	/**
	 * Create a page.
	 *
	 * @param  string $title
	 * @param  string $content
	 * @param  string $option
	 */
	public function create_page($title, $content, $option) {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => sanitize_title($title),
			'post_title'     => $title,
			'post_content'   => $content,
			'post_parent'    => 0,
			'comment_status' => 'closed',
		);
		$page_id = wp_insert_post($page_data);
		if($option) {
			update_option($option, $page_id);
		}
	}

	/**
	 * Output addons page.
	 */
	public function output() {
		$step = !empty($_GET['step']) ? absint($_GET['step']) : 1;
		$wpem_url = esc_url(get_option('wp_event_manager_store_url'));
		if(isset($_GET['skip-event-manager-setup']) && sanitize_text_field( wp_unslash($_GET['skip-event-manager-setup'])) === 1) {
			update_option('wpem_installation', 0);
			update_option('wpem_installation_skip', 1);
			wp_safe_redirect(admin_url('index.php'));
			exit;
		}

		if(3 === $step && !empty($_POST)) {
			if(!isset($_REQUEST['setup_wizard']) || false == wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['setup_wizard'])), 'step_3')) {
				wp_die(esc_attr__('Error in nonce. Try again.', 'wp-event-manager'));
			}
			$create_pages = isset($_POST['wp-event-manager-create-page']) ? $this->sanitize_array(wp_unslash($_POST['wp-event-manager-create-page'])) : array();
			$page_titles = isset($_POST['wp-event-manager-page-title']) ? $this->sanitize_array(wp_unslash($_POST['wp-event-manager-page-title'])) : array();
			$pages_to_create = array(
				'submit_event_form'     => '[submit_event_form]',
				'event_dashboard'       => '[event_dashboard]',
				'events'                => '[events]',
				'submit_organizer_form' => '[submit_organizer_form]',
				'organizer_dashboard'   => '[organizer_dashboard]',
				'event_organizers'      => '[event_organizers]',
				'submit_venue_form'     => '[submit_venue_form]',
				'venue_dashboard'       => '[venue_dashboard]',
				'event_venues'          => '[event_venues]',
			);
			foreach ($pages_to_create as $page => $content) {
				if(!isset($create_pages[$page]) || empty($page_titles[$page])) {
					continue;
				}
				$this->create_page($page_titles[$page], $content, 'event_manager_' . $page . '_page_id');
			}
			update_option('wpem_installation', 1);
			update_option('wpem_installation_skip', 0);
		} ?>

		<div class="wp_event_manager wp_event_manager_addons_wrap">
			<h2><?php esc_attr_e('WP Event Manager Setup', 'wp-event-manager'); ?></h2>
			<div class="wpem-setup-wrapper">
				<ul class="wp-event-manager-setup-steps">
					<?php if($step === 1) : ?>
						<li class="wp-event-manager-setup-active-step"><?php esc_attr_e('1. Introduction', 'wp-event-manager'); ?></li>
						<li><?php esc_attr_e('2. Page Setup', 'wp-event-manager'); ?></li>
						<li><?php esc_attr_e('3. Done', 'wp-event-manager'); ?></li>
					<?php elseif($step === 2) : ?>
						<li class="wp-event-manager-setup-active-step"><?php esc_attr_e('1. Introduction', 'wp-event-manager'); ?></li>
						<li class="wp-event-manager-setup-active-step"><?php esc_attr_e('2. Page Setup', 'wp-event-manager'); ?></li>
						<li><?php esc_attr_e('3. Done', 'wp-event-manager'); ?></li>
					<?php elseif($step === 3) : ?>
						<li class="wp-event-manager-setup-active-step"><?php esc_attr_e('1. Introduction', 'wp-event-manager'); ?></li>
						<li class="wp-event-manager-setup-active-step"><?php esc_attr_e('2. Page Setup', 'wp-event-manager'); ?></li>
						<li class="wp-event-manager-setup-active-step"><?php esc_attr_e('3. Done', 'wp-event-manager'); ?></li>
					<?php endif; ?>
				</ul>
				<?php if(1 === $step) : ?>
					<div class="wpem-step-window">
						<img src="<?php echo esc_url(EVENT_MANAGER_PLUGIN_URL.'/assets/images/wpem-setup-welcome-image.png'); ?>" alt="WP Event Manager Welcome Setup" />
						<h3><?php esc_attr_e('Welcome to WP Event Manager!', 'wp-event-manager'); ?></h3>
						<p><?php esc_attr_e('Thank you for trusting WP Event Manager to manage your upcoming events. Now you can effortlessly manage unlimited online and offline events independently.', 'wp-event-manager'); ?></p>
						<p><?php
							// translators: %1$s and %2$s wrap "documentation" in a hyperlink to the support page.
							printf(esc_attr__('If you want to avoid the Setup wizard and want to creates pages manually, you can refer to the %1$sdocumentation%2$s for support.', 'wp-event-manager'), '<a href="'.esc_url($wpem_url).'help-center/">', '</a>'); 
						?></p>
					</div>
					<p class="submit">
						<a href="<?php echo esc_url(add_query_arg('step', 2)); ?>" class="button button-primary"><?php esc_attr_e('Continue to page setup', 'wp-event-manager'); ?></a>
						<a href="<?php echo esc_url(add_query_arg('skip-event-manager-setup', 1, admin_url('index.php?page=event-manager-setup&step=3'))); ?>" class="button button-border"><?php esc_attr_e('Skip for now', 'wp-event-manager'); ?></a>
					</p>
				<?php endif; ?>
				<?php if(2 === $step) : ?>
					<h3><?php esc_attr_e('Page Setup', 'wp-event-manager'); ?></h3>
					<p><?php 
						// translators: %1$s is the opening tag for shortcodes documentation link; %2$s is the closing tag for that link; %3$s is the opening tag for pages documentation link; %4$s is the opening tag for event shortcodes documentation link. 
						printf(esc_html('The WP Event Manager includes %1$sshortcodes%2$s which can be used to output content within your %3$spages%2$s. These can be generated directly as mentioned below. Check the shortcode documentation for more information on event %4$sshortcodes%2$s.', 'wp-event-manager'), '<a href="'.esc_url($wpem_url).'knowledge-base/" title="What is a shortcode?" target="_blank" class="help-page-link">', '</a>', '<a href="https://wordpress.org/support/article/pages/" target="_blank" class="help-page-link">', '<a href="'.esc_url($wpem_url).'knowledge-base/" target="_blank" class="help-page-link">'); 
					?></p>
					<form action="<?php echo esc_url(add_query_arg('step', 3)); ?>" method="post">
						<?php wp_nonce_field('step_3', 'setup_wizard'); ?>
						<div class="wpem-table-responsive">
							<table class="wp-event-manager-shortcodes widefat">
								<thead>
									<tr>
										<th>&nbsp;</th>
										<th><?php esc_attr_e('Page Title', 'wp-event-manager'); ?></th>
										<th><?php esc_attr_e('Page Description', 'wp-event-manager'); ?></th>
										<th><?php esc_attr_e('Content Shortcode', 'wp-event-manager'); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[submit_event_form]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Post an Event', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[submit_event_form]" /></td>
										<td>
											<p><?php esc_attr_e('This page allows peoples to add events to your website from the front-end.', 'wp-event-manager'); ?></p>
											<p><?php esc_attr_e('If you do not wish to accept submissions from users in this way (for example you just want to post events from the admin dashboard) you can skip creating this page.', 'wp-event-manager'); ?></p>
										</td>
										<td><code>[submit_event_form]</code></td>
									</tr>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[event_dashboard]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Event Dashboard', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[event_dashboard]" /></td>
										<td>
											<p><?php esc_attr_e('This page allows peoples to manage (edit, delete and duplicate) their own events from the front-end.', 'wp-event-manager'); ?></p>
											<p><?php esc_attr_e('If you plan on managing all listings from the admin dashboard you can skip creating this page.', 'wp-event-manager'); ?></p>
										</td>
										<td><code>[event_dashboard]</code></td>
									</tr>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[events]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Events', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[events]" /></td>
										<td><?php esc_attr_e('This page allows users to browse, search, and filter event listings on the front-end of your site.', 'wp-event-manager'); ?></td>
										<td><code>[events]</code></td>
									</tr>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[submit_organizer_form]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Submit Organizer Form', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[submit_organizer_form]" /></td>
										<td>
											<p><?php esc_attr_e('This page allows users to Submit the organizers form the frontend.', 'wp-event-manager'); ?></p>
											<p><?php esc_attr_e('In case if you do not want to allow your users to submit organizers from the frontend, you can uncheck this and skip creating this page.', 'wp-event-manager'); ?></p>
										</td>
										<td><code>[submit_organizer_form]</code></td>
									</tr>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[organizer_dashboard]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Organizer Dashboard', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[organizer_dashboard]" /></td>
										<td>
											<p><?php esc_attr_e('This page allows people to manage (edit, delete and duplicate) the organizers form the frontend.', 'wp-event-manager'); ?></p>
											<p><?php esc_attr_e('In case if you do not want to allow your users to manage organizers from the frontend, you can uncheck this and skip creating this page.', 'wp-event-manager'); ?></p>
										</td>
										<td><code>[organizer_dashboard]</code></td>
									</tr>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[event_organizers]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Event Organizers', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[event_organizers]" /></td>
										<td>
											<p><?php esc_attr_e('This page allows peoples to show organizers from the front-end.', 'wp-event-manager'); ?></p>
											<p><?php esc_attr_e('In case if you do not want to allow your users to show organizers from the frontend, you can uncheck this and skip creating this page.', 'wp-event-manager'); ?></p>
										</td>
										<td><code>[event_organizers]</code></td>
									</tr>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[submit_venue_form]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Submit Venue Form', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[submit_venue_form]" /></td>
										<td>
											<p><?php esc_attr_e('This page allows people to Submit the venues from the frontend.', 'wp-event-manager'); ?></p>
											<p><?php esc_attr_e('In case if you do not want to allow your users to submit venues from the frontend, you can uncheck this and skip creating this page.', 'wp-event-manager'); ?></p>
										</td>
										<td><code>[submit_venue_form]</code></td>
									</tr>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[venue_dashboard]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Venue Dashboard', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[venue_dashboard]" /></td>
										<td>
											<p><?php esc_attr_e('This page allows people to manage (edit, delete and duplicate) the venues form the frontend.', 'wp-event-manager'); ?></p>
											<p><?php esc_attr_e('In case if you do not want to allow your users to manage venues from the frontend, you can uncheck this and skip creating this page.', 'wp-event-manager'); ?></p>
										</td>
										<td><code>[venue_dashboard]</code></td>
									</tr>
									<tr>
										<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[event_venues]" /></td>
										<td><input type="text" value="<?php echo esc_attr(_x('Event Venues', 'Default page title (wizard)', 'wp-event-manager')); ?>" name="wp-event-manager-page-title[event_venues]" /></td>
										<td>
											<p><?php esc_attr_e('This page allows peoples to show venues from the front-end.', 'wp-event-manager'); ?></p>
											<p><?php esc_attr_e('In case if you do not want to allow your users to show venues from the frontend, you can uncheck this and skip creating this page.', 'wp-event-manager'); ?></p>
										</td>
										<td><code>[event_venues]</code></td>
									</tr>
								</tbody>
							</table>
						</div>
						<p class="submit">
							<input type="submit" class="button button-primary" value="Create selected pages" />
							<a href="<?php echo esc_url(add_query_arg('step', 3)); ?>" class="button button-border"><?php esc_attr_e('Skip this step', 'wp-event-manager'); ?></a>
						</p>
					</form>
				<?php endif; ?>
				<?php if(3 === $step) : ?>
					<div class="wpem-setup-intro-block">
						<div class="wpem-setup-end-step-settings">
							<h4>Settings</h4>
							<form method="post">
							<div class="wpem-setup-end-step-setting">
								<label>Set Date Format:</label>
								<select name="wpem_date_format">
									<?php 
									$date_formats = WP_Event_Manager_Date_Time::get_event_manager_date_admin_settings();
									if ( ! empty( $date_formats ) ) :
										foreach ( $date_formats as $key => $format ) : ?>
											<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $format ); ?></option>
										<?php endforeach;
									endif; ?>
								</select>
							</div>
							<div class="wpem-setup-end-step-setting">
								<label>Set Time Format:</label>
								<select name="wpem-time-format">
									<option value="12">12h</option>
									<option value="24">24h</option>
								</select>
							</div>
							<div class="wpem-setup-end-step-block">
								<div class="wpem-setup-end-step-link"><a href="<?php echo esc_url(admin_url('post-new.php?post_type=event_listing')); ?>" target="_blank"><i class="wpem-icon-plus"></i><?php esc_attr_e('Create new Event', 'wp-event-manager'); ?></a></div>
								<div class="wpem-setup-end-step-link"><a href="<?php echo esc_url(admin_url('post-new.php?post_type=event_organizer')); ?>" target="_blank"><i class="wpem-icon-user"></i><?php esc_attr_e('Create new Organizer', 'wp-event-manager'); ?></a></div>
								<div class="wpem-setup-end-step-link"><a href="<?php echo esc_url(admin_url('post-new.php?post_type=event_venue')); ?>" target="_blank"><i class="wpem-icon-location"></i><?php esc_attr_e('Create new Venue', 'wp-event-manager'); ?></a></div>
							</div>
						</div>
						<div class="wpem-setup-help-center">
							<h1><?php esc_attr_e('Helpful Resources', 'wp-event-manager'); ?></h1>
							<div class="wpem-setup-help-center-block-wrap">
								<div class="wpem-setup-help-center-block">
									<div class="wpem-setup-help-center-block-icon">
										<span class="wpem-setup-help-center-knowledge-base-icon"></span>
									</div>
									<div class="wpem-setup-help-center-block-content">
										<div class="wpem-setup-help-center-block-heading"><?php esc_attr_e('Knowledge Base', 'wp-event-manager'); ?></div>
										<div class="wpem-setup-help-center-block-desc"><?php esc_attr_e('Solve your queries by browsing our documentation.', 'wp-event-manager'); ?></div>
										<a href="<?php echo esc_url($wpem_url);?>knowledge-base" target="_blank" class="wpem-setup-help-center-block-link"><span class="wpem-setup-help-center-box-target-text"><?php esc_attr_e('Browse More', 'wp-event-manager'); ?> »</span></a>
									</div>
								</div>
								<div class="wpem-setup-help-center-block">
									<div class="wpem-setup-help-center-block-icon">
										<span class="wpem-setup-help-center-faqs-icon"></span>
									</div>
									<div class="wpem-setup-help-center-block-content">
										<div class="wpem-setup-help-center-block-heading"><?php esc_attr_e('FAQs', 'wp-event-manager'); ?></div>
										<div class="wpem-setup-help-center-block-desc"><?php esc_attr_e('Explore through the frequently asked questions.', 'wp-event-manager'); ?></div>
										<a href="<?php echo esc_url($wpem_url);?>faqs" target="_blank" class="wpem-setup-help-center-block-link"><span class="wpem-setup-help-center-box-target-text"><?php esc_attr_e('Get Answers', 'wp-event-manager'); ?> »</span></a>
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
							<div class="wpem-setup-addon-support">
									<div class="wpem-setup-addon-support-wrap">
										<div class="wpem-setup-help-center-block-icon">
											<span class="wpem-setup-help-center-support-icon"></span>
										</div>
										<div class="wpem-setup-help-center-block-content">
											<div class="wpem-setup-help-center-block-heading"><?php esc_attr_e('Add ons Support', 'wp-event-manager'); ?></div>
											<div class="wpem-setup-help-center-block-desc"><?php esc_attr_e('Get support for all the Add ons related queries with our experienced/ talented support team.', 'wp-event-manager'); ?></div>
											<a href="https://support.wp-eventmanager.com/" target="_blank" class="wpem-setup-help-center-block-link"><span class="wpem-setup-help-center-box-target-text"><?php esc_attr_e('Get Add ons Support', 'wp-event-manager'); ?> »</span></a>
										</div>
									</div>
								</div>
						</div>
						<p class="submit">
							<a href="#" name="wpem_save_installation_settings" id="wpem_save_installation_settings" class="button button-primary"><?php esc_attr_e('Finish Setup', 'wp-event-manager'); ?></a>
						</p>
						</form>
					</div>
				<?php endif; ?>
			</div>
		</div>
<?php
	}

	/**
	 * Sanitize a 2d array.
	 *
	 * @param  array $array
	 * @return array
	 */
	private function sanitize_array($input)	{
		if(is_array($input)) {
			foreach ($input as $k => $v) {
				$input[$k] = $this->sanitize_array($v);
			}
			return $input;
		} else {
			return sanitize_text_field($input);
		}
	}

	/**
	 * Saving settings.
	 *
	 * @param  array $array
	 * @return array
	 */
	public function wpem_save_installation_settings() {
		if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'wpem_save_installation_settings_nonce' ) ) {
			wp_send_json_error(array('message' => 'Invalid nonce'));
			exit;
		}

		$date_format = isset( $_POST['date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['date_format'] ) ) : '';
		$time_format = isset( $_POST['time_format'] ) ? sanitize_text_field( wp_unslash( $_POST['time_format'] ) ) : '';

		if ( ! empty( $date_format ) ) {
			update_option('event_manager_datepicker_format', $date_format);
		}
		if ( ! empty( $time_format ) ) {
			update_option('event_manager_timepicker_format', $time_format);
		}
		update_option('wpem_installation', 1);
		wp_send_json_success(array(
			'message' => 'Settings saved successfully!',
			'redirect_url' => admin_url('index.php')
		));
	}

}
new WP_Event_Manager_Setup();