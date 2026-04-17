<?php
if(!defined('ABSPATH') ) {
	exit;
}

/**
 * WP_Event_Manager_Filters class.
 */
class WP_Event_Manager_Filters {
    
	/**
	 * Get datetime filter as array.
	 */
	public static function get_datetimes_filter() {	
		$datetimes=array();		
		//$datetimes["datetime_any"]=__("Any Date",'wp-event-manager');
		$datetimes["datetime_today"]=__("Today",'wp-event-manager');
		$datetimes["datetime_tomorrow"]=__("Tomorrow",'wp-event-manager');
		$datetimes["datetime_thisweek"]=__("This Week",'wp-event-manager');
		$datetimes["datetime_thisweekend"]=__("This Weekend",'wp-event-manager');
		$datetimes["datetime_thismonth"]=__("This Month",'wp-event-manager');
		$datetimes["datetime_thisyear"]=__("This Year",'wp-event-manager');
		$datetimes["datetime_nextweek"]=__("Next Week",'wp-event-manager');
		$datetimes["datetime_nextweekend"]=__("Next Weekend",'wp-event-manager');
		$datetimes["datetime_nextmonth"]=__("Next Month",'wp-event-manager');
		$datetimes["datetime_nextyear"]=__("Next Year",'wp-event-manager');		
		return $datetimes;
	}

	/**
	 * Get date time filter value for the key.
	 */
	public static function get_datetime_value($key) {
		$datetimes=self::get_datetimes_filter();
		if(array_key_exists($key,$datetimes)){
		  return $datetimes[$key];
		} else	{
		  return  $datetimes["datetime_thismonth"];
		}		
	}

	/**
	 * Get ticket price filter as array.
	 */
	public static function get_ticket_prices_filter() {
		$ticket_prices=array();
		$ticket_prices["ticket_price_any"]=__("Any Ticket Price",'wp-event-manager');
		$ticket_prices["ticket_price_paid"]=__("Paid",'wp-event-manager');
		$ticket_prices["ticket_price_free"]=__("Free",'wp-event-manager');
		return $ticket_prices;
	}

	/**
	 * Get ticket price filter value for the key. 
	 */
	public static function get_ticket_price_value($key) {
		$ticket_prices=self::get_ticket_prices_filter();

		if(array_key_exists($key,$ticket_prices)){
		  return $ticket_prices[$key];
		}else{
		  return  $ticket_prices["ticket_price_any"];
		}	
	}
}

/**
 * Listing instance stack and markup helpers (pairs event-listings-start / event-listings-end).
 */

/**
 * Reference to the static stack for the current request.
 *
 * @return array
 */
function &wpem_listing_instance_stack_ref() {
	static $stack = array();
	return $stack;
}

/**
 * Push when opening the listing wrapper (event-listings-start).
 *
 * @param string $listing_instance_id Sanitized instance key.
 */
function wpem_listing_instance_stack_push( $listing_instance_id ) {
	$stack = &wpem_listing_instance_stack_ref();
	$stack[] = $listing_instance_id;
}

/**
 * Pop when closing the listing wrapper (event-listings-end).
 */
function wpem_listing_instance_stack_pop() {
	$stack = &wpem_listing_instance_stack_ref();
	if ( ! empty( $stack ) ) {
		array_pop( $stack );
	}
}

/**
 * Opening wrapper div markup for one listing instance (escaped).
 *
 * @param string $listing_instance_id Sanitized instance key.
 * @return string Safe HTML fragment.
 */
function wpem_listing_instance_wrapper_html( $listing_instance_id ) {
	return '<div class="wpem-event-listing-instance" data-wpem-listing-instance="' . esc_attr( $listing_instance_id ) . '">';
}

/**
 * Main event listings container opening tag (escaped).
 *
 * @param string $listing_instance_id Sanitized instance key.
 * @param string $wpem_list_type_class  Layout CSS classes.
 * @param string $layout_type           Layout type attribute value.
 * @return string Safe HTML fragment.
 */
function wpem_listing_view_container_open_html( $listing_instance_id, $wpem_list_type_class, $layout_type ) {
	return '<div id="wpem-el-view-' . esc_attr( $listing_instance_id ) . '" class="wpem-main wpem-event-listings event_listings wpem-event-listing-view-root ' . esc_attr( $wpem_list_type_class ) . '" data-id="' . esc_attr( $layout_type ) . '">';
}

/**
 * Allowed HTML for listing instance fragments (wp_kses).
 *
 * @return array
 */
function wpem_listing_instance_markup_kses_allowed() {
	return array(
		'div' => array(
			'id'                         => true,
			'class'                      => true,
			'data-wpem-listing-instance' => true,
			'data-id'                    => true,
		),
	);
}

new WP_Event_Manager_Filters();