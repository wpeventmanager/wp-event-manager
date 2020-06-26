<?php
/*
* This file is use to create a sortcode of wp event manager plugin. 
* This file include sortcode of event listing,event submit form and event dashboard etc.
*/
?>
<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * WP_Event_Manager_Shortcodes class.
 */

class WP_Event_Manager_Shortcodes {

	private $event_dashboard_message = '';
	private $organizer_dashboard_message = '';
	private $venue_dashboard_message = '';
	
	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'wp', array( $this, 'shortcode_action_handler' ) );

		add_action( 'event_manager_event_dashboard_content_edit', array( $this, 'edit_event' ) );
		add_action( 'event_manager_organizer_dashboard_content_edit', array( $this, 'edit_organizer' ) );
		add_action( 'event_manager_venue_dashboard_content_edit', array( $this, 'edit_venue' ) );

		add_action( 'event_manager_event_filters_end', array( $this, 'event_filter_results' ), 30 );
		add_action( 'event_manager_output_events_no_results', array( $this, 'output_no_results' ) );

		add_action( 'single_event_listing_organizer_action_start', array( $this, 'organizer_more_info_link' ) );

		add_shortcode( 'submit_event_form', array( $this, 'submit_event_form' ) );
		add_shortcode( 'event_dashboard', array( $this, 'event_dashboard' ) );

		//hide the shortcode if organizer or venue not enabled
		if(get_option('enable_event_organizer')){
			add_shortcode( 'submit_organizer_form', array( $this, 'submit_organizer_form' ) );
			add_shortcode( 'organizer_dashboard', array( $this, 'organizer_dashboard' ) );
		}
		
		if(get_option('enable_event_venue')){
			add_shortcode( 'submit_venue_form', array( $this, 'submit_venue_form' ) );
			add_shortcode( 'venue_dashboard', array( $this, 'venue_dashboard' ) );
		}
		
		add_shortcode( 'events', array( $this, 'output_events' ) );
		add_shortcode( 'event', array( $this, 'output_event' ) );
		add_shortcode( 'event_summary', array( $this, 'output_event_summary' ) );
		add_shortcode( 'past_events', array( $this, 'output_past_events' ) );
		add_shortcode( 'event_register', array( $this, 'output_event_register' ) );

		add_shortcode( 'event_organizers', array( $this, 'output_event_organizers' ) );
	}

	/**
	 * Handle actions which need to be run before the shortcode e.g. post actions
	 */
	public function shortcode_action_handler() {

		global $post;
		
		if ( is_page() && strstr( $post->post_content, '[event_dashboard' ) ) {
			$this->event_dashboard_handler();
		}
		elseif ( is_page() && strstr( $post->post_content, '[organizer_dashboard' )) {
			$this->organizer_dashboard_handler();
		}
		elseif ( is_page() && strstr( $post->post_content, '[venue_dashboard' )) {
			$this->venue_dashboard_handler();
		}
	}
	
	/**
	 * Show the event submission form
	*/
	public function submit_event_form( $atts = array() ) {

		return $GLOBALS['event_manager']->forms->get_form( 'submit-event', $atts );
	}
	
	/**
	 * Show the organizer submission form
	 */
	public function submit_organizer_form( $atts = array() ) {

		return $GLOBALS['event_manager']->forms->get_form( 'submit-organizer', $atts );
	}

	/**
	 * Show the organizer submission form
	 */
	public function submit_venue_form( $atts = array() ) {

		return $GLOBALS['event_manager']->forms->get_form( 'submit-venue', $atts );
	}

	/**
	 * Handles actions on event dashboard
	 */
	public function event_dashboard_handler() {

		if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'event_manager_my_event_actions' ) ) {

			$action = sanitize_title( $_REQUEST['action'] );

			$event_id = absint( $_REQUEST['event_id'] );

			try {

				// Get Event

				$event    = get_post( $event_id );

				// Check ownership

				if ( ! event_manager_user_can_edit_event( $event_id ) ) {

					throw new Exception( __( 'Invalid ID', 'wp-event-manager' ) );
				}

				switch ( $action ) {

					case 'mark_cancelled' :

						// Check status

						if ( $event->_cancelled == 1 )

							throw new Exception( __( 'This event has already been cancelled', 'wp-event-manager' ) );

						// Update

						update_post_meta( $event_id, '_cancelled', 1 );

						// Message

						$this->event_dashboard_message = '<div class="event-manager-message wpem-alert wpem-alert-success">' . sprintf( __( '%s has been cancelled', 'wp-event-manager' ), esc_html( $event->post_title ) ) . '</div>';

						break;

					case 'mark_not_cancelled' :

						// Check status
						if ( $event->_cancelled != 1 ) {

							throw new Exception( __( 'This event is not cancelled', 'wp-event-manager' ) );

						}

						// Update
						update_post_meta( $event_id, '_cancelled', 0 );
						
						// Message
						$this->event_dashboard_message = '<div class="event-manager-message wpem-alert wpem-alert-success">' . sprintf( __( '%s has been marked as not cancelled', 'wp-event-manager' ), esc_html( $event->post_title ) ) . '</div>';

						break;

					case 'delete' :

						// Trash it
						wp_trash_post( $event_id );

						// Message
						$this->event_dashboard_message = '<div class="event-manager-message wpem-alert wpem-alert-danger">' . sprintf( __( '%s has been deleted', 'wp-event-manager' ), esc_html( $event->post_title ) ) . '</div>';

						break;
					case 'duplicate' :
						if ( ! event_manager_get_permalink( 'submit_event_form' ) ) {
							throw new Exception( __( 'Missing submission page.', 'wp-event-manager' ) );
						}
					
						$new_event_id = event_manager_duplicate_listing( $event_id );
					
						if ( $new_event_id ) {
							wp_redirect( add_query_arg( array( 'event_id' => absint( $new_event_id ) ), event_manager_get_permalink( 'submit_event_form' ) ) );
							exit;
						}
					
					break;

					case 'relist' :

						// redirect to post page

						wp_redirect( add_query_arg( array( 'event_id' => absint( $event_id ) ), event_manager_get_permalink( 'submit_event_form' ) ) );

						break;

					default :

						do_action( 'event_manager_event_dashboard_do_action_' . $action );

						break;
				}
				
				do_action( 'event_manager_my_event_do_action', $action, $event_id );

			} catch ( Exception $e ) {

				$this->event_dashboard_message = '<div class="event-manager-error wpem-alert wpem-alert-danger">' . $e->getMessage() . '</div>';
			}
		}
	}
	
	/**
	 * Shortcode which lists the logged in user's events
	 */	 
	public function event_dashboard( $atts ) {

		if ( ! is_user_logged_in() ) {

			ob_start();

			get_event_manager_template( 'event-dashboard-login.php' );

			return ob_get_clean();
		}
		
		extract( shortcode_atts( array(

			'posts_per_page' => '25',

		), $atts ) );

		wp_enqueue_script( 'wp-event-manager-event-dashboard' );

		ob_start();

		// If doing an action, show conditional content if needed....

		if ( ! empty( $_REQUEST['action'] ) ) {

			$action = sanitize_title( $_REQUEST['action'] );

			// Show alternative content if a plugin wants to

			if ( has_action( 'event_manager_event_dashboard_content_' . $action ) ) {

				do_action( 'event_manager_event_dashboard_content_' . $action, $atts );

				return ob_get_clean();
			}
		}
		
		// ....If not show the event dashboard

		$args     = apply_filters( 'event_manager_get_dashboard_events_args', array(

			'post_type'           => 'event_listing',
			'post_status'         => array( 'publish', 'expired', 'pending' ),
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $posts_per_page,
			'offset'              => ( max( 1, get_query_var('paged') ) - 1 ) * $posts_per_page,
			'orderby'             => 'date',
			'order'               => 'desc',
			'author'              => get_current_user_id()

		) );

		$events = new WP_Query;
		echo $this->event_dashboard_message;

		$event_dashboard_columns = apply_filters( 'event_manager_event_dashboard_columns', array(

			'event_title' => __( 'Title', 'wp-event-manager' ),
			'event_location' => __( 'Location', 'wp-event-manager' ),
			'event_start_date' => __( 'Start Date', 'wp-event-manager' ),
			'event_end_date' => __( 'End Date', 'wp-event-manager' ),
			'view_count' => __( 'Viewed', 'wp-event-manager' ),
			'event_action' => __( 'Action', 'wp-event-manager' ), 
		) );

		get_event_manager_template( 'event-dashboard.php', array( 'events' => $events->query( $args ), 'max_num_pages' => $events->max_num_pages, 'event_dashboard_columns' => $event_dashboard_columns ) );

		return ob_get_clean();
	}

	/**
	 * Edit event form
	 */
	public function edit_event() {

		global $event_manager;

		echo $event_manager->forms->get_form( 'edit-event' );
	}

	/**
	 * Handles actions on organizer dashboard
	 */
	public function organizer_dashboard_handler() {

		if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'event_manager_my_organizer_actions' ) ) {

			$action = sanitize_title( $_REQUEST['action'] );

			$organizer_id = absint( $_REQUEST['organizer_id'] );

			try {

				// Get Event

				$event    = get_post( $organizer_id );

				// Check ownership

				if ( ! event_manager_user_can_edit_event( $organizer_id ) ) {

					throw new Exception( __( 'Invalid ID', 'wp-event-manager' ) );
				}

				switch ( $action ) {

					

					case 'delete' :

						// Trash it
						wp_trash_post( $organizer_id );

						// Message
						$this->organizer_dashboard_message = '<div class="event-manager-message wpem-alert wpem-alert-danger">' . sprintf( __( '%s has been deleted', 'wp-event-manager' ), esc_html( $event->post_title ) ) . '</div>';

						break;
					case 'duplicate' :
						if ( ! event_manager_get_permalink( 'submit_organizer_form' ) ) {
							throw new Exception( __( 'Missing submission page.', 'wp-event-manager' ) );
						}
					
						$new_organizer_id = event_manager_duplicate_listing( $organizer_id );
					
						if ( $new_organizer_id ) {
							// Puslish organizer
						  	$my_post = array(
						      	'ID'           => $new_organizer_id,
						      	'post_status'   => 'publish',
						  	);
							  
							// Update the post into the database
							wp_update_post( $my_post );

							wp_redirect( add_query_arg( array( 'organizer_id' => absint( $new_organizer_id ) ), event_manager_get_permalink( 'submit_organizer_form' ) ) );
							exit;
						}
					
					break;

					default :

						do_action( 'event_manager_organizer_dashboard_do_action_' . $action );

						break;
				}
				
				do_action( 'event_manager_my_organizer_do_action', $action, $organizer_id );

			} catch ( Exception $e ) {

				$this->organizer_dashboard_message = '<div class="event-manager-error wpem-alert wpem-alert-danger">' . $e->getMessage() . '</div>';
			}
		}
	}

	/**
	 * Shortcode which lists the logged in user's organizers
	 */	 
	public function organizer_dashboard( $atts ) {

		if ( ! is_user_logged_in() ) {

			ob_start();

			get_event_manager_template( 'event-dashboard-login.php' );

			return ob_get_clean();
		}
		
		extract( shortcode_atts( array(

			'posts_per_page' => '25',

		), $atts ) );

		wp_enqueue_script( 'wp-event-manager-organizer-dashboard' );

		ob_start();

		// If doing an action, show conditional content if needed....

		if ( ! empty( $_REQUEST['action'] ) ) {

			$action = sanitize_title( $_REQUEST['action'] );

			// Show alternative content if a plugin wants to

			if ( has_action( 'event_manager_organizer_dashboard_content_' . $action ) ) {

				do_action( 'event_manager_organizer_dashboard_content_' . $action, $atts );

				return ob_get_clean();
			}
		}
		
		// ....If not show the event dashboard

		$args     = apply_filters( 'event_manager_get_dashboard_organizers_args', array(

			'post_type'           => 'event_organizer',
			'post_status'         => array( 'publish' ),
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $posts_per_page,
			'offset'              => ( max( 1, get_query_var('paged') ) - 1 ) * $posts_per_page,
			'orderby'             => 'date',
			'order'               => 'desc',
			'author'              => get_current_user_id()

		) );

		$organizers = new WP_Query;
		echo $this->organizer_dashboard_message;

		$organizer_dashboard_columns = apply_filters( 'event_manager_organizer_dashboard_columns', array(

			'organizer_name' => __( 'Organizer name', 'wp-event-manager' ),

			'organizer_events' => __( 'Events', 'wp-event-manager' ),
			
			'organizer_action' => __( 'Action', 'wp-event-manager' ), 
		) );

		get_event_manager_template( 'organizer-dashboard.php', array( 'organizers' => $organizers->query( $args ), 'max_num_pages' => $organizers->max_num_pages, 'organizer_dashboard_columns' => $organizer_dashboard_columns ) );

		return ob_get_clean();
	}

	/**
	 * Edit event form
	 */
	public function edit_organizer() {

		global $event_manager;

		echo $event_manager->forms->get_form( 'edit-organizer' );
	}

	/**
	 * Handles actions on venue dashboard
	 */
	public function venue_dashboard_handler() {

		if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'event_manager_my_venue_actions' ) ) {

			$action = sanitize_title( $_REQUEST['action'] );

			$venue_id = absint( $_REQUEST['venue_id'] );

			try {

				// Get Event

				$venue    = get_post( $venue_id );

				// Check ownership

				if ( ! event_manager_user_can_edit_event( $venue_id ) ) {

					throw new Exception( __( 'Invalid ID', 'wp-event-manager' ) );
				}

				switch ( $action ) {

					

					case 'delete' :

						// Trash it
						wp_trash_post( $venue_id );

						// Message
						$this->venue_dashboard_message = '<div class="event-manager-message wpem-alert wpem-alert-danger">' . sprintf( __( '%s has been deleted', 'wp-event-manager' ), esc_html( $venue->post_title ) ) . '</div>';

						break;
					case 'duplicate' :
						if ( ! event_manager_get_permalink( 'submit_venue_form' ) ) {
							throw new Exception( __( 'Missing submission page.', 'wp-event-manager' ) );
						}
					
						$new_venue_id = event_manager_duplicate_listing( $venue_id );
					
						if ( $new_venue_id ) {
							// Puslish organizer
							$my_post = array(
							    'ID'           => $new_venue_id,
							    'post_status'   => 'publish',
							);
							  
							// Update the post into the database
							wp_update_post( $my_post );

							wp_redirect( add_query_arg( array( 'venue_id' => absint( $new_venue_id ) ), event_manager_get_permalink( 'submit_venue_form' ) ) );
							exit;
						}
					
					break;

					default :

						do_action( 'event_manager_venue_dashboard_do_action_' . $action );

						break;
				}
				
				do_action( 'event_manager_my_venue_do_action', $action, $venue_id );

			} catch ( Exception $e ) {

				$this->venue_dashboard_message = '<div class="event-manager-error wpem-alert wpem-alert-danger">' . $e->getMessage() . '</div>';
			}
		}
	}

	/**
	 * Shortcode which lists the logged in user's venues
	 */	 
	public function venue_dashboard( $atts ) {

		if ( ! is_user_logged_in() ) {

			ob_start();

			get_event_manager_template( 'event-dashboard-login.php' );

			return ob_get_clean();
		}
		
		extract( shortcode_atts( array(

			'posts_per_page' => '25',

		), $atts ) );

		//wp_enqueue_script( 'wp-event-manager-venue-dashboard' );

		ob_start();

		// If doing an action, show conditional content if needed....

		if ( ! empty( $_REQUEST['action'] ) ) {

			$action = sanitize_title( $_REQUEST['action'] );

			// Show alternative content if a plugin wants to

			if ( has_action( 'event_manager_venue_dashboard_content_' . $action ) ) {

				do_action( 'event_manager_venue_dashboard_content_' . $action, $atts );

				return ob_get_clean();
			}
		}
		
		// ....If not show the event dashboard

		$args     = apply_filters( 'event_manager_get_dashboard_venue_args', array(

			'post_type'           => 'event_venue',
			'post_status'         => array( 'publish' ),
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $posts_per_page,
			'offset'              => ( max( 1, get_query_var('paged') ) - 1 ) * $posts_per_page,
			'orderby'             => 'date',
			'order'               => 'desc',
			'author'              => get_current_user_id()

		) );

		$venues = new WP_Query;
		echo $this->venue_dashboard_message;

		$venue_dashboard_columns = apply_filters( 'event_manager_venue_dashboard_columns', array(

			'venue_name' => __( 'Venue name', 'wp-event-manager' ),

			'venue_events' => __( 'Events', 'wp-event-manager' ),
			
			'venue_action' => __( 'Action', 'wp-event-manager' ), 
		) );

		get_event_manager_template( 'venue-dashboard.php', array( 'venues' => $venues->query( $args ), 'max_num_pages' => $venues->max_num_pages, 'venue_dashboard_columns' => $venue_dashboard_columns ) );

		return ob_get_clean();
	}

	/**
	 * Edit venue form
	 */
	public function edit_venue() {

		global $event_manager;

		echo $event_manager->forms->get_form( 'edit-venue' );
	}

	/**
	 * output_events function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function output_events( $atts ) {

		ob_start();

		extract( $atts = shortcode_atts( apply_filters( 'event_manager_output_events_defaults', array(

			'per_page'                  => get_option( 'event_manager_per_page' ),

			'orderby'                   => 'meta_value', // meta_value

			'order'                     => 'ASC',

			// Filters + cats

			'show_filters'              => true,			

			'show_categories'           => true,

			'show_event_types'          => true,

			'show_ticket_prices'        => true,

			'show_category_multiselect' => get_option( 'event_manager_enable_default_category_multiselect', false ),

			'show_event_type_multiselect' => get_option( 'event_manager_enable_default_event_type_multiselect', false ),

			'show_pagination'           => false,

			'show_more'                 => true,

			// Limit what events are shown based on category and type

			'categories'                => '',

			'event_types'               => '',

			'ticket_prices'             => '',

			'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.

			'cancelled'                 => null, // True to show only cancelled, false to hide cancelled, leave null to show both/use the settings.

			// Default values for filters

			'location'                  => '',

			'keywords'                  => '',

			'selected_datetime'         => '',

			'selected_category'         => '',

			'selected_event_type'       => '',

			'selected_ticket_price'      => '',
		    
		    'layout_type'      => 'all',

		) ), $atts ) );

		//Categories

		if ( ! get_option( 'event_manager_enable_categories' ) ) {

			$show_categories = false;

		}

		//Event types

		if ( ! get_option( 'event_manager_enable_event_types' ) ) {

			$show_event_types = false;

		}

		//Event ticket prices		

		if ( ! get_option( 'event_manager_enable_event_ticket_prices' ) ) {

			$show_ticket_prices = false;

		}

		// String and bool handling

		$show_filters              = $this->string_to_bool( $show_filters );

		$show_categories           = $this->string_to_bool( $show_categories );

		$show_event_types          = $this->string_to_bool( $show_event_types );

		$show_ticket_prices        = $this->string_to_bool( $show_ticket_prices );

		$show_category_multiselect = $this->string_to_bool( $show_category_multiselect );

		$show_event_type_multiselect= $this->string_to_bool( $show_event_type_multiselect);

		$show_more                 = $this->string_to_bool( $show_more );

		$show_pagination           = $this->string_to_bool( $show_pagination );
		
		//order by meta value and it will take default sort order by start date of event
		if ( is_null( $orderby ) ||  empty($orderby ) ) {
			$orderby  = 'meta_value';
		}
		
		if ( ! is_null( $featured ) ) {

			$featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;
		}

		if ( ! is_null( $cancelled ) ) {

			$cancelled = ( is_bool( $cancelled ) && $cancelled ) || in_array( $cancelled, array( '1', 'true', 'yes' ) ) ? true : false;
		}
		
		//set value for the event datetimes

		$datetimes=WP_Event_Manager_Filters::get_datetimes_filter();

		//Set value for the ticket prices		

		$ticket_prices	=	WP_Event_Manager_Filters::get_ticket_prices_filter();

		// Array handling

		$datetimes            = is_array( $datetimes) ? $datetimes: array_filter( array_map( 'trim', explode( ',', $datetimes) ) );		

		$categories           = is_array( $categories ) ? $categories : array_filter( array_map( 'trim', explode( ',', $categories ) ) );

		$event_types          = is_array( $event_types ) ? $event_types : array_filter( array_map( 'trim', explode( ',', $event_types ) ) );

		$ticket_prices        = is_array( $ticket_prices) ? $ticket_prices: array_filter( array_map( 'trim', explode( ',', $ticket_prices) ) );

		// Get keywords, location, datetime, category, event type and ticket price from query string if set

		if ( ! empty( $_GET['search_keywords'] ) ) {

			$keywords = sanitize_text_field( $_GET['search_keywords'] );
		}

		if ( ! empty( $_GET['search_location'] ) ) {

			$location = sanitize_text_field( $_GET['search_location'] );
		}

		if ( ! empty( $_GET['search_datetime'] ) ) {

			$selected_datetime = sanitize_text_field( $_GET['search_datetime'] );
		}

		if ( ! empty( $_GET['search_category'] ) ) {

			$selected_category = sanitize_text_field( $_GET['search_category'] );
		}

		if ( ! empty( $_GET['search_event_type'] ) ) {

			$selected_event_type = sanitize_text_field( $_GET['search_event_type'] );
		}

		if ( ! empty( $_GET['search_ticket_price'] ) ) {

			$selected_ticket_price = sanitize_text_field( $_GET['search_ticket_price'] );
		}

		if ( $show_filters ) {

			get_event_manager_template( 'event-filters.php', array( 

										'per_page' => $per_page, 

										'orderby' => $orderby, 

										'order' => $order, 

										'datetimes' => $datetimes,

										'selected_datetime' => $selected_datetime , 

										'show_categories' => $show_categories, 

										'show_category_multiselect' => $show_category_multiselect,

										'categories' => $categories,

										'selected_category' => $selected_category, 

										'show_event_types' => $show_event_types ,

										'show_event_type_multiselect' => $show_event_type_multiselect,

										'event_types' => $event_types, 

										'selected_event_type' => $selected_event_type, 

										'show_ticket_prices' => $show_ticket_prices ,

										'ticket_prices' => $ticket_prices, 

										'selected_ticket_price' => $selected_ticket_price , 

										'atts' => $atts, 

										'location' => $location, 

										'keywords' => $keywords,						
										
									      ));

			get_event_manager_template( 'event-listings-start.php',array('layout_type'=>$layout_type) );

			get_event_manager_template( 'event-listings-end.php' );

			if ( ! $show_pagination && $show_more ) {

				echo '<a class="load_more_events" id="load_more_events" href="#" style="display:none;"><strong>' . __( 'Load more events', 'wp-event-manager' ) . '</strong></a>';
			}
			
		} else {
		    
			$events = get_event_listings( apply_filters( 'event_manager_output_events_args', array(

				'search_location'   => $location,

				'search_keywords'   => $keywords,

				'search_datetimes'  => $datetimes,

				'search_categories' => $categories,

				'search_event_types'       => $event_types,

				'search_ticket_prices'       => $ticket_prices,

				'orderby'           => $orderby,

				'order'             => $order,

				'posts_per_page'    => $per_page,

				'featured'          => $featured,

				'cancelled'         => $cancelled

			) ) );

			if ( $events->have_posts() ) : ?>

				<?php get_event_manager_template( 'event-listings-start.php' ,array('layout_type'=>$layout_type)); ?>			

				<?php while ( $events->have_posts() ) : $events->the_post(); ?>

					<?php  get_event_manager_template_part( 'content', 'event_listing' ); ?>
					
				<?php endwhile; ?>

				<?php get_event_manager_template( 'event-listings-end.php' ); ?>

				<?php if ( $events->found_posts > $per_page && $show_more ) : ?>

					<?php wp_enqueue_script( 'wp-event-manager-ajax-filters' ); ?>

					<?php if ( $show_pagination ) : ?>

						<?php echo get_event_listing_pagination( $events->max_num_pages ); ?>

					<?php else : ?>

						<a class="load_more_events" id="load_more_events" href="#"><strong><?php _e( 'Load more listings', 'wp-event-manager' ); ?></strong></a>

					<?php endif; ?>

				<?php endif; ?>

			<?php else :

				do_action( 'event_manager_output_events_no_results' );

			endif;

			wp_reset_postdata();
		}

		$data_attributes_string = '';

		$data_attributes        = array(

			'location'        => $location,

			'keywords'        => $keywords,

			'show_filters'    => $show_filters ? 'true' : 'false',

			'show_pagination' => $show_pagination ? 'true' : 'false',

			'per_page'        => $per_page,

			'orderby'         => $orderby,

			'order'           => $order,

			'datetimes'       => $selected_datetime,

			'categories'      => !empty($selected_category) ? implode( ',', $selected_category ) : '',

			'event_types'     => !empty($selected_event_type) ? implode( ',', $selected_event_type) : '',

			'ticket_prices'   => !empty($selected_ticket_price) ? $selected_ticket_price : ''
		);

		if ( ! is_null( $featured ) ) {

			$data_attributes[ 'featured' ] = $featured ? 'true' : 'false';
		}

		if ( ! is_null( $cancelled ) ) {

			$data_attributes[ 'cancelled' ]   = $cancelled ? 'true' : 'false';
		}

		foreach ( $data_attributes as $key => $value ) {

			$data_attributes_string .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
		}
		
		$event_listings_output = apply_filters( 'event_manager_event_listings_output', ob_get_clean() );

		return '<div class="event_listings" ' . $data_attributes_string . '>' . $event_listings_output . '</div>';
	}

	/**
	 * Output some content when no results were found
	 */
	public function output_no_results() {

		get_event_manager_template( 'content-no-events-found.php' );
	}

	/**
	 * Output anchor tag close: single organizer details url
	 */
	public function organizer_more_info_link( $organizer_id ) {

		global $post;
		
		if ( ! $post || 'event_listing' !== $post->post_type ) {
			return;
		}

		if(isset($organizer_id) && !empty($organizer_id))
		{	
			$organizer_url = get_permalink( $organizer_id );

			if(isset($organizer_url) && !empty($organizer_url))
			{
				printf( '<div class="wpem-organizer-page-url-button"><a href="%s" class="wpem-theme-button"><span>%s</span></a></div>',  get_permalink( $organizer_id ), __( 'More info', 'wp-event-manager' ) );	
			}
		}
	}
	
	/**
	 * Get string as a bool
	 * @param  string $value
	 * @return bool
	 */
	public function string_to_bool( $value ) {

		return ( is_bool( $value ) && $value ) || in_array( $value, array( '1', 'true', 'yes' ) ) ? true : false;
	}

	/**
	 * Show results div
	 */
	public function event_filter_results() {

		echo '<div class="showing_applied_filters"></div>';
	}

	/**
	 * output_event function.
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_event( $atts ) {
	    
		extract( shortcode_atts( array(
		    
			'id' => '',

		), $atts ) );

		if ( ! $id )

			return;
			
		ob_start();

		$args = array(

			'post_type'   => 'event_listing',

			'post_status' => 'publish',

			'p'           => $id
		);

		$events = new WP_Query( $args );

		if ( $events->have_posts() ) : ?>

			<?php while ( $events->have_posts() ) : $events->the_post(); ?>
				
				<div class="clearfix" />
                <?php get_event_manager_template_part( 'content-single', 'event_listing' ); ?>

			<?php endwhile; ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="event_shortcode single_event_listing">' . ob_get_clean() . '</div>';
	}
	
	/**
	 * Event Summary shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_event_summary( $atts ) {

		extract( shortcode_atts( array(

			'id'       => '',	
			'width'    => '250px',
			'align'    => 'left',
			'featured' => null, // True to show only featured, false to hide featured, leave null to show both (when leaving out id)

			'limit'    => 1

		), $atts ) );

		ob_start();
		
		$args = array(

			'post_type'   => 'event_listing',

			'post_status' => 'publish'
		);


		if ( ! $id ) {

			$args['posts_per_page'] = $limit;

			$args['orderby']        = 'rand';

			if ( ! is_null( $featured ) ) {

				$args['meta_query'] = array( array(

					'key'     => '_featured',

					'value'   => '1',

					'compare' => $featured ? '=' : '!='
				) );
			}
			
		} else {

			$args['p'] = absint( $id );
		}

		$events = new WP_Query( $args );

		if ( $events->have_posts() ) : ?>

			<?php while ( $events->have_posts() ) : $events->the_post();

				echo '<div class="event_summary_shortcode align' . esc_attr( $align ) . '" style="width: ' . esc_attr( $width ) . '">';

				get_event_manager_template_part( 'content-summary', 'event_listing' );

				echo '</div>';

			endwhile; ?>

		<?php endif;

		wp_reset_postdata();

		return ob_get_clean();
	}
	
	/**
	 * Show the registration area
	 */
	public function output_event_register( $atts ) {
		extract( shortcode_atts( array(
			'id'       => ''
		), $atts ) );

		ob_start();

		$args = array(
			'post_type'   => 'event_listing',
			'post_status' => 'publish'
		);

		if ( ! $id ) {
			return '';
		} else {
			$args['p'] = absint( $id );
		}

		$events = new WP_Query( $args );

		if ( $events->have_posts() ) : ?>

			<?php while ( $events->have_posts() ) : $events->the_post(); ?>

				<div class="event-manager-registration-wrapper">
					<?php
						$register = get_event_registration_method();
						do_action( 'event_manager_registration_details_' . $register->type, $register );
					?>
				</div>

			<?php endwhile; ?>

		<?php endif;

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * output_events function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function output_past_events( $atts ) {

		ob_start();

		extract( shortcode_atts ( array(

			'show_pagination'           => true,

			'per_page'                  => get_option( 'event_manager_per_page' ),

			'order'                     => 'DESC',

			'orderby'                   => 'meta_value', // meta_value

			'meta_key'  				=> 'event_start_date',

			'location'                  => '',

			'keywords'                  => '',

			'selected_datetime'         => '',

			'selected_categories'       => '',

			'selected_event_types'     => '',
		), $atts ) );

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		if(substr( $meta_key, 0, 1 ) !== "_")
		{
			$meta_key = '_'.$meta_key;
		}
		
		$args_past = array(
			'post_type'  	=> 'event_listing',
			'post_status'	=> array('expired'),
			'posts_per_page' => $per_page,
			'paged'			=> $paged,
			'order'			=> $order,
			'orderby'		=> $orderby,
			'meta_key'		=> $meta_key,
		);

		if(!empty($keywords))
		{
			$args_past['s'] = $keywords;
		}

		if(!empty($selected_categories))
		{
			$categories = explode(',', sanitize_text_field($selected_categories) );

			$args_past['tax_query'][] = [
				'taxonomy'	=> 'event_listing_category',
				'field'   	=> 'name',
				'terms'   	=> $categories,
			];
		}

		if(!empty($selected_event_types))
		{
			$event_types = explode(',', sanitize_text_field($selected_event_types) );

			$args_past['tax_query'][] = [
				'taxonomy'	=> 'event_listing_type',
				'field'   	=> 'name',
				'terms'   	=> $event_types,
			];
		}

		if(!empty($selected_datetime))
		{
			$datetimes = explode(',', $selected_datetime);

			$args_past['meta_query'][] = [
				'key' => '_event_start_date',
				'value'   => $datetimes,
				'compare' => 'BETWEEN',
				'type'    => 'date'
			];
		}

		if(!empty($location))
		{
			$args_past['meta_query'][] = [
				'key' 		=> '_event_location',
				'value'  	=> $location,
				'compare'	=> 'LIKE'
			];
		}

		$past_events = new WP_Query( $args_past );

		wp_reset_query();

		if ( $past_events->have_posts() ) : ?>
			<div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view">	
			<?php while ( $past_events->have_posts() ) : $past_events->the_post(); ?>

				<?php  get_event_manager_template_part( 'content', 'past_event_listing' ); ?>
				
			<?php endwhile; ?>

			<?php if ($past_events->found_posts > $per_page) : ?>
                <?php if ($show_pagination == "true") : ?>
                    <div class="event-organizer-pagination">
                    	<?php get_event_manager_template('pagination.php', array('max_num_pages' => $past_events->max_num_pages)); ?>
                    </div> 
                <?php endif; ?>
            <?php endif; ?>

			</div>
		<?php else :

			do_action( 'event_manager_output_events_no_results' );

		endif;

		wp_reset_postdata();
		
		$event_listings_output = apply_filters( 'event_manager_event_listings_output', ob_get_clean() );

		return  $event_listings_output;
		
	}

	/**
	 *  It is very simply a plugin that outputs a list of all organizers that have listed events on your website. 
     *  Once you have installed " WP Event Manager - Organizer Profiles" simply visit "Pages > Add New". 
     *  Once you have added a title to your page add the this shortcode: [event_organizers]
     *  This will output a grouped and alphabetized list of all organizers.
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_event_organizers($atts)
	{
		$orgnizers   = get_all_organizer_array();
		$countAllEvents = get_event_organizer_count();        
        $orgnizers_array = [];

        if(!empty($orgnizers))
        {
        	foreach ( $orgnizers as $orgnizer_id => $orgnizer )
        	{
        		$orgnizers_array[ strtoupper( $orgnizer[0] ) ][$orgnizer_id] = $orgnizer;
        	}
        }        
         
		wp_enqueue_script( 'wp-event-manager-organizer');
        
        get_event_manager_template( 
      		'event-organizers.php', 
      		array(
				'orgnizers'			=> $orgnizers,
				'orgnizers_array'   => $orgnizers_array,
            	'countAllEvents'    => $countAllEvents,
			), 
			'wp-event-manager', 
			EVENT_MANAGER_PLUGIN_DIR . '/templates/organizer/' 
		);
              
		wp_reset_postdata();
		
		return ob_get_clean();
	}


}

new WP_Event_Manager_Shortcodes(); ?>
