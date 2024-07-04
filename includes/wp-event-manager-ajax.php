<?php
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
	 * Check for WC Ajax request and fire action.
	 */
	public static function do_em_ajax() {
		global $wp_query;
		if(!empty($_GET['em-ajax'])) {
			 $wp_query->set('em-ajax', esc_attr($_GET['em-ajax']));
		}

   		if($action = $wp_query->get('em-ajax')) {
   			if(!defined('DOING_AJAX')) {
				define('DOING_AJAX', true);
			}
			// Not home - this is an ajax endpoint
			$wp_query->is_home = false;
   			do_action('event_manager_ajax_' . esc_attr($action));
   			die();
   		}
	}

	/**
	 * Get listings via ajax.
	 */
	public function get_listings() {
		global $wp_post_types;
		$result            = array();
		$search_location   = esc_attr(stripslashes($_REQUEST['search_location']));
		$search_keywords   = esc_attr(stripslashes($_REQUEST['search_keywords']));
		$post_type_label   = $wp_post_types['event_listing']->labels->name;
		$orderby           = esc_attr($_REQUEST['orderby']);
		$search_datetimes = '';
		$search_categories = '';
		$search_event_types = '';
		$search_ticket_prices = '';

		if(isset($_REQUEST['search_datetimes'])) {
			$search_datetimes = is_array($_REQUEST['search_datetimes']) ?  array_filter( array_map('stripslashes', $_REQUEST['search_datetimes'])) : array_filter(array(stripslashes($_REQUEST['search_datetimes'])));
		}

		if(isset($_REQUEST['search_categories'])) {
			$search_categories = is_array($_REQUEST['search_categories']) ?  array_filter( array_map('stripslashes', $_REQUEST['search_categories'])) : array_filter(array(stripslashes($_REQUEST['search_categories'])));
		}

		if(isset($_REQUEST['search_event_types'])) {
			$search_event_types =  is_array($_REQUEST['search_event_types']) ?  array_filter( array_map('stripslashes', $_REQUEST['search_event_types'])) :	array_filter(array(stripslashes($_REQUEST['search_event_types'])));
		}

		if(isset($_REQUEST['search_ticket_prices'])) {
			$search_ticket_prices = is_array($_REQUEST['search_ticket_prices']) ?  array_filter( array_map('stripslashes', $_REQUEST['search_ticket_prices'])) : array_filter(array(stripslashes($_REQUEST['search_ticket_prices'])));
		} 
		$args = array(
			'search_location'    	=> $search_location,
			'search_keywords'    	=> $search_keywords,
			'search_datetimes'  	=> $search_datetimes,
			'search_categories'  	=> $search_categories,
			'search_event_types'  	=> $search_event_types,
			'search_ticket_prices'	=> $search_ticket_prices,			
			'orderby'            	=> $orderby,
			'order'              	=> esc_attr($_REQUEST['order']),
			'offset'             	=> (absint($_REQUEST['page']) - 1) * absint($_REQUEST['per_page']),
			'posts_per_page'     	=> absint($_REQUEST['per_page']),
			'lang'    	            => apply_filters('wpem_set_default_page_language', $_REQUEST['lang']),
		);

		if(isset($_REQUEST['cancelled']) && ($_REQUEST['cancelled'] === 'true' || $_REQUEST['cancelled'] === 'false')) {
			$args['cancelled'] = $_REQUEST['cancelled'] === 'true' ? true : false;
		}

		if(isset($_REQUEST['featured']) && ($_REQUEST['featured'] === 'true' || $_REQUEST['featured'] === 'false')) {
			$args['featured'] = $_REQUEST['featured'] === 'true' ? true : false;
			$args['orderby']  = 'featured' === $orderby ? 'date' : $orderby;
		}

		if(isset($_REQUEST['event_online']) && ($_REQUEST['event_online'] === 'true' || $_REQUEST['event_online'] === 'false')) {
			$args['event_online'] = $_REQUEST['event_online'] === 'false' ? $_REQUEST['event_online'] : true;
		}

		ob_start();
		$events = get_event_listings(apply_filters('event_manager_get_listings_args', $args, $_REQUEST));
		$result['found_events'] = false;
		if($events->have_posts()) : $result['found_events'] = true;
			while ($events->have_posts()) : $events->the_post(); 
				get_event_manager_template_part('content', 'event_listing');
			endwhile; ?>
		<?php else : 
			$default_events = get_posts(array(
					'numberposts' => -1,
					'post_type'   => 'event_listing',
					'post_status'   => 'publish'
			));
			if(count($default_events) == 0): ?>
				<div class="no_event_listings_found wpem-alert wpem-alert-danger wpem-mb-0"><?php _e('There are currently no events.', 'wp-event-manager'); ?></div>
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

		if(sizeof($result['filter_value']) > 1) {	    
        	$message = sprintf(_n('Search completed. Found %d matching record.', 'Search completed. Found %d matching records.', $events->found_posts, 'wp-event-manager'), $events->found_posts);
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
			$result['pagination'] = get_event_listing_pagination($events->max_num_pages, absint($_REQUEST['page']));
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

		$params = array();
		parse_str($_REQUEST['form_data'], $params);
		$params['organizer_description'] = sanitize_text_field($_REQUEST['organizer_description']);
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
					'message' => '<div class="wpem-alert wpem-alert-danger">'. __('Successfully created') . '</div>',
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
				'message' => '<div class="wpem-alert wpem-alert-danger">'. __('Organizer Name is a required field.') . '</div>',
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

		$params = array();
		parse_str($_REQUEST['form_data'], $params);
		$params['venue_description'] = sanitize_text_field($_REQUEST['venue_description']);
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
					'message' => '<div class="wpem-alert wpem-alert-danger">'. __('Successfully created') . '</div>',
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
				'message' => '<div class="wpem-alert wpem-alert-danger">'. __('Venue Name is a required field.') . '</div>',
			];
		}
		wp_send_json($data);
		wp_die();
	}
}
 WP_Event_Manager_Ajax::instance();