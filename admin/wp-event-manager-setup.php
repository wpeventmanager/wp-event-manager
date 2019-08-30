<?php
/*
* From admin panel, setuping post event page, event dashboard page and event listings page.
*
*/

if ( ! defined( 'ABSPATH' ) ) {
    
	exit;
}

/**
 * WP_Event_Manager_Setup class.
*/

class WP_Event_Manager_Setup {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );

		add_action( 'admin_head', array( $this, 'admin_head' ) );

		add_action( 'admin_init', array( $this, 'redirect' ) );

		if ( isset( $_GET['page'] ) && 'event-manager-setup' === $_GET['page'] ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 12 );
		}
	}

	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */

	public function admin_menu() {

		add_dashboard_page( __( 'Setup', 'wp-event-manager' ), __( 'Setup', 'wp-event-manager' ), 'manage_options', 'event-manager-setup', array( $this, 'output' ) );
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	 
	public function admin_head() {

		remove_submenu_page( 'index.php', 'event-manager-setup' );
	}

	/**
	 * Sends user to the setup page on first activation
	 */
	 
	public function redirect() {

		// Bail if no activation redirect transient is set

	    if ( ! get_transient( '_event_manager_activation_redirect' ) ) {

			return;
	    }

	    if ( ! current_user_can( 'manage_options' ) ) {
	    	
	    	return;
	    }

		// Delete the redirect transient
		delete_transient( '_event_manager_activation_redirect' );

		// Bail if activating from network, or bulk, or within an iFrame
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) ) {

			return;
		}

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'wp-event-manager.php' ) ) ) {

			return;
		}

		wp_redirect( admin_url( 'index.php?page=event-manager-setup' ) );
		
		exit;
	}

	/**
	 * Enqueue scripts for setup page
	 */

	public function admin_enqueue_scripts() {
	    
		wp_enqueue_style( 'event_manager_setup_css', EVENT_MANAGER_PLUGIN_URL . '/assets/css/setup.css', array( 'dashicons' ) );
	}

	/**
	 * Create a page.
	 * @param  string $title
	 * @param  string $content
	 * @param  string $option
	 */
	 
	public function create_page( $title, $content, $option ) {

		$page_data = array(

			'post_status'    => 'publish',

			'post_type'      => 'page',

			'post_author'    => 1,

			'post_name'      => sanitize_title( $title ),

			'post_title'     => $title,

			'post_content'   => $content,

			'post_parent'    => 0,

			'comment_status' => 'closed'
		);

		$page_id = wp_insert_post( $page_data );

		if ( $option ) {

			update_option( $option, $page_id );
		}
	}

	/**
	 * Output addons page
	 */
	 
	public function output() {

		$step = ! empty( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;

		if ( 3 === $step && ! empty( $_POST ) ) 
		{
			if ( false == wp_verify_nonce( $_REQUEST[ 'setup_wizard' ], 'step_3' ) )
				wp_die( __('Error in nonce. Try again.', 'wp-event-manager') );

			$create_pages    = isset( $_POST['wp-event-manager-create-page'] ) ? $_POST['wp-event-manager-create-page'] : array();

			$page_titles     = $_POST['wp-event-manager-page-title'];

			$pages_to_create = array(

				'submit_event_form' => '[submit_event_form]',

				'event_dashboard'   => '[event_dashboard]',

				'events'            => '[events]'
			);

			foreach ( $pages_to_create as $page => $content ) {

				if ( ! isset( $create_pages[ $page ] ) || empty( $page_titles[ $page ] ) ) {

					continue;
				}

				$this->create_page( sanitize_text_field( $page_titles[ $page ] ), $content, 'event_manager_' . $page . '_page_id' );
			}
		}

		?>

		<div class="wrap wp_event_manager wp_event_manager_addons_wrap">
		
			<h2><?php _e( 'WP Event Manager Setup', 'wp-event-manager' ); ?></h2>

			<ul class="wp-event-manager-setup-steps">

				<li class="<?php if ( $step === 1 ) echo 'wp-event-manager-setup-active-step'; ?>"><?php _e( '1. Introduction', 'wp-event-manager' ); ?></li>

				<li class="<?php if ( $step === 2 ) echo 'wp-event-manager-setup-active-step'; ?>"><?php _e( '2. Page Setup', 'wp-event-manager' ); ?></li>

				<li class="<?php if ( $step === 3 ) echo 'wp-event-manager-setup-active-step'; ?>"><?php _e( '3. Done', 'wp-event-manager' ); ?></li>

			</ul>

			<?php if ( 1 === $step ) : ?>

				<h3><?php _e( 'Setup Wizard Introduction', 'wp-event-manager' ); ?></h3>

				<p><?php _e( 'Thanks for installing <em>WP Event Manager</em>!', 'wp-event-manager' ); ?></p>

				<p><?php _e( 'This setup wizard will help you get started by creating the pages for event submission, event management, and listing your events.', 'wp-event-manager' ); ?></p>

				<p><?php printf( __( 'If you want to skip the wizard and setup the pages and shortcodes yourself manually, the process is still relatively simple. Refer to the %sdocumentation%s for help.', 'wp-event-manager' ), '<a href="http://www.wp-eventmanager.com/plugins-documentation/wp-event-manager/">', '</a>' ); ?></p>

				<p class="submit">

					<a href="<?php echo esc_url( add_query_arg( 'step', 2 ) ); ?>" class="button button-primary"><?php _e( 'Continue to page setup', 'wp-event-manager' ); ?></a>

					<a href="<?php echo esc_url( add_query_arg( 'skip-event-manager-setup', 1, admin_url( 'index.php?page=event-manager-setup&step=3' ) ) ); ?>" class="button"><?php _e( 'Skip setup. I will setup the plugin manually', 'wp-event-manager' ); ?></a>

				</p>
				
			<?php endif; ?>

			<?php if ( 2 === $step ) : ?>

				<h3><?php _e( 'Page Setup', 'wp-event-manager' ); ?></h3>

				<p><?php printf( __( '<em>WP Event Manager</em> includes %1$sshortcodes%2$s which can be used within your %3$spages%2$s to output content. These can be created for you below. For more information on the event shortcodes view the %4$sshortcode documentation%2$s.', 'wp-event-manager' ), '<a href="http://codex.wordpress.org/Shortcode" title="What is a shortcode?" target="_blank" class="help-page-link">', '</a>', '<a href="http://codex.wordpress.org/Pages" target="_blank" class="help-page-link">', '<a href="http://www.wp-eventmanager.com/plugins-documentation/wp-event-manager/" target="_blank" class="help-page-link">' ); ?></p>

				<form action="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>" method="post">
					<?php wp_nonce_field( 'step_3', 'setup_wizard' ); ?>
					<table class="wp-event-manager-shortcodes widefat">
					
						<thead>
						
							<tr>		
								<th>&nbsp;</th>

								<th><?php _e( 'Page Title', 'wp-event-manager' ); ?></th>

								<th><?php _e( 'Page Description', 'wp-event-manager' ); ?></th>

								<th><?php _e( 'Content Shortcode', 'wp-event-manager' ); ?></th>
							</tr>

						</thead>

						<tbody>

							<tr>

								<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[submit_event_form]" /></td>

								<td><input type="text" value="<?php echo esc_attr( _x( 'Post an Event', 'Default page title (wizard)', 'wp-event-manager' ) ); ?>" name="wp-event-manager-page-title[submit_event_form]" /></td>

								<td>
									<p><?php _e( 'This page allows peoples to post events to your website from the front-end.', 'wp-event-manager' ); ?></p>

									<p><?php _e( 'If you do not want to accept submissions from users in this way (for example you just want to post events from the admin dashboard) you can skip creating this page.', 'wp-event-manager' ); ?></p>
								</td>
								<td><code>[submit_event_form]</code></td>
							</tr>
							<tr>

								<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[event_dashboard]" /></td>

								<td><input type="text" value="<?php echo esc_attr( _x( 'Event Dashboard', 'Default page title (wizard)', 'wp-event-manager' ) ); ?>" name="wp-event-manager-page-title[event_dashboard]" /></td>

								<td>

									<p><?php _e( 'This page allows peoples to manage and edit their own events from the front-end.', 'wp-event-manager' ); ?></p>

									<p><?php _e( 'If you plan on managing all listings from the admin dashboard you can skip creating this page.', 'wp-event-manager' ); ?></p>
								</td>
								
								<td><code>[event_dashboard]</code></td>
							</tr>
							
							<tr>
								<td><input type="checkbox" checked="checked" name="wp-event-manager-create-page[events]" /></td>

								<td><input type="text" value="<?php echo esc_attr( _x( 'Events', 'Default page title (wizard)', 'wp-event-manager' ) ); ?>" name="wp-event-manager-page-title[events]" /></td>

								<td><?php _e( 'This page allows users to browse, search, and filter event listings on the front-end of your site.', 'wp-event-manager' ); ?></td>

								<td><code>[events]</code></td>
							</tr>

						</tbody>

						<tfoot>

							<tr>
								<th colspan="4">
									<input type="submit" class="button button-primary" value="Create selected pages" />

									<a href="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>" class="button"><?php _e( 'Skip this step', 'wp-event-manager' ); ?></a>
								</th>
							</tr>
						</tfoot>
					</table>
				</form>
				
			<?php endif; ?>

			<?php if ( 3 === $step ) : ?>

				<h3><?php _e( 'All Done!', 'wp-event-manager' ); ?></h3>
				
				<p><?php _e( 'Looks like you\'re all set to start using the plugin. In case you\'re wondering where to go next:', 'wp-event-manager' ); ?></p>

				<ul class="wp-event-manager-next-steps">

					<li><a href="<?php echo admin_url( 'edit.php?post_type=event_listing&page=event-manager-settings' ); ?>"><?php _e( 'Tweak the plugin settings', 'wp-event-manager' ); ?></a></li>

					<li><a href="<?php echo admin_url( 'post-new.php?post_type=event_listing' ); ?>"><?php _e( 'Add a event via the back-end', 'wp-event-manager' ); ?></a></li>

					<?php if ( $permalink = event_manager_get_permalink( 'submit_event_form' ) ) : ?>

						<li><a href="<?php echo esc_url( $permalink ); ?>"><?php _e( 'Add a event via the front-end', 'wp-event-manager' ); ?></a></li>

					<?php else : ?>

						<li><a href="http://www.wp-eventmanager.com/plugins-documentation/wp-event-manager/the-event-submission-form/"><?php _e( 'Find out more about the front-end event submission form', 'wp-event-manager' ); ?></a></li>

					<?php endif; ?>

					<?php if ( $permalink = event_manager_get_permalink( 'events' ) ) : ?>

						<li><a href="<?php echo esc_url( $permalink ); ?>"><?php _e( 'View submitted event listings', 'wp-event-manager' ); ?></a></li>

					<?php else : ?>

						<li><a href="http://www.wp-eventmanager.com/plugins-documentation/wp-event-manager/the-event-listings/"><?php _e( 'Add the [events] shortcode to a page to list events', 'wp-event-manager' ); ?></a></li>

					<?php endif; ?>

					<?php if ( $permalink = event_manager_get_permalink( 'event_dashboard' ) ) : ?>

						<li><a href="<?php echo esc_url( $permalink ); ?>"><?php _e( 'View the event dashboard', 'wp-event-manager' ); ?></a></li>

					<?php else : ?>

						<li><a href="http://www.wp-eventmanager.com/plugins-documentation/wp-event-manager/the-event-dashboard/"><?php _e( 'Find out more about the front-end event dashboard', 'wp-event-manager' ); ?></a></li>

					<?php endif; ?>

				</ul>

				<p><?php printf( __( 'And don\'t forget, if you need any more help using <em>WP Event Manager</em> you can consult the %1$sdocumentation%2$s or %3$spost on the forums%2$s!', 'wp-event-manager' ), '<a href="http://www.wp-eventmanager.com/plugins-documentation/wp-event-manager/">', '</a>', '<a href="https://wordpress.org/support/plugin/wp-event-manager">' ); ?></p>

				<div class="wp-event-manager-support-the-plugin">

					<h3><?php _e( 'Support the Ongoing Development of this Plugin', 'wp-event-manager' ); ?></h3>

					<p><?php _e( 'There are many ways to support open-source projects such as WP Event Manager, for example code contribution, translation, or even telling your friends how awesome the plugin (hopefully) is. Thanks in advance for your support - it is much appreciated!', 'wp-event-manager' ); ?></p>

					<ul>

						<li class="icon-review"><a href="https://wordpress.org/support/view/plugin-reviews/wp-event-manager#postform"><?php _e( 'Leave a positive review', 'wp-event-manager' ); ?></a></li>

						<li class="icon-localization"><a href="https://translate.wordpress.org/projects/wp-plugins/wp-event-manager"><?php _e( 'Contribute a localization', 'wp-event-manager' ); ?></a></li>

						<li class="icon-code"><a href="http://www.wp-eventmanager.com/forum/"><?php _e( 'Contribute code or report a bug', 'wp-event-manager' ); ?></a></li>

						<li class="icon-forum"><a href="https://wordpress.org/support/plugin/wp-event-manager"><?php _e( 'Help other users on the forums', 'wp-event-manager' ); ?></a></li>

					</ul>

				</div>

			<?php endif; ?>

		</div>

		<?php
	}
}
new WP_Event_Manager_Setup();
