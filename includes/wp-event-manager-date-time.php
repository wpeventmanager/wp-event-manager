<?php
if(!defined('ABSPATH')) {
	exit;
}

/**
 * WP_Event_Manager_Date_Time
 */
class WP_Event_Manager_Date_Time {

	const DATABASE_DATE_TIME_FORMAT      = 'Y-m-d H:i:s';
	const DBTIMEFORMAT          = 'H:i:s';
	/**
	* Get datepicker format function will return all the date formats for datepicker.
	*
	* @param null
	* @return format of datepicker
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
	* Function get_default_date_formats will return all the date formats.
	* This function has both type of format jquery-ui-datepicker as well as for php date format
	*
	* @return array
	* @since 3.0
	**/
	public static function get_default_date_formats(){
		$date_formats['datepicker_date_formats']  = apply_filters('wp_event_manager_datepicker_date_formats',
						array(
							'yy-mm-dd',
							'm-d-yy',
							'mm-dd-yy',
							'd-m-yy',
							'dd-mm-yy',

							'm/d/yy',
							'mm/dd/yy',
							'd/m/yy',
							'dd/mm/yy',

							'yy.mm.dd',
							'mm.dd.yy',
							'dd.mm.yy'
						)
			);
		$date_formats['view_date_formats'] = apply_filters('wp_event_manager_view_date_formats',
				array(
					'Y-m-d',
					'n-j-Y',
					'm-d-Y',
					'j-n-Y',
					'd-m-Y',

					'n/j/Y',
					'm/d/Y',
					'j/n/Y',
					'd/m/Y',

					'Y.m.d',
					'm.d.Y',
					'd.m.Y',
			));
		return $date_formats;
	}


	/**
	 * This function will parse the date to dbformatted date.
	 * Convert date and time value into DB formatted format and save eg. 1970-01-01 00:00:00
	 * php date format parsing has error so we need to parse via our custom parsing method
	 * Ref. https://stackoverflow.com/questions/6668223/php-date-parse-from-format-alternative-in-php-5-2
	 *
	 *  @since 3.0
	 */
	public static function date_parse_from_format($format, $date) {
	  // reverse engineer date formats
        $keys = array(
				// Year with 4 Digits
				'Y' => array('year', '\d{4}'),
				// Year with 2 Digits
				'y' => array('year', '\d{2}'),
				// Month with leading 0
				'm' => array('month', '\d{2}'),
				// Month without the leading 0
				'n' => array('month', '\d{1,2}'),
				// Month ABBR 3 letters
				'M' => array('month', '[A-Z][a-z]{2}'),
				// Month Name
				'F' => array('month', '[A-Z][a-z]{2,8}'),
				// Day with leading 0
				'd' => array('day', '\d{2}'),
				// Day without leading 0
				'j' => array('day', '\d{1,2}'),
				// Day ABBR 3 Letters
				'D' => array('day', '[A-Z][a-z]{2}'),
				// Day Name
				'l' => array('day', '[A-Z][a-z]{5,8}'),
				// Hour 12h formatted, with leading 0
				'h' => array('hour', '\d{2}'),
				// Hour 24h formatted, with leading 0
				'H' => array('hour', '\d{2}'),
				// Hour 12h formatted, without leading 0
				'g' => array('hour', '\d{1,2}'),
				// Hour 24h formatted, without leading 0
				'G' => array('hour', '\d{1,2}'),
				// Minutes with leading 0
				'i' => array('minute', '\d{2}'),
				// Seconds with leading 0
				's' => array('second', '\d{2}'),
       );
        // Convert format string to regex
        $regex = '';
        $chars = str_split($format);
        foreach ($chars AS $n => $char) {
            $lastChar = isset($chars[$n-1]) ? $chars[$n-1] : '';
            $skipCurrent = '\\' == $lastChar;
            if(!$skipCurrent && isset($keys[$char])) {
                $regex .= '(?P<'.$keys[$char][0].'>'.$keys[$char][1].')';
            } else if('\\' == $char) {
                $regex .= $char;
            }  else {
                $regex .= preg_quote($char);
            }
        }
        $dt = array();
        // Now try to match it
        if(preg_match('#^'.$regex.'$#', $date, $dt)){
            foreach ($dt AS $k => $v){
                if(is_int($k)){
                    unset($dt[$k]);
                }
            }
            if(!checkdate($dt['month'], $dt['day'], $dt['year'])){
                return false;
            }
        } else {
            return false;
        }

        $dt['month'] = str_pad($dt['month'], 2, '0', STR_PAD_LEFT);
		$dt['day'] = str_pad($dt['day'], 2, '0', STR_PAD_LEFT);
		$formatted = '{year}-{month}-{day}' . (isset($dt['hour'], $dt['minute'] ,$dt['second']) ? ' {hour}:{minute}:{second}' : '');
		foreach ($dt as $key => $value) {
			$formatted = str_replace('{' . $key . '}', $value, $formatted);
		}
        return $formatted;
	}

	/**
	* This function will return php formatted date format from datepicker formatted date.
	* For eg. in date picker date format is yy-mm-dd where this format in php will be Y-m-d
	* So, We need one central function will allow to convert datepicker format in to php formatted format.
	*
	* @since 3.0
	**/
	public static function get_view_date_format_from_datepicker_date_format($datepicker_format = 'yy-mm-dd'){
		$all_formats = self::get_default_date_formats();
		$indexof_view_date_format = array_search($datepicker_format, $all_formats['datepicker_date_formats']);
		return $all_formats['view_date_formats'][$indexof_view_date_format];
	}

	/**
	* Get timepicker format function will return all the date formats for timepicker.
	*
	* @since 3.0
	* @param null
	* @return string format
	**/
	public static function get_timepicker_format() {
		$selected_format = get_option('event_manager_timepicker_format',12);

		$formats =  array('h:i A','H:i');
		if($selected_format && $selected_format == 24 )
			return $formats[1];
		else
			return  $formats[0];
	}

	/**
	 * Returns the time only.
	 *
	 * @since 3.0
	 * @param string $time time in 24 hour or 12 hour.
	 * @return string it will return time in DB formatted 24 hours time
	 */
	public static function get_db_formatted_time($time) {
		$time = is_numeric($time) ? $time : strtotime($time);
		return date(self::DBTIMEFORMAT, $time);
	}

	/**
	 * Returns the date time DB formatted.
	 *
	 * @since 3.1.10
	 * @param string  $date and $time
	 * @return string it will return time in DB formatted date and time
	 */
	public static function get_db_formatted_date_time($date = '', $time = '00:00:00') {
		if(empty($date))
			return;
		// Get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format 	= self::get_datepicker_format();

		// Covert datepicker format  into php date() function date format
		$php_date_format 		= self::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
		$time = self::get_db_formatted_time($time);
		// Convert date and time value into DB formatted format and save eg. 1970-01-01 00:00:00
		$db_date_time = self::date_parse_from_format($php_date_format . ' H:i:s'  , $date .' '.$time);

		return $db_date_time;
	}

	/**
	* Get timepicker format function will return all the date formats for timepicker.
	*
	* @since 3.0
	**/
	public static function get_timepicker_step() {
		$selected_step = get_option('event_manager_timepicker_step',30);
		return isset($selected_step) && $selected_step >= 1 && $selected_step <= 60 ? $selected_step : 30;
	}

	/**
	 * Get wp event manager view date format.
	 * This format is set by user from event listing -> settings -> date and time
	 *
	 * @return string
	 * @since 3.0
	 **/
	public static function get_event_manager_view_date_format(){
		return get_option('event_manager_view_date_format','M d ,Y');
	}

	/**
	 * Get Wp event manager date admin setting where you can get array of dummy date.
	 * The key of each value will be a php date format which is generated from the get_default_date_formats()
	 * It will just make array for dropdown for showing dummy date and key as php formatted so we can save it
	 * Currentrly it is used at event listing -> settings -> date and time - datepicker format
	 * In wp event manager settings we don't have way to generate html so we have generated array for select option
	 *
	 * @since 3.0
	 * @return array
	 */
	public static function get_event_manager_date_admin_settings(){
		$dummy_date = strtotime('January 15 ' . date('Y'));
		$default_foramts = self::get_default_date_formats();
		$setting_values = array();
		foreach($default_foramts['view_date_formats'] as $key => $value){
			$setting_values[$key] = date($value, $dummy_date);
		}
		return $setting_values;
	}

	/**
	 * Get event manager timezone setting defined in event listing -> settings.
	 *
	 * @since 3.0
	 * @return string $selected_timezone
	 */
	public static function get_event_manager_timezone_setting(){
		$selected_timezone = get_option('event_manager_timezone_setting','site_timezone');
		return $selected_timezone;
	}

	/**
	 * Manage current site timezon.
	 * @return string
	 */
	public static function get_current_site_timezone(){
		$current_offset = get_option('gmt_offset');
		$tzstring = get_option('timezone_string');
		$check_zone_info = true;

		// Remove old Etc mappings. Fallback to gmt_offset.
		if(false !== strpos($tzstring,'Etc/GMT'))
			$tzstring = '';

		if(empty($tzstring)) { // Create a UTC+- zone if no timezone string exists
			$check_zone_info = false;
			if(0 == $current_offset)
				$tzstring = 'UTC+0';
			elseif($current_offset < 0)
				$tzstring = 'UTC' . $current_offset;
			else
				$tzstring = 'UTC+' . $current_offset;
		}
		return $tzstring;
	}

	/**
	 * 
	 * @param string $tzstring
	 */
	public static function wp_event_manager_timezone_choice($tzstring = null){
		if(empty($tzstring))
			$tzstring = self::get_current_site_timezone();
		return apply_filters('wp_event_manager_timezone_choice', wp_timezone_choice($tzstring, get_user_locale()));
	}

	/**
	 * convert_event_timezone_into_abbr function
	 * @param string $event_timezone
	 */
	public static function convert_event_timezone_into_abbr($event_timezone){
		// Get string of event timezone if it is UTC offset
		$tzstring 	= self::generate_timezone_string_from_utc_offset($event_timezone);

		$date_time 	= new DateTime('NOW');
		$date_time->setTimeZone(new DateTimeZone($tzstring));
		return $date_time->format('T');
	}

	/**
	* current_timestamp_from_event_timezone will return the current timestamp according to the
	* timezone selected in event or passed in argument
	* @since 3.0
	* @param $event_timezone
	* @return  timestamp
	**/
	public static function current_timestamp_from_event_timezone($event_timezone){
		if(empty($event_timezone))
			return;
		// Get string of event timezone if it is UTC offset
		$tzstring 	= self::generate_timezone_string_from_utc_offset($event_timezone);
		$date_time = new DateTime("now");
		$date_time->setTimezone(new DateTimeZone($tzstring));
		return strtotime($date_time->format('Y-m-d H:i:s'));
	}

	/**
	 * Tests to see if the timezone string is a UTC offset, ie "UTC+2".
	 *
	 * @param string $timezone
	 * @return bool
	 */
	public static function is_utc_offset($timezone) {
		$timezone = trim($timezone);
		return (0 === strpos($timezone, 'UTC') && strlen($timezone) > 3);
	}

	/**
	 * Helper function to retrieve the timezone string for a given UTC offset
	 * This is a close copy of WooCommerce's wc_timezone_string() method
	 *
	 * @param string $offset UTC offset
	 * @return string
	 */
	public static function generate_timezone_string_from_utc_offset($offset) {

		if(!self::is_utc_offset($offset)) {
			return $offset;
		}

		// Ensure we have the minutes on the offset
		if(!strpos($offset, ':')) {
			$offset .= ':00';
		}

		$offset = str_replace('UTC', '', $offset);
		list($hours, $minutes) = explode(':', $offset);
		$seconds = $hours * 60 * 60 + $minutes * 60;

		// Attempt to guess the timezone string from the UTC offset
		$timezone = timezone_name_from_abbr('', $seconds, 0);

		if(false === $timezone) {
			$is_dst = date('I');

			foreach (timezone_abbreviations_list() as $abbr) {
				foreach ($abbr as $city) {
					if($city['dst'] == $is_dst && $city['offset'] == $seconds && !empty($city['timezone_id'])) {
						return $city['timezone_id'];
					}
				}
			}
			// Fallback to UTC
			return 'UTC';
		}
		return $timezone;
	}

	/**
	 * Localizes a date or timestamp using WordPress timezone and returns it in the specified format.
	 *
	 * @param string     $format   The format the date shouuld be formatted to.
	 * @param string|int $date     The date UNIX timestamp or `strtotime` parseable string.
	 * @param string     $timezone An optional timezone string identifying the timezone the date shoudl be localized
	 *                             to; defaults to the WordPress installation timezone (if available) or to the system
	 *                             timezone.
	 *
	 * @return string|bool The parsed date in the specified format and localized to the system or specified
	 *                     timezone, or `false` if the specified date is not a valid date string or timestamp
	 *                     or the specified timezone is not a valid timezone string.
	 */
	public static function localize_date($date = null, $format = null,  $timezone = null) {

		if(empty($timezone))
			$timezone = self::get_current_site_timezone();
		$timezone = self::generate_timezone_string_from_utc_offset($timezone);
		try {
			$date = new DateTime(strtotime($date));
			$date->setTimezone(new DateTimeZone($timezone));
		} catch (Exception $e) {
			return false;
		}

		return $date->format($format);
	}

    /**
     * Converts PHP/Wordpress default datetime format selectors to Moment.js/Date.js usable formats
     * @param string $format PHP/Wordpress datetime format selector
     * @return string Converted Datetime selectors to use with Moment.js/Date.js
     * @since 3.1.26
     */
	public static function wpem_convert_php_to_moment_format($format) {
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
}
new WP_Event_Manager_Date_Time();