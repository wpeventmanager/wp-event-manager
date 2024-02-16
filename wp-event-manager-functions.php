<?php
if(!function_exists('get_event_listings')) :
	/**
	 * Queries event listings with certain criteria and returns them.
	 *
	 * @access public
	 * @return WP_Query
	 */
	function get_event_listings($args = array()) {

		global $wpdb, $event_manager_keyword;
		$args = wp_parse_args($args, array(
			'search_location'   => '',
			'search_keywords'   => '',
			'search_datetimes' => array(),
			'search_categories' => array(),
			'search_event_types' => array(),
			'search_ticket_prices' => array(),
			'offset'            => 0,
			'posts_per_page'    => 15,
			'orderby'           => 'date',
			'order'             => 'DESC',
			'featured'          => null,
			'cancelled'         => null,
			'event_online'      => null,
			'fields'            => 'all',
			'lang'              => '',
			'post_status'       => array(),
		));
		/**
		 * Perform actions that need to be done prior to the start of the event listings query.
		 * @param array $args Arguments used to retrieve event listings.
		 * @since 1.5
		 */
		do_action('get_event_listings_init', $args);
		if(get_option('event_manager_hide_expired')) {
			$post_status = 'publish';
		} else {
			$post_status = array('publish', 'expired');
		}
		$query_args = array(
			'post_type'              => 'event_listing',
			'post_status'            => $post_status,
			'ignore_sticky_posts'    => 1,
			'offset'                 => absint($args['offset']),
			'posts_per_page'         => intval($args['posts_per_page']),
			'orderby'                => $args['orderby'],
			'order'                  => $args['order'],
			'tax_query'              => array(),
			'meta_query'             => array(),
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false,
			'fields'                 => $args['fields']
		);
		if($args['posts_per_page'] < 0) {
			$query_args['no_found_rows'] = true;
		}
		if(!empty($args['search_location'])) {
			$location_meta_keys = array('geolocation_formatted_address', '_event_location', 'geolocation_state_long');
			$location_search    = array('relation' => 'OR');
			foreach($location_meta_keys as $meta_key) {
				$location_search[] = array(
					'key'     => $meta_key,
					'value'   => 	$args['search_location'], 
					'compare' => 'like',
					'type'    => 'char',
				);
				$location_search[] = array(
					'key'     => $meta_key,
					'value'   => trim(preg_replace("/[^a-zA-Z,\s]/", "", $args['search_location']), ','),
					'compare' => 'like',
					'type'    => 'char',
				);
			}
			$query_args['meta_query'][] = $location_search;
		}
		if(!is_null($args['featured'])) {
			$query_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => '_event_featured',
					'value'   => '1',
					'compare' => $args['featured'] ? '=' : '!='
				),
				array(
					'key'     => '_featured',
					'value'   => '1',
					'compare' => $args['featured'] ? '=' : '!='
				)
			);
			$query_args['meta_query']['relation'] = 'AND';
		}

		if(!is_null($args['cancelled']) || 1 === absint(get_option('event_manager_hide_cancelled_events'))) {
			$query_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key' => '_event_cancelled',
					'value'   => '1',
					'compare' => $args['cancelled'] ? '=' : '!='
				),
				array(
					'key' => '_cancelled',
					'value'   => '1',
					'compare' => $args['cancelled'] ? '=' : '!='
				)
			);
		}

		if(isset($args['event_online']) && !empty($args['event_online'])) {
		
			if($args['event_online'] == 'true')
				$event_online = 'yes';
			elseif($args['event_online'] == 'false')
				$event_online = 'no';
			$query_args['meta_query'][] = array(
				'key'     => '_event_online',
				'value'   => $event_online,
				'compare' => $args['event_online'] ? '=' : '!='
			);
		}
			
		if(!empty($args['search_datetimes'][0])) {		
			$date_search = array();
			
			$dates = json_decode($args['search_datetimes'][0], true);
			// Get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
			$datepicker_date_format 	= WP_Event_Manager_Date_Time::get_datepicker_format();

			// Covert datepicker format  into php date() function date format
			$php_date_format 		= WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
			if (!empty($dates)) {
				$dates['start'] = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $dates['start']);
				$dates['end'] = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $dates['end']);
				$date_search['relation'] = 'AND';
				$date_search[] = array(
					'key'     => '_event_start_date',
					'value'   =>  $dates['end'],
					'compare' => '<=',
					'type'    => 'date'
				);
				$date_search[] = array(
					'key'     => '_event_start_date',
					'value'   => $dates['start'],
					'compare' => '>=',
					'type'    => 'date'
				);
				apply_filters('event_manager_get_listings_date_filter_args', $date_search, $dates);
				if(!empty( $date_search))
					$query_args['meta_query'][] = $date_search;
			}
		}

		if(!empty($args['search_categories'][0])) {
			$field    = is_numeric($args['search_categories'][0]) ? 'term_id' : 'slug';
			$operator = 'all' === get_option('event_manager_category_filter_type', 'all') && sizeof($args['search_categories']) > 1 ? 'AND' : 'IN';
			$query_args['tax_query'][] = array(
									'taxonomy'         => 'event_listing_category',
									'field'            => $field,
									'terms'            => array_values($args['search_categories']),
									'include_children' => 'AND' !== $operator,
									'operator'         => $operator
								);
		}
		
		if(!empty($args['search_event_types'][0])) {
			$field    = is_numeric($args['search_event_types'][0]) ? 'term_id' : 'slug';	
			$operator = 'all' === get_option('event_manager_event_type_filter_type', 'all') && sizeof($args['search_event_types']) > 1 ? 'AND' : 'IN';	
			$query_args['tax_query'][] = array(
									'taxonomy'         => 'event_listing_type',
									'field'            => $field,
									'terms'            => array_values($args['search_event_types']),
									'include_children' => $operator !== 'AND' ,
									'operator'         => $operator
							);	
		}
		if(!empty($args['search_tags'][0])) {
			$field    = is_numeric($args['search_tags'][0]) ? 'term_id' : 'slug';
			$operator = 'all' === get_option('event_manager_event_type_filter_type', 'all') && sizeof($args['search_tags']) > 1 ? 'AND' : 'IN';
			$query_args['tax_query'][] = array(
				'taxonomy'         => 'event_listing_tag',
				'field'            => $field,
				'terms'            => array_values($args['search_tags']),
				'include_children' => $operator !== 'AND' ,
				'operator'         => $operator
			);
		}
		// Must match with event_ticket_options options value at wp-event-manager-form-submit-event.php
		if(!empty($args['search_ticket_prices'][0]))  {	
			$ticket_price_value='';
			if($args['search_ticket_prices'][0]==='paid' || $args['search_ticket_prices'][0]==='ticket_price_paid') {  
				$ticket_price_value='paid';     
			} elseif($args['search_ticket_prices'][0]==='free' || $args['search_ticket_prices'][0]==='ticket_price_free') {
				$ticket_price_value='free';
			}
			$ticket_search[] = array(
							'key'     => '_event_ticket_options',
							'value'   => $ticket_price_value,
							'compare' => 'LIKE',
						);
			$query_args['meta_query'][] = $ticket_search;
		}

		if('featured' === $args['orderby']) {
			$query_args['meta_query'] = array(
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
			$query_args['orderby'] = array(
				'featured_clause' => 'desc',
				'event_start_date_clause' => $args['order'],
				'event_start_time_clause' => $args['order'],
			);
		}

		if('rand_featured' === $args['orderby']) {
			$query_args['orderby'] = array(
				'menu_order' => 'ASC',
				'rand'       => 'ASC',
			);
		}
		// If orderby meta key _event_start_date 
		if('event_start_date' === $args['orderby']) {
			$query_args['orderby'] ='meta_value';
			$query_args['meta_key'] ='_event_start_date';
			$query_args['meta_type'] ='DATETIME';
		}
		// If orderby event_start_date and time  both
		if('event_start_date_time' === $args['orderby']) {
			$query_args['meta_query'] = array(
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
			$query_args['orderby'] = array(
				'event_start_date_clause' => $args['order'],
				'event_start_time_clause' => $args['order'],
			);
		}
	
		$event_manager_keyword = esc_attr($args['search_keywords']); 
		if(!empty($event_manager_keyword) && strlen($event_manager_keyword) >= apply_filters('event_manager_get_listings_keyword_length_threshold', 2)) {
			$query_args['s'] = $event_manager_keyword;
			add_filter('posts_search', 'get_event_listings_keyword_search');
		}
	
		$query_args = apply_filters('event_manager_get_listings', $query_args, $args);

		if(empty($query_args['meta_query'])) {
			unset($query_args['meta_query']);
		}

		if(empty($query_args['tax_query'])) {
			unset($query_args['tax_query']);
		} else {
			$query_args['meta_query']['tax_query'] = array($query_args['tax_query']);
			$query_args['meta_query']['relation'] = 'AND';
			// $query_args['tax_query']['relation'] = 'AND';
		}
		// Polylang LANG arg
		if(function_exists('pll_current_language') && !empty($args['lang'])) {
			$query_args['lang'] = $args['lang'];
		}

		// Filter args
		$query_args = apply_filters('get_event_listings_query_args', $query_args, $args);
		do_action('before_get_event_listings', $query_args, $args);
		// Cache results.
		if(apply_filters('get_event_listings_cache_results', true)) {
			$to_hash         = wp_json_encode($query_args) . apply_filters('wpml_current_language', '');
			$query_args_hash = 'em_' . md5($to_hash . EVENT_MANAGER_VERSION) . WP_Event_Manager_Cache_Helper::get_transient_version('get_event_listings');
			
			$result               = false;
			$cached_query_results = true;
			$cached_query_posts   = get_transient($query_args_hash);

			if(is_string($cached_query_posts)) {
				$cached_query_posts = json_decode($cached_query_posts, false);

				if($cached_query_posts
				&& is_object($cached_query_posts)
				&& isset($cached_query_posts->max_num_pages)
				&& isset($cached_query_posts->found_posts)
				&& isset($cached_query_posts->posts)
				&& is_array($cached_query_posts->posts)
				) {
					$posts  = array_map('get_post', $cached_query_posts->posts);
					$result = new WP_Query();
					$result->parse_query($query_args);
					$result->posts         = $posts;
					$result->found_posts   = intval($cached_query_posts->found_posts);
					$result->max_num_pages = intval($cached_query_posts->max_num_pages);
					$result->post_count    = count($posts);
				}
			}

			if(false === $result) {
				$result = new WP_Query($query_args);
				$cached_query_results = false;
				$cacheable_result                  = array();
				$cacheable_result['posts']         = array_values($result->posts);
				$cacheable_result['found_posts']   = $result->found_posts;
				$cacheable_result['max_num_pages'] = $result->max_num_pages;
				set_transient($query_args_hash, wp_json_encode($cacheable_result), DAY_IN_SECONDS);
			}

			if($cached_query_results) {
				// Random order is cached so shuffle them.
				if('rand_featured' === $args['orderby']) {
					usort($result->posts, '_wpem_shuffle_featured_post_results_helper');
				} elseif('rand' === $args['orderby']) {
					shuffle($result->posts);
				}
			}
		} else {
			$result = new WP_Query($query_args);
		}
	
		$result = apply_filters('get_event_listings_result_args',$result,$query_args);
		do_action('after_get_event_listings', $query_args, $args);
		remove_filter('posts_search', 'get_event_listings_keyword_search');
		return $result;
	}
endif;

if(!function_exists('_wpem_shuffle_featured_post_results_helper')) :
	/**
	 * Helper function to maintain featured status when shuffling results.
	 *
	 * @param WP_Post $a
	 * @param WP_Post $b
	 *
	 * @return bool
	 */
	function _wpem_shuffle_featured_post_results_helper($a, $b) {
		if(-1 === $a->menu_order || -1 === $b->menu_order) {
			// Left is featured.
			if(0 === $b->menu_order) {
				return -1;
			}
			// Right is featured.
			if(0 === $a->menu_order) {
				return 1;
			}
		}
		return rand(-1, 1);
	}
endif;

if(!function_exists('get_event_listings_keyword_search')) :

	/**
	 * Join and where query for keywords
	 *
	 * @param array $search
	 * @return array
	 */
	function get_event_listings_keyword_search($search) {
		
		global $wpdb, $event_manager_keyword;
		// Searchable Meta Keys: set to empty to search all meta keys
		$searchable_meta_keys = array(
			'_event_location',
			'_organizer_name',
			'_event_tags',
			'_event_address',
			'_event_pincode',
			'_event_location',
			'_registration',
			'_event_start_date',
			'_event_start_time',
			'_organizer_contact_person_name',
			'_organizer_email',
			'_organizer_website',
			'_organizer_video',
			'_organizer_youtube',
			'_organizer_google_plus',
			'_organizer_facebook',
			'_organizer_linkedin',
			'_organizer_twitter',
			'_organizer_xing',
			'_organizer_pinterest',
			'_organizer_instagram',
		);
		$searchable_meta_keys = apply_filters('event_listing_searchable_meta_keys', $searchable_meta_keys);
		$conditions   = array();
		
		// Search Post Meta
		if(apply_filters('event_listing_search_post_meta', true)) {
			// Only selected meta keys
			if($searchable_meta_keys) {
				$conditions[] = "{$wpdb->posts}.ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ('" . implode("','", array_map('esc_sql', $searchable_meta_keys)) . "') AND meta_value LIKE '%" . esc_sql($event_manager_keyword) . "%')";
			} else {
				// No meta keys defined, search all post meta value
				$conditions[] = "{$wpdb->posts}.ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%" . esc_sql($event_manager_keyword) . "%')";
			}
		}
		
		// Search taxonomy
		$conditions[] = "{$wpdb->posts}.ID IN (SELECT object_id FROM {$wpdb->term_relationships} AS tr LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id WHERE t.name LIKE '%" . esc_sql($event_manager_keyword) . "%')";
		
		/**
 		 * Filters the conditions to use when querying event listings. Resulting array is joined with OR statements.
 		 *
 		 * @since 1.5
 		 *
 		 * @param array  $conditions Conditions to join by OR when querying event listings.
 		 * @param string $event_manager_keyword Search query.
 		 */
		$conditions = apply_filters('event_listing_search_conditions', $conditions, $event_manager_keyword);
		if(empty($conditions)) {
				return $search;			
		}
		$conditions_str = implode(' OR ', $conditions);
		
		if(!empty($search)) {
			$search = preg_replace('/^ AND /', '', $search);
			$search = " AND ({$search} OR ({$conditions_str}))";
		} else {
			$search = " AND ({$conditions_str})";
		}
		return $search;
	}
endif;

if(!function_exists('get_event_listing_post_statuses')) :
	/**
	 * Get post statuses used for events.
	 *
	 * @access public
	 * @return array
	 */
	function get_event_listing_post_statuses() {
		return apply_filters('event_listing_post_statuses', array(
			'draft'           => _x('Draft', 'post status', 'wp-event-manager'),
			'expired'         => _x('Expired', 'post status', 'wp-event-manager'),
			'preview'         => _x('Preview', 'post status', 'wp-event-manager'),
			'pending'         => _x('Pending approval', 'post status', 'wp-event-manager'),
			'pending_payment' => _x('Pending payment', 'post status', 'wp-event-manager'),
			'publish'         => _x('Active', 'post status', 'wp-event-manager'),
		));
	}
endif;

if(!function_exists('get_featured_event_ids')) :
	/**
	 * Gets the ids of featured events.
	 *
	 * @access public
	 * @return array
	 */
	function get_featured_event_ids() {
		return get_posts(array(
			'posts_per_page' => -1,
			'suppress_filters' => false,
			'post_type'      => 'event_listing',
			'post_status'    => 'publish',
			'meta_key'       => '_featured',
			'meta_value'     => '1',
			'fields'         => 'ids'
		));
	}
endif;

if(!function_exists('get_event_listing_types')) :
	/**
	 * Get event listing types.
	 *
	 * @access public
	 * @return array
	 */
	function get_event_listing_types($fields = 'all') {

		if(!get_option('event_manager_enable_event_types')){
			return array();
		}else{	
			$args = array(
					'fields'     => $fields,
					'hide_empty' => false,
					'order'      => 'ASC',
					'orderby'    => 'name'
			);
			$args = apply_filters('get_event_listing_types_args', $args);
			// Prevent users from filtering the taxonomy
			$args['taxonomy'] = 'event_listing_type';
			return get_terms($args);
		}
	}
endif;

if(!function_exists('get_event_listing_categories')) :
	/**
	 * Get event categories.
	 *
	 * @access public
	 * @return array
	 */
	function get_event_listing_categories() {
		if(!get_option('event_manager_enable_categories')) {
			return array();
		}
		$args = array(
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
			);
		/**
		 * Change the category query arguments.
		 *
		 * @since 2.5
		 *
		 * @param array $args
		 */
		$args = apply_filters('get_event_listing_category_args', $args);

		// Prevent users from filtering the taxonomy.
		$args['taxonomy'] = 'event_listing_category';

		return get_terms($args);
	}
endif;

if(!function_exists('event_manager_get_filtered_links')) :
	/**
	 * Shows links after filtering events.
	 */
	function event_manager_get_filtered_links($args = array()) {
		$search_datetimes= array();
		$search_categories = array();
		$search_event_types= array();
		$search_ticket_prices= array();	

		// Convert to slugs
		if($args['search_categories']) {
			foreach($args['search_categories'] as $category) {
				if(is_numeric($category)) {
					$category_object = get_term_by('id', $category, 'event_listing_category');
					if(!is_wp_error($category_object)) {
						$search_categories [] = $category_object->slug;
					}
				} else {
					$search_categories [] = $category;
				}
			}
		}
		
		// Convert to slugs
		if($args['search_event_types']) {
			foreach($args['search_event_types'] as $type) {
				if(is_numeric($type)) {
					$type_object = get_term_by('id', $type, 'event_listing_type');
					if(!is_wp_error($type_object)) {
						$search_event_types[] = $type_object->slug;
					}
				} else {
					$search_event_types[] = $type;
				}
			}
		}
		
		// Datetimes
		// Add just key like datetime_any, datetime_today..
		if($args['search_datetimes']) {	
			foreach($args['search_datetimes'] as $datetime) { 	
				$search_datetimes[]=$datetime;
			}
		}

		// Ticket price
		// Add just key like ticket_price_any, ticket_price_paid..	
		if($args['search_ticket_prices']) {	
			foreach($args['search_ticket_prices'] as $ticket_price) { 	
				$search_ticket_prices[]=$ticket_price;
			}
		}

		$links = apply_filters('event_manager_event_filters_showing_events_links', array(
			'reset' => array(
				'name' => __('Reset', 'wp-event-manager'),
				'url'  => '#'
			),
			'rss_link' => array(
				'name' => __('RSS', 'wp-event-manager'),
				'url'  => get_event_listing_rss_link(apply_filters('event_manager_get_listings_custom_filter_rss_args', array(
					'search_keywords' => $args['search_keywords'],
					'search_location' => $args['search_location'],	
					'search_datetimes'  => implode(',', $search_datetimes),
					'search_categories'  => implode(',', $search_categories),
					'search_event_types'  => implode(',', $search_event_types),
					'search_ticket_prices'  => implode(',', $search_ticket_prices)
				)))
			)
		), $args);

		if(!$args['search_keywords'] && !$args['search_location'] && !$args['search_datetimes'] && !$args['search_categories'] && !$args['search_event_types'] && !$args['search_ticket_prices'] && !apply_filters('event_manager_get_listings_custom_filter', false)) {
			unset($links['reset']);
		}

		$return = '';
		$i = 1;
		foreach($links as $key => $link) {
			if($i > 1)
				$return .= ' <a href="#">|</a> ';
			$return .= '<a href="' . esc_url($link['url']) . '" class="' . esc_attr($key) . '">' . $link['name'] . '</a>';
			$i++;
		}
		
		return $return;
	}
endif;

if(!function_exists('get_event_listing_rss_link')) :

	/**
	 * Get the Event Listing RSS link.
	 *
	 * @return string
	 */
	function get_event_listing_rss_link($args = array()) {
		$rss_link = add_query_arg(urlencode_deep(array_merge(array('feed' => 'event_feed'), $args)), home_url());
		return $rss_link;
	}
endif;

if(!function_exists('wp_event_manager_notify_new_user')) :

	/**
	 * Handle account creation.
	*
	* @param  int $user_id
	* @param  string $password
	*/
	function wp_event_manager_notify_new_user($user_id, $password) {
		global $wp_version;
		
		if(version_compare($wp_version, '4.3.1', '<')) {
			wp_new_user_notification($user_id, $password);
		} else {
			$notify = 'admin';
			if(empty($password)) {
				$notify = 'both';
			}
			wp_new_user_notification($user_id, null, $notify);
		}
	}
endif;

if(!function_exists('wp_event_manager_create_account')) :
	/**
	 * Handle account creation.
	 *
	 * @param  array $args containing username, email, role
	 * @param  string $deprecated role string
	 * @return WP_error | bool was an account created?
	 */
	function wp_event_manager_create_account($args, $deprecated = '') {

		global $current_user;
		global $wp_version;
		
		// Soft Deprecated in 1.0
		if(!is_array($args)) {
			$args = array(
						'username' => '',
						'password' => false,
						'email'    => $args,
						'role'     => $deprecated,
					);
		} else {
			$defaults = array(
					'username' => '',
					'email'    => '',
					'password' => false,
					'role'     => get_option('default_role')
			);
			$args = wp_parse_args($args, $defaults);
			extract($args);
		}
		
		$username = sanitize_user($args['username'], true);
		$email    = apply_filters('user_registration_email', sanitize_email($args['email']));
		
		if(empty($email)) {
			return new WP_Error('validation-error', __('Invalid email address.', 'wp-event-manager'));
		}
		
		if(empty($username)) {
			$username = sanitize_user(current(explode('@', $email)));
		}
		
		if(!is_email($email)) {
			return new WP_Error('validation-error', __('Your email address isn&#8217;t correct.', 'wp-event-manager'));
		}
		
		if(email_exists($email)) {
			return new WP_Error('validation-error', __('This email is already registered, please choose another one.', 'wp-event-manager'));
		}
		
		// Ensure username is unique
		$append     = 1;
		$o_username = $username;
		while (username_exists($username)) {
			$username = $o_username . $append;
			$append ++;
		}
		
		// Final error checking
		$reg_errors = new WP_Error();
		$reg_errors = apply_filters('event_manager_registration_errors', $reg_errors, $username, $email);
		
		do_action('event_manager_register_post', $username, $email, $reg_errors);
		
		if($reg_errors->get_error_code()) {
			return $reg_errors;
		}
		
		// Create account
		$new_user = array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
				'role'       => $role
		);
		
		// User is forced to set up account with email sent to them. This password will remain a secret.
		if(empty($new_user['user_pass'])) {
			$new_user['user_pass'] = wp_generate_password();
		}
		
		$user_id = wp_insert_user(apply_filters('event_manager_create_account_data', $new_user));
		
		if(is_wp_error($user_id)) {
			return $user_id;
		}
		
		// Notify
		/**
		 * Send notification to new users.
		 *
		 * @since 1.8
		 *
		 * @param  int         $user_id
		 * @param  string|bool $password
		 * @param  array       $new_user {
		 *     Information about the new user.
		 *
		 *     @type string $user_login Username for the user.
		 *     @type string $user_pass  Password for the user (may be blank).
		 *     @type string $user_email Email for the new user account.
		 *     @type string $role       New user's role.
		 * }
		 */
		do_action('event_manager_notify_new_user', $user_id, $password, $new_user);
		
		// Login
		if(!is_user_logged_in()){
			wp_set_auth_cookie($user_id, true, is_ssl());
			$current_user = get_user_by('id', $user_id);
		}
		return true;
	}
endif;

/**
 * True if an the user can post a event. If accounts are required, and reg is enabled, users can post (they signup at the same time).
 *
 * @return bool
 */
function event_manager_user_can_post_event() {
	$can_post = true;
	if(!is_user_logged_in()) {
		if(event_manager_user_requires_account() && !event_manager_enable_registration()) {
			$can_post = false;
		}
	}
	return apply_filters('event_manager_user_can_post_event', $can_post);
}

/**
 * True if an the user can edit a event.
 *
 * @return bool
 */
function event_manager_user_can_edit_event($event_id) {

	$can_edit = true;
	if(!is_user_logged_in() || !$event_id)  {
		$can_edit = false;
	} else {
		$event  = get_post($event_id);
		if(!$event || (absint($event->post_author) !== get_current_user_id() && !current_user_can('edit_post', $event_id))) {
			$can_edit = false;
		}
	}
	return apply_filters('event_manager_user_can_edit_event', $can_edit, $event_id);
}

/**
 * Checks if the visitor is currently on a WPEM page, event listing, or taxonomy.
 *
 * @return bool
 * @since 2.5
 */
function is_wpem() {
	/**
	 * Filter the result of is_wpem().
	 */
	return apply_filters('is_wpem', (is_wpem_page() || has_wpem_shortcode() || is_wpem_event_listing() || is_wpem_taxonomy()));
}

/**
 * Checks if the visitor is currently on a WPEM page.
 * 
 * @return bool
 * @since 2.5
 */
function is_wpem_page() {
	$is_wpem_page = is_post_type_archive('event_listing');

	if(!$is_wpem_page) {
		$wpem_page_ids = array_filter(
			array(
				get_option('event_manager_submit_event_form_page_id', false),
				get_option('event_manager_event_dashboard_page_id', false),
				get_option('event_manager_events_page_id', false),
			)
		);

		/**
		 * Filters a list of all page IDs related to WPEM.
		 */
		$wpem_page_ids = array_unique(apply_filters('event_manager_page_ids', $wpem_page_ids));
		$is_wpem_page = is_page($wpem_page_ids);
	}

	/**
	 * Filter the result of is_wpem_page().
	 */
	return apply_filters('is_wpem_page', $is_wpem_page);
}

/**
 * Checks if the provided content or the current single page or post has a WPEM shortcode.
 *
 * @param string|null $content Content to check. If not provided, it uses the current post content.
 * @param string|array|null $tag Check specifically for one or more shortcodes. If not provided, checks for any WPJM shortcode.
 *
 * @return bool
 */
function has_wpem_shortcode($content = null, $tag = null) {
	global $post;

	$has_wpem_shortcode = false;

	if(null === $content && is_singular() && is_a($post, 'WP_Post')) {
		$content = $post->post_content;
	}

	if(!empty($content)) {
		$wpem_shortcodes = array('submit_event_form', 'event_dashboard', 'events', 'event', 'event_summary', 'event_apply');
		/**
		 * Filters a list of all shortcodes associated with WPEM.
		 *
		 * @since 2.5
		 *
		 * @param string[] $wpem_shortcodes
		 */
		$wpem_shortcodes = array_unique(apply_filters('event_manager_shortcodes', $wpem_shortcodes));

		if(null !== $tag) {
			if(!is_array($tag)) {
				$tag = array($tag);
			}
			$wpem_shortcodes = array_intersect($wpem_shortcodes, $tag);
		}
		foreach($wpem_shortcodes as $shortcode) {
			if(has_shortcode($content, $shortcode)) {
				$has_wpem_shortcode = true;
				break;
			}
		}
	}

	/**
	 * Filter the result of has_wpem_shortcode().
	 *
	 * @since 2.5
	 *
	 * @param bool $has_wpem_shortcode
	 */
	return apply_filters('has_wpem_shortcode', $has_wpem_shortcode);
}

/**
 * Checks if the current page is a event listing.
 *
 * @since 2.5
 *
 * @return bool
 */
function is_wpem_event_listing() {
	return is_singular(array('event_listing'));
}

/**
 * Checks if the visitor is on a page for a WPEM taxonomy.
 *
 * @since 2.5
 *
 * @return bool
 */
function is_wpem_taxonomy() {
	return is_tax(get_object_taxonomies('event_listing'));
}

/**
 * True if only one type allowed per event.
 *
 * @return bool
 */
function event_manager_multiselect_event_type() {

	if(!class_exists('WP_Event_Manager_Form_Submit_Event')) {
        include_once(EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php');
        include_once(EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php');
    }

    $form_submit_event_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Event', 'instance'));
    $event_fields = $form_submit_event_instance->merge_with_custom_fields();

    if(isset($event_fields['event']['event_type']['type']) && $event_fields['event']['event_type']['type'] === 'term-multiselect') {
    	return apply_filters('event_manager_multiselect_event_type', true);
    } else {
    	return apply_filters('event_manager_multiselect_event_type', false);
    }
}

/**
 * True if only one category allowed per event.
 *
 * @return bool
 */
function event_manager_multiselect_event_category() {

	if(!class_exists('WP_Event_Manager_Form_Submit_Event')) {
        include_once(EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-abstract.php');
        include_once(EVENT_MANAGER_PLUGIN_DIR . '/forms/wp-event-manager-form-submit-event.php');
    }

    $form_submit_event_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Event', 'instance'));
    $event_fields = $form_submit_event_instance->merge_with_custom_fields();

    if(isset($event_fields['event']['event_category']['type']) && $event_fields['event']['event_category']['type'] === 'term-multiselect') {
    	return apply_filters('event_manager_multiselect_event_category', true);
    } else {
    	return apply_filters('event_manager_multiselect_event_category', false);
    }
}

/**
 * True if registration is enabled.
 *
 * @return bool
 */
function event_manager_enable_registration() {
	return apply_filters('event_manager_enable_registration', get_option('event_manager_enable_registration') == 1 ? true : false);
}

/**
 * True if usernames are generated from email addresses.
 *
 * @return bool
 */
function event_manager_generate_username_from_email() {
	return apply_filters('event_manager_generate_username_from_email', get_option('event_manager_generate_username_from_email') == 1 ? true : false);
}

/**
 * True if an account is required to post a event.
 *
 * @return bool
 */
function event_manager_user_requires_account() {
	return apply_filters('event_manager_user_requires_account', get_option('event_manager_user_requires_account') == 1 ? true : false);
}

/**
 * True if users are allowed to edit submissions that are pending approval.
 *
 * @return bool
 */
function event_manager_user_can_edit_pending_submissions() {
	return apply_filters('event_manager_user_can_edit_pending_submissions', get_option('event_manager_user_can_edit_pending_submissions') == 1 ? true : false);
}

/**
 * Based on wp_dropdown_categories, with the exception of supporting multiple selected categories, event types.
 * @see  wp_dropdown_categories
 */
function event_manager_dropdown_selection($args = '') {
	$defaults = array(
		'orderby'         => 'id',
		'order'           => 'ASC',
		'show_count'      => 0,
		'hide_empty'      => 1,
		'child_of'        => 0,
		'exclude'         => '',
		'echo'            => 1,
		'selected'        => 0,
		'hierarchical'    => 0,
		'name'            => 'cat',
		'id'              => '',
		'class'           => 'event-manager-category-dropdown ' . (is_rtl() ? 'chosen-rtl' : ''),
		'depth'           => 0,
		'taxonomy'        => 'event_listing_category',
		'value'           => 'id',
		'multiple'        => true,
		'show_option_all' => false,
		'placeholder'     => __('Choose a Category', 'wp-event-manager'),
		'no_results_text' => __('No results match', 'wp-event-manager'),
		'multiple_text'   => __('Choose Categories', 'wp-event-manager'),
	);
	$query = wp_parse_args($args, $defaults);

	if(!isset($query['pad_counts']) && $query['show_count'] && $query['hierarchical']) {
		$query['pad_counts'] = true;
	}
	extract($query);

	// Store in a transient to help sites with many cats
	$categories_hash = 'em_cats_' . md5(json_encode($query) . WP_Event_Manager_Cache_Helper::get_transient_version('em_get_' . $query['taxonomy']));
	$categories      = get_transient($categories_hash);

	if(empty($categories)) {
		$categories = get_terms($taxonomy, array(
			'orderby'         => $query['orderby'],
			'order'           => $query['order'],
			'hide_empty'      => $query['hide_empty'],
			'child_of'        => $query['child_of'],
			'exclude'         => $query['exclude'],
			'hierarchical'    => $query['hierarchical']
		));

		set_transient($categories_hash, $categories, DAY_IN_SECONDS * 30);
	}

	$categories = apply_filters('event_manager_dropdown_selection_' . $taxonomy, $categories);
	$name       = esc_attr($name);
	$class      = esc_attr($class);
	$id = $query['id'] ? $query['id'] : $query['name'];

	if($taxonomy === 'event_listing_type'):
		$placeholder = __('Choose an event type', 'wp-event-manager');
		$multiple_text = __('Choose event types', 'wp-event-manager');
	endif;

	$output = "<select name='" . esc_attr($name) . "[]' id='" . esc_attr($id) . "' class='" . esc_attr($class) . "' " . ($multiple ? "multiple='multiple'" : '') . " data-placeholder='" . esc_attr($placeholder) . "' data-no_results_text='" . esc_attr($no_results_text) . "' data-multiple_text='" . esc_attr($placeholder) . "'>\n";

	if($show_option_all) {
		$output .= '<option value="">' . $show_option_all . '</option>';
	}

	if(!empty($categories)) {
		include_once(EVENT_MANAGER_PLUGIN_DIR . '/includes/wp-event-manager-category-walker.php');
		$walker = new WP_Event_Manager_Category_Walker;
		if($hierarchical) {
			$depth = $query['depth'];  // Walk the full depth.
		} else {
			$depth = -1; // Flat.
		}
		$output .= $walker->walk($categories, $depth, $query);
	}

	$output .= "</select>\n";

	if($echo) {
		printf('%s', $output);
	}
	return $output;
}

/**
 * Get the page ID of a page if set, with PolyLang compat.
 * @param  string $page e.g. event_dashboard, submit_event_form, events
 * @return int
 */
function event_manager_get_page_id($page){	
	$page_id = get_option('event_manager_' . $page . '_page_id', false);
	if($page_id) {
		return apply_filters('wpml_object_id', absint(function_exists('pll_get_post') ? pll_get_post($page_id) : $page_id), 'page', TRUE);
	} else {
		return 0;
	}
}

/**
 * Get the permalink of a page if set.
 * @param  string $page e.g. event_dashboard, submit_event_form, events
 * @return string|bool
 */
function event_manager_get_permalink($page) {

	if($page_id = event_manager_get_page_id($page)) {
		return get_permalink($page_id);
	} else {
		return false;
	}
}

/**
 * Filters the upload dir when $event_manager_upload is true.
 * @param  array $pathdata
 * @return array
 */
function event_manager_upload_dir($pathdata) {

	global $event_manager_upload, $event_manager_uploading_file;

	if(!empty($event_manager_upload)) {

		$dir = untrailingslashit(apply_filters('event_manager_upload_dir', 'event-manager-uploads/' . sanitize_key($event_manager_uploading_file), sanitize_key($event_manager_uploading_file)));

		if(empty($pathdata['subdir'])) {
			$pathdata['path']   = $pathdata['path'] . '/' . $dir;
			$pathdata['url']    = $pathdata['url'] . '/' . $dir;
			$pathdata['subdir'] = '/' . $dir;
		} else {
			$new_subdir         = '/' . $dir . $pathdata['subdir'];
			$pathdata['path']   = str_replace($pathdata['subdir'], $new_subdir, $pathdata['path']);
			$pathdata['url']    = str_replace($pathdata['subdir'], $new_subdir, $pathdata['url']);
			$pathdata['subdir'] = str_replace($pathdata['subdir'], $new_subdir, $pathdata['subdir']);
		}
	}
	return $pathdata;
}
add_filter('upload_dir', 'event_manager_upload_dir');

/**
 * Prepare files for upload by standardizing them into an array. This adds support for multiple file upload fields.
 * @param  array $file_data
 * @return array
 */
function event_manager_prepare_uploaded_files($file_data) {

	$files_to_upload = array();
	
	if(is_array($file_data['name'])) {
		foreach($file_data['name'] as $file_data_key => $file_data_value) {
			if($file_data['name'][$file_data_key]) {
				$type              = wp_check_filetype($file_data['name'][$file_data_key]); // Map mime types to those that WordPress knows.
				$files_to_upload[] = array(
					'name'     => $file_data['name'][$file_data_key],
					'type'     => $type['type'],
					'tmp_name' => $file_data['tmp_name'][$file_data_key],
					'error'    => $file_data['error'][$file_data_key],
					'size'     => $file_data['size'][$file_data_key]
				);
			}
		}
	} else {
		$type              = wp_check_filetype($file_data['name']); // Map mime types to those that WordPress knows.
		$file_data['type'] = $type['type'];
		$files_to_upload[] = $file_data;
	}
	return apply_filters('event_manager_prepare_uploaded_files', $files_to_upload);
}

/**
 * Upload a file using WordPress file API.
 * @param  array $file_data Array of $_FILE data to upload.
 * @param  array $args Optional arguments
 * @return array|WP_Error Array of objects containing either file information or an error
 */
function event_manager_upload_file($file, $args = array()) {

	global $event_manager_upload, $event_manager_uploading_file;

	require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

	$args = wp_parse_args($args, array(
		'file_key'           => '',
		'file_label'         => '',
		'allowed_mime_types' => ''
	));
	$event_manager_upload         = true;
	$event_manager_uploading_file = $args['file_key'];
	$uploaded_file                = new stdClass();
	
    if('' === $args['allowed_mime_types']) {
        $allowed_mime_types = event_manager_get_allowed_mime_types($event_manager_uploading_file);
    } else {
        $allowed_mime_types = $args['allowed_mime_types'];
    }
 
    /**
     * Filter file configuration before upload.
     *
     * This filter can be used to modify the file arguments before being uploaded, or return a WP_Error
     * object to prevent the file from being uploaded, and return the error.
     *
     * @since 1.0
     *
     * @param array $file               Array of $_FILE data to upload.
     * @param array $args               Optional file arguments
     * @param array $allowed_mime_types Array of allowed mime types from field config or defaults
     */
    $file = apply_filters('event_manager_upload_file_pre_upload', $file, $args, $allowed_mime_types);
   
    if(is_wp_error($file)) {
        return $file;
    }
    
	if(!in_array($file['type'], $allowed_mime_types)) {
		if($args['file_label']) {
			return new WP_Error('upload', sprintf('"%s" (filetype %s) needs to be one of the following file types: %s', 'wp-event-manager'), $args['file_label'], $file['type'], implode(', ', array_keys($args['allowed_mime_types']))) ;
		} else {
			if(is_array($args['allowed_mime_types'])){
				return new WP_Error('upload', sprintf('Uploaded files need to be one of the following file types: %s', 'wp-event-manager'), implode(', ', array_keys($args['allowed_mime_types']))) ;
			}
		}
	} else {
		$upload = wp_handle_upload($file, apply_filters('submit_event_wp_handle_upload_overrides', array('test_form' => false)));
		if(!empty($upload['error'])) {
			return new WP_Error('upload', $upload['error']);
		} else {
			$uploaded_file->url       = $upload['url'];
			$uploaded_file->file      = $upload['file'];
			$uploaded_file->name      = basename($upload['file']);
			$uploaded_file->type      = $upload['type'];
			$uploaded_file->size      = $file['size'];
			$uploaded_file->extension = substr(strrchr($uploaded_file->name, '.'), 1);
		}
	}
	$event_manager_upload         = false;
	$event_manager_uploading_file = '';
	return $uploaded_file;
}

/**
 * Allowed Mime types specifically for WP Event Manager.
 * @param   string $field Field used.
 * @return  array  Array of allowed mime types
 */
function event_manager_get_allowed_mime_types($field = ''){
	if('organizer_logo' === $field)  {
		$allowed_mime_types = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif'          => 'image/gif',
				'png'          => 'image/png',
		);
	} else {
		$allowed_mime_types = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif'          => 'image/gif',
				'png'          => 'image/png',
				'pdf'          => 'application/pdf',
				'doc'          => 'application/msword',
				'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		);
	}

	/**
	 * Mime types to accept in uploaded files.
	 * Default is image, pdf, and doc(x) files.
	 */
	return apply_filters('event_manager_mime_types', $allowed_mime_types, $field);
}

/**
 * Calculate and return the event expiry date.
 * @param  int $event_id
 * @return string
 */
function get_event_expiry_date($event_id) {
	//get set listing expiry time duration
	$option=get_option('event_manager_submission_expire_options');
	$event_start_date = esc_attr(get_post_meta($event_id, '_event_start_date', true));
	$event_end_date = esc_attr(get_post_meta($event_id, '_event_end_date', true));
	$expiry_base_date = $event_end_date ? $event_end_date : $event_start_date;

	if($option==='event_end_date')	{
		if($expiry_base_date)
			return date('Y-m-d', strtotime($expiry_base_date));
	} else {
		// Get duration from the admin settings if set.
		$duration = esc_attr(get_post_meta($event_id, '_event_duration', true));		

		if(!$duration) {		   
			$duration = absint(get_option('event_manager_submission_duration'));
		}
		if($duration) 
			return date('Y-m-d', strtotime("+{$duration} days", strtotime($expiry_base_date)));
	}
	return '';
}

/**
 * Duplicate a listing.
 * @param  int $post_id
 * @return int 0 on fail or the post ID.
 */
function event_manager_duplicate_listing($post_id) {
	if(empty($post_id) || !($post = get_post($post_id))) {
		return 0;
	}

	global $wpdb;

	/**
	 * Duplicate the post.
	 */
	$new_post_id = wp_insert_post(array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $post->post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'preview',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
	));
	
	/**
	 * Copy taxonomies.
	 */
	$taxonomies = get_object_taxonomies($post->post_type);

	foreach($taxonomies as $taxonomy) {
		$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
		wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
	}

	/*
	 * Duplicate post meta, aside from some reserved fields.
	 */
	$post_meta = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id=%d", $post_id));

	do_action('event_manager_duplicate_listing_meta_start', $post_meta, $post, $new_post_id);

	if(!empty($post_meta)) {
		$post_meta = wp_list_pluck($post_meta, 'meta_value', 'meta_key');
		foreach($post_meta as $meta_key => $meta_value) {
			if(in_array($meta_key, apply_filters('event_manager_duplicate_listing_ignore_keys', array('_cancelled', '_featured', '_event_expires', '_event_duration')))) {
				continue;
			}
			if($meta_key === '_view_count'){
				$meta_value=0;
			}
			update_post_meta($new_post_id, $meta_key, maybe_unserialize($meta_value));
		}
	}

	update_post_meta($new_post_id, '_cancelled', 0);
	update_post_meta($new_post_id, '_featured', 0);

	do_action('event_manager_duplicate_listing_meta_end', $post_meta, $post, $new_post_id);

	return $new_post_id;
}

/**
 * Checks if the user can upload a file via the Ajax endpoint.
 *
 * @since 1.7
 * @return bool
 */
function event_manager_user_can_upload_file_via_ajax() {
	$can_upload = is_user_logged_in() && event_manager_user_can_post_event();
	/**
	 * Override ability of a user to upload a file via Ajax.
	 *
	 * @since 1.7
	 * @param bool $can_upload True if they can upload files from Ajax endpoint.
	 */
	return apply_filters('event_manager_user_can_upload_file_via_ajax', $can_upload);
}

/**
 * Here add post types of event.
 * 
 * @param $types
 * @return $types
 */
function event_manager_add_post_types($types) {
	$types[] = 'event_listing';
	return $types;
}
add_filter('post_types_to_delete_with_user', 'event_manager_add_post_types', 10);

/**
 * Checks to see if the standard password setup email should be used.
 *
 * @return bool True if they are to use standard email, false to allow user to set password at first event creation.
 * @since 1.8
 */
function event_manager_use_standard_password_setup_email() {
	$use_standard_password_setup_email = false;
	
	// If username is being automatically generated, force them to send password setup email.
	if(event_manager_generate_username_from_email()) {
		$use_standard_password_setup_email = get_option('event_manager_use_standard_password_setup_email', 1) == 1 ? true : false;
	}
	
	/**
	 * Allows an override of the setting for if a password should be auto-generated for new users.
	 *
	 * @param bool $use_standard_password_setup_email True if a standard account setup email should be sent.
	 * @since 1.8
	 */
	return apply_filters('event_manager_use_standard_password_setup_email', $use_standard_password_setup_email);
}

/**
 * Checks if a password should be auto-generated for new users.
 *
 * @param string $password Password to validate.
 * @return bool True if password meets rules.
 * @since 1.8
 */
function event_manager_validate_new_password($password) {
	// Password must be at least 8 characters long. Trimming here because `wp_hash_password()` will later on.
	$is_valid_password = strlen(trim ($password)) >= 8;
	
	/**
	 * Allows overriding default Event Manager password validation rules.
	 *
	 * @param bool   $is_valid_password True if new password is validated.
	 * @param string $password          Password to validate.
	 * @since 1.8
	 */
	return apply_filters('event_manager_validate_new_password', $is_valid_password, $password);
}

/**
 * Returns the password rules hint.
 *
 * @return string
 */
function event_manager_get_password_rules_hint() {
	/**
	 * Allows overriding the hint shown below the new password input field. Describes rules set in `event_manager_validate_new_password`.
	 *
	 * @param string $password_rules Password rules description.
	 * @since 1.8
	 */
	return apply_filters('event_manager_password_rules_hint', __('Passwords must be at least 8 characters long.', 'wp-event-manager'));
}

/**
 * Returns all organizers.
 * 
 * @since 3.1.14
 * @param null
 * @return string
 */
function get_all_event_organizer($user_id = '', $args = []) {
	if(!get_option('enable_event_organizer'))
		return false;

	$query_args = array(
					'post_type'   => 'event_organizer',
					'post_status' => 'publish',
					'posts_per_page'=> -1,
					'suppress_filters' => 0

				);

	if(isset($user_id) && !empty($user_id) && !is_admin()) {
		$query_args['author'] = $user_id;	
	}

	if(isset($args) && !empty($args)) {
		$query_args = array_merge($query_args,$args);
	}

	$query_args = apply_filters('get_all_event_organizer_args', $query_args);

	$all_organizer = get_posts($query_args);

	if(!empty($all_organizer)) {
		return $all_organizer;	
	} else {
		return false;
	}	
}

/**
 * Returns array of all organizers.
 * 
 * @since 3.1.14
 * @param null
 * @return string
 */
function get_all_organizer_array($user_id = '', $args = []) {
	$all_organizer =get_all_event_organizer($user_id, $args);

	$organizer_array =array();

	if(is_array($all_organizer) && !empty($all_organizer)) {
		foreach($all_organizer as $organizer) {
			$organizer_array[$organizer->ID] = $organizer->post_title;
		}	
	}
	return $organizer_array;
}

/**
 * Returns total organizer.
 * 
 * @param null
 * @return string
 * @since 3.1.14
 */
function get_event_organizer_count($organizer_id = '') {
	return sizeof(get_event_by_organizer_id($organizer_id));
}

/**
 * Returns organizer ids.
 * 
 * @param null
 * @return string
 * @since 3.1.14
 */
function get_event_by_organizer_id($organizer_id = '') {
	if(!get_option('enable_event_organizer'))
		return false;

	$args = [
		'post_type'      => 'event_listing',
		'post_status'    => array('publish'),
		'posts_per_page' => -1,
		'suppress_filters' => 0,
	];

	if(!empty($organizer_id)) {
		$args['meta_query'][] = [
			'key' => '_event_organizer_ids',
            'value' => $organizer_id,
            'compare' => 'LIKE',
		];
	}

	return get_posts($args);
}

/**
 * Returns all venue of event.
 * 
 * @param null
 * @return string
 * @since 3.1.14
 */
function get_all_event_venue($user_id = '', $args = []) {
	if(!get_option('enable_event_venue'))
		return false;

	$query_args = array(
					'post_type'   => 'event_venue',
					'post_status' => 'publish',
					'posts_per_page'=> -1,
					'suppress_filters' => 0,
				);

	if(isset($user_id) && !empty($user_id) && !is_admin()) {
		$query_args['author'] = $user_id;	
	}

	if(isset($args) && !empty($args)) {
		$query_args = array_merge($query_args,$args);
	}

	$query_args = apply_filters('get_all_event_venue_args', $query_args);

	$all_venue = get_posts($query_args);

	if(!empty($all_venue)) {
		return $all_venue;	
	} else {
		return false;	
	}
}

/**
 * Returns array of venues.
 * 
 * @param null
 * @return string
 * @since 3.1.14
 */
function get_all_venue_array($user_id = '', $args = [], $blank_option = false) {
	$all_venue =get_all_event_venue($user_id, $args);
	
	$venue_array =array();

	if(is_array($all_venue) && !empty($all_venue)) {
		if($blank_option) {
			$venue_array[''] = __('Select Venue', 'wp-event-manager');
		}
		foreach($all_venue as $venue) {
			$venue_array[$venue->ID] = $venue->post_title;
		}	
	}
	return $venue_array;
}

/**
 * Returns total venues.
 * 
 * @param null
 * @return string
 * @since 3.1.16
 */
function get_event_venue_count($venue_id = '') {
	return sizeof(get_event_by_venue_id($venue_id));
}

/**
 * Returns venue id of event.
 * 
 * @param null
 * @return string
 * @since 3.1.14
 */
function get_event_by_venue_id($venue_id = '') {
	if(!get_option('enable_event_venue'))
		return false;
	
	$args = [
		'post_type'      => 'event_listing',
		'post_status'    => array('publish'),
		'posts_per_page' => -1,
		'suppress_filters' => 0,
	];
	if(!empty($venue_id)) {
		$args['meta_query'][] = [
			'key' => '_event_venue_ids',
            'value' => $venue_id,
            'compare' => 'LIKE',
		];
	}

	return get_posts($args);
}

/**
 * Check organizer ids.
 * @since 3.1.13
 * @param
 * @return
 **/
function has_event_organizer_ids($post = null) {
	$post = get_post($post);

	if($post->post_type !== 'event_listing')
		return;

	if(!empty($post->_event_organizer_ids))	{
		$organizer = get_post($post->_event_organizer_ids[0]);

		if(empty($organizer))
			return;

		if($organizer->post_status != 'publish')
			return;
	}
	return !empty($post->_event_organizer_ids) ? true : false;
}

/**
 * Get organizer ids.
 * 
 * @since 3.1.13
 **/
function get_event_organizer_ids($post = null) {
	$post = get_post($post);

	if($post->post_type !== 'event_listing')
		return;

	return !empty($post->_event_organizer_ids) ? $post->_event_organizer_ids : '';
}

/**
 * Check organizer exist in event organizer.
 * 
 * @since 3.1.15
 **/
function check_organizer_exist($organizer_email) {
	$args = [
			'post_type' 	=> 'event_organizer',
			'post_status' 	=> ['publish'],
			'suppress_filters' => 0,
			'meta_query' => [
	        [
	            'key'     => '_organizer_email',
	            'value'   => $organizer_email,
	            'compare' => '=',
	        ],
	    ],
	];

	$args = apply_filters('check_organizer_exist_query_args', $args);
	$organizer = get_posts($args);

	if(!empty($organizer) && isset($organizer[0]->ID)) {
		return $organizer[0]->ID;
	} else {
		return false;
	}
}

/**
 * Check venue ids.
 * 
 * @since 3.1.16
 **/
function has_event_venue_ids($post = null) {
	$post = get_post($post);

	if($post->post_type !== 'event_listing')
		return;

	if(!empty($post->_event_venue_ids))	{
		$venue = get_post($post->_event_venue_ids);
		if(empty($venue))
			return;

		if($venue->post_status != 'publish')
			return;
	}

	return !empty($post->_event_venue_ids) ? true : false;
}

/**
 * Get venue ids.
 * 
 * @since 3.1.16
 **/
function get_event_venue_ids($post = null) {
	$post = get_post($post);

	if($post->post_type !== 'event_listing')
		return;

	return !empty($post->_event_venue_ids) ? $post->_event_venue_ids : '';
}

/**
 * Manage order of event.
 * @since 3.1.18
 * @param null
 * @return array
 */
function get_event_order_by() {
	$args = [
				'title'   => [
					'label' => __('Event Title', 'wp-event-manager'),
					'type' => [
						'title|asc' => __('Ascending (ASC)', 'wp-event-manager'),
						'title|desc' => __('Descending (DESC)', 'wp-event-manager'),
					]
				],
				'event_start_date'   => [
					'label' => __('Event Start Date', 'wp-event-manager'),
					'type' => [
						'event_start_date|asc' => __('Ascending (ASC)', 'wp-event-manager'),
						'event_start_date|desc' => __('Descending (DESC)', 'wp-event-manager'),
					]
				],
				'event_end_date'   => [
					'label' => __('Event End Date', 'wp-event-manager'),
					'type' => [
						'event_end_date|asc' => __('Ascending (ASC)', 'wp-event-manager'),
						'event_end_date|desc' => __('Descending (DESC)', 'wp-event-manager'),
					]
				],
				'event_location'   => [
					'label' => __('Event Location', 'wp-event-manager'),
					'type' => [
						'event_location|asc' => __('Ascending (ASC)', 'wp-event-manager'),
						'event_location|desc' => __('Descending (DESC)', 'wp-event-manager'),
					]
				],
			];

	return apply_filters('get_event_order_by_args', $args);
}

if(!function_exists('get_wpem_email_from_name')) {
	/**
	* Get the from name for outgoing emails.
	*
	* @param string $from_name Default wp_mail() name associated with the "from" email address.
	* @return string
	* @since 3.1.35 
	*/
   function get_wpem_email_from_name($from_name = '') {
		$from_name = get_option('wpem_email_from_name');
		if(empty($from_name))
			$from_name = get_bloginfo('name');
	   $from_name = apply_filters( 'wpem_email_from_name', $from_name );
	   return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
   }
}

if(!function_exists('get_wpem_email_from_address')){
	/**
	 * Get the from address for outgoing emails.
	 *
	 * @param string $from_email Default wp_mail() email address to send from.
	 * @return string
	 * @since 3.1.35
	 */
	function get_wpem_email_from_address( $from_email = '' ) {
		$from_email = get_option('wpem_email_from_address');
		if(empty($from_email))
			$from_email = 'noreply@' . (isset($_SERVER['HTTP_HOST']) ? str_replace('www.', '', esc_url_raw( wp_unslash($_SERVER['HTTP_HOST']))) : 'noreply.com');
			// $from_email = "wordpress@".trim( str_replace( array( 'http://', 'https://' ), '', get_bloginfo('url')));
		$from_email = apply_filters( 'wpem_email_from_address', $from_email );
		return sanitize_email( $from_email );
	}
}

if(!function_exists('get_wpem_email_headers')) {
	/**
	 * Get email headers.
	 *
	 * @param string, string, string
	 * @return string
	 * @since 3.1.35
	 */
	function get_wpem_email_headers($post_id, $sender_name = '', $sender_address = '', $reply_name = '', $replay_address = '', $content_type = 'text/html; charset=UTF-8') {
		$header[] = 'Content-Type: text/html; charset=UTF-8';

		if (empty($sender_name)) 
			$sender_name = get_wpem_email_from_name();
		if(empty($sender_address))
			$sender_address = get_wpem_email_from_address();

		if (empty($reply_name)) 
			$reply_name = $sender_name;
		if(empty($replay_address))
			$replay_address = $sender_address;
		
		$header[] = 'From: '.$sender_name.'<'.$sender_address.'>';
		$header[] = 'Reply-to: ' . $reply_name . '<' . $replay_address . '>';
		return apply_filters( 'change_wpem_default_email_headers', $header, $content_type, $sender_name, $sender_address, $post_id);
	}
}

/**
 * wpem_convert_php_to_moment_format function
 * 
 */
function wpem_convert_php_to_moment_format($format) {
    $replacements = [
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '', // no equivalent
        'L' => '', // no equivalent
        'o' => 'YYYY',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'B' => '', // no equivalent
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz', // deprecated since version 1.6.0 of moment.js
        'I' => '', // no equivalent
        'O' => '', // no equivalent
        'P' => '', // no equivalent
        'T' => '', // no equivalent
        'Z' => '', // no equivalent
        'c' => '', // no equivalent
        'r' => '', // no equivalent
        'U' => 'X',
    ];
    $momentFormat = strtr($format, $replacements);
    return $momentFormat;
}

if ( !function_exists( 'get_event_by_user_id' ) ) {
    /**
     * This function is used to get event by user id.
     *
     * @access public
     * @param
     * @return array
     * @since 1.0.0
     */
    function get_event_by_user_id( $user_id = '' ) {
        return get_posts( apply_filters('wpem_events_by_user_id_args', array( 
            'posts_per_page' => - 1,
            'post_type' => 'event_listing',
            'post_status' => 'publish',
            'suppress_filters' => 'false',
            'author' => $user_id,
            'fields' => 'ids',
        ) ) );
    }
}

/**
 * Get all countries with country code.
 * 
 * We have used this method for organizer country selection.
 * @since 3.1.39
 * @return countries information
 */
function wpem_get_all_countries() {
	return apply_filters('wpem_all_countries', array (
		'' => 'Select Country',
		'AF' => 'Afghanistan',
		'AX' => 'Aland Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua And Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia And Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'CD' => 'Congo, Democratic Republic',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote D\'Ivoire',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island & Mcdonald Islands',
		'VA' => 'Holy See (Vatican City State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran, Islamic Republic Of',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle Of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KR' => 'Korea',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Lao People\'s Democratic Republic',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States Of',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territory, Occupied',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'BL' => 'Saint Barthelemy',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts And Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin',
		'PM' => 'Saint Pierre And Miquelon',
		'VC' => 'Saint Vincent And Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome And Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia And Sandwich Isl.',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard And Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad And Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks And Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Viet Nam',
		'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.S.',
		'WF' => 'Wallis And Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	) );
}

function wpem_embed_oembed_html($content) {
	echo apply_filters('wpem_embed_oembed_custome', $content);
}