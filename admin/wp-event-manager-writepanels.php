<?php
/*
* This file use to create fields of wp event manager at admin side.
*/
if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 *  Class with details of Field editor functionality .
 */
class WP_Event_Manager_Writepanels {

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
	public static function instance(){
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post', array($this, 'save_post'), 1, 2);
		add_action('event_manager_save_event_listing', array($this, 'save_event_listing_data'), 20, 2);

		add_action('event_manager_save_organizer_listing', array($this, 'save_organizer_listing_data'), 20, 2);
		add_action('event_manager_save_venue_listing', array($this, 'save_venue_listing_data'), 20, 2);

		add_action('before_delete_post', array($this, 'delete_event_with_attachment'), 10);
		add_filter('rest_prepare_taxonomy', array($this, 'wpem_hide_taxonomy_metabox'), 10, 2);
	}

	/**
	 * Display the tabs which is used in edit or add event in backend.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 * @since 1.0.0
	 */
	public function event_manager_data($post) {
		global $post, $wpem_thepostid;
		$wpem_thepostid = $post->ID;
		wp_enqueue_script('wp-event-manager-admin-js');
		wp_nonce_field('save_meta_data', 'event_manager_nonce');
		include('templates/listings-data-tabs.php');
	}

	/**
	 * Return array of tabs to show.
	 *
	 * @access public
	 * @return array
	 * @since 1.0.0
	 */
	public function get_event_data_tabs() {
		wp_register_script( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
		wp_localize_script('chosen', 'wpem_chosen', array(
			'multiple_text' => __('Select Some Options', 'wp-event-manager'),
			'single_text' => __('Select an Option', 'wp-event-manager'),
			'no_result_text' => __('No results match', 'wp-event-manager'),
		));
		wp_enqueue_script('chosen');
		wp_enqueue_style( 'chosen', EVENT_MANAGER_PLUGIN_URL . '/assets/css/chosen.css', array(), '1.0.0' );
		$tabs = apply_filters(
			'wpem_event_data_tabs',
			array(
				'general'        => array(
					'label'    => __('General', 'wp-event-manager'),
					'target'   => 'general_event_data_content',
					'class'    => array(''),
					'priority' => 1,
				),
				'venue'        => array(
					'label'    => __('Venue/Location', 'wp-event-manager'),
					'target'   => 'venue_event_data_content',
					'class'    => array(),
					'priority' => 2,
				),
				'date'        => array(
					'label'    => __('Date and Time', 'wp-event-manager'),
					'target'   => 'date_time_event_data_content',
					'class'    => array(''),
					'priority' => 3,
				),
				'registration'        => array(
					'label'    => __('Registration', 'wp-event-manager'),
					'target'   => 'registration_event_data_content',
					'class'    => array(''),
					'priority' => 4,
				),
				'advanced'        => array(
					'label'    => __('Advanced', 'wp-event-manager'),
					'target'   => 'advanced_event_data_content',
					'class'    => array(''),
					'priority' => 10,
				),
			)
		);

		// Sort tabs based on priority.
		uasort($tabs, array($this, 'sort_by_priority'));
		return $tabs;
	}

	/**
	 * It used to get event listing fields.
	 *
	 * @access public
	 * @return void
	 */
	public function event_listing_fields(){
		global $post;
		
		$current_user = wp_get_current_user();
		if(isset($post->ID)) {
			$registration = metadata_exists('post', $post->ID, '_event_registration_email') ? esc_html(get_post_meta($post->ID, '_event_registration_email', true)) : $current_user->user_email;
			$registration = metadata_exists('post', $post->ID, '_registration') ? esc_html(get_post_meta($post->ID, '_registration', true)) : $registration;
			$expiry_date  = esc_attr(get_post_meta($post->ID, '_event_expiry_date', true));
			if($expiry_date) {
				$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
				$php_date_format        = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
				$expiry_date = gmdate( $php_date_format, strtotime( $expiry_date ) );
			}
		} else {
			$registration = $current_user->user_email;
			$expiry_date  = '';
		}
		$GLOBALS['event_manager']->forms->get_form('submit-event', array());
		$form_submit_event_instance = call_user_func(array('WPEM_Event_Manager_Form_Submit_Event', 'instance'));
		$fields                     = $form_submit_event_instance->wpem_merge_with_custom_fields('backend');
		/** add _ (prefix) for all backend fields.
		 *  Field editor will only return fields without _(prefix).
		 */
		foreach ($fields as $group_key => $group_fields) {
			foreach ($group_fields as $field_key => $field_value) {
				
				if($field_key === 'registration') {
					$field_value['value'] = $registration;
				}
				if(strpos($field_key, '_') !== 0) {
					$fields['_' . $field_key] = $field_value;
				} else {
					$fields[$field_key] = $field_value;
				}
				if(isset($fields['_' .$field_key]['visibility']) && ($fields['_' .$field_key]['visibility'] == 0 || $fields['_' .$field_key]['visibility'] == false)) :
					unset($fields['_' .$field_key]);
				endif; 
			}
			unset($fields[$group_key]);
		}
		$fields = apply_filters('event_manager_event_listing_data_fields', $fields);
		if(isset($fields['_event_title'])) {
			unset($fields['_event_title']);
		}
		if(isset($fields['_event_description'])) {
			unset($fields['_event_description']);
		}
		if(isset($fields['_organizer_logo'])) {
			unset($fields['_organizer_logo']);
		}
		if(isset($fields['_event_thumbnail'])) {
			unset($fields['_event_thumbnail']);
		}

		if($current_user->has_cap('manage_event_listings')) {
			$fields['_featured'] = array(
				'label'       => __('Featured Listing', 'wp-event-manager'),
				'type'        => 'checkbox',
				'description' => __('Featured listings will be sticky during searches, and can be styled differently.', 'wp-event-manager'),
				'priority'    => 39,
				'tabgroup' => 1,
			);
			$fields['_cancelled'] = array(
				'label'       => __('Cancelled Listing', 'wp-event-manager'),
				'type'        => 'checkbox',
				'description' => __('Cancelled listings will be sticky during searches, and can be styled differently.', 'wp-event-manager'),
				'priority'    => 39,
				'tabgroup' => 1,
			);
			$fields['_event_expiry_date'] = array(
				'label'       => __('Listing Expiry Date', 'wp-event-manager'),
				'type'        => 'date',
				'placeholder' => __('Please enter event expiry date', 'wp-event-manager'),
				'priority'    => 40,
				'value'       => $expiry_date,
				'tabgroup' => 3,
			);
		}
		if($current_user->has_cap('edit_others_event_listings')) {
			$fields['_event_author'] = array(
				'label'    => __('Posted by', 'wp-event-manager'),
				'type'     => 'author',
				'priority' => 41,
				'tabgroup' => 1,
			);
		} 

		if( !isset( $fields['_event_end_date'] ) ) {
			unset( $fields['_event_expiry_date'] );
		}
		if( !get_option( 'enable_event_organizer' ) ) {
			unset( $fields['organizer']);
			unset( $fields['_event_organizer_ids'] );
		}

		if( !get_option( 'enable_event_venue' ) ) {
			unset( $fields['venue'] );
			unset( $fields['event_venue_ids'] );
		}
		uasort($fields, array($this, 'sort_by_priority'));
		return apply_filters( 'wpem_admin_event_form_fields', $fields);
	}

	/**
	 * Sort array by priority value.
	 */
	protected function sort_by_priority($a, $b)	{
		if(!isset($a['priority']) || !isset($b['priority']) || $a['priority'] === $b['priority']) {
			return 0;
		}
		return ($a['priority'] < $b['priority']) ? -1 : 1;
	}

	/**
	 * Add metabox of event.
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes(){
		global $wp_post_types;

		add_meta_box('event_manager_data', sprintf(wp_kses('%s Data', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name), array($this, 'event_manager_data'), 'event_listing', 'normal', 'high');

		if(!get_option('event_manager_enable_event_types')) {
			remove_meta_box('event_listing_typediv', 'event_listing', 'side');
		} elseif(false == event_manager_multiselect_event_type()) {
			remove_meta_box('event_listing_typediv', 'event_listing', 'side');
			$event_listing_type = get_taxonomy('event_listing_type');
			add_meta_box('event_listing_type', $event_listing_type->labels->menu_name, array($this, 'event_listing_type_metabox'), 'event_listing', 'side', 'core');
		}

		if(!get_option('event_manager_enable_categories')) {
			remove_meta_box('event_listing_categorydiv', 'event_listing', 'side');
		} elseif(false == event_manager_multiselect_event_category()) {
			remove_meta_box('event_listing_categorydiv', 'event_listing', 'side');
			$event_listing_category = get_taxonomy('event_listing_category');
			add_meta_box('event_listing_category', $event_listing_category->labels->menu_name, array($this, 'event_listing_category_metabox'), 'event_listing', 'side', 'core');
		}

		if(isset($wp_post_types['event_organizer'])) {
			add_meta_box('event_organizer_data', sprintf(wp_kses('%s Data', 'wp-event-manager'), $wp_post_types['event_organizer']->labels->singular_name), array($this, 'event_organizer_data'), 'event_organizer', 'normal', 'high');
		}

		if(isset($wp_post_types['event_venue'])) {
			add_meta_box('event_venue_data', sprintf(wp_kses('%s Data', 'wp-event-manager'), $wp_post_types['event_venue']->labels->singular_name), array($this, 'event_venue_data'), 'event_venue', 'normal', 'high');
		}
	}
	/**
	 * Hide texonomy metabox on event edit page.
	 *
	 * @access public
	 * @return void
	 */
	public function wpem_hide_taxonomy_metabox($response, $taxonomy){
		if(false == event_manager_multiselect_event_type()) {
			if('event_listing_type' === $taxonomy->name) {
				$response->data['visibility']['show_ui'] = false;
			}
		}
		if(false == event_manager_multiselect_event_category()) {
			if('event_listing_category' === $taxonomy->name) {
				$response->data['visibility']['show_ui'] = false;
			}
		}
		return $response;
	}

	/**
	 * Use of event listing type metabox.
	 *
	 * @param mixed $post
	 * @param
	 */
	function event_listing_type_metabox($post) {
		// Set up the taxonomy object and get terms
		$taxonomy = 'event_listing_type';
		$tax      = get_taxonomy($taxonomy); // This is the taxonomy object event

		// The name of the form
		$name = 'tax_input[' . $taxonomy . '][]';

		// Get all the terms for this taxonomy
		$terms     = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => 0]);
		$postterms = get_the_terms($post->ID, $taxonomy);
		$current   = ($postterms ? array_pop($postterms) : false);
		$current   = ($current ? $current->term_id : 0);
		// Get current and popular terms
		$popular   = get_terms([
			'taxonomy' => $taxonomy,
			'orderby'      => 'count',
			'order'        => 'DESC',
			'number'       => 10,
			'hierarchical' => false,
		]);
		$postterms = get_the_terms($post->ID, $taxonomy);
		$current   = ($postterms ? array_pop($postterms) : false);
		$current   = ($current ? $current->term_id : 0); ?>

		<div id="taxonomy-<?php echo esc_attr($taxonomy); ?>" class="categorydiv">
			<!-- Display tabs-->
			<ul id="<?php echo esc_attr($taxonomy); ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo esc_attr($taxonomy); ?>-all" tabindex="3"><?php echo esc_attr($tax->labels->all_items); ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo esc_attr($taxonomy); ?>-pop" tabindex="3"><?php esc_attr_e('Most Used', 'wp-event-manager'); ?></a></li>
			</ul>
			<!-- Display taxonomy terms -->
			<div id="<?php echo esc_attr($taxonomy); ?>-all" class="tabs-panel">
				<ul id="<?php echo esc_attr($taxonomy); ?>checklist" class="list:<?php echo esc_attr($taxonomy); ?> categorychecklist form-no-clear">
					<?php
					foreach ($terms as $term) {
						$id = $taxonomy . '-' . $term->term_id;
						echo "<li id='" . esc_attr($id) . "'><label class='selectit'>";
						echo "<input type='radio' id='in-" . esc_attr($id) . "' name='" . esc_attr($name) . "' " . checked($current, $term->term_id, false) . "value='" . esc_attr($term->term_id) . "' />" . esc_html($term->name) . '<br />';
						echo wp_kses_post('</label></li>');
					}
					?>
				</ul>
			</div>
			<!-- Display popular taxonomy terms -->
			<div id="<?php echo esc_attr($taxonomy); ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo esc_attr($taxonomy); ?>checklist-pop" class="categorychecklist form-no-clear">
					<?php
					foreach ($popular as $term) {
						$id = 'popular-' . esc_attr($taxonomy) . '-' . esc_attr($term->term_id);
						echo "<li id='" . esc_attr($id) . "'><label class='selectit'>";
						echo "<input type='radio' name='" . esc_attr($name) . "' id='in-" . esc_attr($id) . "' value='" . esc_attr($term->term_id) . "' />" . esc_html($term->name) . '<br />';
						echo wp_kses_post('</label></li>');
					} ?>
				</ul>
			</div>
		</div>
	<?php
	}

	/**
	 * Use of event listing category metabox.
	 *
	 * @param mixed $post
	 * @param
	 */
	function event_listing_category_metabox($post)	{
		// Set up the taxonomy object and get terms
		$taxonomy = 'event_listing_category';
		$tax      = get_taxonomy($taxonomy); // This is the taxonomy object event

		// The name of the form
		$name = 'tax_input[' . $taxonomy . '][]';

		// Get all the terms for this taxonomy
		$terms     = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => 0]);
		$postterms = get_the_terms($post->ID, $taxonomy);
		$current   = ($postterms ? array_pop($postterms) : false);
		$current   = ($current ? $current->term_id : 0);
		// Get current and popular terms
		$popular   = get_terms([
			'taxonomy' => $taxonomy,
			'orderby'      => 'count',
			'order'        => 'DESC',
			'number'       => 10,
			'hierarchical' => false,
		]);
		$postterms = get_the_terms($post->ID, $taxonomy);
		$current   = ($postterms ? array_pop($postterms) : false);
		$current   = ($current ? $current->term_id : 0);?>

		<div id="taxonomy-<?php echo esc_attr($taxonomy); ?>" class="categorydiv">
			<!-- Display tabs-->
			<ul id="<?php echo esc_attr($taxonomy); ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo esc_attr($taxonomy); ?>-all" tabindex="3"><?php echo esc_attr($tax->labels->all_items); ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo esc_attr($taxonomy); ?>-pop" tabindex="3"><?php esc_attr_e('Most Used', 'wp-event-manager'); ?></a></li>
			</ul>
			<!-- Display taxonomy terms -->
			<div id="<?php echo esc_attr($taxonomy); ?>-all" class="tabs-panel">
				<ul id="<?php echo esc_attr($taxonomy); ?>checklist" class="list:<?php echo esc_attr($taxonomy); ?> categorychecklist form-no-clear">
					<?php
					foreach ($terms as $term) {
						$id = $taxonomy . '-' . $term->term_id;
						echo wp_kses_post("<li id='" . esc_attr($id) . "'><label class='selectit'>");
						echo "<input type='radio' id='" . esc_attr("in-$id") . "' name='" . esc_attr($name) . "'" . checked($current, $term->term_id, false) . " value='" . esc_attr($term->term_id) . "' />" . esc_html($term->name) . "<br />";
						echo wp_kses_post('</label></li>');
						echo wp_kses_post('</label></li>');
					}
					?>
				</ul>
			</div>
			<!-- Display popular taxonomy terms -->
			<div id="<?php echo esc_attr($taxonomy); ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo esc_attr($taxonomy); ?>checklist-pop" class="categorychecklist form-no-clear">
					<?php
					foreach ($popular as $term) {
						$id = wp_kses_post('popular-' . esc_attr($taxonomy) . '-' . esc_attr($term->term_id));
						echo "<li id='" . esc_attr($id) . "'><label class='selectit'>";
						echo "<input type='radio' name='" . esc_attr($name) . "' id='" . esc_attr("in-$id") . "' value='" . esc_attr($term->term_id) . "' />" . esc_html($term->name) . "<br />";
						echo '</label></li>';
						echo wp_kses_post('</label></li>');
					}
					?>
				</ul>
			</div>
		</div>
	<?php
	}

	/**
	 * Manage of upload image.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_file($key, $field)	{
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = get_post_meta($post_id, stripslashes($key), true);
		}
		if(empty($field['placeholder'])) {
			$field['placeholder'] = 'http://';
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<p class="form-field <?php echo esc_attr($key); ?>" data-field-name="<?php echo esc_attr($key); ?>">
		<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
			<?php
			if(!empty($field['description'])) : ?>
			<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">
				<span class="file_url">
					<?php foreach ((array) $field['value'] as $value) { ?>
						<span class="event-manager-uploaded-file event-manager-uploaded-files multiple-file">
							<input type="hidden" name="<?php echo esc_attr($name); ?>[]" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($value); ?>" />
							<span class="event-manager-uploaded-file-preview">
								<?php if (in_array(pathinfo($value, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'])) : ?>
									<img src="<?php echo esc_attr($value); ?>">
									<a class="event-manager-remove-uploaded-file" href="javascript:void(0);">[remove]</a>
									<?php else :
									if (!wpem_begnWith($value, "http")) {
										$value	= '';
									}
									if (!empty($value)) { ?>
										<span class="wpfm-icon">
											<strong style="display: block; padding-top: 5px;"><?php echo esc_attr(wp_basename($value)); ?></strong>
										</span>
										<a class="event-manager-remove-uploaded-file" href="javascript:void(0);">[remove]</a>
								<?php }
								endif; ?>
							</span>
						</span>
					<?php } ?>
			</span> 
			<?php
			if (!empty($field['multiple'])) { ?>
				<button class="button button-small wp_event_manager_upload_file_button_multiple" style="display: block;" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button>
			<?php } else { ?>
				<span class="event-manager-uploaded-file2">
					<button class="button button-small wp_event_manager_upload_file_button" style="display: block;" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button>
				</span>
				<?php if (!empty($field['description'])) : ?><small class="description"><?php echo esc_html(trim($field['description'])); ?></small><?php endif; ?>
			<?php }
	}

	/**
	 * Manage of upload image.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_media_library_image($key, $field)	{
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = get_post_meta($post_id, stripslashes($key), true);
		}
		if(empty($field['placeholder'])) {
			$field['placeholder'] = 'http://';
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<p class="form-field <?php echo esc_attr($key); ?>" data-field-name="<?php echo esc_attr($key); ?>">
		<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
			<?php
			if(!empty($field['description'])) : ?>
			<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">
				<span class="file_url">
					<?php foreach ((array) $field['value'] as $value) { ?>
						<span class="event-manager-uploaded-file event-manager-uploaded-files multiple-file">
							<input type="hidden" name="<?php echo esc_attr($name); ?>[]" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($value); ?>" />
							<span class="event-manager-uploaded-file-preview">
								<?php if (in_array(pathinfo($value, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'])) : ?>
									<img src="<?php echo esc_attr($value); ?>">
									<a class="event-manager-remove-uploaded-file" href="javascript:void(0);">[remove]</a>
									<?php else :
									if (!wpem_begnWith($value, "http")) {
										$value	= '';
									}
									if (!empty($value)) { ?>
										<span class="wpfm-icon">
											<strong style="display: block; padding-top: 5px;"><?php echo esc_attr(wp_basename($value)); ?></strong>
										</span>
										<a class="event-manager-remove-uploaded-file" href="javascript:void(0);">[remove]</a>
								<?php }
								endif; ?>
							</span>
						</span>
					<?php } ?>
			</span> 
			<?php
			if (!empty($field['multiple'])) { ?>
				<button class="button button-small wp_event_manager_upload_file_button_multiple" style="display: block;" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button>
			<?php } else { ?>
				<span class="event-manager-uploaded-file2">
					<button class="button button-small wp_event_manager_upload_file_button" style="display: block;" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button>
				</span>
				<?php if (!empty($field['description'])) : ?><small class="description"><?php echo esc_html(trim($field['description'])); ?></small><?php endif; ?>
			<?php }
	}

	/**
	 * Manage switch input with multiple options.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_switch($key, $field) {
		global $post_id;
		
		$saved_values = get_post_meta($post_id, stripslashes($key), true);
		$saved_values = !empty($saved_values) ? (array) $saved_values : [];

		$options = isset($field['options']) ? $field['options'] : [];

		?>
		<div class="health-guidelines-switches">
			<?php foreach ($options as $option_key => $option_label) : ?>
				<p class="form-field">
					<label for="<?php echo esc_attr($key . '_' . $option_key); ?>">
						<?php echo esc_html($option_label); ?>
					</label>
					<span class="wpem-input-field">
						<label class="switch">
							<input type="checkbox" 
								name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($option_key); ?>]" 
								id="<?php echo esc_attr($key . '_' . $option_key); ?>" class="<?php echo esc_attr($key); ?>"
								value="1"
								<?php echo isset($saved_values[$option_key]) ? 'checked' : ''; ?>>
							<span class="slider round"></span>
						</label>
					</span>
				</p>
				
				<?php if ($option_key === 'custom_guidelines') : ?>
					<input type="text" 
						name="<?php echo esc_attr($key); ?>[custom_text]" 
						id="<?php echo esc_attr($key . '_custom_text'); ?>"
						placeholder="<?php esc_attr_e('Enter custom health guideline', 'wp-event-manager'); ?>"
						value="<?php echo isset($saved_values['custom_text']) ? esc_attr($saved_values['custom_text']) : ''; ?>">
				<?php endif; ?>

			<?php endforeach; ?>
		</div>
		<?php
	}


	/**
	 * Manage of text input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_text($key, $field) {
		global $post_id;
	
		// Get the default address from options
		$default_address = get_option('default_address');
	
		// Set the field value: prioritize post meta value, then default address, then empty
		if (!isset($field['value']) || empty($field['value'])) {
			//$field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true)) ?: esc_attr($default_address);
			if ($key === '_event_location') {
				$field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true)) ?: '';
				if (empty($field['value']) && !empty($default_address)) {
					$field['value'] = $default_address;
				}
			} else {
				$field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true)) ?: '';
			}
		}
	
		// Determine the name for the input field
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
		?>
	
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>">
				<?php echo esc_html($field['label']); ?>:
				<?php if (!empty($field['description'])) : ?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span>
				<?php endif; ?>
			</label>
			<span class="wpem-input-field">
				<input type="text" 
				   name="<?php echo esc_attr($name); ?>" 
				   id="<?php echo esc_attr($key); ?>" 
				   placeholder="<?php echo esc_attr($field['placeholder']); ?>" 
				   value="<?php echo esc_attr($field['value']); ?>" />
			</span>
		</p>
		<?php
	}
	

	/**
	 * Manage of wp_editor.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 * @since 2.8
	 */
	public static function input_wp_editor($key, $field) {
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = wp_kses_post(get_post_meta($post_id, stripslashes($key), true));
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<div class="wpem_editor">
			<p class="form-field">
				<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
					<?php if(!empty($field['description'])) : ?>
						<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			</p>
			<span class="wpem-input-field"><?php wp_editor($field['value'], $name, array('media_buttons' => false)); ?></span>
		</div>
	<?php
	}

	/**
	 * input_date function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_date($key, $field)	{
		global $post_id;
		$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
		$php_date_format        = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
		if(!isset($field['value']) || empty($field['value'])) {
			$date = esc_attr(get_post_meta($post_id, stripslashes($key), true));
			if(!empty($date)) {
				$date = gmdate($php_date_format, strtotime($date));
				$field['value']         = $date;
			}
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">		
				<input type="hidden" name="date_format" id="date_format" value="<?php echo esc_attr($php_date_format)   ?>" />
				<input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo (isset($field['value']) ?  esc_attr($field['value']) : '') ?>" data-picker="datepicker" />
			</span>
		</p>
	<?php
	}

	/**
	 * Manage of textarea input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_textarea($key, $field) {
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = esc_html(get_post_meta($post_id, stripslashes($key), true));
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span>
				<?php endif; ?>
			</label>
			<span class="wpem-input-field"><textarea name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>"><?php echo esc_html($field['value']); ?></textarea></span>
		</p>
	<?php
	}

	/**
	 * Manage of select input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_select($key, $field) {
		global $post_id;
		$default_venue = get_option( 'default_venue' );
		
		$field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true));
		// If the meta value is still empty, use the default venue
		if (empty($field['value']) && !empty($default_venue)) {
			$field['value'] = $default_venue;
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>

		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">
				<input name="<?php echo esc_attr($name); ?>_hidden" type="hidden" value="<?php echo (isset($field['value']) ?  esc_attr($field['value']) : '') ?>" />
				<select name=" <?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" class="input-select <?php echo esc_attr(isset($field['class']) ? $field['class'] : $key); ?>">
					<?php foreach ($field['options'] as $key => $value) : ?>
						<option value="<?php echo esc_attr($key); ?>" <?php
							if(isset($field['value'])) {
								selected($field['value'], $key);
							}?>>
						<?php echo esc_html($value); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</span>
		</p>
	<?php
	}

	/**
	 * Manage of select input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_multiselect($key, $field)	{
		global $post_id;
		$default_organizer = get_option('default_organizer');
		$field['value'] = get_post_meta($post_id, stripslashes($key), true);
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?>
			</label>
			<span class="wpem-input-field">
				<select multiple="multiple" name="<?php echo esc_attr($name); ?>[]" id="<?php echo esc_attr($key); ?>" class="input-select event-manager-select-chosen <?php echo esc_attr(isset($field['class']) ? $field['class'] : $key); ?>">
					<?php foreach ($field['options'] as $key => $value) : ?>
						<option value="<?php echo esc_attr($key); ?>" <?php
												if(!empty($field['value']) && is_array($field['value'])) {
													selected(in_array($key, $field['value']), true);
												}elseif ($key == $default_organizer) {
													echo 'selected="selected"';
												}
												?>><?php echo esc_html($value); ?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</p>
	<?php
	}

	/**
	 * Manage of checkbox input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_checkbox($key, $field)	{
		global $post_id;
		if(empty($field['value'])) {
			$field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true));
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<p class="form-field form-field-checkbox">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?></label>
			<span class="wpem-input-field">
				<input type="checkbox" class="checkbox" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" value="1" <?php checked($field['value'], 1); ?> />
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="description"><?php echo esc_html($field['description']); ?></span><?php endif; ?>
			</span>
		</p>
	<?php
	}

	/**
	 * Manage time of event.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_time($key, $field)	{
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true));
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">
				<input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" data-picker="timepicker" />
			</span>
		</p>
	<?php
	}

	/**
	 * Manage timezone of event.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_timezone($key, $field)	{
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = esc_html(get_post_meta($post_id, stripslashes($key), true));
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		} ?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">
				<select name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" id="<?php echo isset($field['id']) ? esc_attr($field['id']) : esc_attr($key); ?>" class="input-select <?php echo esc_attr(isset($field['class']) ? $field['class'] : $key); ?>">
					<?php
					$value = isset($field['value']) ? $field['value'] : $field['default'];
					echo wp_kses(
						WP_Event_Manager_Date_Time::wpem_timezone_choice( esc_attr( $value ) ),
						array(
							'select' => array(
								'name'     => true,
								'id'       => true,
								'class'    => true,
								'required' => true,
							),
							'option' => array(
								'value'    => true,
								'selected' => true,
							),
							'optgroup' => array(
								'label' => true,
							),
						)
					); ?>
				</select>
			</span>
		</p>
	<?php
	}

	/**
	 * Manage of number input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_number($key, $field) {
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true));
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">
				<input type="number" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" min="<?php echo isset($field['min']) ? esc_attr($field['min']) : esc_attr('0'); ?>" max="<?php echo isset($field['max']) ? esc_attr($field['max']) : ''; ?>" />
			</span>
		</p>
	<?php
	}

	/**
	 * Manage of number input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_url($key, $field) {
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = esc_url(get_post_meta($post_id, stripslashes($key), true));
		}
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">
				<input type="url" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" />
			</span>
		</p>
	<?php
	}

	/**
	 * Manage of button input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_button($key, $field) {
		global $post_id;
		if(!isset($field['value']) || empty($field['value'])) {
			$field['value'] = $field['placeholder'];
		}

		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if(!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_html($field['description']); ?>">[?]</span><?php endif; ?></label>
			<span class="wpem-input-field">
				<input type="button" class="button button-small" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" />
			</span>
		</p>
	<?php
	}

	/**
	 * Box to choose who posted the event.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_author($key, $field) {
		global $post_id, $post;
		if(!$post || $post_id !== $post->ID) {
			$the_post  = get_post($post_id);
			$author_id = $the_post->post_author;
		} else {
			$author_id = $post->post_author;
		}
		$posted_by      = get_user_by('id', $author_id);
		$field['value'] = !isset($field['value']) ? esc_attr(get_post_meta($post_id, stripslashes($key), true)) : $field['value'];
		$name           = !empty($field['name']) ? $field['name'] : $key; ?>
		<p class="form-field form-field-author">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
			</label>
			<span class="wpem-input-field">
				<span class="current-author">
					<?php
					if($posted_by) {
						echo wp_kses_post('<a href="' . esc_url(admin_url('user-edit.php?user_id=' . absint($author_id))) . '">#' . absint($author_id) . ' &ndash; ' . esc_attr($posted_by->user_login) . '</a>');
					} else {
						esc_attr_e('Guest User', 'wp-event-manager');
					}
					?>
					<a href="#" class="change-author button button-small"><?php esc_attr_e('Change', 'wp-event-manager'); ?></a>
				</span>
				<span class="hidden change-author">
					<input type="number" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" step="1" value="<?php echo esc_attr($author_id); ?>" style="width: 4em;" />
					<span class="description"><?php echo esc_attr_e('Enter the ID of the user, or leave blank if submitted by a guest.', 'wp-event-manager'); ?></span>
				</span>
			</span>
		</p>
	<?php
	}

	/**
	 * Manage of radio input.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_radio($key, $field) {
		global $post_id;
		$field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true));
		if(!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}?>
		<p class="form-field form-field-checkbox">
			<label><?php echo esc_html($field['label']); ?></label>
			<span class="wpem-input-field">
				<span class="wpem-admin-radio-inline">
					<?php foreach ($field['options'] as $option_key => $value) : ?>
						<label>
							<input type="radio" class="radio" name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" value="<?php echo esc_attr($option_key); ?>" <?php checked($field['value'], $option_key); ?> /> 
							<?php echo esc_html($value); ?>
						</label>
					<?php endforeach; ?>
				</span>
				<?php if(!empty($field['description'])) : ?>
					<span class="description"><?php echo esc_html($field['description']); ?></span>
				<?php endif; ?>
			</span>
		</p>
	<?php
	}
	
	/**
	 * Save post.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_post($post_id, $post){
		if(empty($post_id) || empty($post) || empty($_POST)) {
			return;
		}
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if(is_int(wp_is_post_revision($post))) {
			return;
		}
		if(is_int(wp_is_post_autosave($post))) {
			return;
		}
		if(empty($_POST['event_manager_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['event_manager_nonce'])), 'save_meta_data')) {
			return;
		}
		if(!current_user_can('edit_post', $post_id)) {
			return;
		}
		if($post->post_type == 'event_listing') {
			do_action('event_manager_save_event_listing', $post_id, $post);
		}
		if($post->post_type == 'event_organizer') {
			do_action('event_manager_save_organizer_listing', $post_id, $post);
		}
		if($post->post_type == 'event_venue') {
			do_action('event_manager_save_venue_listing', $post_id, $post);
		}
	}

	/**
	 * Save event listing data.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_event_listing_data($post_id, $post) {
		global $wpdb;

		// Security: verify nonce and user capability before processing form data.
		if ( empty($_POST) ) return;

		if ( empty($_POST['event_manager_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['event_manager_nonce'])), 'save_meta_data') ) return;

		if ( ! current_user_can('edit_post', $post_id) ) return;

		// Ensure default meta exists
		add_post_meta($post_id, '_cancelled', 0, true);
		add_post_meta($post_id, '_featured', 0, true);
		update_post_meta($post_id, '_event_title', get_the_title($post_id));

		// Get date format from admin settings
		$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
		$php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

		$ticket_type = '';
		$event_online = '';

		foreach ($this->event_listing_fields() as $key => $field) {

			$raw_value = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : null;

			// Event Expiry Date
			if ($key === '_event_expiry_date') {
				if (!empty($raw_value)) {
					$date_format = isset($_POST['date_format']) ? sanitize_text_field(wp_unslash($_POST['date_format'])) : '';
					$date_value = sanitize_text_field($raw_value);
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($date_format, $date_value);
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date_value;
					update_post_meta($post_id, sanitize_key($key), trim($date_dbformatted));
				} else {
					update_post_meta($post_id, sanitize_key($key), '');
				}
			}
			// Event Location
			elseif ($key === '_event_location') {
				$location_value = sanitize_text_field($raw_value);
				if (update_post_meta($post_id, $key, $location_value)) {
					// location updated by hooked methods
				} elseif (apply_filters('event_manager_geolocation_enabled', true) && !WP_Event_Manager_Geocode::has_location_data($post_id)) {
					WP_Event_Manager_Geocode::generate_location_data($post_id, $location_value);
				}
			}
			// Event Author
			elseif ($key === '_event_author') {
				$author_id = $raw_value > 0 ? absint($raw_value) : 0;
				$wpdb->update($wpdb->posts, ['post_author' => $author_id], ['ID' => $post_id]);
			}
			// Event Banner
			elseif ($key === '_event_banner') {
				if (!empty($raw_value)) {
					$meta_key = sanitize_key($key);
					if (is_array($raw_value)) {
						$sanitized_values = array_filter(array_map('sanitize_text_field', $raw_value));
						$thumbnail_image = $sanitized_values[0] ?? '';
						update_post_meta($post_id, $meta_key, $sanitized_values);
					} else {
						$thumbnail_image = sanitize_text_field($raw_value);
						update_post_meta($post_id, $meta_key, $thumbnail_image);
					}

					$image = get_the_post_thumbnail_url($post_id);
					if (empty($image) && !empty($thumbnail_image)) {
						$wp_upload_dir = wp_get_upload_dir();
						$baseurl = trailingslashit($wp_upload_dir['baseurl']);
						$wp_attached_file = str_replace($baseurl, '', $thumbnail_image);
						$attachments = get_posts([
							'post_type' => 'attachment',
							'posts_per_page' => 1,
							// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Required for attachment lookup
							'meta_key' => '_wp_attached_file',
							// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Required for attachment lookup
							'meta_value' => $wp_attached_file
						]);
						if (!empty($attachments)) {
							set_post_thumbnail($post_id, $attachments[0]->ID);
						}
					}
				}
			}
			// Event Start Date
			elseif ($key === '_event_start_date') {
				if (!empty($raw_value)) {
					$start_time = !empty($_POST['_event_start_time']) ? WP_Event_Manager_Date_Time::get_db_formatted_time(sanitize_text_field(wp_unslash($_POST['_event_start_time']))) : gmdate('H:i:s');
					$date_input = sanitize_text_field(explode(' ', $raw_value)[0] . ' ' . $start_time);
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format(wp_kses_post(sanitize_text_field(wp_unslash($_POST['date_format'])) . ' H:i:s'), $date_input);
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date_input;
					update_post_meta($post_id, sanitize_key($key), $date_dbformatted);
				} else {
					update_post_meta($post_id, sanitize_key($key), '');
				}
			}
			// Event End Date
			elseif ($key === '_event_end_date') {
				if (!empty($raw_value)) {
					$end_time = !empty($_POST['_event_end_time']) ? WP_Event_Manager_Date_Time::get_db_formatted_time(sanitize_text_field(wp_unslash($_POST['_event_end_time']))) : gmdate('H:i:s');
					$date_input = sanitize_text_field(explode(' ', $raw_value)[0] . ' ' . $end_time);
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format(wp_kses_post(sanitize_text_field(wp_unslash($_POST['date_format'])) . ' H:i:s'), $date_input);
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date_input;
					update_post_meta($post_id, sanitize_key($key), $date_dbformatted);
				} else {
					update_post_meta($post_id, sanitize_key($key), '');
				}
			}
			// Event Registration Deadline
			elseif ($key === '_event_registration_deadline') {
				if (!empty($raw_value)) {
					$post_value = sanitize_text_field($raw_value);
					$date_input = sanitize_text_field(explode(' ', $post_value)[0]);
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format(wp_kses_post(sanitize_text_field(wp_unslash($_POST['date_format']))), $date_input);
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date_input;
					update_post_meta($post_id, sanitize_key($key), $date_dbformatted);
				} else {
					update_post_meta($post_id, sanitize_key($key), '');
				}
			}
			// Organizer IDs
			elseif ($key === '_event_organizer_ids') {
				if (!empty($raw_value)) {
					$value = is_array($raw_value) ? array_filter(array_map('sanitize_text_field', $raw_value)) : sanitize_text_field($raw_value);
					update_post_meta($post_id, sanitize_key($key), $value);
				} else {
					update_post_meta($post_id, sanitize_key($key), '');
				}
			}
			// Venue IDs
			elseif ($key === '_event_venue_ids') {
				if (!empty($raw_value) && is_array($raw_value)) {
					update_post_meta($post_id, sanitize_key($key), array_filter(array_map('sanitize_text_field', $raw_value)));
				} else {
					update_post_meta($post_id, sanitize_key($key), '');
				}
			}
			// All other fields
			else {
				$type = $field['type'] ?? '';

				switch ($type) {
					case 'textarea':
						update_post_meta($post_id, sanitize_key($key), wp_kses_post($raw_value));
						break;

					case 'multiselect':
						if (!empty($raw_value) && is_array($raw_value)) {
							update_post_meta($post_id, sanitize_key($key), array_filter(array_map('sanitize_text_field', $raw_value)));
						} else {
							update_post_meta($post_id, sanitize_key($key), '');
						}
						break;

					case 'checkbox':
						update_post_meta($post_id, sanitize_key($key), isset($_POST[$key]) ? 1 : 0);
						break;

					case 'date':
						if (!empty($raw_value)) {
							$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, sanitize_text_field($raw_value));
							update_post_meta($post_id, sanitize_key($key), $date_dbformatted ?: $raw_value);
						}
						break;

					case 'time':
						if (!empty($raw_value)) {
							$time_dbformatted = WP_Event_Manager_Date_Time::get_db_formatted_time(sanitize_text_field($raw_value));
							update_post_meta($post_id, sanitize_key($key), $time_dbformatted ?: '');
						}
						break;

					case 'wp-editor':
						update_post_meta($post_id, sanitize_key($key), wp_kses_post($raw_value));
						break;

					default:
						$add_data = apply_filters('wpem_save_event_data', true, $key, $raw_value);
						if ($add_data) {
							if (is_array($raw_value)) {
								update_post_meta($post_id, sanitize_key($key), array_filter(array_map('sanitize_text_field', $raw_value)));
							} elseif (!is_null($raw_value)) {
								$saved_value = sanitize_text_field($raw_value);
								update_post_meta($post_id, sanitize_key($key), $saved_value);
							}
							if ($key == '_event_ticket_options' && $raw_value == 'free') $ticket_type = 'free';
							if ($key == '_event_online') $event_online = sanitize_text_field($raw_value);
						}
						break;
				}
			}
		}

		// Handle online events
		if ($event_online === 'yes') {
			update_post_meta($post_id, '_event_location', '');
			update_post_meta($post_id, '_event_pincode', '');
			update_post_meta($post_id, '_event_country', '');
		}

		// Handle free tickets
		if ($ticket_type === 'free') {
			update_post_meta($post_id, '_event_ticket_price', '');
		}

		// Handle timezone-based timestamps
		$current_timestamp = (WP_Event_Manager_Date_Time::get_event_manager_timezone_setting() === 'each_event') 
			? WP_Event_Manager_Date_Time::current_timestamp_from_event_timezone(esc_html(get_post_meta($post_id, '_event_timezone', true)))
			: current_time('timestamp');

		$expiry_date = apply_filters('wpem_expire_date_time', gmdate('Y-m-d H:i:s', strtotime(esc_html(get_post_meta($post_id, '_event_expiry_date', true)) . ' 23:59:30')), $post);
		$today_date = apply_filters('wpem_get_current_expire_time', gmdate('Y-m-d H:i:s', $current_timestamp));

		$post_status = $expiry_date && strtotime($today_date) > strtotime($expiry_date) ? 'expired' : false;

		if ($post_status) {
			remove_action('event_manager_save_event_listing', [$this, 'save_event_listing_data'], 20, 2);
			wp_update_post(['ID' => $post_id, 'post_status' => $post_status]);
			add_action('event_manager_save_event_listing', [$this, 'save_event_listing_data'], 20, 2);
		}

		// Handle custom author
		if (isset($_POST['_event_author'])) {
			$custom_author = absint(sanitize_text_field(wp_unslash($_POST['_event_author'])));
			if ($custom_author && get_user_by('ID', $custom_author)) {
				remove_action('event_manager_save_event_listing', [$this, 'save_event_listing_data'], 20, 2);
				wp_update_post(['ID' => $post_id, 'post_author' => $custom_author]);
				add_action('event_manager_save_event_listing', [$this, 'save_event_listing_data'], 20, 2);
			}
		}
	}

	/**
	 * Organizer listing fields.
	 *
	 * @access public
	 * @return void
	 */
	public function organizer_listing_fields() {
		global $post;
		$current_user = wp_get_current_user();

		$GLOBALS['event_manager']->forms->get_form('submit-organizer', array());
		$form_submit_organizer_instance = call_user_func(array('WPEM_Event_Manager_Form_Submit_Organizer', 'instance'));
		$fields                         = $form_submit_organizer_instance->wpem_merge_with_custom_fields('backend');

		/** add _ (prefix) for all backend fields.
		 *  Field editor will only return fields without _(prefix).
		 */
		foreach ($fields as $group_key => $group_fields) {
			foreach ($group_fields as $field_key => $field_value) {
				
				if(strpos($field_key, '_') !== 0) {
					$fields['_' . $field_key] = $field_value;
				} else {
					$fields[$field_key] = $field_value;
				}
				if(isset($fields['_' .$field_key]['visibility']) && ($fields['_' .$field_key]['visibility'] == 0 || $fields['_' .$field_key]['visibility'] == false)) :
					unset($fields['_' .$field_key]);
				endif; 
			}
			unset($fields[$group_key]);
		}
		$fields = apply_filters('event_manager_organizer_listing_data_fields', $fields);
		if(isset($fields['_organizer_name'])) {
			unset($fields['_organizer_name']);
		}
		if(isset($fields['_organizer_description'])) {
			unset($fields['_organizer_description']);
		}
		if($current_user->has_cap('edit_others_event_listings')) {
			$fields['_organizer_author'] = array(
				'label'    => __('Posted by', 'wp-event-manager'),
				'type'     => 'author',
				'priority' => 41,
			);
		}
		uasort($fields, array($this, 'sort_by_priority'));
		return $fields;
	}


	/**
	 * Event organizer data.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function event_organizer_data($post)	{
		global $post, $post_id;
		$post_id = $post->ID;
		echo wp_kses_post('<div class="wp_event_manager_meta_data">');
		wp_nonce_field('save_meta_data', 'event_manager_nonce');
		do_action('event_manager_event_organizer_data_start', $post_id);
		foreach ($this->organizer_listing_fields() as $key => $field) {
			$type = !empty($field['type']) ? $field['type'] : 'text';
			if($type == 'wp-editor') {
				$type = 'textarea';
			}
			if(has_action('event_manager_input_' . $type)) {
				do_action('event_manager_input_' . $type, $key, $field);
			} elseif(method_exists($this, 'input_' . $type)) {
				call_user_func(array($this, 'input_' . $type), $key, $field);
			}
		}
		do_action('event_manager_event_organizer_data_end', $post_id);
		echo wp_kses_post('</div>');
	}

	/**
	 * Save organizer listing data.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_organizer_listing_data($post_id, $post) {
		global $wpdb;

		// Security: verify nonce and user capability before processing form data.
		if ( empty( $_POST ) ) {
			return;
		}
		if ( empty( $_POST['event_manager_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['event_manager_nonce'] ) ), 'save_meta_data' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$post_title = isset($_POST['post_title']) ? sanitize_text_field(wp_unslash($_POST['post_title'])) : '';
		$post_content = isset($_POST['content']) ? wp_kses_post(wp_unslash($_POST['content'])) : '';
		// Get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();

		// Covert datepicker format  into php date() function date format
		$php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

		update_post_meta($post_id, '_organizer_name', $post_title);
		update_post_meta($post_id, '_organizer_description', $post_content);

		// Save fields
		foreach ($this->organizer_listing_fields() as $key => $field) {
			$key = isset($key) ? sanitize_text_field(wp_unslash($key)) : '';
			if('_organizer_author' === $key) {
				$wpdb->update($wpdb->posts, array('post_author' => $_POST[$key] > 0 ? absint(sanitize_text_field($_POST[$key])) : 0), array('ID' => $post_id));
			}
			// Everything else
			else {
				$type = isset($field['type']) ? sanitize_text_field(wp_unslash($field['type'])) : '';
				switch ($type) {
					case 'textarea':
						update_post_meta($post_id, $key, wp_kses_post(stripslashes($_POST[$key])));
						break;
					case 'file':
						if (isset($_POST[$key])) {
							$value = '';
							if (!empty($_POST['_thumbnail_id'])) {
								$thumb_id = intval(wp_unslash($_POST['_thumbnail_id']));
								$thumb_url = wp_get_attachment_url($thumb_id);

								if ($thumb_url) {
									$value = esc_url_raw($thumb_url);
								}
							}

							update_post_meta($post_id, $key, $value);
						}
						break;
					case 'checkbox':
						if(isset($_POST[$key])) {
							update_post_meta($post_id, $key, 1);
						} else {
							update_post_meta($post_id, $key, 0);
						}
						break;
					case 'date':
						if(isset($_POST[$key])) {
							$date = wp_kses_post($_POST[$key]);

							// Convert date and time value into DB formatted format and save eg. 1970-01-01
							$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $date);
							$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;
							update_post_meta($post_id, $key, sanitize_text_field(trim($date_dbformatted)));
						}
						break;
					case 'wp-editor':
                        if(!empty($_POST[$key])) {
                            $v_text = wp_kses_post(stripslashes($_POST[$key]));
                            update_post_meta($post_id, $key, $v_text);
                        }
                        break;
					default:
						if(!isset($_POST[$key])) {
							continue 2;
						} elseif(is_array($_POST[$key])) {
							update_post_meta($post_id, $key, array_filter(array_map('sanitize_text_field', $_POST[$key])));
						} else {
							update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
						}
						break;
				}
			}
		}
	}

	/**
	 * Venue listing fields.
	 *
	 * @access public
	 * @return void
	 */
	public function venue_listing_fields() {
		global $post;
		$current_user = wp_get_current_user();

		$GLOBALS['event_manager']->forms->get_form('submit-venue', array());
		$form_submit_venue_instance = call_user_func(array('WPEM_Event_Manager_Form_Submit_Venue', 'instance'));
		$fields                     = $form_submit_venue_instance->wpem_merge_with_custom_fields('backend');

		/** add _ (prefix) for all backend fields.
		 *  Field editor will only return fields without _(prefix).
		 */
		foreach ($fields as $group_key => $group_fields) {
			foreach ($group_fields as $field_key => $field_value) {

				if(strpos($field_key, '_') !== 0) {
					$fields['_' . $field_key] = $field_value;
				} else {
					$fields[$field_key] = $field_value;
				}
				if(isset($fields['_' .$field_key]['visibility']) && ($fields['_' .$field_key]['visibility'] == 0 || $fields['_' .$field_key]['visibility'] == false)) :
					unset($fields['_' .$field_key]);
				endif; 
			}
			unset($fields[$group_key]);
		}
		$fields = apply_filters('event_manager_venue_listing_data_fields', $fields);
		if(isset($fields['_venue_name'])) {
			unset($fields['_venue_name']);
		}
		if(isset($fields['_venue_description'])) {
			unset($fields['_venue_description']);
		}
		if($current_user->has_cap('edit_others_event_listings')) {
			$fields['_venue_author'] = array(
				'label'    => __('Posted by', 'wp-event-manager'),
				'type'     => 'author',
				'priority' => 41,
			);
		}
		uasort($fields, array($this, 'sort_by_priority'));
		return $fields;
	}

	/**
	 * Event venue data.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function event_venue_data($post)	{
		global $post, $post_id;
		$post_id = $post->ID;
		echo wp_kses_post('<div class="wp_event_manager_meta_data">');
		wp_nonce_field('save_meta_data', 'event_manager_nonce');
		do_action('event_manager_event_venue_data_start', $post_id);
		foreach ($this->venue_listing_fields() as $key => $field) {
			$type = !empty($field['type']) ? $field['type'] : 'text';
			if($type == 'wp-editor') {
				$type = 'textarea';
			}
			if(has_action('event_manager_input_' . $type)) {
				do_action('event_manager_input_' . $type, $key, $field);
			} elseif(method_exists($this, 'input_' . $type)) {
				call_user_func(array($this, 'input_' . $type), $key, $field);
			}
		}
		do_action('event_manager_event_venue_data_end', $post_id);
		echo wp_kses_post('</div>');
	}

	/**
	 * Save venue listing data.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_venue_listing_data($post_id, $post){
		global $wpdb;

		// Security: verify nonce and user capability before processing form data.
		if ( empty( $_POST ) ) {
			return;
		}
		if ( empty( $_POST['event_manager_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['event_manager_nonce'] ) ), 'save_meta_data' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();

		// Covert datepicker format  into php date() function date format
		$php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

		update_post_meta($post_id, '_venue_name', sanitize_text_field($_POST['post_title']));
		update_post_meta($post_id, '_venue_description', wp_kses_post($_POST['content']));

		// Save fields
		foreach ($this->venue_listing_fields() as $key => $field) {
			$key = sanitize_text_field($key);
			if('_venue_author' === $key) {
				$wpdb->update($wpdb->posts, array('post_author' => $_POST[$key] > 0 ? absint($_POST[$key]) : 0), array('ID' => $post_id));
			} else {
				$type = !empty($field['type']) ? $field['type'] : '';
				switch ($type) {
					case 'textarea':
						update_post_meta($post_id, $key, wp_kses_post(wp_unslash($_POST[$key])));
						break;
					case 'file':
						if (isset($_POST[$key])) {
							$value = '';
							if (!empty($_POST['_thumbnail_id'])) {
								$thumb_id = sanitize_text_field(wp_unslash($_POST['_thumbnail_id']));
								$thumb_url = wp_get_attachment_url($thumb_id);

								if ($thumb_url) {
									$value = esc_url_raw($thumb_url);
								}
							}

							update_post_meta($post_id, $key, $value);
						}
						break;
					case 'checkbox':
						if(isset($_POST[$key])) {
							update_post_meta($post_id, $key, 1);
						} else {
							update_post_meta($post_id, $key, 0);
						}
						break;
					case 'date':
						if(isset($_POST[$key])) {
							$date = wp_kses_post(wp_unslash($_POST[$key]));

							// Convert date and time value into DB formatted format and save eg. 1970-01-01
							$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $date);
							$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;
							update_post_meta($post_id, $key, sanitize_text_field(trim($date_dbformatted)));
						}
						break;
						case 'wp-editor':
							if(!empty($_POST[$key])) {
								$v_text = wp_kses_post(wp_unslash($_POST[$key]));
								update_post_meta($post_id, $key, $v_text);
							}
							break;
					default:
						if(!isset($_POST[$key])) {
							continue 2;
						} elseif(is_array($_POST[$key])) {
							update_post_meta($post_id, $key, array_filter(array_map('sanitize_text_field', $_POST[$key])));
						} else {
							update_post_meta($post_id, $key, sanitize_text_field(wp_unslash($_POST[$key])));
						}
						break;
				}
			}
		}
	}

	/**
	 * Delete attachment  function.
	 *
	 * @param $post_id
	 * @access public
	 * @return void
	 */
	public function delete_event_with_attachment($post_id) {
		if(!in_array(get_post_type($post_id), array('event_listing', 'event_organizer'))) {
			return;
		}

		$args = array(
			'posts_per_page' => -1,
			'post_parent'    => $post_id,
			'post_type' => 'event_listing'
		);
		$children = get_children($args, ARRAY_A);
		$retain_attachment = get_option( 'wpem_retain_attachment' );
		if(sizeof($children) == 0) {
			$event_banner = wpem_get_event_banner($post_id);
			if(!empty($event_banner)) {
				$wp_upload_dir = wp_get_upload_dir();
				$baseurl = $wp_upload_dir['baseurl'] . '/';
				if(is_array($event_banner)) {
					foreach ($event_banner as $banner) {
						$wp_attached_file = str_replace($baseurl, '', $banner);
						$args = array(
							'meta_key'       => '_wp_attached_file',
							'meta_value'     => $wp_attached_file,
							'post_type'      => 'attachment',
							'posts_per_page' => 1,
						);
						$attachments = get_posts($args);
						if(!empty($attachments)) {
							if( !$retain_attachment ){
								foreach ($attachments as $attachment) {
									wp_delete_attachment($attachment->ID, true);
								}
							}
						}
					}
				} else {
					$wp_attached_file = str_replace($baseurl, '', $event_banner);
					$args = array(
						'meta_key'       => '_wp_attached_file',
						'meta_value'     => $wp_attached_file,
						'post_type'      => 'attachment',
						'posts_per_page' => 1,
					);
					$attachments = get_posts($args);
					if(!empty($attachments)) {
						if( !$retain_attachment ){
							foreach ($attachments as $attachment) {
								wp_delete_attachment($attachment->ID, true);
							}
						}
					}
				}
			}
			$thumbnail_id = get_post_thumbnail_id($post_id);
			if(!empty($thumbnail_id)) {
				if( !$retain_attachment ){
					wp_delete_attachment($thumbnail_id, true);
				}
			}
		}
	}
}
WP_Event_Manager_Writepanels::instance();