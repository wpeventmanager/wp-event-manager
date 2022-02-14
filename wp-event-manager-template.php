<?php
/**
 * Template Functions
 *
 * Template functions specifically created for event listings and other event related methods.
 *
 * @author 	WP Event Manager
 * @category 	Core
 * @package 	Event Manager/Template
 * @version     1.0.5
 */

/**
 * Returns the translated role of the current user. If that user has
 * no role for the current `log, it returns false.
 *
 * @return string The name of the current role
 * @since 1.0.0
 **/
function get_event_manager_current_user_role() {
	global $wp_roles;
	$current_user = wp_get_current_user();
	$roles = $current_user->roles;
	$role = array_shift($roles);
	return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
}

/**
 * Get and include template files.
 *
 * @param mixed $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function get_event_manager_template( $template_name, $args = array(), $template_path = 'wp-event-manager', $default_path = '' ) {

	if ( $args && is_array( $args ) ) {

		extract( $args );
	}
	include( locate_event_manager_template( $template_name, $template_path, $default_path ) );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @param string $template_name
 * @param string $template_path (default: 'wp-event-manager')
 * @param string|bool $default_path (default: '') False to not load a default
 * @return string
 */
function locate_event_manager_template( $template_name, $template_path = 'wp-event-manager', $default_path = '' ) {

	// Look within passed path within the theme - this is priority

	$template = locate_template(
		
	array(
		
	trailingslashit( $template_path ) . $template_name,
		
	$template_name
	)
	);

	// Get default template

	if ( ! $template && $default_path !== false ) {

		$default_path = $default_path ? $default_path : EVENT_MANAGER_PLUGIN_DIR . '/templates/';

		if ( file_exists( trailingslashit( $default_path ) . $template_name ) ) {
				
			$template = trailingslashit( $default_path ) . $template_name;
		}
	}

	// Return what we found

	return apply_filters( 'event_manager_locate_template', $template, $template_name, $template_path );
}

/**
 * Get template part (for templates in loops).
 *
 * @param string $slug
 * @param string $name (default: '')
 * @param string $template_path (default: 'wp-event-manager')
 * @param string|bool $default_path (default: '') False to not load a default
 */
function get_event_manager_template_part( $slug, $name = '', $template_path = 'wp-event-manager', $default_path = '' ) {

	$template = '';

	if ( $name ) {

		$template = locate_event_manager_template( "{$slug}-{$name}.php", $template_path, $default_path );
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wp-event-manager/slug.php

	if ( ! $template ) {

		$template = locate_event_manager_template( "{$slug}.php", $template_path, $default_path );
	}

	if ( $template ) {

		load_template( $template, false );
	}
}

/**
 * Add custom body classes
 * @param  array $classes
 * @return array
 */
function event_manager_body_class( $classes ) {

	$classes   = (array) $classes;

	$classes[] = sanitize_title( wp_get_theme() );

	return array_unique( $classes );
}

add_filter( 'body_class', 'event_manager_body_class' );

/**
 * Get events pagination for [events] shortcode
 * @return [type] [description]
*/
function get_event_listing_pagination( $max_num_pages, $current_page = 1 ) {

	ob_start();

	get_event_manager_template( 'event-pagination.php', array( 'max_num_pages' => $max_num_pages, 'current_page' => absint( $current_page ) ) );

	return ob_get_clean();
}

/**
 * Outputs the events status
 *
 * @return void
 */
function display_event_status( $post = null ) {

	echo get_event_status( $post );
}

/**
 * Gets the events status
 *
 * @return string
 */
function get_event_status( $post = null ) {

	$post     = get_post( $post );

	$status   = $post->post_status;

	$statuses = get_event_listing_post_statuses();

	if ( isset( $statuses[ $status ] ) ) {

		$status = $statuses[ $status ];

	} else {

		$status = __( 'Inactive', 'wp-event-manager' );
	}
	return apply_filters( 'display_event_status', $status, $post );
}

/**
 * Return whether or not the position has been marked as cancelled
 *
 * @param  object $post
 * @return boolean
 */
function is_event_cancelled( $post = null ) {

	$post = get_post( $post );

	return $post->_cancelled ? true : false;
}

/**
 * Return whether or not the position has been featured
 *
 * @param  object $post
 * @return boolean
 */
function is_event_featured( $post = null ) {

	$post = get_post( $post );

	return $post->_featured ? true : false;
}

/**
 * Return whether or not registrations are allowed
 *
 * @param  object $post
 * @return boolean
 */
function attendees_can_apply( $post = null ) {
	$post = get_post( $post );
	return apply_filters( 'event_manager_attendees_can_register', ( ! is_event_cancelled() && ! in_array( $post->post_status, array( 'preview', 'expired' ) ) ), $post );
}

/**
 * display_event_permalink function.
 *
 * @access public
 * @return void
 */
function display_event_permalink( $post = null ) {

	echo get_event_permalink( $post );
}

/**
 * get_event_registration_method function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return object
 */
function get_event_registration_method( $post = null ) {
	$post = get_post( $post );

	if ( $post && $post->post_type !== 'event_listing' ) {
		return;
	}

	$method = new stdClass();
	$register  = $post->_registration;

	if ( empty( $register ) ) {
		$method->type ='url';
		return apply_filters( 'get_event_registration_method', $method, $post );
	}

	if ( strstr( $register, '@' ) && is_email( $register ) ) {
		$method->type      = 'email';
		$method->raw_email = $register;
		$method->email     = antispambot( $register );
		$method->subject   = apply_filters( 'event_manager_registration_email_subject', sprintf( __( 'Registration via "%s" listing on %s', 'wp-event-manager' ), $post->post_title, home_url() ), $post );
	} else {
		if ( strpos( $register, 'http' ) !== 0 )
			$register = 'http://' . $register;
		$method->type = 'url';
		$method->url  = $register;
	}

	return apply_filters( 'display_event_registration_method', $method, $post );
}

/**
 * get_event_permalink function
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_event_permalink( $post = null ) {

	$post = get_post( $post );

	$link = get_permalink( $post );

	return apply_filters( 'display_event_permalink', $link, $post );
}

/**
 * display_event_type function.
 *
 * @access public
 * @return void
 */
function display_event_type( $post = null, $after = '') {

	if ( $event_type = get_event_type( $post ) ) {
		if (! empty( $event_type ) ) {
		    $numType = count($event_type);
		    $i = 0;
			foreach ( $event_type as $type ) {
				echo '<a href="'.get_term_link($type->term_id).'"><span class="wpem-event-type-text event-type '. esc_attr( sanitize_title( $type->slug ) ).' ">'. $type->name.'</span></a>';
				if($numType > ++$i){
				    echo $after;
				}
			}
		}
	}
}

/**
 * get_event_type function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_event_type( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' || !get_option( 'event_manager_enable_event_types' ) ) {
		return;
	}

	$types = wp_get_post_terms( $post->ID, 'event_listing_type' );

	// Return single if not enabled.
	/*if ( !empty($types) ) {
		$types = array( current( $types ) );
	}*/
	if(empty($types))
		$types = '';
	return apply_filters( 'display_event_type', $types, $post );
}
/**
 * display_event_category function.
 *
 * @access public
 * @return void
 */
function display_event_category( $post = null, $after = '' ) {

	if ( $event_category = get_event_category( $post ) ) {

		if (! empty( $event_category ) ) {
		    $numCategory = count($event_category);
		    $i = 0;
			foreach ( $event_category as $cat ) {
				echo '<a href="'.get_term_link($cat->term_id).'"><span class="wpem-event-category-text event-category '. esc_attr( sanitize_title( $cat->slug ) ).' ">'. $cat->name.'</span></a>';
				if($numCategory > ++$i){
				    echo $after;
				}
			}
		}
	}
}

/**
 * get_event_category function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_event_category( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' || !get_option( 'event_manager_enable_categories' ) ) {
		return;
	}

	$categories = wp_get_post_terms( $post->ID, 'event_listing_category' );

	// Return single if not enabled.
	/*if ( !empty($categories) ) {
		$categories = array( current( $categories ) );
	}*/
	if(empty($categories))
		$categories = '';
	return apply_filters( 'display_event_category', $categories, $post );
}


/**
 * Returns the registration fields used when an account is required.
 *
 * @since 2.2
 *
 * @return array $registration_fields
 */
function wp_event_manager_get_registration_fields() {
	$generate_username_from_email      = event_manager_generate_username_from_email();
	$use_standard_password_setup_email = event_manager_use_standard_password_setup_email();
	$account_required  = event_manager_user_requires_account();

	$registration_fields = array();
	if ( event_manager_enable_registration() ) {

		$registration_fields['create_account_email'] = array(
				'type'        => 'text',
				'label'       => __( 'Your email', 'wp-event-manager' ),
				'placeholder' => __( 'you@yourdomain.com', 'wp-event-manager' ),
				'required'    => $account_required,
				'value'       => isset( $_POST['create_account_email'] ) ? sanitize_email($_POST['create_account_email']) : '',
		);

		if ( ! $generate_username_from_email ) {
			$registration_fields['create_account_username'] = array(
					'type'     => 'text',
					'label'    => __( 'Username', 'wp-event-manager' ),
					'required' => $account_required,
					'value'    => isset( $_POST['create_account_username'] ) ? sanitize_text_field($_POST['create_account_username']) : '',
			);
		}
		if ( !$use_standard_password_setup_email ) {
			$registration_fields['create_account_password'] = array(
					'type'         => 'password',
					'label'        => __( 'Password', 'wp-event-manager' ),
					'placeholder' => __( 'Password', 'wp-event-manager' ),
					'autocomplete' => false,
					'required'     => $account_required,
			);
			$password_hint = event_manager_get_password_rules_hint();
			if ( $password_hint ) {
				$registration_fields['create_account_password']['description'] = $password_hint;
			}
			$registration_fields['create_account_password_verify'] = array(
					'type'         => 'password',
					'label'        => __( 'Verify Password', 'wp-event-manager' ),
					'placeholder' => __( 'Confirm Password', 'wp-event-manager' ),
					'autocomplete' => false,
					'required'     => $account_required,
			);
		}
		
	}
	return apply_filters( 'event_manager_get_registration_fields', $registration_fields );
}

/**
 * display_event_publish_date function.
 * @param mixed $post (default: null)
 * @return [type]
 */
function display_event_publish_date( $post = null ) {
	$date_format = get_option( 'event_manager_date_format' );
	if ( $date_format === 'default' ) {
		$display_date = __( 'Posted on ', 'wp-event-manager' ) . get_post_time( get_option( 'date_format' ) );
	} else {
		$display_date = sprintf( __( 'Posted %s ago', 'wp-event-manager' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) );
	}
	echo '<time datetime="' . get_post_time( 'Y-m-d' ) . '">' . esc_html($display_date) . '</time>';
}

/**
 * get_event_publish_date function.
 * @param mixed $post (default: null)
 * @return [type]
 */
function get_event_publish_date( $post = null ) {
	$date_format = get_option( 'event_manager_date_format' );
	if ( $date_format === 'default' ) {
		return get_post_time( get_option( 'date_format' ) );
	} else {
		return sprintf( __( 'Posted %s ago', 'wp-event-manager' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) );
	}
}

/**
 * get_event_location function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_event_location( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' )
		return;

	return apply_filters( 'display_event_location', $post->_event_location, $post );
}

/**
* display_event_location function.
* @param  boolean $map_link whether or not to link to the map on google maps
* @return [type]
*/
function display_event_location( $map_link = true, $post = null ) {

	$location = get_event_location( $post );

	if(is_event_online($post))
	{
		echo apply_filters( 'display_event_location_anywhere_text', __( 'Online Event', 'wp-event-manager' ) );
	}
	else {

		if ( $map_link )
			echo apply_filters( 'display_event_location_map_link', '<a  href="http://maps.google.com/maps?q=' . urlencode( $location ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false" target="_blank">' . $location . '</a>', $location, $post );
		else
			echo  $location;

	}
}

/**
 * get_the_event_ticket function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_event_ticket_option( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' )
		return;
	if($post->_event_ticket_options == 'paid')
		$ticket_option = __('Paid','wp-event-manager');
	elseif($post->_event_ticket_options == 'free')
		$ticket_option = __('Free','wp-event-manager');
	elseif($post->_event_ticket_options == 'donation')
		$ticket_option = __('Donation','wp-event-manager');
	elseif($post->_event_ticket_options == 'paid/free')
		$ticket_option = __('Paid/Free','wp-event-manager');
	elseif($post->_event_ticket_options == 'paid/donation')
		$ticket_option = __('Paid/Donation','wp-event-manager');
	elseif($post->_event_ticket_options == 'free/donation')
		$ticket_option = __('Free/Donation','wp-event-manager');
	elseif($post->_event_ticket_options == 'paid/free/donation')
		$ticket_option = __('Paid/Free/Donation','wp-event-manager');
	elseif($post->_event_ticket_options == '')
		$ticket_option = '';


	return apply_filters( 'display_event_ticket_option', $ticket_option, $post );
}

/**
 * Display or retrieve the current event ticket price information with optional content.
 *
 * @access public
 * @param mixed $id (default: null
 * @return void
 */
function display_event_ticket_option( $before = '', $after = '', $echo = true, $post = null ) {

	$event_ticket_option = get_event_ticket_option( $post );

	if ( strlen( $event_ticket_option ) == 0 )
		return;

	$event_ticket_option = esc_attr( strip_tags( $event_ticket_option ) );
	
	//find the option value from the field editor array
	$fields = get_option( 'event_manager_form_fields',true);
	if(is_array($fields) && count($fields) > 0 ){
		$ticket_option_field = array_column($fields,'event_ticket_options');
		foreach ($ticket_option_field as $key => $value) {
			if(isset($value['options']) && isset($value['options'][$event_ticket_option]) ){
				$event_ticket_option = $value['options'][$event_ticket_option];
			}
		}
	}

	$event_ticket_option = $before . $event_ticket_option . $after;

	if ( $echo )
			
		echo $event_ticket_option;
		
	else
		return $event_ticket_option;
}

/**
 * get_event_registration_end_date function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_event_registration_end_date( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' )
		return;

	return apply_filters( 'display_event_registration_end_date', $post->_event_registration_deadline, $post );
}

/**
 * Display or retrieve the current event registration end date.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_registration_end_date( $before = '', $after = '', $echo = true, $post = null ) {

	$event_registration_end_date = get_event_registration_end_date( $post );

	if ( strlen( $event_registration_end_date ) == 0 )
		return;

		$date_format 		= WP_Event_Manager_Date_Time::get_event_manager_view_date_format();
		$event_registration_end_date 	= date_i18n( $date_format, strtotime( $event_registration_end_date ) );

	$event_registration_end_date = $before . $event_registration_end_date . $after;

	if ( $echo )
			
		echo $event_registration_end_date;
		
	else

		return $event_registration_end_date;
}

/**
 * get_the_event_logo function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_event_banner( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' )
		return;

	if(isset($post->_event_banner) && empty($post->_event_banner))
		$event_banner = apply_filters( 'event_manager_default_event_banner', EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-placeholder-wide.jpg' );
	else
		$event_banner = $post->_event_banner;			
	
	return apply_filters( 'display_event_banner', $event_banner, $post );
}

/**
 * get_event_thumbnail function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_event_thumbnail( $post = null, $size = 'full' ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' )
		return;

	$event_thumbnail = get_the_post_thumbnail_url( $post, $size );

	if( isset($event_thumbnail) && empty($event_thumbnail) )
		$event_thumbnail = apply_filters( 'event_manager_default_event_banner', EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-placeholder.jpg' );	
	
	return apply_filters( 'display_event_thumbnail', $event_thumbnail, $post );
}

/**
 * display_event_banner function.
 *
 * @access public
 * @param string $size (default: 'full')
 * @param mixed $default (default: null)
 * @return void
 */
function display_event_banner( $size = 'full', $default = null, $post = null ) {

	$banner = get_event_banner( $post );

	if ( ! empty( $banner ) && ! is_array( $banner )  && ( strstr( $banner, 'http' ) || file_exists( $banner ) ) )
	{
		if ( $size !== 'full' ) {
				
			$banner = event_manager_get_resized_image( $banner, $size );
		}
		echo '<link rel="image_src" href="' . esc_attr( $banner ) . '"/>';
		echo '<img itemprop="image" content="' . esc_attr( $banner ) . '" src="' . esc_attr( $banner ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';

	} else if ( $default ) {

		echo '<img itemprop="image" content="' . esc_attr( $default ) . '" src="' . esc_attr( $default ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';

	} else if(is_array($banner) && isset($banner[0]) ){
		echo '<img itemprop="image" content="' . esc_attr( $banner[0] ) . '" src="' . esc_attr( $banner[0] ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';
	}
	else  {
		echo '<img itemprop="image" content="' . esc_attr( apply_filters( 'event_manager_default_event_banner', EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-placeholder.jpg' ) ) . '" src="' . esc_attr( apply_filters( 'event_manager_default_event_banner', EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-placeholder.jpg' ) ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
	}
}

/**
 * get_event_start_date function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function get_event_start_date( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' ) {

		return '';
	}

	$event_start_date 	= $post->_event_start_date;

	return apply_filters( 'display_event_start_date', $event_start_date, $post );
}

/**
 * Display or retrieve the current event  start date.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_start_date( $before = '', $after = '', $echo = true, $post = null ) {

	$event_start_date = get_event_start_date( $post );

	if ( strlen( $event_start_date ) == 0 )
		return;

	$date_format 		= WP_Event_Manager_Date_Time::get_event_manager_view_date_format();
	$event_start_date 	= date_i18n( $date_format, strtotime( $event_start_date ) );

	$event_start_date = $before . $event_start_date . $after;

	if ( $echo )			
		echo $event_start_date;		
	else
		return $event_start_date;
}

/**
 * get_event_start_time function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function get_event_start_time( $post = null ) {

	$post = get_post( $post );
	if ( $post->post_type !== 'event_listing'  || empty($post->_event_start_time)) {
		return '';
	}
	
	$event_timezone 	= get_event_timezone_abbr( $post );
	$time_format 		= WP_Event_Manager_Date_Time::get_timepicker_format();
	$event_start_time 	= date_i18n( $time_format ,strtotime($post->_event_start_time) );

	if( $event_timezone )
		$event_start_time = $event_start_time.' (' . $event_timezone.')';
	else 
		$event_start_time = $event_start_time;

	return apply_filters( 'display_event_start_time', $event_start_time , $post );
}

/**
 * Display or retrieve the current event start time.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_start_time( $before = '', $after = '', $echo = true, $post = null ) {

	$event_start_time = get_event_start_time( $post );

	if ( strlen( $event_start_time ) == 0 )
		return;

	$event_start_time = esc_attr( strip_tags( $event_start_time ) );

	$event_start_time = $before . $event_start_time . $after;

	if ( $echo )			
		echo $event_start_time;		
	else
		return $event_start_time;
}

/**
 * get_event_end_date function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function get_event_end_date( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' ) {

		return '';
	}
	$event_end_date = $post->_event_end_date;
	return apply_filters( 'display_event_end_date', $event_end_date, $post );
}

/**
 * Display or retrieve the current event end date.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_end_date( $before = '', $after = '', $echo = true, $post = null ) {

	$event_end_date = get_event_end_date( $post );
	if ( strlen( $event_end_date ) == 0 )
		return;

	$event_end_date = esc_attr( strip_tags( $event_end_date ) );
	$date_format = WP_Event_Manager_Date_Time::get_event_manager_view_date_format();
	$event_end_date = date_i18n( $date_format, strtotime( $event_end_date ) );	

	$event_end_date = $before . $event_end_date . $after;

	if ( $echo )
		echo $event_end_date;
	else
		return $event_end_date;
}

/**
 * get_event_end_time function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function get_event_end_time( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' || empty($post->_event_end_time) ) {
		return '';
	}

	$event_timezone 	= get_event_timezone_abbr( $post );
	$time_format 		= WP_Event_Manager_Date_Time::get_timepicker_format();
	$event_end_time 	= date_i18n( $time_format ,strtotime($post->_event_end_time) );

	if( $event_timezone )
		$event_end_time = $event_end_time.' (' . $event_timezone.')';
	else 
		$event_end_time = $event_end_time;

	return apply_filters( 'display_event_end_time', $event_end_time, $post );
}

/**
 * Display or retrieve the current event end time.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_end_time( $before = '', $after = '', $echo = true, $post = null ) {

	$event_end_time = get_event_end_time( $post );
	if ( strlen( $event_end_time ) == 0 )
		return;

	$event_end_time= esc_attr( strip_tags( $event_end_time ) );

	$event_end_time= $before . $event_end_time . $after;

	if ( $echo )
		echo $event_end_time;		
	else
		return $event_end_time;
}

/**
 * get_event_timezone function.
 *
 * @access public
 * @since 3.0
 * @param int $post (default: null)
 * @return string
 */
function get_event_timezone( $post = null , $abbr = true ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' ) {

		return '';
	}

	if(WP_Event_Manager_Date_Time::get_event_manager_timezone_setting() == 'site_timezone')
		return false;

	$timezone = $post->_event_timezone;

	if( empty($timezone) )
	{
		$timezone = wp_timezone_string();
	}

	return apply_filters( 'display_event_timezone', $timezone, $post );
}

/**
 * Display or retrieve the user selected timezone.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_timezone( $before = '', $after = '', $echo = true, $post = null ) {

	$event_timezone = get_event_timezone( $post );

	if ( strlen( $event_timezone ) == 0 )
		return;

	$event_timezone= $before . $event_timezone . $after;

	if ( $echo )			
		echo $event_timezone;
	else
		return $event_timezone;
}

/**
 * get_event_timezone function.
 *
 * @since 3.0
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function get_event_timezone_abbr( $post = null  ){

    $event_timezone = get_event_timezone($post);

	if( $event_timezone ) 
		$event_timezone = WP_Event_Manager_Date_Time::convert_event_timezone_into_abbr( $event_timezone );

	return apply_filters( 'display_event_timezone_abbr', $event_timezone, $post );
}

/**
 * Display or retrieve the user selected timezone in abbriviation.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_timezone_abbr( $before = '', $after = '', $echo = true, $post = null ) {

	$event_timezone = get_event_timezone_abbr( $post );

	if ( strlen( $event_timezone ) == 0 )
		return;

	$event_timezone= $before . $event_timezone . $after;

	if ( $echo )			
		echo $event_timezone;
	else
		return $event_timezone;
}

/**
 * get_event_venue_name function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_event_venue_name( $post = null, $link = false ) {

	$post = get_post( $post );
	/* if ( $post->post_type !== 'event_listing' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_venue']) )
		return;

	if(!empty($post->_event_venue_ids))
	{
		$venue_name = '';
		
		if($link)
		{
			$venue_name .= '<a href="'. get_permalink( $post->_event_venue_ids ) .'">';
		}

		$venue_name .= get_post_meta($post->_event_venue_ids, '_venue_name', true);

		if($link)
		{
			$venue_name .= '</a>';
		}

		return apply_filters( 'display_event_venue_name', $venue_name, $post );
	}

	if( $post->post_type == 'event_venue' )
		return apply_filters( 'display_event_venue_name', $post->_venue_name, $post );
	else
		return apply_filters( 'display_event_venue_name', $post->_event_venue_name, $post );
}

/**
 * Display or retrieve the current event venue name.
 *
 * @access public
 *
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_venue_name( $before = '', $after = '', $echo = true, $post = null ) {

	$event_venue_name = get_event_venue_name( $post );

	if ( strlen( $event_venue_name ) == 0 )
		return;

	$event_venue_name = esc_attr( strip_tags( $event_venue_name ) );

	$event_venue_name = $before . $event_venue_name . $after;

	if ( $echo )
		echo $event_venue_name;
	else
		return $event_venue_name;
}

/**
 * is_event_online function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function is_event_online($post = null ) {

	$post = get_post( $post );
	if ( $post->post_type !== 'event_listing' )
		return;

	if(get_event_location($post)=='Online Event' || get_event_location($post) == '' || $post->_event_online =='yes' )
		return true;
	else
		return false;
}

/**
 * get_event_address function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_event_address( $post = null ) {

	$post = get_post( $post );
	if ( $post->post_type !== 'event_listing' )
		return;

	return apply_filters( 'display_event_address', $post->_event_address, $post );
}

/**
 * Display or retrieve the current event address.
 *
 * @access public
 *
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_address( $before = '', $after = '', $echo = true, $post = null ) {

	$event_address = get_event_address( $post );

	if ( strlen( $event_address ) == 0 )
		return;

	$event_address = esc_attr( strip_tags( $event_address ) );

	$event_address = $before . $event_address . $after;

	if ( $echo )
		echo $event_address;
	else
		return $event_address;
}

/**
 * get_event_pincode function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_event_pincode( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' )
		return;

	return apply_filters( 'display_event_pincode', $post->_event_pincode, $post );
}

/**
 * Display or retrieve the current event pincode.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_event_pincode( $before = '', $after = '', $echo = true, $post = null ) {

	$event_pincode = get_event_pincode( $post );

	if ( strlen( $event_pincode ) == 0 )
		return;

	$event_pincode = esc_attr( strip_tags( $event_pincode ) );

	$event_pincode = $before . $event_pincode . $after;

	if ( $echo )
			
		echo $event_pincode;
		
	else

		return $event_pincode;
}

/**
 * get_organizer_name function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function get_organizer_name( $post = null, $link = false, $link_type = 'frontend' ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
	{
		return '';
	}

	if(!empty($post->_event_organizer_ids))
	{
		$organizer_name = '';

		foreach ($post->_event_organizer_ids as $key => $organizer_id) {
			if($key > 0)
			{
				$organizer_name .= ', ';					
			}

			if($link)
			{
				if($link_type == 'backend')
				{
					$organizer_name .= '<a href="'. get_edit_post_link( $organizer_id ) .'">';	
				}
				else
				{
					$organizer_name .= '<a href="'. get_permalink( $organizer_id ) .'">';
				}
			}

			$organizer_name .= get_post_meta($organizer_id, '_organizer_name', true);

			if($link)
			{
				$organizer_name .= '</a>';
			}
		}

		return apply_filters( 'display_organizer_name', $organizer_name, $post );
	}

	return apply_filters( 'display_organizer_name', $post->_organizer_name, $post );
}

/**
 * Display or retrieve the current organization or company name who oraganizing events with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_name( $before = '', $after = '', $echo = true, $post = null ) {
	$organizer_name = get_organizer_name( $post );

	if ( strlen( $organizer_name ) == 0 )
		return;

	$organizer_name = esc_attr( strip_tags( $organizer_name ) );

	$organizer_name = $before . $organizer_name . $after;

	if ( $echo )			
		echo $organizer_name;		
	else
		return $organizer_name;
}

/**
 * get_organizer_description_data function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function get_organizer_description( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
	{
		return '';
	}

	return apply_filters( 'display_organizer_description', $post->_organizer_description, $post );
}

/**
 * display_organizer_logo function.
 *
 * @access public
 * @param string $size (default: 'full')
 * @param mixed $default (default: null)
 * @return void
 */
function display_organizer_logo( $size = 'full', $default = null, $post = null ) {

	/* $logo = get_organizer_logo( $post = null, $size = 'full'  ); */
	$logo = get_organizer_logo( $post, $size );

	if ( has_post_thumbnail( $post ) ) {
		echo '<img class="organizer_logo" src="' . esc_attr( $logo ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';

		// Before 1.0., logo URLs were stored in post meta.
	} elseif ( ! empty( $logo ) && !is_array($logo) && ( strstr( $logo, 'http' ) || file_exists( $logo ) ) ) {

		if ( $size !== 'full' ) {
				
			$logo = event_manager_get_resized_image( $logo, $size );
		}

		echo '<img src="' . esc_attr( $logo ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';

	} elseif ( $default ) {

		echo '<img src="' . esc_attr( $default ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';

	}else if(is_array($logo) && isset($logo[0]) ){
		echo '<img itemprop="image" content="' . esc_attr( $logo[0] ) . '" src="' . esc_attr( $logo[0] ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';
	}
	 else {

		echo '<img src="' . esc_attr( apply_filters( 'event_manager_default_organizer_logo', EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-placeholder.jpg' ) ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';
	}
}

/**
 * get_organizer_logo function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_organizer_logo( $post = null , $size = 'full') {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_organizer' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	if ( has_post_thumbnail( $post->ID ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
		return $src ? $src[0] : '';
	} 
	elseif ( ! empty( $post->_organizer_logo ) ) {
		// Before were stored in post meta.
		return apply_filters( 'display_organizer_logo', $post->_organizer_logo, $post );
	}

	return '';
}

/**
 * get_venue_description_data function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function get_venue_description( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_venue']) )
	{
		return '';
	}

	return apply_filters( 'display_venue_description', $post->_venue_description, $post );
}

/**
 * display_venue_logo function.
 *
 * @access public
 * @param string $size (default: 'full')
 * @param mixed $default (default: null)
 * @return void
 */
function display_venue_logo( $size = 'full', $default = null, $post = null ) {

	/* $logo = get_venue_logo( $post = null, $size = 'full'  ); */
	$logo = get_venue_logo( $post, $size );

	if ( has_post_thumbnail( $post ) ) {
		echo '<img class="venue_logo" src="' . esc_attr( $logo ) . '" alt="' . esc_attr( get_event_venue_name( $post ) ) . '" />';

		// Before 1.0., logo URLs were stored in post meta.
	} elseif ( ! empty( $logo ) && ! is_array( $logo ) && ( strstr( $logo, 'http' ) || file_exists( $logo ) ) ) {

		if ( $size !== 'full' ) {
				
			$logo = event_manager_get_resized_image( $logo, $size );
		}

		echo '<img src="' . esc_attr( $logo ) . '" alt="' . esc_attr( get_event_venue_name( $post ) ) . '" />';

	} elseif ( $default ) {

		echo '<img src="' . esc_attr( $default ) . '" alt="' . esc_attr( get_event_venue_name( $post ) ) . '" />';

	}



	else if(is_array($logo) && isset($logo[0]) ){
		echo '<img itemprop="image" content="' . esc_attr( $logo[0] ) . '" src="' . esc_attr( $logo[0] ) . '" alt="' . esc_attr( get_organizer_name( $post ) ) . '" />';
	}

	 else {

		echo '<img src="' . esc_attr( apply_filters( 'event_manager_default_venue_logo', EVENT_MANAGER_PLUGIN_URL . '/assets/images/wpem-placeholder.jpg' ) ) . '" alt="' . esc_attr( get_event_venue_name( $post ) ) . '" />';
	}
}

/**
 * get_venue_logo function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_venue_logo( $post = null , $size = 'full') {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_venue' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_venue']) )
		return;

	if ( has_post_thumbnail( $post->ID ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
		return $src ? $src[0] : '';
	} 
	elseif ( ! empty( $post->_venue_logo ) ) {
		// Before were stored in post meta.
		return apply_filters( 'display_venue_logo', $post->_venue_logo, $post );
	}

	return '';
}

/**
 * Resize and get url of the image
 *
 * @param  string $logo
 * @param  string $size
 * @return string
 */
function event_manager_get_resized_image( $logo, $size ) {

	global $_wp_additional_image_sizes;

	if ( $size !== 'full' && strstr( $logo, WP_CONTENT_URL ) && ( isset( $_wp_additional_image_sizes[ $size ] ) || in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) ) {

		if ( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				
			$img_width  = get_option( $size . '_size_w' );
				
			$img_height = get_option( $size . '_size_h' );
				
			$img_crop   = get_option( $size . '_size_crop' );
				
		} else {
				
			$img_width  = $_wp_additional_image_sizes[ $size ]['width'];
				
			$img_height = $_wp_additional_image_sizes[ $size ]['height'];
				
			$img_crop   = $_wp_additional_image_sizes[ $size ]['crop'];
		}

		$upload_dir        = wp_upload_dir();

		$logo_path         = str_replace( array( $upload_dir['baseurl'], $upload_dir['url'], WP_CONTENT_URL ), array( $upload_dir['basedir'], $upload_dir['path'], WP_CONTENT_DIR ), $logo );

		$path_parts        = pathinfo( $logo_path );

		$dims              = $img_width . 'x' . $img_height;
		$resized_logo_path = str_replace( '.' . $path_parts['extension'], '-' . $dims . '.' . $path_parts['extension'], $logo_path );

		if ( strstr( $resized_logo_path, 'http:' ) || strstr( $resized_logo_path, 'https:' ) ) {
				
			return $logo;
		}

		if ( ! file_exists( $resized_logo_path ) ) {
				
			ob_start();
				
			$image = wp_get_image_editor( $logo_path );
				
			if ( ! is_wp_error( $image ) ) {

				$resize = $image->resize( $img_width, $img_height, $img_crop );

				if ( ! is_wp_error( $resize ) ) {
						
					$save = $image->save( $resized_logo_path );
						
					if ( ! is_wp_error( $save ) ) {

						$logo = dirname( $logo ) . '/' . basename( $resized_logo_path );
					}
				}
			}
				
			ob_get_clean();
				
		} else {
				
			$logo = dirname( $logo ) . '/' . basename( $resized_logo_path );
		}
	}
	return $logo;
}

/**
 * get_event_organizer_contact_person_name function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_event_organizer_contact_person_name( $post = null ) {

	$post = get_post( $post );

	if ( $post->post_type !== 'event_listing' )
		return;

	return apply_filters( 'display_organizer_contact_person_name', $post->_organizer_contact_person_name, $post );
}

/**
 * Display or retrieve the current organization's contact person name with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_contact_person_name( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_contact_person_name = get_event_organizer_contact_person_name( $post );

	if ( strlen( $organizer_contact_person_name ) == 0 )
		return;

	$organizer_contact_person_name = esc_attr( strip_tags( $organizer_contact_person_name ) );

	$organizer_contact_person_name = $before . $organizer_contact_person_name . $after;

	if ( $echo )
			
		echo $organizer_contact_person_name;
		
	else
		return $organizer_contact_person_name;
}

/**
 * get_event_organizer_email function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_event_organizer_email( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	if(!empty($post->_event_organizer_ids))
	{
		$organizers_email = '';

		foreach ($post->_event_organizer_ids as $key => $organizer_id) {
			if($key > 0)
			{
				$organizers_email .= ', ';					
			}

			$organizers_email .= get_post_meta($organizer_id, '_organizer_email', true);
		}

		return apply_filters( 'display_organizer_email', $organizers_email, $post );
	}

	return apply_filters( 'display_organizer_email', $post->_organizer_email, $post );
}

/**
 * Display or retrieve the current organizer email with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_email( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_email = get_event_organizer_email( $post );

	if ( strlen( $organizer_email ) == 0 )
		return;

	$organizer_email = esc_attr( strip_tags( $organizer_email ) );

	$organizer_email = $before . $organizer_email . $after;

	if ( $echo )
			
		echo $organizer_email;
		
	else
		return $organizer_email;
}

/**
 * Get the organizer video URL
 *
 * @param mixed $post (default: null)
 * @return string
 */
function get_organizer_video( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	return apply_filters( 'display_organizer_video', $post->_organizer_video, $post );
}

/**
 * Output the organizer video
 */
function display_organizer_video( $before = '', $after = '', $echo = true, $post = null) {

	$video_embed = false;
	$video       = get_organizer_video( $post );
	$filetype    = wp_check_filetype( $video );
	if( ! empty( $video ) )
	{
		// FV Wordpress Flowplayer Support for advanced video formats
		if ( shortcode_exists( 'flowplayer' ) ) {
			$video_embed = '[flowplayer src="' . esc_attr( $video ) . '"]';
		} elseif ( ! empty( $filetype[ 'ext' ] ) ) {
			$video_embed = wp_video_shortcode( array( 'src' => $video ) );
		} else {
			$video_embed = wp_oembed_get( $video );
		}
	}
	$video_embed = apply_filters( 'display_organizer_video_embed', $video_embed, $post );
	if ( $video_embed ) {
		echo '<div class="organizer_video">' . $video_embed . '</div>';
	}
}

/**
 * get_organizer_website function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function get_organizer_website( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_organizer' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	if(!empty($post->_event_organizer_ids))
	{
		$website = '';

		foreach ($post->_event_organizer_ids as $key => $organizer_id) 
		{
			$website .= get_post_meta($organizer_id, '_organizer_website', true);

			if ( $website && ! strstr( $website, 'http:' ) && ! strstr( $website, 'https:' ) ) {
			
				$website .= 'http://' . $website;
			}			
		}

		return apply_filters( 'display_organizer_website', $website, $post );
	}

	$website = $post->_organizer_website;

	if ( $website && ! strstr( $website, 'http:' ) && ! strstr( $website, 'https:' ) ) {
			
		$website = 'http://' . $website;
	}
	return apply_filters( 'display_organizer_website', $website, $post );
}

/**
 * Display or retrieve the current organizer website with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_website( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_website = get_organizer_website( $post );

	if ( strlen( $organizer_website ) == 0 )
		return;

	$organizer_website = esc_attr( strip_tags( $organizer_website ) );

	$organizer_website = $before . $organizer_website . $after;

	if ( $echo )			
		echo $organizer_website;		
	else
		return $organizer_website;
}

/**
 * get_venue_website function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function get_venue_website( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_venue' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_venue']) )
		return;

	if(!empty($post->_event_venue_ids))
	{
		$website = '';

		$website .= get_post_meta($post->_event_venue_ids, '_venue_website', true);

		if ( $website && ! strstr( $website, 'http:' ) && ! strstr( $website, 'https:' ) ) {
		
			$website .= 'http://' . $website;
		}

		return apply_filters( 'display_venue_website', $website, $post );
	}

	$website = $post->_venue_website;

	if ( $website && ! strstr( $website, 'http:' ) && ! strstr( $website, 'https:' ) ) {
			
		$website = 'http://' . $website;
	}
	return apply_filters( 'display_venue_website', $website, $post );
}

/**
 * Display or retrieve the current venue website with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_venue_website( $before = '', $after = '', $echo = true, $post = null ) {

	$venue_website = get_venue_website( $post );

	if ( strlen( $venue_website ) == 0 )
		return;

	$venue_website = esc_attr( strip_tags( $venue_website ) );

	$venue_website = $before . $venue_website . $after;

	if ( $echo )			
		echo $venue_website;		
	else
		return $venue_website;
}

/**
 * Display or retrieve the current organizer tagline with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_tagline( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_tagline = get_organizer_tagline( $post );

	if ( strlen( $organizer_tagline ) == 0 )
		return;

	$organizer_tagline = esc_attr( strip_tags( $organizer_tagline ) );

	$organizer_tagline = $before . $organizer_tagline . $after;

	if ( $echo )			
		echo $organizer_tagline;
	else
		return $organizer_tagline;
}

/**
 * get_organizer_tagline function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_organizer_tagline( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	return apply_filters( 'display_organizer_tagline', $post->_organizer_tagline, $post );
}

/**
 * get_organizer_twitter function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_organizer_twitter( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_organizer' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	$organizer_twitter = $post->_organizer_twitter;

	if ( strlen( $organizer_twitter ) == 0 )			
		return;
		
	if ( strpos( $organizer_twitter, '@' ) === 0 )

		$organizer_twitter = substr( $organizer_twitter, 1 );

	return apply_filters( 'display_organizer_twitter', $organizer_twitter, $post );
}

/**
 * Display or retrieve the current organizer twitter link with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_twitter( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_twitter = get_organizer_twitter( $post );

	if ( strlen( $organizer_twitter ) == 0 )
		return;

	$organizer_twitter = esc_attr( strip_tags( $organizer_twitter ) );

	$organizer_twitter = $before . '<a href="http://twitter.com/' . $organizer_twitter . '" class="organizer_twitter" target="_blank">' . $organizer_twitter . '</a>' . $after;

	if ( $echo )			
		echo $organizer_twitter;		
	else
		return $organizer_twitter;
}

/**
 * get_venue_twitter function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_venue_twitter( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_venue' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_venue']) )
		return;

	$venue_twitter = $post->_venue_twitter;

	if ( strlen( $venue_twitter ) == 0 )			
		return;
		
	if ( strpos( $venue_twitter, '@' ) === 0 )

		$venue_twitter = substr( $venue_twitter, 1 );

	return apply_filters( 'display_venue_twitter', $venue_twitter, $post );
}

/**
 * Display or retrieve the current venue twitter link with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_venue_twitter( $before = '', $after = '', $echo = true, $post = null ) {

	$venue_twitter = get_venue_twitter( $post );

	if ( strlen( $venue_twitter ) == 0 )
		return;

	$venue_twitter = esc_attr( strip_tags( $venue_twitter ) );

	$venue_twitter = $before . '<a href="http://twitter.com/' . $venue_twitter . '" class="venue_twitter" target="_blank">' . $venue_twitter . '</a>' . $after;

	if ( $echo )			
		echo $venue_twitter;		
	else
		return $venue_twitter;
}

/**
 * get_organizer_facebook function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_organizer_facebook( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_organizer' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	$organizer_facebook = $post->_organizer_facebook;

	if ( strlen( $organizer_facebook ) == 0 )			
		return;
		
	return apply_filters( 'display_organizer_facebook', $organizer_facebook, $post );
}

/**
 * Display or retrieve the current organizer page on facebook.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_facebook( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_facebook = get_organizer_facebook( $post );

	if ( strlen( $organizer_facebook ) == 0 )
		return;

	$organizer_facebook = esc_attr( strip_tags( $organizer_facebook ) );

	$organizer_facebook = $before . $organizer_facebook . $after;

	if ( $echo )			
		echo $organizer_facebook;		
	else
		return $organizer_facebook;
}

/**
 * get_venue_facebook function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_venue_facebook( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_venue' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_venue']) )
		return;

	$venue_facebook = $post->_venue_facebook;

	if ( strlen( $venue_facebook ) == 0 )			
		return;
		
	return apply_filters( 'display_venue_facebook', $venue_facebook, $post );
}

/**
 * Display or retrieve the current venue page on facebook.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_venue_facebook( $before = '', $after = '', $echo = true, $post = null ) {

	$venue_facebook = get_venue_facebook( $post );

	if ( strlen( $venue_facebook ) == 0 )
		return;

	$venue_facebook = esc_attr( strip_tags( $venue_facebook ) );

	$venue_facebook = $before . $venue_facebook . $after;

	if ( $echo )			
		echo $venue_facebook;		
	else
		return $venue_facebook;
}

/**
 * get_organizer_linkedin function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_organizer_linkedin( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	$organizer_linkedin = $post->_organizer_linkedin;

	if ( strlen( $organizer_linkedin ) == 0 )			
		return;
		
	return apply_filters( 'display_organizer_linkedin', $organizer_linkedin, $post );
}

/**
 * Display or retrieve the current organizer page on Linkedin.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_linkedin( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_linkedin = get_organizer_linkedin( $post );

	if ( strlen( $organizer_linkedin ) == 0 )
		return;

	$organizer_linkedin = esc_attr( strip_tags( $organizer_linkedin ) );

	$organizer_linkedin = $before . $organizer_linkedin . $after;

	if ( $echo )			
		echo $organizer_linkedin;		
	else
		return $organizer_linkedin;
}

/**
 * get_organizer_xing function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_organizer_xing( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	$organizer_xing = $post->_organizer_xing;

	if ( strlen( $organizer_xing ) == 0 )			
		return;
		
	return apply_filters( 'display_organizer_xing', $organizer_xing, $post );
}

/**
 * Display or retrieve the current organizer link on xing.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_xing( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_xing = get_organizer_xing( $post );

	if ( strlen( $organizer_xing ) == 0 )
		return;

	$organizer_xing = esc_attr( strip_tags( $organizer_xing ) );

	$organizer_xing = $before . $organizer_xing . $after;

	if ( $echo )			
		echo $organizer_xing;
	else
		return $organizer_xing;
}

/**
 * get_organizer_instagram function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_organizer_instagram( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	$organizer_instagram = $post->_organizer_instagram;

	if ( strlen( $organizer_instagram ) == 0 )			
		return;
		
	return apply_filters( 'display_organizer_instagram', $organizer_instagram, $post );
}

/**
 * Display or retrieve the current organizer link on instagram.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_instagram( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_instagram = get_organizer_instagram( $post );

	if ( strlen( $organizer_instagram ) == 0 )
		return;

	$organizer_instagram = esc_attr( strip_tags( $organizer_instagram ) );

	$organizer_instagram = $before . $organizer_instagram . $after;

	if ( $echo )			
		echo $organizer_instagram;		
	else
		return $organizer_instagram;
}

/**
 * get_venue_instagram function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_venue_instagram( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_venue']) )
		return;

	$venue_instagram = $post->_venue_instagram;

	if ( strlen( $venue_instagram ) == 0 )			
		return;
		
	return apply_filters( 'display_venue_instagram', $venue_instagram, $post );
}

/**
 * Display or retrieve the current venue link on instagram.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_venue_instagram( $before = '', $after = '', $echo = true, $post = null ) {

	$venue_instagram = get_venue_instagram( $post );

	if ( strlen( $venue_instagram ) == 0 )
		return;

	$venue_instagram = esc_attr( strip_tags( $venue_instagram ) );

	$venue_instagram = $before . $venue_instagram . $after;

	if ( $echo )			
		echo $venue_instagram;		
	else
		return $venue_instagram;
}

/**
 * get_organizer_pinterest function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_organizer_pinterest( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	$organizer_pinterest = $post->_organizer_pinterest;

	if ( strlen( $organizer_pinterest ) == 0 )			
		return;
		
	return apply_filters( 'display_organizer_pinterest', $organizer_pinterest, $post );
}

/**
 * Display or retrieve the current organizer link on pinterest.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_pinterest( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_pinterest = get_organizer_pinterest( $post );

	if ( strlen( $organizer_pinterest ) == 0 )
		return;

	$organizer_pinterest = esc_attr( strip_tags( $organizer_pinterest ) );

	$organizer_pinterest = $before . $organizer_pinterest . $after;

	if ( $echo )
		echo $organizer_pinterest;
	else
		return $organizer_pinterest;
}

/**
 * get_organizer_youtube function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */

function get_organizer_youtube( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_organizer' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	$organizer_youtube = $post->_organizer_youtube;

	if( in_array($post->post_type, ['event_listing']) )
	{
		if($organizer_youtube == '')
			$organizer_youtube = $post->_event_video_url;	
	}

	if ( strlen( $organizer_youtube ) == 0 )			
		return;
		
	return apply_filters( 'display_organizer_youtube', $organizer_youtube, $post );
}

/**
 * Display or retrieve the current organizer link on youtube.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_youtube( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_youtube = get_organizer_youtube( $post );

	if ( strlen( $organizer_youtube ) == 0 )
		return;

	$organizer_youtube = esc_attr( strip_tags( $organizer_youtube ) );

	$organizer_youtube = $before . $organizer_youtube . $after;

	if ( $echo )			
		echo $organizer_youtube;
	else
		return $organizer_youtube;
}

/**
 * get_venue_youtube function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */

function get_venue_youtube( $post = null ) {

	$post = get_post( $post );

	/* if ( $post->post_type !== 'event_listing' || $post->post_type !== 'event_venue' ) */
	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_venue']) )
		return;

	$venue_youtube = $post->_venue_youtube;

	if ( strlen( $venue_youtube ) == 0 )			
		return;
		
	return apply_filters( 'display_venue_youtube', $venue_youtube, $post );
}

/**
 * Display or retrieve the current venue link on youtube.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_venue_youtube( $before = '', $after = '', $echo = true, $post = null ) {

	$venue_youtube = get_venue_youtube( $post );

	if ( strlen( $venue_youtube ) == 0 )
		return;

	$venue_youtube = esc_attr( strip_tags( $venue_youtube ) );

	$venue_youtube = $before . $venue_youtube . $after;

	if ( $echo )			
		echo $venue_youtube;
	else
		return $venue_youtube;
}

/**
 * get_organizer_google_plus function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_organizer_google_plus( $post = null ) {

	$post = get_post( $post );

	if( empty($post) || !in_array($post->post_type, ['event_listing', 'event_organizer']) )
		return;

	$organizer_google_plus = $post->_organizer_google_plus;

	if ( strlen( $organizer_google_plus ) == 0 )			
		return;
		
	return apply_filters( 'display_organizer_google_plus', $organizer_google_plus, $post );
}

/**
 * Display or retrieve the current organizer link on google plus.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function display_organizer_google_plus( $before = '', $after = '', $echo = true, $post = null ) {

	$organizer_google_plus = get_organizer_google_plus( $post );

	if ( strlen( $organizer_google_plus ) == 0 )
		return;

	$organizer_google_plus = esc_attr( strip_tags( $organizer_google_plus ) );

	$organizer_google_plus = $before . $organizer_google_plus . $after;

	if ( $echo )			
		echo $organizer_google_plus;		
	else
		return $organizer_google_plus;
}

/**
 * event_listing_class function.
 *
 * @access public
 * @param string $class (default: '')
 * @param mixed $post_id (default: null)
 * @return void
 */
function event_listing_class( $class = '', $post_id = null ) {

	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_event_listing_class( $class, $post_id ) ) . '"';

}

/**
 * get_event_listing_class function.
 *
 * @access public
 * @return array
 */
function get_event_listing_class( $class = '', $post_id = null ) {

	$post = get_post( $post_id );

	if ( $post->post_type !== 'event_listing' ) {
		return array();
	}

	$classes = array();

	if ( empty( $post ) ) {
		return $classes;
	}

	$classes[] = 'event_listing';

	if ( $event_type = get_event_type() ) {

		if ( $event_type && ! empty( $event_type ) ) {
			foreach ( $event_type as $type ) {
				$classes[] = 'event-type-' . sanitize_title( $type->name );
			}
		}
	}

	if ( is_event_cancelled( $post ) ) {

		$classes[] = 'event_cancelled';
	}

	if ( is_event_featured( $post ) ) {

		$classes[] = 'event_featured';
	}

	if ( ! empty( $class ) ) {

		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}

		$classes = array_merge( $classes, $class );
	}

	return get_post_class( $classes, $post->ID );
}

/** This function is use to get the counts the event views and attendee views.
 *   This function also used at event, attendee dashboard file.
 *   @return number counted view.
 *   @param $post
 **/
function get_post_views_count($post)
{
	$count_key = '_view_count';
	$count = get_post_meta($post->ID, $count_key, true);

	if($count=='' || $count==null)
	{
		delete_post_meta($post->ID, $count_key);
		add_post_meta($post->ID, $count_key, '0');
		return "-";
	}
	return $count;
}

/**
 * Count event view on the single event page
*/
function get_single_listing_view_count($post) {

	get_post_views_count($post);

}

/**
 * Returns the registration fields used when an account is required.
 *
 * @since 1.8
 *
 * @return array $registration_fields
*/
function event_manager_get_registration_fields() {
	$generate_username_from_email      = event_manager_generate_username_from_email();
	$use_standard_password_setup_email = event_manager_use_standard_password_setup_email();
	$account_required  = event_manager_user_requires_account();
	$registration_fields = array();
	if ( event_manager_enable_registration() ) {
		if ( ! $generate_username_from_email ) {
			$registration_fields['create_account_username'] = array(
					'type'     => 'text',
					'label'    => __( 'Username', 'wp-event-manager' ),
					'required' => $account_required,
					'value'    => isset( $_POST['create_account_username'] ) ? sanitize_text_field($_POST['create_account_username']) : '',
			);
		}
		if ( ! $use_standard_password_setup_email ) {
			$registration_fields['create_account_password'] = array(
					'type'         => 'password',
					'label'        => __( 'Password', 'wp-event-manager' ),
					'autocomplete' => false,
					'required'     => $account_required,
			);
			$password_hint = event_manager_get_password_rules_hint();
			if ( $password_hint ) {
				$registration_fields['create_account_password']['description'] = $password_hint;
			}
			$registration_fields['create_account_password_verify'] = array(
					'type'         => 'password',
					'label'        => __( 'Verify Password', 'wp-event-manager' ),
					'autocomplete' => false,
					'required'     => $account_required,
			);
		}
		$registration_fields['create_account_email'] = array(
				'type'        => 'text',
				'label'       => __( 'Your email', 'wp-event-manager' ),
				'placeholder' => __( 'you@yourdomain.com', 'wp-event-manager' ),
				'required'    => $account_required,
				'value'       => isset( $_POST['create_account_email'] ) ? sanitize_email( $_POST['create_account_email']) : '',
		);
	}

	/**
	 * Filters the fields used at registration.
	 *
	 * @since 1.8
	 *
	 * @param array $registration_fields
	 */
	return apply_filters( 'event_manager_get_registration_fields', $registration_fields );
}


/**
 * Returns if we allow indexing of a event listing.
 *
 * @since 1.8
 *
 * @param WP_Post|int|null $post
 * @return bool
 */
function event_manager_allow_indexing_event_listing( $post = null ) {
	$post = get_post( $post );
	if ( $post && $post->post_type !== 'event_listing' ) {
		return true;
	}
	// Only index event listings that are not expired and published.
	$index_event_listing = ! is_event_cancelled( $post ) && 'publish' === $post->post_status;
	/**
	 * Filter if we should allow indexing of event listing.
	 *
	 * @since 1.8
	 * @param bool $index_event_listing True if we should allow indexing of event listing.
	 */
	return apply_filters( 'event_manager_allow_indexing_event_listing', $index_event_listing);
}

/**
 * Returns if we output event listing structured data for a post.
 *
 * @since 1.8
 *
 * @param WP_Post|int|null $post
 * @return bool
 */
function event_manager_output_event_listing_structured_data( $post = null ) {
	$post = get_post( $post );
	if ( $post && $post->post_type !== 'event_listing' ) {
		return false;
	}
	// Only show structured data for un-filled and published event listings.
	$output_structured_data = ! is_event_cancelled( $post ) && 'publish' === $post->post_status;
	/**
	 * Filter if we should output structured data.
	 *
	 * @since 1.8
	 * @param bool $output_structured_data True if we should show structured data for post.
	 */
	return apply_filters( 'event_manager_output_event_listing_structured_data', $output_structured_data );
}

/**
 * Gets the structured data for the event listing.
 *
 * @since 1.8
 * @see https://developers.google.com/search/docs/data-types/events
 *
 * @param WP_Post|int|null $post
 * @return bool|array False if functionality is disabled; otherwise array of structured data.
 */
function event_manager_get_event_listing_structured_data( $post = null ) {
	$post = get_post( $post );
	if ( $post && $post->post_type !== 'event_listing' ) {
		return false;
	}
	$data = array();
	$data['@context'] = 'http://schema.org/';
	$data['@type'] = 'Event';

	$event_expires = get_post_meta( $post->ID, '_event_expires', true );
	if ( ! empty( $event_expires ) ) {
		$data[ 'validThrough' ] = date( 'c', strtotime( $event_expires ) );
	}

	$data['description'] = get_event_description( $post );

	$data['name'] = strip_tags( get_event_title( $post ));
	$data['image'] = get_event_banner( $post );
	$data['startDate'] = get_event_start_date( $post );
	$data['endDate'] = get_event_end_date( $post );
	$data['performer'] = get_organizer_name( $post );
	$data['eventAttendanceMode'] = is_event_online($post) ? 'Online' : 'offline';
	$data['eventStatus'] = 'EventScheduled';

	$data['Organizer']['@type'] = 'Organization';
	$data['Organizer']['name'] = get_organizer_name( $post );
	if ( $organizer_website = get_organizer_website( $post ) ) {
		$data['Organizer']['sameAs'] = $organizer_website;
		$data['Organizer']['url'] = $organizer_website;
	}

	$location = get_event_location( $post );
	if ( ! empty( $location ) && !is_event_online($post) ) {
		$data['Location'] = array();
		$data['Location']['@type'] = 'Place';
		$data['Location']['name'] = $location;
		$data['Location']['address'] = event_manager_get_event_listing_location_structured_data( $post );
		if ( empty( $data['Location']['address'] ) ) {
			$data['Location']['address'] = $location;
		}
	}
	else{

		$data['Location'] = array();
		$data['Location']['@type'] = 'VirtualLocation';
		$data['Location']['url'] = get_permalink( $post->ID );
	}
	/**
	 * Filter the structured data for a event listing.
	 *
	 * @since 1.8
	 *
	 * @param bool|array $structured_data False if functionality is disabled; otherwise array of structured data.
	 * @param WP_Post    $post
	 */
	return apply_filters( 'event_manager_get_event_listing_structured_data', $data, $post );
}

/**
 * Gets the event listing location data.
 *
 * @see http://schema.org/PostalAddress
 *
 * @param WP_Post $post
 * @return array|bool
 */
function event_manager_get_event_listing_location_structured_data( $post ) {
	$post = get_post( $post );
	if ( $post && $post->post_type !== 'event_listing' ) {
		return false;
	}

	
	$mapping = array();
	$mapping['streetAddress'] = array( 'street_number', 'street' );
	$mapping['addressLocality'] = 'city';
	$mapping['addressRegion'] = 'state_short';
	$mapping['postalCode'] = 'postcode';
	$mapping['addressCountry'] = 'country_short';
	$address = array();
	$address['@type'] = 'PostalAddress';
	foreach ( $mapping as $schema_key => $geolocation_key ) {
		if ( is_array( $geolocation_key ) ) {
			$values = array();
			foreach ( $geolocation_key as $sub_geo_key ) {
				$geo_value = get_post_meta( $post->ID, 'geolocation_' . $sub_geo_key, true );
				if ( ! empty( $geo_value ) ) {
					$values[] = $geo_value;
				}
			}
			$value = implode( ' ', $values );
		} else {
			$value = get_post_meta( $post->ID, 'geolocation_' . $geolocation_key, true );
		}
		if ( ! empty( $value ) ) {
			$address[ $schema_key ] = $value;
		}
	}
	// No address parts were found
	if ( 1 === count( $address ) ) {
		$address = false;
	}
	/**
	 * Gets the event listing location structured data.
	 *
	 * @since 1.8
	 *
	 * @param array|bool $address Array of address data.
	 * @param WP_Post    $post
	 */
	return apply_filters( 'event_manager_get_event_listing_location_structured_data', $address, $post );
}


/**
 * Displays the event title for the listing.
 *
 * @since 1.8
 * @param int|WP_Post $post
 * @return string
 */
function display_event_title( $post = null ) {
	if ( $event_title = get_event_title( $post ) ) {
		echo $event_title;
	}
}

/**
 * Gets the event title for the listing.
 *
 * @since 1.8
 * @param int|WP_Post $post (default: null)
 * @return string|bool|null
 */
function get_event_title( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || 'event_listing' !== $post->post_type ) {
		return;
	}

	$title = esc_html( get_the_title( $post ) );

	/**
	 * Filter for the event title.
	 *
	 * @since 1.8
	 * @param string      $title Title to be filtered.
	 * @param int|WP_Post $post
	*/
	return apply_filters( 'display_event_title', $title, $post );
}

/**
 * Displays the event description for the listing.
 *
 * @since 1.8
 * @param int|WP_Post $post
 * @return string
 */
function display_event_description( $post = null ) {
	if ( $event_description = get_event_description( $post ) ) {
		echo $event_description;
	}
}

/**
 * Gets the event description for the listing.
 *
 * @since 1.8
 * @param int|WP_Post $post (default: null)
 * @return string|bool|null
 */
function get_event_description( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || 'event_listing' !== $post->post_type ) {
		return;
	}

	$description = apply_filters( 'display_event_description', get_the_content( $post ) );

	/**
	 * Filter for the event description.
	 *
	 * @since 1.8
	 * @param string      $title Title to be filtered.
	 * @param int|WP_Post $post
	*/
	return apply_filters( 'event_manager_get_event_description', $description, $post );
}

/**
 * Get event ticket price
 * @return event ticket price
 **/
function get_event_ticket_price( $post = null ) {

	$post = get_post( $post );
	if ( $post->post_type !== 'event_listing' || get_event_ticket_option() == 'free' )
		return;

		return apply_filters( 'display_event_ticket_price', $post->_event_ticket_price, $post );
}

/**
 * Display event ticket price
 * @return
 **/
function display_event_ticket_price( $before = '', $after = '', $echo = true, $post = null ) {

	$event_ticket_price = get_event_ticket_price( $post );
	
	if ( strlen( $event_ticket_price ) == 0 )
		return;

	$event_ticket_price = esc_attr( strip_tags( $event_ticket_price ) );

	$event_ticket_price = $before . $event_ticket_price . $after;

	if ( $echo )
		echo $event_ticket_price;
	else
		return $event_ticket_price;
}

/**
 * get date and time separator
 * @since 3.1.8
 * @param null
 * @return string 
 **/
function get_wpem_date_time_separator(){
	return	apply_filters('event_manager_date_time_format_separator',get_option('event_manager_date_time_format_separator','@') );
}

/**
 * Display date and time separator
 * @since 3.1.8
 * @param
 * @return
 **/
function display_date_time_separator() {
	printf( __('%s', 'wp-event-manager'),get_wpem_date_time_separator() );
}

/**
 * Hide feature image in single page
 * @since 3.1.8
 * @param
 * @return
 **/
add_filter( 'post_thumbnail_html', 'hide_feature_image_single_page', 10, 3);
function hide_feature_image_single_page( $html, $post_id, $post_image_id ) {
  	if( is_singular( 'event_listing' ) )
  	{
  		return '';
  	}
  	else if( is_singular( 'event_organizer' ) )
  	{
  		return '';
  	}
  	else if( is_singular( 'event_venue' ) )
  	{
  		return '';
  	}

  	return $html;
}

/**
 * Display query pagination
 * @since 3.1.18
 * @param
 * @return
 **/
function display_wpem_get_query_pagination($max_num_pages = 0, $current_page = 1, $tab = '') 
{
	ob_start();

	// Calculate pages to output 
	$end_size    = 3;
	$mid_size    = 3;
	$start_pages = range( 1, $end_size );
	$end_pages   = range( $max_num_pages - $end_size + 1, $max_num_pages );
	$mid_pages   = range( $current_page - $mid_size, $current_page + $mid_size );
	$pages       = array_intersect( range( 1, $max_num_pages ), array_merge( $start_pages, $end_pages, $mid_pages ) );
	$prev_page   = 0;

	?>
	<nav class="event-manager-pagination-2 wpem-mt-3">
		<ul class="page-numbers">
			<?php if ( $current_page && $current_page > 1 ) : ?>
				<?php
				$prev_page_link = add_query_arg( 
					array(
						'pagination' => $current_page - 1,
						'tab' => $tab,

					)
				);
				?>
				<li><a href="<?php echo $prev_page_link; ?>" class="page-numbers">&larr;</a></li>
			<?php endif; ?>
			
			<?php
				foreach ( $pages as $page ) 
				{
					if ( $prev_page != $page - 1 ) {
						echo '<li><span class="gap">...</span></li>';
					}

					if ( $current_page == $page ) {
						echo '<li><span class="page-numbers current">' . $page . '</span></li>';
					} else {
						$page_link = add_query_arg( 
							array(
								'pagination' => $page,
								'tab' => $tab,

							)
						);
						echo '<li><a href="' . $page_link . '" class="page-numbers">' . $page . '</a></li>';
					}

					$prev_page = $page;
				}
			?>

			<?php if ( $current_page && $current_page < $max_num_pages ) : ?>
				<?php
				$next_page_link = add_query_arg( 
					array(
						'pagination' => $current_page + 1,
						'tab' => $tab,

					)
				);
				?>
				<li><a href="<?php echo $next_page_link; ?>" class="page-numbers">&rarr;</a></li>
			<?php endif; ?>
		</ul>
	</nav>
	<?php

	echo ob_get_clean();
}
