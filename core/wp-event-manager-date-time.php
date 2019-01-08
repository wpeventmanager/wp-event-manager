<?php

class WP_Event_Manager_Date_Time {

	/**
	* Get datepicker format function will return all the date formats for datepicker
	* 
	* 
	* @since 3.0
	**/
	public static function get_datepicker_format() {

		$selected_format = get_option('event_manager_datepicker_format',0);
		$formats = self::get_default_date_formats();

		if($selected_format && isset($formats['datepicker_date_formats'][$selected_format]))
			return $formats['datepicker_date_formats'][$selected_format];
		else
			return  $formats['datepicker_date_formats'][0];
	}

	/**
	
	**/
	public static function get_default_date_formats(){

		$date_formats['datepicker_date_formats']  = apply_filters('wp_event_manager_datepicker_date_formats',
																		array(
																			'yy-mm-dd',
																			'yy-m-d',
																			'yy-dd-mm',
																			'yy-d-m',
																			'dd-mm-yy',
																			'd-m-yy',

																			'yy/mm/dd',
																			'yy/m/d',
																			'yy/dd/mm',
																			'yy/d/m',
																			'dd/mm/yy',
																			'd/m/yy',

																			'yy.mm.dd',
																			'yy.m.d',
																			'yy.dd.mm',
																			'yy.d.m',
																			'dd.mm.yy',
																			'd.m.yy',
																		) 
															);

		$date_formats['view_date_formats'] = apply_filters('wp_event_manager_view_date_formats',
											 	array( 
											 		'Y-m-d',
											 		'Y-n-j',
											 		'Y-d-m',
											 		'Y-j-n',
											 		'd-m-Y',
											 		'j-n-Y',

											 		'Y/m/d',
											 		'Y/n/j',
											 		'Y/d/m',
											 		'Y/j/n',
											 		'd/m/Y',
											 		'j/n/Y',

											 		'Y.m.d',
											 		'Y.n.j',
											 		'Y.d.m',
											 		'Y.j.n',
											 		'd.m.Y',
											 		'j.n.Y',
											 		
											 ));
		return $date_formats;
	}

	public static function get_event_manager_date_admin_settings(){
				$dummy_date = strtotime( 'January 15 ' . date( 'Y' ) );
				$default_foramts = self::get_default_date_formats();
				$setting_values = array();
				foreach($default_foramts['view_date_formats'] as $key => $value ){	
					$setting_values[$key] = date( $value, $dummy_date );
				}
				return $setting_values;
	}

	/*
	*  yy-dd-mm = Y-d-m = 2019-15-01

	*/

	public static function get_view_date_format_from_datepicker_date_format( $datepicker_format = 'yy-mm-dd' ){

		$all_formats = self::get_default_date_formats();
		$indexof_view_date_format = array_search( $datepicker_format, $all_formats['datepicker_date_formats'] ); 

		return $all_formats['view_date_formats'][$indexof_view_date_format];
	}

	/**
	* Get timepicker format function will return all the date formats for timepicker
	* 
	* @since 3.0
	**/
	public static function get_timepicker_format() {
		$selected_format = get_option('event_manager_timepicker_format',12);
		
		$formats =  array('h:i A','H:i');
		if($selected_format && $selected_format == 24  ){
			return $formats[1];
		}
		else
			return  $formats[0];
	}

	/**
	* Get timepicker format function will return all the date formats for timepicker
	* 
	* @since 3.0
	**/
	public static function get_timepicker_step() {
		$selected_step = get_option('event_manager_timepicker_step',30);
		
		return isset($selected_step) && $selected_step >= 1 && $selected_step <= 60 ? $selected_step : 30;
	}


	public static function get_event_manager_view_date_format(){
		return get_option('event_manager_view_date_format','M d ,Y');
	}
}

new WP_Event_Manager_Date_Time();