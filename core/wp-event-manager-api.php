<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Event_Manager_API API
 *
 * This API class handles API requests.
 */
 
class WP_Event_Manager_API extends WP_REST_Controller{

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  2.5
	 */
	private static $_instance = null;

	/**
     * Authentication error.
     *
     * @var WP_Error
     */
    protected $error = null;

    /**
     * Logged in user data.
     *
     * @var stdClass
     */
    protected $user = null;

    /**
     * Logged in user id.
     *
     * @var stdClass
     */
    protected $userid = 0;

    /**
     * Current event id.
     *
     * @var stdClass
     */
    protected $eventid = 0;

    /**
     * add new event.
     *
     * @var stdClass
     */
    protected $add_new_event = 'no';

    /**
     * Current auth method.
     *
     * @var string
     */
    protected $auth_method = '';

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  2.5
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	 
	public function __construct() {

		add_action('rest_api_init', [$this, 'rest_api_init'], 10);

        add_filter( 'event_manager_get_listings', [$this, 'get_events_query_filter'], 10, 2);
	}

	public function rest_api_init()
    {

        $namespace = 'event-manager-api/v2/';

        /**
         * API User
         */
        $this->post_login($namespace, 'user/login/', $this, 'login');
        $this->post($namespace, 'user/event/', $this, 'get_user_event_list');
        $this->post($namespace, 'user/event/add', $this, 'add_event');
        $this->put($namespace, 'user/event/update/(?P<eventid>[0-9]+)/', $this, 'update_event');
        $this->delete($namespace, 'user/event/delete/(?P<eventid>[0-9]+)/', $this, 'delete_event');

        $this->get($namespace, 'event/', $this, 'get_event_list');
        $this->get($namespace, 'event/(?P<eventid>[0-9]+)/', $this, 'get_single_event');
    }

    private function perform_basic_authentication()
    {
        $this->auth_method = 'basic_auth';
        $consumer_key      = '';
        $consumer_secret   = '';

        // If the $_GET parameters are present, use those first.
        if (!empty($_GET['consumer_key']) && !empty($_GET['consumer_secret']))
        {
            $consumer_key    = $_GET['consumer_key'];
            $consumer_secret = $_GET['consumer_secret'];
        }

        // If the above is not present, we will do full basic auth.
        if (!$consumer_key && !empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']))
        {
            $consumer_key    = $_SERVER['PHP_AUTH_USER'];
            $consumer_secret = $_SERVER['PHP_AUTH_PW'];
        }

        // Stop if don't have any key.
        if (!$consumer_key || !$consumer_secret)
        {
            return false;
        }

        $this->user = wp_authenticate($consumer_key, $consumer_secret);

        if (empty($this->user))
        {
            return new WP_Error('rest_logged_out', 'Sorry, you must be logged in to make a request.', array('status' => 401));
        }
        return $this->user;
    }

    private function perform_custom_authentication()
    {
        $user_token = $_SERVER['HTTP_AUTHORIZATION'];
        $userid = $_SERVER['HTTP_USERID'];
        $get_user_token = get_user_meta($userid, 'user_token', true);

        if($user_token == "Bearer ".$get_user_token)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get($namespace, $route, $class, $method_name)
    {
        register_rest_route(
            $namespace, $route, array(
                'methods'             => ['GET'],
                'callback'            => [$class, $method_name]
            )
        );
    }

    public function post_login($namespace, $route, $class, $method_name)
    {
        register_rest_route(
            $namespace, $route, array(
                'methods'             => ['POST'],
                'callback'            => [$class, $method_name],
                'permission_callback' => function ()
                {
                    return $this->perform_basic_authentication();
                }
            )
        );
    }

    public function post($namespace, $route, $class, $method_name)
    {
        register_rest_route(
            $namespace, $route, array(
                'methods'             => ['POST'],
                'callback'            => [$class, $method_name],
                'permission_callback' => function ()
                {
                    return $this->perform_custom_authentication();
                }
            )
        );
    }

    public function put($namespace, $route, $class, $method_name)
    {
        register_rest_route(
            $namespace, $route, array(
                'methods'             => ['PUT'],
                'callback'            => [$class, $method_name],
                'permission_callback' => function ()
                {
                    return $this->perform_custom_authentication();
                }
            )
        );
    }

    public function delete($namespace, $route, $class, $method_name)
    {
        register_rest_route(
            $namespace, $route, array(
                'methods'             => ['DELETE'],
                'callback'            => [$class, $method_name],
                'permission_callback' => function ()
                {
                    return $this->perform_custom_authentication();
                }
            )
        );
    }

    public function login($request)
    {
        $params = $request->get_params();

        $username = $params['email'];
        $password = $params['password'];

        if ($username != "" && $password != "")
        {
            $user = wp_authenticate($username, $password);

            if (!$user->errors)
            {
                if ($user->ID)
                {
                    $user_data = get_userdata($user->ID);
                    $user_token = substr(base64_encode(md5( $user_data->data->user_email . time() . $user_data->data->user_pass )), 0, 40);
                    update_user_meta($user->ID, 'user_token', $user_token);

                    $arrData = [];
                    $arrData['user'] = $user_data;
                    $arrData['user_token'] = $user_token;

                    $resp['code']     = 'SUCCESS';
                    $resp['message']     = 'Login successfully';
                    $resp['data']['user'] = $arrData;
                }
            }
            else
            {
                foreach ($user->errors as $key => $value)
                {
                    if ($key == 'invalid_email')
                    {
                        $resp['code'] = 'FAIL';
                        $resp['message'] = 'Email address does not exist..';
                        $resp['data']     = new stdClass();
                    }
                    elseif ($key == 'incorrect_password')
                    {
                        $resp['code'] = 'FAIL';
                        $resp['message'] = 'The password you entered for the email address ' . $username . ' is incorrect.';
                        $resp['data']     = new stdClass();
                    }
                    else
                    {
                        $resp['code'] = 'FAIL';
                        $resp['message'] = 'Something wrong!!';
                        $resp['data']     = new stdClass();
                    }
                }
            }
        }
        else
        {
            $resp['code'] = 'Fail';
            $resp['message'] = 'Required parameter(s) missing.';
            $resp['data']     = new stdClass();
        }

        return $resp;
    }

    public function get_user_event_list($request)
    {
        $userid = $request->get_header('userid');
        $posts_per_page = $request->get_header('perpage');
    	$paged = $request->get_header('paged');
        $params = $request->get_params();

    	if ($userid != "")
        {
        	$arrData = $this->get_events($params, $posts_per_page, $paged, $userid);

        	$resp['code']     = 'SUCCESS';
            $resp['message']  = 'Get user event successfully';
            $resp['data']     = $arrData;
        }
        else
        {
        	$resp['code'] = 'Fail';
            $resp['message'] = 'Required parameter(s) missing.';
            $resp['data']     = new stdClass();
        }

        return $resp;
    }

    public function get_events($params = [], $posts_per_page = 10, $paged = 1, $userid = 0, $eventid = 0)
    {
        $this->userid = $userid;
        $this->eventid = $eventid;

        $args = [];

        $args['posts_per_page'] = $posts_per_page;

        $args['offset'] = $paged - 1;

        if(!empty($params['search_keywords']))
        {
            $args['search_keywords'] = $params['search_keywords'];
        }

        if(!empty($params['search_location']))
        {
            $args['search_location'] = $params['search_location'];
        }

        if(!empty($params['search_datetimes']))
        {
            $args['search_datetimes'][] = $params['search_datetimes'];
        }

        $events = get_event_listings($args);

		$arrData = [];

        if($events->found_posts > 0)
        {
            $arrEvent = [];
            foreach ($events->posts as $key => $event) 
            {
                $arrEvent[$key]['eventid'] = $event->ID;
                $arrEvent[$key]['event_title'] = $event->post_title;
                $arrEvent[$key]['event_online'] = is_event_online($event);
                $arrEvent[$key]['event_venue_name'] = get_event_venue_name($event);
                $arrEvent[$key]['event_address'] = get_event_address($event);
                $arrEvent[$key]['event_location'] = get_event_location($event);
                $arrEvent[$key]['event_pincode'] = get_event_pincode($event);

                $registration = get_event_registration_method($event);
                if($registration->url != '' || $registration->raw_email != '')
                {
                    $arrEvent[$key]['registration'] = $registration;
                }
                else
                {
                    $arrEvent[$key]['registration'] = new stdClass();
                }                

                $arrEvent[$key]['event_banner'] = get_event_banner($event);
                $arrEvent[$key]['event_description'] = $event->post_content;
                $arrEvent[$key]['event_start_date'] = get_event_start_date($event);
                $arrEvent[$key]['event_start_time'] = get_event_start_time($event);
                $arrEvent[$key]['event_end_date'] = get_event_end_date($event);
                $arrEvent[$key]['event_end_time'] = get_event_end_time($event);
                $arrEvent[$key]['event_registration_deadline'] = get_event_registration_end_date($event);
                $arrEvent[$key]['organizer_name'] = get_organizer_name($event);
                $arrEvent[$key]['organizer_logo'] = get_organizer_logo($event, 'full');
                $arrEvent[$key]['organizer_description'] = get_organizer_description($event);
                $arrEvent[$key]['organizer_email'] = get_event_organizer_email($event);
                $arrEvent[$key]['organizer_website'] = get_organizer_website($event);
                $arrEvent[$key]['organizer_twitter'] = get_organizer_twitter($event);
                $arrEvent[$key]['organizer_youtube'] = get_organizer_youtube($event);
                $arrEvent[$key]['organizer_facebook'] = get_organizer_facebook($event);
            }

            $arrData['event'] = $arrEvent;

            if($eventid == '' && $eventid == 0)
            {
                $arrData['total_event'] = intval($events->found_posts);
                $arrData['total_page'] = intval($events->max_num_pages);
                $arrData['current_page'] = intval($paged);
            }
        }		

		return $arrData;
    }

    public function get_events_query_filter($query_args, $args)
    {
        if($this->add_new_event == 'yes')
        {
            $query_args['post_status'][] = 'pending';
        }

        if($this->userid != '' && $this->userid != 0)
        {
            $query_args['author'] = $this->userid;
        }

        if($this->eventid != '' && $this->eventid != 0)
        {
            $query_args['p'] = $this->eventid;
        }

        return $query_args;
    }

    public function add_event($request)
    {
    	$userid = $request->get_header('userid');
    	$params = $request->get_params();

    	if ($userid != "" && $params['event_title'] != "" && $params['event_description'] != '' )
        {
        	$args = [
        		'post_title'     => $params['event_title'],
				'post_content'   => $params['event_description'],
				'post_type'      => 'event_listing',
                'post_author'    => $userid,
				'comment_status' => 'closed',
				'post_status'    => 'publish',
        	];

        	$eventid = wp_insert_post($args);

        	$my_event = [
        		'ID'           => $eventid,
      			'post_status'  => 'pending',
        	];
        	wp_update_post( $my_event );

            if($eventid != '')
            {
            	foreach ($params as $key => $value) 
            	{
            		if($key == 'event_banner')
            		{
            			$imageData = $this->upload_image($value);

            			update_post_meta($eventid, '_'.$key, $imageData['image_url']);
            		}
            		else if($key == 'organizer_logo')
            		{
            			$imageData = $this->upload_image($value);

            			update_post_meta($eventid, '_thumbnail_id', $imageData['image_id']);	
            		}
            		else
            		{
            			update_post_meta($eventid, '_'.$key, $value);
            		}
            	}
            }

            $this->add_new_event = 'yes';
            $arrData = $this->get_events([], 1, 0, $userid, $eventid);

            $resp['code']     = 'SUCCESS';
            $resp['message']  = 'Add event successfully';
            $resp['data']     = $arrData;
        }
        else
        {
            $resp['code'] = 'Fail';
            $resp['message'] = 'Required parameter(s) missing.';
            $resp['data']     = new stdClass();
        }

        return $resp;
    	
    }

    public function update_event($request)
    {
    	$userid = $request->get_header('userid');
    	$params = $request->get_params();
    	$eventid = $params['eventid'];

    	if ($userid != "" && $eventid != '' )
        {
        	$my_event = [
        		'ID'           => $eventid,
        		'post_title'     => $params['event_title'],
				'post_content'   => $params['event_description'],
        	];

        	wp_update_post( $my_event );

            if($eventid != '')
            {
            	foreach ($params as $key => $value) 
            	{
            		if($key == 'event_banner')
            		{
            			$old_image_url = get_post_meta($eventid, '_'.$key, true);

            			if($old_image_url != $value)
            			{
            				$imageData = $this->upload_image($value);

            				update_post_meta($eventid, '_'.$key, $imageData['image_url']);	
            			}
            		}
            		else if($key == 'organizer_logo')
            		{
            			$old_image_id = get_post_meta($eventid, '_thumbnail_id', true);
            			$old_image_url = wp_get_attachment_url($old_image_id);

            			if($old_image_url != $value)
            			{
            				$imageData = $this->upload_image($value);

            				update_post_meta($eventid, '_thumbnail_id', $imageData['image_id']);
            			}
            		}
            		else
            		{
            			update_post_meta($eventid, '_'.$key, $value);
            		}
            	}
            }

            $arrData = $this->get_events([], 1, 0, $userid, $eventid);

            $resp['code']     = 'SUCCESS';
            $resp['message']  = 'Update event successfully';
            $resp['data']     = $arrData;
        }
        else
        {
            $resp['code'] = 'Fail';
            $resp['message'] = 'Required parameter(s) missing.';
            $resp['data']     = new stdClass();
        }

        return $resp;
    	
    }

    public function get_event_list($request)
    {
    	$posts_per_page = $request->get_header('perpage');
        $paged = $request->get_header('paged');
        $params = $request->get_params();

    	$arrData = $this->get_events($params, $posts_per_page, $paged);

    	if(!empty($arrData))
    	{
    		$resp['code']     = 'SUCCESS';
            $resp['message']  = 'Get events successfully';
            $resp['data']     = $arrData;
    	}
    	else
    	{
    		$resp['code']     = 'SUCCESS';
            $resp['message']  = 'Not found any event';
            $resp['data']     = new stdClass();
    	}

        return $resp;
    }

    public function get_single_event($request)
    {
    	$params = $request->get_params();
        $eventid = $params['eventid'];

    	if($eventid != '')
    	{
    		$arrData = $this->get_events([], '', '', '', $eventid);

	    	if(!empty($arrData))
	    	{
	    		$resp['code']     = 'SUCCESS';
	            $resp['message']  = 'Get event successfully';
	            $resp['data']     = $arrData;
	    	}
	    	else
	    	{
	    		$resp['code']     = 'SUCCESS';
	            $resp['message']  = 'Not found any event!';
	            $resp['data']     = new stdClass();
	    	}
    	}
    	else
    	{
    		$resp['code']     = 'FAIL';
            $resp['message']  = 'Required parameter(s) missing.';
            $resp['data']     = new stdClass();
    	}

        return $resp;
    }

    public function delete_event($request)
    {
        $params = $request->get_params();
        $eventid = $params['eventid'];

        if($eventid != '')
        {
            $my_event = [
                'ID'           => $eventid,
                'post_status'  => 'trash',
            ];

            wp_update_post( $my_event );

            $resp['code']     = 'SUCCESS';
            $resp['message']  = 'Event delete successfully';
            $resp['data']     = new stdClass();
        }
        else
        {
            $resp['code']     = 'FAIL';
            $resp['message']  = 'Required parameter(s) missing.';
            $resp['data']     = new stdClass();
        }

        return $resp;
    }

    /*
    * reff : https://developer.wordpress.org/reference/functions/media_handle_sideload/
    */
    public function upload_image($url)
    {
    	$arrData = [];

    	if($url != '')
    	{
    		require_once(ABSPATH . 'wp-admin' . '/includes/image.php');
    		require_once(ABSPATH . 'wp-admin' . '/includes/file.php');
    		require_once(ABSPATH . 'wp-admin' . '/includes/media.php');

    		$tmp = download_url( $url );
 
			$file_array = array(
			    'name' => basename( $url ),
			    'tmp_name' => $tmp
			);
			 
			/**
			 * Check for download errors
			 * if there are error unlink the temp file name
			 */
			if ( is_wp_error( $tmp ) ) {
			    @unlink( $file_array[ 'tmp_name' ] );
			    return $tmp;
			}
			 
			/**
			 * now we can actually use media_handle_sideload
			 * we pass it the file array of the file to handle
			 * and the post id of the post to attach it to
			 * $post_id can be set to '0' to not attach it to any particular post
			 */
			$post_id = '0';
			 
			$image_id = media_handle_sideload( $file_array, $post_id );
			 
			/**
			 * We don't want to pass something to $id
			 * if there were upload errors.
			 * So this checks for errors
			 */
			if ( is_wp_error( $image_id ) ) {
			    @unlink( $file_array['tmp_name'] );
			    return $image_id;
			}
			 
			/**
			 * No we can get the url of the sideloaded file
			 * $image_url now contains the file url in WordPress
			 * $id is the attachment id
			 */
			$image_url = wp_get_attachment_url( $image_id );

			$arrData['image_id'] = $image_id;
			$arrData['image_url'] = $image_url;
    	}

    	return $arrData;
    }

}

WP_Event_Manager_API::instance();