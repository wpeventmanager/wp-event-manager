<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Event_Manager_Filters class.
 */

class WP_Event_Manager_Filters {
    
	/**
	 * Get datetime filter as array
	 */

	public static function get_datetimes_filter() 
	{	
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
	 * Get date time filter value for the key 
	 */

	public static function get_datetime_value($key) 
	{
		$datetimes=self::get_datetimes_filter();

		if (array_key_exists($key,$datetimes))

		{
		  return $datetimes[$key];
		}
		else
		{
		  return  $datetimes["datetime_thismonth"];
		}		
	}

	/**
	 * Get ticket price filter as array
	 */

	public static function get_ticket_prices_filter() 
	{
		$ticket_prices=array();

		$ticket_prices["ticket_price_any"]=__("Any Ticket Price",'wp-event-manager');

		$ticket_prices["ticket_price_paid"]=__("Paid",'wp-event-manager');

		$ticket_prices["ticket_price_free"]=__("Free",'wp-event-manager');

		return $ticket_prices;
	}

	

	/**
	 * Get ticket price filter value for the key 
	 */

	public static function get_ticket_price_value($key) 
	{
		$ticket_prices=self::get_ticket_prices_filter();

		if (array_key_exists($key,$ticket_prices))
		{
		  return $ticket_prices[$key];
		}
		else
		{
		  return  $ticket_prices["ticket_price_any"];
		}	
	}
}

new WP_Event_Manager_Filters();
