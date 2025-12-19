<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/*
* This file the functionality of ajax for event listing and file upload.
*/ 

if(!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * WP_Event_Manager_Ajax class.
*/
class WP_Event_Manager_Ajax {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  2.5
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  2.5
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	*/
	public function __construct() {
		add_action('init', array(__CLASS__, 'add_endpoint'));
		add_action('template_redirect', array(__CLASS__, 'do_em_ajax'), 0);

		// EM Ajax endpoints
		add_action('event_manager_ajax_get_listings', array($this, 'get_listings'));
		add_action('event_manager_ajax_upload_file', array($this, 'upload_file'));
		add_action('event_manager_ajax_load_more_upcoming_events', array($this, 'load_more_upcoming_events'));
		add_action('event_manager_ajax_load_more_past_listings', array($this, 'load_more_past_events'));
		add_action('event_manager_ajax_get_upcoming_listings', array($this, 'get_upcoming_listings'));

		// BW compatible handlers
		add_action('wp_ajax_nopriv_event_manager_get_listings', array($this, 'get_listings'));
		add_action('wp_ajax_event_manager_get_listings', array($this, 'get_listings'));
		add_action('wp_ajax_nopriv_event_manager_upload_file', array($this, 'upload_file'));
		add_action('wp_ajax_event_manager_upload_file', array($this, 'upload_file'));
		add_action('wp_ajax_add_organizer', array($this, 'add_organizer'));
		add_action('wp_ajax_nopriv_add_organizer', array($this, 'add_organizer'));

		add_action('wp_ajax_add_venue', array($this, 'add_venue'));
		add_action('wp_ajax_nopriv_add_venue', array($this, 'add_venue'));
	}
	
	/**
	 * Add our endpoint for frontend ajax requests.
	*/
	public static function add_endpoint() {

		add_rewrite_tag('%em-ajax%', '([^/]*)');
		add_rewrite_rule('em-ajax/([^/]*)/?', 'index.php?em-ajax=$matches[1]', 'top');
		add_rewrite_rule('index.php/em-ajax/([^/]*)/?', 'index.php?em-ajax=$matches[1]', 'top');
	}

	/**
	 * Get Event Manager Ajax Endpoint.
	 * @param  string $request Optional
	 * @param  string $ssl     Optional
	 * @return string
	 */
	public static function get_endpoint($request = '%%endpoint%%', $ssl = null) {

		if(strstr(get_option('permalink_structure'), '/index.php/')) {
			$endpoint = trailingslashit(home_url('/index.php/em-ajax/' . $request . '/', 'relative'));
		} elseif(get_option('permalink_structure')) {
			$endpoint = trailingslashit(home_url('/em-ajax/' . $request . '/', 'relative'));
		} else {
			$endpoint = add_query_arg('em-ajax', $request, trailingslashit(home_url('', 'relative')));
		}
		return esc_url_raw($endpoint);
	}

	/**
	 * Handle AJAX requests for WP Event Manager.
	 *
	 * @since 1.0.0
	 */
	public static function do_em_ajax() {
		global $wp_query;
		
		// Verify nonce for AJAX action
		$ajax_action = '';
		if ( ! empty( $_GET['em-ajax'] ) ) {
			$nonce_verified = false;
			if ( ! empty( $_GET['_wpnonce'] ) ) {
				$nonce_verified = wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'event_manager_ajax' );
			}
			if ( $nonce_verified ) {
				$ajax_action = sanitize_text_field(wp_unslash($_GET['em-ajax']));
				$wp_query->set('em-ajax', $ajax_action);
			}
		}
		$action = $wp_query->get( 'em-ajax' );
   		if( $action ) {
   			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}
			// Not home - this is an ajax endpoint
			$wp_query->is_home = false;
   			do_action('event_manager_ajax_' . sanitize_key($action));
   			wp_die();
   		}
	}

	/**
	 * Load more upcoming events
	 */
	function load_more_upcoming_events($atts) {
		// Verify nonce for AJAX request
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'event_manager_ajax' ) ) {
			wp_send_json_error(array('error' => 'Invalid nonce'));
			return;
		}
		
		$paged = isset($_POST['value']) ? intval(wp_unslash($_POST['value'])) : 1;
		$per_page = isset($_POST['per_page']) ? intval(wp_unslash($_POST['per_page'])) : esc_attr(get_option('event_manager_per_page'));
		$orderby = isset($_POST['orderby']) ? sanitize_text_field(wp_unslash($_POST['orderby'])) : 'date';
		$order = isset($_POST['order']) ? sanitize_text_field(wp_unslash($_POST['order'])) : 'DESC';

		$args = array(
			'post_type'      => 'event_listing',
			'post_status'    => array('publish'),
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'order'           => $order,
			'orderby'         => $orderby,
			'meta_query'     => array(
				array(
					'relation' => 'OR',
					array(
						'key'     => '_event_start_date',
						'value'   => current_time('Y-m-d H:i:s'),
						'type'    => 'DATETIME',
						'compare' => '>='
					),
					array(
						'key'     => '_event_end_date',
						'value'   => current_time('Y-m-d H:i:s'),
						'type'    => 'DATETIME',
						'compare' => '>='
					)
				),
				array(
					'key'     => '_cancelled',
					'value'   => '1',
					'compare' => '!='
				),
			)
		);

		if('featured' === $orderby) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				'featured_clause' => array(
					'key'     => '_featured',
					'compare' => 'EXISTS',
				),
				'event_start_date_clause' => array(
					'key'     => '_event_start_date',
					'compare' => 'EXISTS',
				), 
				'event_start_time_clause' => array(
					'key'     => '_event_start_time',
					'compare' => 'EXISTS',
				), 
			);
			$args['orderby'] = array(
				'featured_clause' => 'desc',
				'event_start_date_clause' => $order,
				'event_start_time_clause' => $order,
			);
		}

		if('rand_featured' === $orderby) {
			$args['orderby'] = array(
				'menu_order' => 'ASC',
				'rand'       => 'ASC',
			);
		}
		// If orderby meta key _event_start_date 
		if('event_start_date' === $orderby) {
			$args['orderby'] ='meta_value';
			$args['meta_key'] ='_event_start_date';
			$args['meta_type'] ='DATETIME';
		}
		// If orderby event_start_date and time  both
		if('event_start_date_time' === $orderby) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				'event_start_date_clause' => array(
					'key'     => '_event_start_date',
					'compare' => 'EXISTS',
				),
				'event_start_time_clause' => array(
					'key'     => '_event_start_time',
					'compare' => 'EXISTS',
				), 
			);
			$args['orderby'] = array(
				'event_start_date_clause' => $order,
				'event_start_time_clause' => $order,
			);
		}
		$upcoming_events = new WP_Query($args);

		if ($upcoming_events->have_posts()) {
			ob_start();

			while ($upcoming_events->have_posts()) {
				$upcoming_events->the_post();
				get_event_manager_template_part('content', 'past_event_listing');
			}

			$events_html = ob_get_clean();
			$no_more_events = $upcoming_events->found_posts <= $paged * $per_page;

			wp_send_json_success(array(
				'events_html' => $events_html,
				'no_more_events' => $no_more_events
			));
		} else {
			wp_send_json_error(array(
				'error' => __('No more events found.', 'wp-event-manager')
			));
		}

		wp_reset_postdata();
	}

	/**
	 * Load more past events
	 */
	public function load_more_past_events($atts) {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'event_manager_ajax' ) ) {
			wp_send_json_error(array('error' => 'Invalid nonce'));
			return;
		}
		
		$paged = isset($_POST['value']) ? intval( wp_unslash($_POST['value'])) : 1;
		$per_page = isset($_POST['per_page']) ? intval(wp_unslash($_POST['per_page'])) : esc_attr(get_option('event_manager_per_page'));
		$orderby = isset($_POST['orderby']) ? sanitize_text_field(wp_unslash($_POST['orderby'])) : 'date';
		$order = isset($_POST['order']) ? sanitize_text_field(wp_unslash($_POST['order'])) : 'DESC';

		$args = array(
			'post_type'      => 'event_listing',
			'post_status'    => array('expired'),
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'order'          => 'DESC',
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'meta_type'      => 'DATETIME',
		);
		
		$past_events = new WP_Query($args);

		if ($past_events->have_posts()) {
			ob_start();

			while ($past_events->have_posts()) {
				$past_events->the_post();
				get_event_manager_template_part('content', 'past_event_listing');
			}

			$events_html = ob_get_clean();
			$no_more_events = $past_events->found_posts <= $paged * $per_page;

			wp_send_json_success(array(
				'events_html' => $events_html,
				'no_more_events' => $no_more_events
			));
		} else {
			wp_send_json_error(array(
				'error' => __('No more events found.', 'wp-event-manager')
			));
		}

		wp_reset_postdata();
	}

	/**
	 * Get Upcoming Listings
	 */
	public function get_upcoming_listings($atts) {

		$search_location = isset( $_POST['search_location'] ) ? wp_kses_post( wp_unslash( $_POST['search_location'] ) ) : '';
		$search_categories = isset( $_POST['search_categories'] ) ? sanitize_text_field( wp_unslash( $_POST['search_categories'] ) ) : '';
		$event_manager_keyword = isset( $_POST['search_keywords'] ) ? sanitize_text_field( wp_unslash( $_POST['search_keywords'] ) ) : '';
		if( is_array( $search_categories ) ) {
		$search_categories = array_filter( array_map( 'sanitize_text_field', array_map( 'stripslashes', $search_categories ) ) );
		} else {
			$search_categories = sanitize_text_field( stripslashes( $search_categories ) );
			$search_categories = explode( ',', $search_categories );
		}
		$search_event_types = isset( $_POST['search_event_types'] ) ? sanitize_text_field( wp_unslash( $_POST['search_event_types'] ) ) : '';
		if( is_array( $search_event_types ) ) {
			$search_event_types= array_filter( array_map( 'sanitize_text_field', array_map( 'stripslashes', $search_event_types) ) );
		} else {
			$search_event_types = sanitize_text_field( stripslashes( $search_event_types ) );
			$search_event_types= explode( ',', $search_event_types );
		}
		$paged = isset($_POST['value']) ? intval( wp_unslash($_POST['value'])) : 1;
		$per_page = isset($_POST['per_page']) ? intval(wp_unslash($_POST['per_page'])) : esc_attr(get_option('event_manager_per_page'));
		$orderby = isset($_POST['orderby']) ? sanitize_text_field(wp_unslash($_POST['orderby'])) : 'date';
		$order = isset($_POST['order']) ? sanitize_text_field(wp_unslash($_POST['order'])) : 'DESC';

		$args = array(
			'post_type'      => 'event_listing',
			'post_status'    => array('publish'),
			'posts_per_page' => $per_page,
			'orderby'        => $orderby,
			'order'          => $order,
			'paged'          => $paged,
			'meta_query'     => array(
				array(
					'relation' => 'OR',
					array(
						'key'     => '_event_start_date',
						'value'   => current_time('Y-m-d H:i:s'),
						'type'    => 'DATETIME',
						'compare' => '>='
					),
					array(
						'key'     => '_event_end_date',
						'value'   => current_time('Y-m-d H:i:s'),
						'type'    => 'DATETIME',
						'compare' => '>='
					)
				),
				array(
					'key'     => '_cancelled',
					'value'   => '1',
					'compare' => '!='
				),
			)
		);

		if('featured' === $orderby) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				'featured_clause' => array(
					'key'     => '_featured',
					'compare' => 'EXISTS',
				),
				'event_start_date_clause' => array(
					'key'     => '_event_start_date',
					'compare' => 'EXISTS',
				), 
				'event_start_time_clause' => array(
					'key'     => '_event_start_time',
					'compare' => 'EXISTS',
				), 
			);
			$args['orderby'] = array(
				'featured_clause' => 'desc',
				'event_start_date_clause' => $order,
				'event_start_time_clause' => $order,
			);
		}

		if('rand_featured' === $orderby) {
			$args['orderby'] = array(
				'menu_order' => 'ASC',
				'rand'       => 'ASC',
			);
		}
		// If orderby meta key _event_start_date 
		if('event_start_date' === $orderby) {
			$args['orderby'] ='meta_value';
			$args['meta_key'] ='_event_start_date';
			$args['meta_type'] ='DATETIME';
		}
		// If orderby event_start_date and time  both
		if('event_start_date_time' === $orderby) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				'event_start_date_clause' => array(
					'key'     => '_event_start_date',
					'compare' => 'EXISTS',
				),
				'event_start_time_clause' => array(
					'key'     => '_event_start_time',
					'compare' => 'EXISTS',
				), 
			);
			$args['orderby'] = array(
				'event_start_date_clause' => $order,
				'event_start_time_clause' => $order,
			);
		}

		if ( isset( $search_location ) && !empty( $search_location ) ) {
			$args['meta_query'][] = array(
				'key'     => '_event_location',
				'value'   => $search_location,
				'compare' => 'LIKE',
			);
		}
		$tax_query = array();
		if ( isset( $search_event_types ) && !empty( $search_event_types ) && !empty( $search_event_types[0] ) ) {
			$field    = is_numeric($search_event_types[0]) ? 'term_id' : 'slug';
			$operator = 'all' === get_option('event_manager_event_type_filter_type', 'all') && count($search_event_types) > 1 ? 'AND' : 'IN';
			$tax_query[] = array(
				'taxonomy'         => 'event_listing_type',
				'field'            => $field,
				'terms'            => array_filter(array_values($search_event_types)),
				'include_children' => $operator !== 'AND',
				'operator'         => $operator,
			);
		}

		if ( isset( $search_categories ) && !empty( $search_categories ) && !empty( $search_categories[0] ) ) {
			$field    = is_numeric($search_categories[0]) ? 'term_id' : 'slug';
			$operator = 'all' === get_option('event_manager_category_filter_type', 'all') && count($search_categories) > 1 ? 'AND' : 'IN';
			$tax_query[] = array(
				'taxonomy'         => 'event_listing_category',
				'field'            => $field,
				'terms'            => array_filter(array_values($search_categories)),
				'include_children' => $operator !== 'AND',
				'operator'         => $operator,
			);
		}

		if ( !empty( $tax_query ) ) {
			$args['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax_query );
		}

		$upcoming_events = new WP_Query($args);

		if ($upcoming_events->have_posts()) {
			ob_start();

			while ($upcoming_events->have_posts()) {
				$upcoming_events->the_post();
				get_event_manager_template_part('content', 'past_event_listing');
			}

			$events_html = ob_get_clean();
			$no_more_events = $upcoming_events->found_posts <= $paged * $per_page;

			wp_send_json_success(array(
				'events_html' => $events_html,
				'no_more_events' => $no_more_events
			));
			// wp_send_json_success(array(
			// 	'events_html' => $events_html,
			// ));
			} else {
				$no_events_html = '<div class="no_event_listings_found wpem-alert wpem-alert-danger">';
				$no_events_html .= esc_html__('There are no events matching your search.', 'wp-event-manager');
				$no_events_html .= '</div>';

				wp_send_json_success(array(
					'events_html' => $no_events_html
				));
			}
		wp_reset_postdata();
	}

	/**
	 * Get listings via ajax.
	 */
	public function get_listings() {
		// Security: Verify nonce for AJAX requests
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( ! isset( $_REQUEST['wpem_filter_nonce'] ) 
				|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wpem_filter_nonce'] ) ), 'wpem_filter_action' ) ) {
				wp_send_json_error([
					'code'    => 403,
					'message' => '<div class="wpem-alert wpem-alert-danger">' . esc_html__( 'Security check failed.', 'wp-event-manager' ) . '</div>',
				]);
				wp_die();
			}
		}
		
		global $wp_post_types;
		$result            = array();
		$search_location   = isset($_REQUEST['search_location']) ? sanitize_text_field(wp_unslash($_REQUEST['search_location'])) : '';
		$search_keywords   = isset($_REQUEST['search_keywords']) ? sanitize_text_field(wp_unslash($_REQUEST['search_keywords'])) : '';
		$post_type_label   = $wp_post_types['event_listing']->labels->name;
		$orderby           = isset($_REQUEST['orderby']) ? sanitize_text_field(wp_unslash($_REQUEST['orderby'])) : '';
		$search_datetimes = '';
		$search_categories = '';
		$search_event_types = '';
		$search_ticket_prices = "";
		if (isset($_REQUEST['search_datetimes'])) {
			$raw_dates = is_array($_REQUEST['search_datetimes']) 
				? array_filter(array_map('stripslashes', $_REQUEST['search_datetimes'])) 
				: array_filter([stripslashes($_REQUEST['search_datetimes'])]);

			if (!empty($raw_dates[0])) {
				$decoded = json_decode($raw_dates[0], true);

				if (!empty($decoded['start']) && !empty($decoded['end'])) {
					$search_datetimes = [$raw_dates[0]];
				} else {
					$search_datetimes = [];
				}
			} else {
				$search_datetimes = [];
			}
		}

		if(isset($_REQUEST['search_categories'])) {
			$search_categories_raw = wp_unslash($_REQUEST['search_categories']);
			$search_categories = is_array($search_categories_raw) ? 
				array_filter(array_map('sanitize_text_field', $search_categories_raw)) : 
				array_filter(array(sanitize_text_field($search_categories_raw)));
		}

		if(isset($_REQUEST['search_event_types'])) {
			$search_event_types_raw = wp_unslash($_REQUEST['search_event_types']);
			$search_event_types = is_array($search_event_types_raw) ? 
				array_filter(array_map('sanitize_text_field', $search_event_types_raw)) : 
				array_filter(array(sanitize_text_field($search_event_types_raw)));
		}

		if(isset($_REQUEST['search_ticket_prices'])) {
			$search_ticket_prices_raw = wp_unslash($_REQUEST['search_ticket_prices']);
			$search_ticket_prices = is_array($search_ticket_prices_raw) ? 
				array_filter(array_map('sanitize_text_field', $search_ticket_prices_raw)) : 
				array_filter(array(sanitize_text_field($search_ticket_prices_raw)));
		} 
		$args = array(
			'search_location'    	=> $search_location,
			'search_keywords'    	=> $search_keywords,
			'search_datetimes'  	=> $search_datetimes,
			'search_categories'  	=> $search_categories,
			'search_event_types'  	=> $search_event_types,
			'search_ticket_prices'	=> $search_ticket_prices,			
			'orderby'            	=> $orderby,
			'order'              	=> isset($_REQUEST['order']) ? sanitize_text_field(wp_unslash($_REQUEST['order'])) : 'DESC',
			'offset'             	=> isset($_REQUEST['page']) ? (absint(wp_unslash($_REQUEST['page'])) - 1) * absint( wp_unslash( $_REQUEST['per_page'])) : 0,
			'posts_per_page'     	=> isset($_REQUEST['per_page']) ? absint(wp_unslash($_REQUEST['per_page'])) : 10,
			'lang'    	            => isset($_REQUEST['lang']) ? apply_filters('wpem_set_default_page_language', sanitize_text_field(wp_unslash($_REQUEST['lang']))) : '',
		);

		if(isset($_REQUEST['cancelled']) && ($_REQUEST['cancelled'] === 'true' || $_REQUEST['cancelled'] === 'false')) {
			$args['cancelled'] = $_REQUEST['cancelled'] === 'true' ? true : false;
		}

		if(isset($_REQUEST['featured']) && ($_REQUEST['featured'] === 'true' || $_REQUEST['featured'] === 'false')) {
			$args['featured'] = $_REQUEST['featured'] === 'true' ? true : false;
			$args['orderby']  = 'featured' === $orderby ? 'date' : $orderby;
		}

		if(isset($_REQUEST['event_online']) && ($_REQUEST['event_online'] === 'true' || $_REQUEST['event_online'] === 'false')) {
			$args['event_online'] = ($_REQUEST['event_online'] === 'false') ? sanitize_text_field(wp_unslash($_REQUEST['event_online'])) : true;
		}

		ob_start();
		$events = get_event_listings(apply_filters('event_manager_get_listings_args', $args, $_REQUEST));
		$result['found_events'] = false;
		$fully_registered_events = 0;
		if($events->have_posts()) : $result['found_events'] = true;
			while ($events->have_posts()) : $events->the_post(); 
				
				$hide_event = apply_filters('wpem_hide_selected_event', false, get_the_id());
				if($hide_event == true){
					$fully_registered_events++;
					continue;
				}
				get_event_manager_template_part('content', 'event_listing');
			endwhile; 
			$events->found_posts -= $fully_registered_events;
			?>
		<?php else : 
			$default_events = get_posts(array(
					'numberposts' => -1,
					'post_type'   => 'event_listing',
					'post_status'   => 'publish'
			));
			if(count($default_events) == 0): ?>
				<div class="no_event_listings_found wpem-alert wpem-alert-danger wpem-mb-0"><?php esc_attr_e('There are currently no events.', 'wp-event-manager'); ?></div>
			<?php else: get_event_manager_template_part('content', 'no-events-found');
			endif;
		endif;

		$result['html']    = ob_get_clean();
		$result['filter_value'] = array();	
		// Categories
		if($search_categories) {
			$showing_categories = array();
			foreach ($search_categories as $category) {
				$category_object = get_term_by(is_numeric($category) ? 'id' : 'slug', $category, 'event_listing_category');
				if(!is_wp_error($category_object)) {
					$showing_categories[] = $category_object->name;
				}
			}
			$result['filter_value'][] = implode(', ', $showing_categories);
		}

		// Event types
		if($search_event_types) {
			$showing_event_types = array();
			foreach ($search_event_types as $event_type) {
				$event_type_object = get_term_by(is_numeric($event_type) ? 'id' : 'slug', $event_type, 'event_listing_type');
				if(!is_wp_error($event_type_object)) {
					$showing_event_types[] = $event_type_object->name;
				}
			}
			$result['filter_value'][] = implode(', ', $showing_event_types);
		}
		
		// Datetimes
		if($search_datetimes) {	
			$showing_datetimes= array();			
			foreach ($search_datetimes as $datetime) { 	
			    $showing_datetimes[]=WP_Event_Manager_Filters::get_datetime_value($datetime);
			}
			$result['filter_value'][] = implode(', ', $showing_datetimes);		
		}
		
		// Ticket prices	
		if($search_ticket_prices) {		
		    $showing_ticket_prices = array();	
			foreach ($search_ticket_prices as $ticket_price) { 	
			    $showing_ticket_prices []= WP_Event_Manager_Filters::get_ticket_price_value($ticket_price);
			}	
			 $result['filter_value'][] = implode(', ', $showing_ticket_prices);		
		}	

		if($search_keywords) {
			$result['filter_value'][] = '&ldquo;' . $search_keywords . '&rdquo;'; 	
		}		
       
        $last_filter_value = array_pop($result['filter_value']);   
        $result_implode=implode(', ', $result['filter_value']);
        if(count($result['filter_value']) >= 1) {
            $result['filter_value']= explode(" ",  $result_implode); 
            $result['filter_value'][]=  " &amp; ";
        } else {
            if(!empty($last_filter_value))
                $result['filter_value']= explode(" ",  $result_implode); 
        }      
        $result['filter_value'][] =  $last_filter_value ." " . $post_type_label;
        
		if($search_location) {
			$result['filter_value'][] = sprintf(wp_kses('located in &ldquo;%s&rdquo;', 'wp-event-manager') , $search_location) ;
		}

		if ( sizeof( $result['filter_value'] ) > 1 ) {
			$message = sprintf(
				esc_html(
					/* translators: %d: number of matching records found */
					_n(
						'Search completed. Found %d matching record.',
						'Search completed. Found %d matching records.',
						$events->found_posts,
						'wp-event-manager'
					)
				),
				(int) $events->found_posts
			);
			$result['showing_applied_filters'] = true;
		} else {
			$message = "";
			$result['showing_applied_filters'] = false;			
		}
		
		$search_values = array(
			'location'   => $search_location,
			'keywords'   => $search_keywords,
			'datetimes'  => $search_datetimes,
			'tickets'	 => $search_ticket_prices,
			'types'		 => $search_event_types,
			'categories' => $search_categories
		);
		$result['filter_value'] = apply_filters('event_manager_get_listings_custom_filter_text', $message, $search_values);
		
		// Generate RSS link
		$result['showing_links'] = event_manager_get_filtered_links(array(
			'search_keywords'   => $search_keywords,			
			'search_location'   => $search_location,
			'search_datetimes' => $search_datetimes,
			'search_categories' => $search_categories,
			'search_event_types' => $search_event_types,
			'search_ticket_prices' => $search_ticket_prices
		));
		
		// Generate pagination
		if(isset($_REQUEST['show_pagination']) && $_REQUEST['show_pagination'] === 'true') {
			$page = isset($_REQUEST['page']) ? absint(wp_unslash($_REQUEST['page'])) : 1;
			$result['pagination'] = get_event_listing_pagination($events->max_num_pages, $page);
		}
		$result['max_num_pages'] = $events->max_num_pages;
		wp_send_json(apply_filters('event_manager_get_listings_result', $result, $events));
	}

	/**
	 * Upload file via ajax.
	 *
	 * No nonce field since the form may be statically cached.
	 */
	public function upload_file() {
		if(!event_manager_user_can_upload_file_via_ajax()) {
			wp_send_json_error(new WP_Error('upload', __('You must be logged in to upload files using this method.', 'wp-event-manager')));
			return;
		}

		$data = array('files' => array());
		if(!empty($_FILES)) {
			foreach ($_FILES as $file_key => $file) {
				$files_to_upload = event_manager_prepare_uploaded_files($file);
				foreach ($files_to_upload as $file_to_upload) {
					$uploaded_file = event_manager_upload_file($file_to_upload, array('file_key' => $file_key));
					if(is_wp_error($uploaded_file)) {
						$data['files'][] = array('error' => $uploaded_file->get_error_message());
					} else {
						$data['files'][] = $uploaded_file;
					}
				}
			}
		}
		wp_send_json($data);
	}

	/**
	 * Add organizer.
	 * add organizer with popup action
	 * @access public
	 * @param 
	 * @return array
	 * @since 3.1.16
	 */
	public function add_organizer() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'wpem_add_organizer_action', 'wpem_add_organizer_nonce' );
		}

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_organizers' ) ) {
			wp_send_json( [
				'code'    => 403,
				'message' => '<div class="wpem-alert wpem-alert-danger">' . esc_html__( 'Please login as Organizer to add an organizer!', 'wp-event-manager' ) . '</div>',
			] );
			wp_die();
		}
		
		if ( ! isset( $_POST['wpem_add_organizer_nonce'] ) 
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpem_add_organizer_nonce'] ) ), 'wpem_add_organizer_action' ) ) {
			wp_send_json([
				'code'    => 403,
				'message' => '<div class="wpem-alert wpem-alert-danger">' . esc_html__( 'Security check failed.', 'wp-event-manager' ) . '</div>',
			]);
			wp_die();
		}

		$params = array();
		// Parse ONLY from POST, not REQUEST
		parse_str($_POST['form_data'], $params);

		// Sanitize dynamic fields
		$clean = [];

		foreach ($params as $key => $value) {

			$safe_key = sanitize_key($key);

			if (is_array($value)) {
				$clean[$safe_key] = array_map('sanitize_text_field', $value);
			} else {
				$clean[$safe_key] = sanitize_text_field($value);
			}
		}

		$params = $clean;		
		$params['organizer_description'] = sanitize_text_field( wp_unslash( $_POST['organizer_description'] ) );
		$params['submit_organizer'] = 'Submit';

		$data = [];

		if(!empty($params['organizer_name']) && isset($params['organizer_id'])  && $params['organizer_id'] == 0){
			$_POST = $params;

			if(isset($_COOKIE['wp-event-manager-submitting-organizer-id']))
			    unset($_COOKIE['wp-event-manager-submitting-organizer-id']);				
			if(isset($_COOKIE['wp-event-manager-submitting-organizer-key']))
			    unset($_COOKIE['wp-event-manager-submitting-organizer-key']);

			$GLOBALS['event_manager']->forms->get_form('submit-organizer', array());
			$form_submit_organizer_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Organizer', 'instance'));
			$event_fields =	$form_submit_organizer_instance->merge_with_custom_fields('frontend');

			// Submit current event with $_POST values
			$form_submit_organizer_instance->submit_handler();

			$organizer_id = $form_submit_organizer_instance->get_organizer_id();

			if(isset($organizer_id) && !empty($organizer_id)){
				$organizer = get_post($organizer_id);

				$data = [
					'code' => 200,
					'organizer' => [
						'organizer_id' 	=> $organizer_id,
						'organizer_name' => $organizer->post_title,
					],
					'message' => '<div class="wpem-alert wpem-alert-danger">'. __('Successfully created', 'wp-event-manager') . '</div>',
				];
			} else {
				$data = [
					'code' => 404,
					'message' => '<div class="wpem-alert wpem-alert-danger">'. $form_submit_organizer_instance->get_errors() . '</div>',
				];
			}
		} else {
			$data = [
				'code' => 404,
				'message' => '<div class="wpem-alert wpem-alert-danger">'. __('Organizer Name is a required field.', 'wp-event-manager') . '</div>',
			];
		}
		wp_send_json($data);
		wp_die();
	}

	/**
	 * Add venue.
	 * add venue with popup action
	 * @access public
	 * @param 
	 * @return array
	 * @since 3.1.16
	 */
	public function add_venue() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'wpem_add_venue_action', 'wpem_add_venue_nonce' );
		}

		if ( ! is_user_logged_in() || ( ! current_user_can( 'manage_venues' ) ) ) {
			wp_send_json( [
				'code'    => 403,
				'message' => '<div class="wpem-alert wpem-alert-danger">' . esc_html__( 'Please login as organizer to add venue!', 'wp-event-manager' ) . '</div>',
			] );
			wp_die();
		}
		
		if ( ! isset( $_POST['wpem_add_venue_nonce'] ) 
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpem_add_venue_nonce'] ) ), 'wpem_add_venue_action' ) ) {
			wp_send_json([
				'code'    => 403,
				'message' => '<div class="wpem-alert wpem-alert-danger">' . esc_html__( 'Security check failed.', 'wp-event-manager' ) . '</div>',
			]);
			wp_die();
		}

		$params = array();
		// Parse ONLY from POST, not REQUEST
		parse_str($_POST['form_data'], $params);
		// Sanitize dynamic fields
		$clean = [];

		foreach ($params as $key => $value) {
			$safe_key = sanitize_key($key);
			if (is_array($value)) {
				$clean[$safe_key] = array_map('sanitize_text_field', $value);
			} else {
				$clean[$safe_key] = sanitize_text_field($value);
			}
		}

		$params = $clean;
		$params['venue_description'] = isset( $_POST['venue_description'] ) ? sanitize_text_field( wp_unslash( $_POST['venue_description'] ) ) : '';
		$params['submit_venue'] = 'Submit';

		$data = [];
		if(!empty($params['venue_name']) && isset($params['venue_id'])  && $params['venue_id'] == 0) {
			$_POST = $params;

			if(isset($_COOKIE['wp-event-manager-submitting-venue-id']))
			    unset($_COOKIE['wp-event-manager-submitting-venue-id']);				
			if(isset($_COOKIE['wp-event-manager-submitting-venue-key']))
			    unset($_COOKIE['wp-event-manager-submitting-venue-key']);

			$GLOBALS['event_manager']->forms->get_form('submit-venue', array());
			$form_submit_venue_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Venue', 'instance'));
			$event_fields =	$form_submit_venue_instance->merge_with_custom_fields('frontend');

			// Submit current event with $_POST values
			$form_submit_venue_instance->submit_handler();
			$venue_id = $form_submit_venue_instance->get_venue_id();

			if(isset($venue_id) && !empty($venue_id)){
				$venue = get_post($venue_id);

				$data = [
					'code' => 200,
					'venue' => [
						'venue_id' 	=> $venue_id,
						'venue_name' => $venue->post_title,
					],
					'message' => '<div class="wpem-alert wpem-alert-danger">'. __('Successfully created', 'wp-event-manager') . '</div>',
				];
			}else{
				$data = [
					'code' => 404,
					'message' => '<div class="wpem-alert wpem-alert-danger">'. $form_submit_venue_instance->get_errors() . '</div>',
				];
			}
		} else {
			$data = [
				'code' => 404,
				'message' => '<div class="wpem-alert wpem-alert-danger">'. __('Venue Name is a required field.', 'wp-event-manager') . '</div>',
			];
		}
		wp_send_json($data);
		wp_die();
	}
}
 WP_Event_Manager_Ajax::instance();