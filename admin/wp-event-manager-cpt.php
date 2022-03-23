<?php
/*
* Admin UI for creating custom post types(CPT) and custom taxonomies in WordPress.
*
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * WP_Event_Manager_CPT class.
 */

class WP_Event_Manager_CPT
{

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */

	public function __construct()
	{

		add_filter('enter_title_here', array($this, 'enter_title_here'), 1, 2);

		add_filter('manage_edit-event_listing_columns', array($this, 'columns'));

		add_filter('list_table_primary_column', array($this, 'primary_column'), 10, 2);
		add_filter('post_row_actions', array($this, 'row_actions'));

		add_action('manage_event_listing_posts_custom_column', array($this, 'custom_columns'), 2);

		add_filter('manage_edit-event_listing_sortable_columns', array($this, 'sortable_columns'));

		add_filter('request', array($this, 'sort_columns'));

		add_filter('manage_event_organizer_posts_columns', array($this, 'organizer_columns'), 10);
		add_action('manage_event_organizer_posts_custom_column', array($this, 'organizer_columns_data'), 10, 2);

		add_filter('post_updated_messages', array($this, 'post_updated_messages'));

		add_action('admin_footer-edit.php', array($this, 'add_bulk_actions'));

		add_action('load-edit.php', array($this, 'do_bulk_actions'));

		add_action('admin_init', array($this, 'approve_event'));

		add_action('admin_notices', array($this, 'approved_notice'));

		add_action('admin_notices', array($this, 'expired_notice'));

		if (get_option('event_manager_enable_categories')) {

			add_action('restrict_manage_posts', array($this, 'events_by_category'));
		}

		if (get_option('event_manager_enable_event_types') && get_option('event_manager_enable_categories')) {

			add_action('restrict_manage_posts', array($this, 'events_by_event_type'));
		}

		foreach (array('post', 'post-new') as $hook) {

			add_action("admin_footer-{$hook}.php", array($this, 'extend_submitdiv_post_status'));
		}
	}

	/**
	 * Edit bulk actions
	 */

	public function add_bulk_actions()
	{

		global $post_type, $wp_post_types;

		if ($post_type == 'event_listing') {
?>
			<script type="text/javascript">
				jQuery(document).ready(function() {

					jQuery('<option>').val('approve_events').text('<?php printf(__('Approve %s', 'wp-event-manager'), esc_attr($wp_post_types['event_listing']->labels->name)); ?>').appendTo("select[name='action']");

					jQuery('<option>').val('approve_events').text('<?php printf(__('Approve %s', 'wp-event-manager'), esc_attr($wp_post_types['event_listing']->labels->name)); ?>').appendTo("select[name='action2']");

					jQuery('<option>').val('expire_events').text('<?php printf(__('Expire %s', 'wp-event-manager'), esc_attr($wp_post_types['event_listing']->labels->name)); ?>').appendTo("select[name='action']");

					jQuery('<option>').val('expire_events').text('<?php printf(__('Expire %s', 'wp-event-manager'), esc_attr($wp_post_types['event_listing']->labels->name)); ?>').appendTo("select[name='action2']");

				});
			</script>

		<?php
		}
	}

	/**
	 * Do custom bulk actions
	 */

	public function do_bulk_actions()
	{

		$wp_list_table = _get_list_table('WP_Posts_List_Table');

		$action = $wp_list_table->current_action();

		switch ($action) {

			case 'approve_events':
				check_admin_referer('bulk-posts');

				$post_ids = array_map('absint', array_filter((array) $_GET['post']));

				$approved_events = array();

				if (!empty($post_ids)) {

					foreach ($post_ids as $post_id) {

						$event_data = array(

							'ID'          => $post_id,

							'post_status' => 'publish',
						);

						if (in_array(get_post_status($post_id), array('pending', 'pending_payment')) && current_user_can('publish_post', $post_id) && wp_update_post($event_data)) {

							$approved_events[] = $post_id;
						}
					}
				}

				wp_redirect(add_query_arg('approved_events', $approved_events, remove_query_arg(array('approved_events', 'expired_events'), admin_url('edit.php?post_type=event_listing'))));

				exit;

				break;

			case 'expire_events':
				check_admin_referer('bulk-posts');

				$post_ids = array_map('absint', array_filter((array) $_GET['post']));

				$expired_events = array();

				if (!empty($post_ids)) {

					foreach ($post_ids as $post_id) {

						$event_data = array(

							'ID'          => $post_id,

							'post_status' => 'expired',
						);

						if (current_user_can('manage_event_listings') && wp_update_post($event_data)) {

							$expired_events[] = $post_id;
						}
					}
				}

				wp_redirect(add_query_arg('expired_events', $expired_events, remove_query_arg(array('approved_events', 'expired_events'), admin_url('edit.php?post_type=event_listing'))));

				exit;

				break;
		}

		return;
	}

	/**
	 * Approve a single event
	 */

	public function approve_event()
	{

		if (!empty($_GET['approve_event']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'approve_event') && current_user_can('publish_post', $_GET['approve_event'])) {

			$post_id = absint($_GET['approve_event']);

			$event_end_date    = get_post_meta($post_id, '_event_end_date', true);
			$current_timestamp = strtotime(current_time('Y-m-d H:i:s'));

			if (strtotime($event_end_date) > $current_timestamp) {
				$event_data = array(
					'ID'          => $post_id,
					'post_status' => 'publish',
				);
			} else {
				$event_data = array(
					'ID'          => $post_id,
					'post_status' => 'expired',
				);
			}

			wp_update_post($event_data);

			wp_redirect(remove_query_arg('approve_event', add_query_arg('approved_events', $post_id, admin_url('edit.php?post_type=event_listing'))));

			exit;
		}
	}

	/**
	 * Show a notice if we did a bulk action or approval
	 */
	public function approved_notice()
	{

		global $post_type, $pagenow;

		if ($pagenow == 'edit.php' && $post_type == 'event_listing' && !empty($_REQUEST['approved_events'])) {

			$approved_events = $_REQUEST['approved_events'];

			if (is_array($approved_events)) {

				$approved_events = array_map('absint', $approved_events);

				$titles = array();

				foreach ($approved_events as $event_id) {

					$titles[] = get_the_title($event_id);
				}

				echo '<div class="updated"><p>' . sprintf(__('%s approved', 'wp-event-manager'), '&quot;' . implode('&quot;, &quot;', $titles) . '&quot;') . '</p></div>';
			} else {

				echo '<div class="updated"><p>' . sprintf(__('%s approved', 'wp-event-manager'), '&quot;' . get_the_title($approved_events) . '&quot;') . '</p></div>';
			}
		}
	}

	/**
	 * Show a notice if we did a bulk action or approval
	 */

	public function expired_notice()
	{

		global $post_type, $pagenow;

		if ($pagenow == 'edit.php' && $post_type == 'event_listing' && !empty($_REQUEST['expired_events'])) {

			$expired_events = $_REQUEST['expired_events'];

			if (is_array($expired_events)) {

				$expired_events = array_map('absint', $expired_events);

				$titles = array();

				foreach ($expired_events as $event_id) {

					$titles[] = get_the_title($event_id);
				}

				echo '<div class="updated"><p>' . sprintf(__('%s expired', 'wp-event-manager'), '&quot;' . implode('&quot;, &quot;', $titles) . '&quot;') . '</p></div>';
			} else {

				echo '<div class="updated"><p>' . sprintf(__('%s expired', 'wp-event-manager'), '&quot;' . get_the_title($expired_events) . '&quot;') . '</p></div>';
			}
		}
	}

	/**
	 * Show category dropdown
	 */
	public function events_by_category()
	{

		global $typenow, $wp_query;

		if ($typenow != 'event_listing' || !taxonomy_exists('event_listing_category')) {

			return;
		}

		include_once EVENT_MANAGER_PLUGIN_DIR . '/core/wp-event-manager-category-walker.php';

		$r = array();

		$r['pad_counts'] = 1;

		$r['hierarchical'] = 1;

		$r['hide_empty'] = 0;

		$r['show_count'] = 1;

		$r['selected'] = (isset($wp_query->query['event_listing_category'])) ? $wp_query->query['event_listing_category'] : '';

		$r['menu_order'] = false;

		$terms = get_terms('event_listing_category', $r);

		$walker = new WP_Event_Manager_Category_Walker();

		if (!$terms) {

			return;
		}

		$output = "<select name='event_listing_category' id='dropdown_event_listing_category'>";

		$output .= '<option value="" ' . selected(isset($_GET['event_listing_category']) ? $_GET['event_listing_category'] : '', '', false) . '>' . __('Select category', 'wp-event-manager') . '</option>';

		$output .= $walker->walk($terms, 0, $r);

		$output .= '</select>';

		echo $output;
	}

	/**
	 * Show Event type dropdown
	 */
	public function events_by_event_type()
	{
		global $typenow, $wp_query;

		if ($typenow != 'event_listing' || !taxonomy_exists('event_listing_type')) {
			return;
		}

		$r                 = array();
		$r['pad_counts']   = 1;
		$r['hierarchical'] = 1;
		$r['hide_empty']   = 0;
		$r['show_count']   = 1;
		$r['selected']     = (isset($wp_query->query['event_listing_type'])) ? $wp_query->query['event_listing_type'] : '';
		$r['menu_order']   = false;
		$terms             = get_terms('event_listing_type', $r);
		$walker            = new WP_Event_Manager_Category_Walker();

		if (!$terms) {
			return;
		}

		$output  = "<select name='event_listing_type' id='dropdown_event_listing_category'>";
		$output .= '<option value="" ' . selected(isset($_GET['event_listing_type']) ? $_GET['event_listing_type'] : '', '', false) . '>' . __('Select Event Type', 'wp-event-manager') . '</option>';
		$output .= $walker->walk($terms, 0, $r);
		$output .= '</select>';

		echo $output;
	}

	/**
	 * enter_title_here function.
	 *
	 * @access public
	 * @return void
	 */
	public function enter_title_here($text, $post)
	{

		if ($post->post_type == 'event_listing') {

			return __('Event Title', 'wp-event-manager');
		}

		return $text;
	}

	/**
	 * post_updated_messages function.
	 *
	 * @access public
	 * @param mixed $messages
	 * @return void
	 */

	public function post_updated_messages($messages)
	{

		global $post, $post_ID, $wp_post_types;

		$messages['event_listing'] = array(

			0  => '',

			1  => sprintf(__('%1$s updated. <a href="%2$s">View</a>', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name, esc_url(get_permalink($post_ID))),

			2  => __('Custom field updated.', 'wp-event-manager'),

			3  => __('Custom field deleted.', 'wp-event-manager'),

			4  => sprintf(__('%s updated.', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name),

			5  => isset($_GET['revision']) ? sprintf(__('%1$s restored to revision from %2$s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name, wp_post_revision_title((int) $_GET['revision'], false)) : false,

			6  => sprintf(__('%1$s published. <a href="%2$s">View</a>', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name, esc_url(get_permalink($post_ID))),

			7  => sprintf(__('%s saved.', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name),

			8  => sprintf(__('%1$s submitted. <a target="_blank" href="%2$s">Preview</a>', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name, esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),

			9  => sprintf(
				__('%s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview</a>', 'wp-event-manager'),
				$wp_post_types['event_listing']->labels->singular_name,
				date_i18n(__('M j, Y @ G:i', 'wp-event-manager'), strtotime($post->post_date)),
				esc_url(get_permalink($post_ID))
			),

			10 => sprintf(__('%1$s draft updated. <a target="_blank" href="%2$s">Preview</a>', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name, esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),

		);

		return $messages;
	}

	/**
	 * columns function.
	 *
	 * @param array $columns
	 * @return array
	 */

	public function columns($columns)
	{

		if (!is_array($columns)) {

			$columns = array();
		}

		unset($columns['title'], $columns['date'], $columns['author']);

		$columns['event_title'] = __('Title', 'wp-event-manager');

		$columns['event_banner'] = '<span class="tips dashicons dashicons-format-image" data-tip="' . __('Banner', 'wp-event-manager') . '">' . __('Banner', 'wp-event-manager') . '</span>';

		$columns['event_listing_type'] = __('Type', 'wp-event-manager');

		$columns['event_location'] = __('Location', 'wp-event-manager');

		$columns['event_organizer'] = __('Organizer', 'wp-event-manager');

		$columns['event_start_date'] = __('Start Date', 'wp-event-manager');

		$columns['event_end_date'] = __('End Date', 'wp-event-manager');

		$columns['event_expires'] = __('Expiry Date', 'wp-event-manager');

		$columns['event_status'] = '<span class="tips" data-tip="' . __('Status', 'wp-event-manager') . '">' . __('Status', 'wp-event-manager') . '</span>';

		$columns['cancelled'] = '<span class="tips" data-tip="' . __('Cancelled?', 'wp-event-manager') . '">' . __('Cancelled?', 'wp-event-manager') . '</span>';

		$columns['featured_event'] = '<span class="tips" data-tip="' . __('Featured?', 'wp-event-manager') . '">' . __('Featured?', 'wp-event-manager') . '</span>';

		$columns['event_actions'] = __('Actions', 'wp-event-manager');

		if (!get_option('event_manager_enable_event_types')) {

			unset($columns['event_listing_type']);
		}

		if (!get_option('enable_event_organizer')) {

			unset($columns['event_organizer']);
		}
		return $columns;
	}

	/**
	 * This is required to make column responsive since WP 4.3
	 *
	 * @access public
	 * @param string $column
	 * @param string $screen
	 * @return string
	 */
	public function primary_column($column, $screen)
	{

		// if we want to set the primary column for CPT
		if ('edit-event_listing' === $screen) {
			$column = 'event_title';
		}

		return $column;
	}

	/**
	 * Removes all action links because WordPress add it to primary column.
	 * Note: Removing all actions also remove mobile "Show more details" toggle button.
	 * So the button need to be added manually in custom_columns callback for primary column.
	 *
	 * @access public
	 * @param array $actions
	 * @return array
	 */
	public function row_actions($actions)
	{
		if ('event_listing' == get_post_type()) {
			return array();
		}
		return $actions;
	}

	/**
	 * custom_columns function.
	 *
	 * @access public
	 * @param mixed $column
	 * @return void
	 */

	public function custom_columns($column)
	{

		global $post;

		switch ($column) {

			case 'event_status':
				echo '<span data-tip="' . esc_attr(get_event_status($post)) . '" class="tips status-' . esc_attr($post->post_status) . '">' . esc_attr(get_event_status($post)) . '</span>';

				break;

			case 'cancelled':
				if (is_event_cancelled($post)) {
					echo '<span class="tips dashicons dashicons-no" data-tip="' . __('Cancelled', 'wp-event-manager') . '">' . __('Cancelled', 'wp-event-manager') . '</span>';
				} else {
					echo '&ndash;';
				}

				break;

				'<span class="tips dashicons dashicons-format-image" data-tip="' . __('Banner', 'wp-event-manager') . '">' . __('Banner', 'wp-event-manager') . '</span>';

			case 'featured_event':
				if (is_event_featured($post)) {
					echo '<span class="tips dashicons dashicons-star-filled" data-tip="' . __('Featured', 'wp-event-manager') . '">' . __('Featured', 'wp-event-manager') . '</span>';
				} else {
					echo '<span class="tips dashicons dashicons-star-empty" data-tip="' . __('Not Featured', 'wp-event-manager') . '">' . __('Not Featured', 'wp-event-manager') . '</span>';
				}

				break;

			case 'event_banner':
				echo '<div class="event_banner">';

				display_event_banner();

				echo '</div>';

				break;

			case 'event_title':
				echo '<div class="event_title">';

				echo '<a href="' . esc_url(admin_url('post.php?post=' . $post->ID . '&action=edit')) . '" class="tips event_title" data-tip="' . sprintf(__('ID: %d', 'wp-event-manager'), $post->ID) . '">' . esc_html($post->post_title) . '</a>';

				echo '</div>';

				echo '<button type="button" class="toggle-row"><span class="screen-reader-text">' . esc_html__('Show more details', 'wp-event-manager') . '</span></button>';

				break;

			case 'event_listing_type':
				$types = get_event_type($post);

				if ($types && !empty($types)) {
					foreach ($types as $type) {
						echo '<span class="event-type ' . $type->slug . '">' . $type->name . '</span>';
					}
				}
				break;

			case 'event_location':
				display_event_location($post);

				break;

			case 'event_organizer':
				echo '<div class="organizer">';

				echo get_organizer_name('', true, 'backend');

				echo '</div>';

				break;

			case 'event_start_date':
				if ($post->_event_start_date) {
					$format = get_option('date_format');
					$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
					if ($datetime = DateTime::createFromFormat("'.$datepicker_date_format.'", "'.$post->_event_start_date.'")) {
						$date = 	$datetime->format($format);
					} else {
						$date = date_i18n(get_option('date_format'), strtotime($post->_event_start_date));
					}
					echo $date;
				} else {
					echo '&ndash;';
				}
				break;

			case 'event_end_date':
				if ($post->_event_end_date) {
					$format = get_option('date_format');
					$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
					if ($datetime = DateTime::createFromFormat("'.$datepicker_date_format.'", "'.$post->_event_end_date.'")) {
						$date = 	$datetime->format($format);
					} else {
						$date = date_i18n(get_option('date_format'), strtotime($post->_event_end_date));
					}

					echo $date;
				} else {
					echo '&ndash;';
				}
				break;

			case 'event_expires':
				if ($post->_event_expiry_date) {
					$format = get_option('date_format');
					$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
					if ($datetime = DateTime::createFromFormat("'.$datepicker_date_format.'", "'.$post->_event_expiry_date.'")) {
						$date = 	$datetime->format($format);
					} else {
						$date = date_i18n(get_option('date_format'), strtotime($post->_event_expiry_date));
					}

					echo $date;
				}

				// echo '<strong>' .date_i18n( get_option( 'date_format' ), strtotime( get_event_expiry_date($post->ID)) )  . '</strong>';
				else {
					echo '&ndash;';
				}
				break;

			case 'event_actions':
				echo '<div class="actions">';

				$admin_actions = apply_filters('post_row_actions', array(), $post);

				if (in_array($post->post_status, array('pending', 'pending_payment')) && current_user_can('publish_post', $post->ID)) {

					$admin_actions['approve'] = array(

						'action' => 'approve',

						'name'   => __('Approve', 'wp-event-manager'),

						'url'    => wp_nonce_url(add_query_arg('approve_event', $post->ID), 'approve_event'),
					);
				}

				if ($post->post_status !== 'trash') {

					if (current_user_can('read_post', $post->ID)) {

						$admin_actions['view'] = array(

							'action' => 'view',

							'name'   => __('View', 'wp-event-manager'),

							'url'    => get_permalink($post->ID),
						);
					}

					if (current_user_can('edit_post', $post->ID)) {

						$admin_actions['edit'] = array(

							'action' => 'edit',

							'name'   => __('Edit', 'wp-event-manager'),

							'url'    => get_edit_post_link($post->ID),
						);
					}

					if (current_user_can('delete_post', $post->ID)) {

						$admin_actions['delete'] = array(

							'action' => 'delete',

							'name'   => __('Delete', 'wp-event-manager'),

							'url'    => get_delete_post_link($post->ID),
						);
					}
				}

				$admin_actions = apply_filters('event_manager_admin_actions', $admin_actions, $post);

				foreach ($admin_actions as $action) {

					if (is_array($action)) {

						printf('<a class="button button-icon tips icon-%1$s" href="%2$s" data-tip="%3$s">%4$s</a>', $action['action'], esc_url($action['url']), esc_attr($action['name']), esc_html($action['name']));
					} else {

						echo str_replace('class="', 'class="button ', $action);
					}
				}

				echo '</div>';

				break;
		}
	}

	/**
	 * sortable_columns function.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return void
	 */

	public function sortable_columns($columns)
	{

		$custom = array(

			'event_posted'     => 'date',

			'event_title'      => 'title',

			'event_location'   => 'event_location',

			'event_start_date' => 'event_start_date',

			'event_end_date'   => 'event_end_date',
			'event_expires'    => 'event_expires',
		);

		return wp_parse_args($custom, $columns);
	}

	/**
	 * sort_columns function.
	 *
	 * @access public
	 * @param mixed $vars
	 * @return void
	 */

	public function sort_columns($vars)
	{

		if (isset($vars['orderby'])) {

			if ('event_expires' === $vars['orderby']) {

				$vars = array_merge(
					$vars,
					array(
						'meta_key'  => '_event_expiry_date',
						'orderby'   => 'meta_value',
						'meta_type' => 'DATE',
					)
				);
			} elseif ('event_start_date' === $vars['orderby']) {

				$vars = array_merge(
					$vars,
					array(
						'meta_key'  => '_event_start_date',
						'orderby'   => 'meta_value',
						'meta_type' => 'DATETIME',
					)
				);
			} elseif ('event_end_date' === $vars['orderby']) {

				$vars = array_merge(
					$vars,
					array(
						'meta_key'  => '_event_end_date',
						'orderby'   => 'meta_value',
						'meta_type' => 'DATETIME',
					)
				);
			} elseif ('event_location' === $vars['orderby']) {

				$vars = array_merge(
					$vars,
					array(
						'meta_key' => '_event_location',
						'orderby'  => 'meta_value',
					)
				);
			} elseif ('event_organizer' === $vars['orderby']) {

				$vars = array_merge(
					$vars,
					array(
						'meta_key' => '_organizer_name',
						'orderby'  => 'meta_value',
					)
				);
			}
		}

		return $vars;
	}

	/**
	 * organizer_columns function.
	 *
	 * @access public
	 * @param $columns
	 * @return
	 * @since 3.1.16
	 */
	public function organizer_columns($columns)
	{

		$columns = array_slice($columns, 0, 2, true) + array('organizer_email' => __('Email', 'wp-event-manager')) + array_slice($columns, 2, count($columns) - 2, true);

		return $columns;
	}

	/**
	 * organizer_columns_data function.
	 *
	 * @access public
	 * @param $column, $post_id
	 * @return
	 * @since 3.1.16
	 */
	public function organizer_columns_data($column, $post_id)
	{

		switch ($column) {
			case 'organizer_email':
				echo get_post_meta($post_id, '_organizer_email', true);
				break;
		}
	}

	/**
	 * Adds post status to the "submitdiv" Meta Box and post type WP List Table screens. Based on https://gist.github.com/franz-josef-kaiser/2930190
	 *
	 * @return void
	 */
	public function extend_submitdiv_post_status()
	{

		global $post, $post_type;

		// Abort if we're on the wrong post type, but only if we got a restriction

		if ('event_listing' !== $post_type) {

			return;
		}

		// Get all non-builtin post status and add them as <option>

		$options = $display = '';

		foreach (get_event_listing_post_statuses() as $status => $name) {

			$selected = selected($post->post_status, $status, false);

			// If we one of our custom post status is selected, remember it

			$selected and $display = $name;

			// Build the options

			$options .= "<option{$selected} value='{$status}'>{$name}</option>";
		}

		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {

				<?php if (!empty($display)) : ?>

					jQuery('#post-status-display').html('<?php echo $display; ?>');

				<?php endif; ?>

				var select = jQuery('#post-status-select').find('select');

				jQuery(select).html("<?php echo $options; ?>");
			});
		</script>
<?php
	}
}
new WP_Event_Manager_CPT();
