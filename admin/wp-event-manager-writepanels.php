<?php
/*
* This file use to create fields of wp event manager at admin side.
*/
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
class WP_Event_Manager_Writepanels
{

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
	public static function instance()
	{
		if (is_null(self::$_instance)) {
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
	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post', array($this, 'save_post'), 1, 2);
		add_action('event_manager_save_event_listing', array($this, 'save_event_listing_data'), 20, 2);

		add_action('event_manager_save_organizer_listing', array($this, 'save_organizer_listing_data'), 20, 2);
		add_action('event_manager_save_venue_listing', array($this, 'save_venue_listing_data'), 20, 2);

		add_action('before_delete_post', array($this, 'delete_event_with_attachment'), 10);

		add_filter('rest_prepare_taxonomy', array($this, 'wpem_hide_taxonomy_metabox'), 10, 2);
	}

	/**
	 * event_listing_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function event_listing_fields()
	{
		global $post;
		$current_user = wp_get_current_user();
		if (isset($post->ID)) {
			$registration = metadata_exists('post', $post->ID, '_registration') ? get_post_meta($post->ID, '_registration', true) : $current_user->user_email;
			$expiry_date  = get_post_meta($post->ID, '_event_expiry_date', true);
			if ($expiry_date) {
				$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
				$php_date_format        = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
				$expiry_date            = date($php_date_format, strtotime($expiry_date));
			}
		} else {
			$registration = $current_user->user_email;
			$expiry_date  = '';
		}

		$GLOBALS['event_manager']->forms->get_form('submit-event', array());
		$form_submit_event_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Event', 'instance'));
		$fields                     = $form_submit_event_instance->merge_with_custom_fields('backend');

		/** add _ (prefix) for all backend fields.
		 *   Field editor will only return fields without _(prefix).
		 */
		foreach ($fields as $group_key => $group_fields) {
			foreach ($group_fields as $field_key => $field_value) {

				if ($field_key === 'registration') {
					$field_value['value'] = $registration;
				}

				if (strpos($field_key, '_') !== 0) {
					$fields['_' . $field_key] = $field_value;
				} else {
					$fields[$field_key] = $field_value;
				}
			}
			unset($fields[$group_key]);
		}

		$fields = apply_filters('event_manager_event_listing_data_fields', $fields);

		if (isset($fields['_event_title'])) {
			unset($fields['_event_title']);
		}

		if (isset($fields['_event_description'])) {
			unset($fields['_event_description']);
		}
		if (isset($fields['_organizer_logo'])) {
			unset($fields['_organizer_logo']);
		}

		if ($current_user->has_cap('manage_event_listings')) {
			$fields['_featured'] = array(
				'label'       => __('Featured Listing', 'wp-event-manager'),
				'type'        => 'checkbox',
				'description' => __('Featured listings will be sticky during searches, and can be styled differently.', 'wp-event-manager'),
				'priority'    => 39,
			);

			$fields['_cancelled'] = array(
				'label'       => __('Cancelled Listing', 'wp-event-manager'),
				'type'        => 'checkbox',
				'description' => __('Cancelled listings will be sticky during searches, and can be styled differently.', 'wp-event-manager'),
				'priority'    => 39,
			);

			$fields['_event_expiry_date'] = array(
				'label'       => __('Listing Expiry Date', 'wp-event-manager'),
				'type'        => 'date',
				'placeholder' => __('Please enter event expiry date', 'wp-event-manager'),
				'priority'    => 40,
				'value'       => $expiry_date,
			);
		}

		if ($current_user->has_cap('edit_others_event_listings')) {
			$fields['_event_author'] = array(
				'label'    => __('Posted by', 'wp-event-manager'),
				'type'     => 'author',
				'priority' => 41,
			);
		}

		uasort($fields, array($this, 'sort_by_priority'));
		return $fields;
	}

	/**
	 * Sort array by priority value
	 */
	protected function sort_by_priority($a, $b)
	{
		if (!isset($a['priority']) || !isset($b['priority']) || $a['priority'] === $b['priority']) {
			return 0;
		}
		return ($a['priority'] < $b['priority']) ? -1 : 1;
	}

	/**
	 * add_meta_boxes function.
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes()
	{
		global $wp_post_types;

		add_meta_box('event_listing_data', sprintf(wp_kses('%s Data', 'wp-event-manager'), $wp_post_types['event_listing']->labels->singular_name), array($this, 'event_listing_data'), 'event_listing', 'normal', 'high');

		if (!get_option('event_manager_enable_event_types')) {
			remove_meta_box('event_listing_typediv', 'event_listing', 'side');
		} elseif (false == event_manager_multiselect_event_type()) {
			remove_meta_box('event_listing_typediv', 'event_listing', 'side');
			$event_listing_type = get_taxonomy('event_listing_type');
			add_meta_box('event_listing_type', $event_listing_type->labels->menu_name, array($this, 'event_listing_type_metabox'), 'event_listing', 'side', 'core');
		}

		if (!get_option('event_manager_enable_categories')) {
			remove_meta_box('event_listing_categorydiv', 'event_listing', 'side');
		} elseif (false == event_manager_multiselect_event_category()) {
			remove_meta_box('event_listing_categorydiv', 'event_listing', 'side');
			$event_listing_category = get_taxonomy('event_listing_category');
			add_meta_box('event_listing_category', $event_listing_category->labels->menu_name, array($this, 'event_listing_category_metabox'), 'event_listing', 'side', 'core');
		}

		if (isset($wp_post_types['event_organizer'])) {
			add_meta_box('event_organizer_data', sprintf(wp_kses('%s Data', 'wp-event-manager'), $wp_post_types['event_organizer']->labels->singular_name), array($this, 'event_organizer_data'), 'event_organizer', 'normal', 'high');
		}

		if (isset($wp_post_types['event_venue'])) {
			add_meta_box('event_venue_data', sprintf(wp_kses('%s Data', 'wp-event-manager'), $wp_post_types['event_venue']->labels->singular_name), array($this, 'event_venue_data'), 'event_venue', 'normal', 'high');
		}
	}

	public function wpem_hide_taxonomy_metabox($response, $taxonomy)
	{
		if (false == event_manager_multiselect_event_type()) {
			if ('event_listing_type' === $taxonomy->name) {
				$response->data['visibility']['show_ui'] = false;
			}
		}

		if (false == event_manager_multiselect_event_category()) {
			if ('event_listing_category' === $taxonomy->name) {
				$response->data['visibility']['show_ui'] = false;
			}
		}

		return $response;
	}

	/**
	 * event_listing_type_metabox function.
	 *
	 * @param mixed $post
	 * @param
	 */
	function event_listing_type_metabox($post)
	{
		// Set up the taxonomy object and get terms
		$taxonomy = 'event_listing_type';
		$tax      = get_taxonomy($taxonomy); // This is the taxonomy object event

		// The name of the form
		$name = 'tax_input[' . $taxonomy . '][]';

		// Get all the terms for this taxonomy
		$terms     = get_terms($taxonomy, array('hide_empty' => 0));
		$postterms = get_the_terms($post->ID, $taxonomy);
		$current   = ($postterms ? array_pop($postterms) : false);
		$current   = ($current ? $current->term_id : 0);
		// Get current and popular terms
		$popular   = get_terms(
			$taxonomy,
			array(
				'orderby'      => 'count',
				'order'        => 'DESC',
				'number'       => 10,
				'hierarchical' => false,
			)
		);
		$postterms = get_the_terms($post->ID, $taxonomy);
		$current   = ($postterms ? array_pop($postterms) : false);
		$current   = ($current ? $current->term_id : 0);
?>

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
					}
					?>
				</ul>
			</div>

		</div>
	<?php
	}

	/**
	 * event_listing_category_metabox function.
	 *
	 * @param mixed $post
	 * @param
	 */
	function event_listing_category_metabox($post)
	{
		// Set up the taxonomy object and get terms
		$taxonomy = 'event_listing_category';
		$tax      = get_taxonomy($taxonomy); // This is the taxonomy object event

		// The name of the form
		$name = 'tax_input[' . $taxonomy . '][]';

		// Get all the terms for this taxonomy
		$terms     = get_terms($taxonomy, array('hide_empty' => 0));
		$postterms = get_the_terms($post->ID, $taxonomy);
		$current   = ($postterms ? array_pop($postterms) : false);
		$current   = ($current ? $current->term_id : 0);
		// Get current and popular terms
		$popular   = get_terms(
			$taxonomy,
			array(
				'orderby'      => 'count',
				'order'        => 'DESC',
				'number'       => 10,
				'hierarchical' => false,
			)
		);
		$postterms = get_the_terms($post->ID, $taxonomy);
		$current   = ($postterms ? array_pop($postterms) : false);
		$current   = ($current ? $current->term_id : 0);
	?>

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
						echo wp_kses_post("<li id='$id'><label class='selectit'>");
						echo "<input type='radio' id='in-$id' name='{$name}'" . checked($current, $term->term_id, false) . "value='$term->term_id' />$term->name<br />";
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
						echo "<li id='$id'><label class='selectit'>";
						echo "<input type='radio'  name='{$name}' id='in-$id'  value='$term->term_id' />" . esc_html($term->name) . '<br />';
						echo wp_kses_post('</label></li>');
					}
					?>
				</ul>
			</div>

		</div>
	<?php
	}

	/**
	 * input_file function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_file($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (empty($field['placeholder'])) {
			$field['placeholder'] = 'http://';
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>

		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<?php
			if (!empty($field['multiple'])) {
				foreach ((array) $field['value'] as $value) {
			?>
					<span class="file_url"><input type="text" name="<?php echo esc_attr($name); ?>[]" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($value); ?>" /><button class="button button-small wp_event_manager_upload_file_button" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button></span>
				<?php
				}
			} else {
				if (isset($field['value']) && is_array($field['value'])) {
					$field['value'] = array_shift($field['value']);
				}
				?>
				<span class="file_url"><input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" /><button class="button button-small wp_event_manager_upload_file_button" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>"><?php esc_attr_e('Upload', 'wp-event-manager'); ?></button></span>
			<?php
			}
			if (!empty($field['multiple'])) {
			?>
				<button class="button button-small wp_event_manager_add_another_file_button" data-field_name="<?php echo esc_attr($key); ?>" data-field_placeholder="<?php echo esc_attr($field['placeholder']); ?>" data-uploader_button_text="<?php esc_attr_e('Use file', 'wp-event-manager'); ?>" data-uploader_button="<?php esc_attr_e('Upload', 'wp-event-manager'); ?>"><?php esc_attr_e('Add file', 'wp-event-manager'); ?></button>
			<?php
			}
			?>
		</p>
	<?php
	}

	/**
	 * input_text function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_text($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>

		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" />
		</p>
	<?php
	}

	/**
	 * input_wp_editor function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 * @since 2.8
	 */
	public static function input_wp_editor($key, $field)
	{
		global $thepostid;
		if (!isset($field['value']) || empty($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<div class="wpem_editor">
			<p class="form-field">
				<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
					<?php
					if (!empty($field['description'])) :
					?>
						<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			</p>


			<?php
			wp_editor($field['value'], $name, array('media_buttons' => false));
			?>
		</div>
	<?php
	}

	/**
	 * input_date function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_date($key, $field)
	{
		global $thepostid;
		$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();
		$php_date_format        = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);
		if (!isset($field['value'])) {
			$date = get_post_meta($thepostid, $key, true);
			if (!empty($date)) {
				$date = date($php_date_format, strtotime($date));
				$field['value']         = $date;
			}
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<input type="hidden" name="date_format" id="date_format" value="<?php echo esc_attr($php_date_format)   ?>" />
			<input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo (isset($field['value']) ?  esc_attr($field['value']) : '') ?>" data-picker="datepicker" />
		</p>
	<?php
	}

	/**
	 * input_text function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_textarea($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<textarea name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>"><?php echo esc_html($field['value']); ?></textarea>
		</p>
	<?php
	}

	/**
	 * input_select function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_select($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>

		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<input name="<?php echo esc_attr($name); ?>_hidden" type="hidden" value="<?php echo (isset($field['value']) ?  esc_attr($field['value']) : '') ?>" />
			<select name=" <?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" class="input-select <?php echo esc_attr(isset($field['class']) ? $field['class'] : $key); ?>">
				<?php foreach ($field['options'] as $key => $value) : ?>
					<option value="<?php echo esc_attr($key); ?>" <?php
																												if (isset($field['value'])) {
																													selected($field['value'], $key);
																												}
																												?>><?php echo esc_html($value); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php
	}

	/**
	 * input_select function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_multiselect($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<select multiple="multiple" name="<?php echo esc_attr($name); ?>[]" id="<?php echo esc_attr($key); ?>" class="input-select event-manager-select-chosen <?php echo esc_attr(isset($field['class']) ? $field['class'] : $key); ?>">
				<?php foreach ($field['options'] as $key => $value) : ?>
					<option value="<?php echo esc_attr($key); ?>" <?php
																												if (!empty($field['value']) && is_array($field['value'])) {
																													selected(in_array($key, $field['value']), true);
																												}
																												?>><?php echo esc_html($value); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php
	}

	/**
	 * input_checkbox function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_checkbox($key, $field)
	{
		global $thepostid;
		if (empty($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field form-field-checkbox">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?></label>
			<input type="checkbox" class="checkbox" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" value="1" <?php checked($field['value'], 1); ?> />
			<?php
			if (!empty($field['description'])) :
			?>
				<span class="description"><?php echo esc_attr($field['description']); ?></span><?php endif; ?>
		</p>
	<?php
	}

	/**
	 * input_time function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_time($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" data-picker="timepicker" />
		</p>
	<?php
	}

	/**
	 * input_timezone function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_timezone($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<select name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" id="<?php echo isset($field['id']) ? esc_attr($field['id']) : esc_attr($key); ?>" class="input-select <?php echo esc_attr(isset($field['class']) ? $field['class'] : $key); ?>">
				<?php
				$value = isset($field['value']) ? $field['value'] : $field['default'];
				echo WP_Event_Manager_Date_Time::wp_event_manager_timezone_choice(esc_attr($value));
				?>
			</select>
		</p>
	<?php
	}



	/**
	 * input_number function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_number($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<input type="number" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" min="<?php echo isset($field['min']) ? esc_attr($field['min']) : esc_attr('0'); ?>" max="<?php echo isset($field['max']) ? esc_attr($field['max']) : ''; ?>" />
		</p>
	<?php
	}


	/**
	 * input_number function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_url($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<input type="url" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" />
		</p>
	<?php
	}


	/**
	 * input_button function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_button($key, $field)
	{
		global $thepostid;
		if (!isset($field['value'])) {
			$field['value'] = $field['placeholder'];
		}

		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:
				<?php
				if (!empty($field['description'])) :
				?>
					<span class="tips" data-tip="<?php echo esc_attr($field['description']); ?>">[?]</span><?php endif; ?></label>
			<input type="button" class="button button-small" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>" value="<?php echo esc_attr($field['value']); ?>" />
		</p>
	<?php
	}

	/**
	 * Box to choose who posted the event
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_author($key, $field)
	{
		global $thepostid, $post;
		if (!$post || $thepostid !== $post->ID) {
			$the_post  = get_post($thepostid);
			$author_id = $the_post->post_author;
		} else {
			$author_id = $post->post_author;
		}
		$posted_by      = get_user_by('id', $author_id);
		$field['value'] = !isset($field['value']) ? get_post_meta($thepostid, $key, true) : $field['value'];
		$name           = !empty($field['name']) ? $field['name'] : $key;
	?>
		<p class="form-field form-field-author">
			<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:</label>
			<span class="current-author">
				<?php
				if ($posted_by) {
					echo wp_kses_post('<a href="' . esc_url(admin_url('user-edit.php?user_id=' . absint($author_id))) . '">#' . absint($author_id) . ' &ndash; ' . esc_attr($posted_by->user_login) . '</a>');
				} else {
					esc_attr_e('Guest User', 'wp-event-manager');
				}
				?>
				<a href="#" class="change-author button button-small"><?php esc_attr_e('Change', 'wp-event-manager'); ?></a>
			</span>
			<span class="hidden change-author">
				<input type="number" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($key); ?>" step="1" value="<?php echo esc_attr($author_id); ?>" style="width: 4em;" />
				<span class="description"><?php esc_attr_e('Enter the ID of the user, or leave blank if submitted by a guest.', 'wp-event-manager'); ?></span>
			</span>
		</p>
	<?php
	}

	/**
	 * input_radio function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_radio($key, $field)
	{
		global $thepostid;
		if (empty($field['value'])) {
			$field['value'] = get_post_meta($thepostid, $key, true);
		}
		if (!empty($field['name'])) {
			$name = $field['name'];
		} else {
			$name = $key;
		}
	?>
		<p class="form-field form-field-checkbox">
			<label><?php echo esc_html($field['label']); ?></label>
			<?php foreach ($field['options'] as $option_key => $value) : ?>
				<label><input type="radio" class="radio" name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" value="<?php echo esc_attr($option_key); ?>" <?php checked($field['value'], $option_key); ?> /> <?php echo esc_html($value); ?></label>
			<?php endforeach; ?>
			<?php
			if (!empty($field['description'])) :
			?>
				<span class="description"><?php echo esc_html($field['description']); ?></span><?php endif; ?>
		</p>
<?php
	}

	/**
	 * event_listing_data function.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function event_listing_data($post)
	{
		global $post, $thepostid;
		$thepostid = $post->ID;
		echo wp_kses_post('<div class="wp_event_manager_meta_data">');
		wp_nonce_field('save_meta_data', 'event_manager_nonce');
		do_action('event_manager_event_listing_data_start', $thepostid);
		foreach ($this->event_listing_fields() as $key => $field) {
			$type = !empty($field['type']) ? $field['type'] : 'text';
			if ($type == 'wp-editor') {
				$type = 'wp_editor';
			}

			if (has_action('event_manager_input_' . $type)) {
				do_action('event_manager_input_' . $type, $key, $field);
			} elseif (method_exists($this, 'input_' . $type)) {
				call_user_func(array($this, 'input_' . $type), $key, $field);
			}
		}
		do_action('event_manager_event_listing_data_end', $thepostid);
		echo wp_kses_post('</div>');
	}

	/**
	 * save_post function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_post($post_id, $post)
	{
		if (empty($post_id) || empty($post) || empty($_POST)) {
			return;
		}
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (is_int(wp_is_post_revision($post))) {
			return;
		}
		if (is_int(wp_is_post_autosave($post))) {
			return;
		}
		if (empty($_POST['event_manager_nonce']) || !wp_verify_nonce($_POST['event_manager_nonce'], 'save_meta_data')) {
			return;
		}
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		if ($post->post_type == 'event_listing') {
			do_action('event_manager_save_event_listing', $post_id, $post);
		}

		if ($post->post_type == 'event_organizer') {
			do_action('event_manager_save_organizer_listing', $post_id, $post);
		}

		if ($post->post_type == 'event_venue') {
			do_action('event_manager_save_venue_listing', $post_id, $post);
		}
	}

	/**
	 * save_event_listing_data function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_event_listing_data($post_id, $post)
	{
		global $wpdb;
		// These need to exist
		add_post_meta($post_id, '_cancelled', 0, true);
		add_post_meta($post_id, '_featured', 0, true);

		// get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();

		// covert datepicker format  into php date() function date format
		$php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

		// Save fields
		foreach ($this->event_listing_fields() as $key => $field) {

			// Event Expiry date
			if ('_event_expiry_date' === $key) {

				if (!empty($_POST[$key])) {
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($_POST['date_format'], $_POST[$key]);
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;

					update_post_meta($post_id, $key, trim($date_dbformatted));
				} else {
					update_post_meta($post_id, $key, '');
				}
			}
			// Locations
			elseif ('_event_location' === $key) {
				if (update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]))) {
					// Location data will be updated by hooked in methods
				} elseif (apply_filters('event_manager_geolocation_enabled', true) && !WP_Event_Manager_Geocode::has_location_data($post_id)) {
					WP_Event_Manager_Geocode::generate_location_data($post_id, sanitize_text_field($_POST[$key]));
				}
			} elseif ('_event_author' === $key) {
				$wpdb->update($wpdb->posts, array('post_author' => $_POST[$key] > 0 ? absint($_POST[$key]) : 0), array('ID' => $post_id));
			} elseif ('_event_banner' === $key) {
				if (is_array($_POST[$key])) {
					$thumbnail_image = $_POST[$key][0];
					update_post_meta($post_id, $key, array_filter(array_map('sanitize_text_field', $_POST[$key])));
				} else {
				$thumbnail_image = $_POST[$key];
					update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
				}

				$image = get_the_post_thumbnail_url($post_id);

				if (empty($image)) {
					if (isset($thumbnail_image) && !empty($thumbnail_image)) {
						$wp_upload_dir = wp_get_upload_dir();

						$baseurl = $wp_upload_dir['baseurl'] . '/';

						$wp_attached_file = str_replace($baseurl, '', $thumbnail_image);

						$args = array(
							'meta_key'       => '_wp_attached_file',
							'meta_value'     => $wp_attached_file,
							'post_type'      => 'attachment',
							'posts_per_page' => 1,
						);

						$attachments = get_posts($args);

						if (!empty($attachments)) {
							foreach ($attachments as $attachment) {
								set_post_thumbnail($post_id, $attachment->ID);
							}
						}
					}
				}
			} elseif ('_event_start_date' === $key) {
				if (isset($_POST[$key]) && !empty($_POST[$key])) {
					if (isset($_POST['_event_start_time']) && !empty($_POST['_event_start_time'])) {
						$start_time = WP_Event_Manager_Date_Time::get_db_formatted_time(sanitize_text_field($_POST['_event_start_time']));
					} else {
						$start_time = date('H:i:s');
					}
					// combine event start date value with event start time
					$date = sanitize_text_field(explode(' ', $_POST[$key])[0] . ' ' . $start_time);
					// Convert date and time value into DB formatted format and save eg. 1970-01-01 00:00:00
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($_POST['date_format'] . ' H:i:s', $date);
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;
					update_post_meta($post_id, $key, sanitize_text_field(($date_dbformatted)));
				} else {
					update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
				}
			} elseif ('_event_end_date' === $key) {
				if (isset($_POST[$key]) && !empty($_POST[$key])) {
					if (isset($_POST['_event_end_time']) && !empty($_POST['_event_end_time'])) {
						$start_time = WP_Event_Manager_Date_Time::get_db_formatted_time(sanitize_text_field($_POST['_event_end_time']));
					} else {
						$start_time = date('H:i:s');
					}
					// combine event start date value with event start time
					$date = sanitize_text_field(explode(' ', $_POST[$key])[0] . ' ' . $start_time);
					// Convert date and time value into DB formatted format and save eg. 1970-01-01 00:00:00
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($_POST['date_format'] . ' H:i:s', $date);
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;

					update_post_meta($post_id, $key, sanitize_text_field(trim($date_dbformatted)));
				} else {
					update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
				}
			} elseif ('_event_registration_deadline' === $key) {
				if (isset($_POST[$key]) && !empty($_POST[$key])) {

					// combine event start date value with event start time
					$date = sanitize_text_field(explode(' ', $_POST[$key])[0]);
					// Convert date and time value into DB formatted format and save eg. 1970-01-01 00:00:00
					$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($_POST['date_format'], $date);
					$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;

					update_post_meta($post_id, $key, sanitize_text_field(trim($date_dbformatted)));
				} else {
					update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
				}
			} elseif ('_event_organizer_ids' === $key) {
				if (!empty($_POST[$key])) {
					update_post_meta($post_id, $key, array_filter(array_map('sanitize_text_field', $_POST[$key])));
				} else {
					update_post_meta($post_id, $key, '');
				}
			} elseif ('_event_venue_ids' === $key) {
				if (!empty($_POST[$key])) {
					update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
				} else {
					update_post_meta($post_id, $key, '');
				}
			}
			// Everything else
			else {
				$type = !empty($field['type']) ? $field['type'] : '';
				switch ($type) {
					case 'textarea':
						update_post_meta($post_id, $key, wp_kses_post(stripslashes($_POST[$key])));
						break;

					case 'checkbox':
						if (isset($_POST[$key])) {
							update_post_meta($post_id, $key, 1);
						} else {
							update_post_meta($post_id, $key, 0);
						}
						break;

					case 'date':
						if (isset($_POST[$key])) {
							$date = sanitize_text_field($_POST[$key]);
							$datetime = sanitize_text_field(explode(' ', $_POST[$key]));

							// Convert date and time value into DB formatted format and save eg. 1970-01-01
							$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format . ' H:i:s', $date);
							if (!isset($datetime[1])) {
								$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $date);
							}

							$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;
							update_post_meta($post_id, $key, trim($date_dbformatted));
							$date_dbformatted = date($php_date_format, strtotime($date_dbformatted));
						}
						break;

					case 'time':
						if (isset($_POST[$key])) {

							$time = sanitize_text_field($_POST[$key]);
							if (empty($_POST[$key])) {
								$time_dbformatted = '';
							} else {
								$time_dbformatted = WP_Event_Manager_Date_Time::get_db_formatted_time($time);
								$time_dbformatted = !empty($time_dbformatted) ? $time_dbformatted : $time;
							}
							update_post_meta($post_id, $key, $time_dbformatted);
						}
						break;

					case 'wp-editor':
						if (!empty($_POST[$key])) {
							$v_text = wp_kses_post(stripslashes($_POST[$key]));
							update_post_meta($post_id, $key, $v_text);
						}
						break;

					default:
						if (!isset($_POST[$key])) {
							continue 2;
						} elseif (is_array($_POST[$key])) {
							update_post_meta($post_id, $key, array_filter(array_map('sanitize_text_field', $_POST[$key])));
						} else {
							update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
						}
						break;
				}
			}
		}

		/* Set Post Status To Expired If Already Expired */
		$event_timezone = get_post_meta($post_id, '_event_timezone', true);
		// check if timezone settings is enabled as each event then set current time stamp according to the timezone
		// for eg. if each event selected then Berlin timezone will be different then current site timezone.
		if (WP_Event_Manager_Date_Time::get_event_manager_timezone_setting() == 'each_event') {
			$current_timestamp = WP_Event_Manager_Date_Time::current_timestamp_from_event_timezone($event_timezone);
		} else {
			$current_timestamp = current_time('timestamp'); // If site wise timezone selected
		}

		$expiry_date = get_post_meta($post_id, '_event_expiry_date', true);
		$today_date  = date('Y-m-d', $current_timestamp);
		$post_status = $expiry_date && $current_timestamp > strtotime($expiry_date) ? 'expired' : false;
		if ($post_status) {
			remove_action('event_manager_save_event_listing', array($this, 'save_event_listing_data'), 20, 2);
			$event_data = array(
				'ID'          => $post_id,
				'post_status' => $post_status,
			);
			wp_update_post($event_data);
			add_action('event_manager_save_event_listing', array($this, 'save_event_listing_data'), 20, 2);
		}
	}

	/**
	 * event_listing_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function organizer_listing_fields()
	{
		global $post;
		$current_user = wp_get_current_user();

		$GLOBALS['event_manager']->forms->get_form('submit-organizer', array());
		$form_submit_organizer_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Organizer', 'instance'));
		$fields                         = $form_submit_organizer_instance->merge_with_custom_fields('backend');

		/** add _ (prefix) for all backend fields.
		 *   Field editor will only return fields without _(prefix).
		 */
		foreach ($fields as $group_key => $group_fields) {
			foreach ($group_fields as $field_key => $field_value) {

				if (strpos($field_key, '_') !== 0) {
					$fields['_' . $field_key] = $field_value;
				} else {
					$fields[$field_key] = $field_value;
				}
			}
			unset($fields[$group_key]);
		}
		$fields = apply_filters('event_manager_organizer_listing_data_fields', $fields);

		if (isset($fields['_organizer_name'])) {
			unset($fields['_organizer_name']);
		}

		if (isset($fields['_organizer_description'])) {
			unset($fields['_organizer_description']);
		}

		if (isset($fields['_organizer_logo'])) {
			unset($fields['_organizer_logo']);
		}

		if ($current_user->has_cap('edit_others_event_listings')) {
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
	 * event_organizer_data function.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function event_organizer_data($post)
	{
		global $post, $thepostid;
		$thepostid = $post->ID;
		echo wp_kses_post('<div class="wp_event_manager_meta_data">');
		wp_nonce_field('save_meta_data', 'event_manager_nonce');
		do_action('event_manager_event_organizer_data_start', $thepostid);
		foreach ($this->organizer_listing_fields() as $key => $field) {
			$type = !empty($field['type']) ? $field['type'] : 'text';
			if ($type == 'wp-editor') {
				$type = 'textarea';
			}

			if (has_action('event_manager_input_' . $type)) {
				do_action('event_manager_input_' . $type, $key, $field);
			} elseif (method_exists($this, 'input_' . $type)) {
				call_user_func(array($this, 'input_' . $type), $key, $field);
			}
		}
		do_action('event_manager_event_organizer_data_end', $thepostid);
		echo wp_kses_post('</div>');
	}

	/**
	 * save_organizer_listing_data function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_organizer_listing_data($post_id, $post)
	{
		global $wpdb;

		// get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();

		// covert datepicker format  into php date() function date format
		$php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

		update_post_meta($post_id, '_organizer_name', sanitize_text_field($_POST['post_title']));
		update_post_meta($post_id, '_organizer_description',  $_POST['content']);

		// Save fields
		foreach ($this->organizer_listing_fields() as $key => $field) {

			if ('_organizer_author' === $key) {
				$wpdb->update($wpdb->posts, array('post_author' => $_POST[$key] > 0 ? absint($_POST[$key]) : 0), array('ID' => $post_id));
			}
			// Everything else
			else {
				$type = !empty($field['type']) ? $field['type'] : '';
				switch ($type) {
					case 'textarea':
						update_post_meta($post_id, $key, wp_kses_post(stripslashes($_POST[$key])));
						break;
					case 'checkbox':
						if (isset($_POST[$key])) {
							update_post_meta($post_id, $key, 1);
						} else {
							update_post_meta($post_id, $key, 0);
						}
						break;
					case 'date':
						if (isset($_POST[$key])) {
							$date = $_POST[$key];

							// Convert date and time value into DB formatted format and save eg. 1970-01-01
							$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $date);
							$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;
							update_post_meta($post_id, $key, sanitize_text_field(trim($date_dbformatted)));
						}
						break;
					case 'wp-editor':
                        if (!empty($_POST[$key])) {
                            $v_text = wp_kses_post(stripslashes($_POST[$key]));
                            update_post_meta($post_id, $key, $v_text);
                        }
                        break;
					default:
						if (!isset($_POST[$key])) {
							continue 2;
						} elseif (is_array($_POST[$key])) {
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
	 * venue_listing_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function venue_listing_fields()
	{
		global $post;
		$current_user = wp_get_current_user();

		$GLOBALS['event_manager']->forms->get_form('submit-venue', array());
		$form_submit_venue_instance = call_user_func(array('WP_Event_Manager_Form_Submit_Venue', 'instance'));
		$fields                     = $form_submit_venue_instance->merge_with_custom_fields('backend');

		/** add _ (prefix) for all backend fields.
		 *   Field editor will only return fields without _(prefix).
		 */
		foreach ($fields as $group_key => $group_fields) {
			foreach ($group_fields as $field_key => $field_value) {

				if (strpos($field_key, '_') !== 0) {
					$fields['_' . $field_key] = $field_value;
				} else {
					$fields[$field_key] = $field_value;
				}
			}
			unset($fields[$group_key]);
		}
		$fields = apply_filters('event_manager_venue_listing_data_fields', $fields);

		if (isset($fields['_venue_name'])) {
			unset($fields['_venue_name']);
		}

		if (isset($fields['_venue_description'])) {
			unset($fields['_venue_description']);
		}

		if (isset($fields['_venue_logo'])) {
			unset($fields['_venue_logo']);
		}

		if ($current_user->has_cap('edit_others_event_listings')) {
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
	 * event_venue_data function.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function event_venue_data($post)
	{
		global $post, $thepostid;
		$thepostid = $post->ID;
		echo wp_kses_post('<div class="wp_event_manager_meta_data">');
		wp_nonce_field('save_meta_data', 'event_manager_nonce');
		do_action('event_manager_event_venue_data_start', $thepostid);
		foreach ($this->venue_listing_fields() as $key => $field) {
			$type = !empty($field['type']) ? $field['type'] : 'text';
			if ($type == 'wp-editor') {
				$type = 'textarea';
			}

			if (has_action('event_manager_input_' . $type)) {
				do_action('event_manager_input_' . $type, $key, $field);
			} elseif (method_exists($this, 'input_' . $type)) {
				call_user_func(array($this, 'input_' . $type), $key, $field);
			}
		}
		do_action('event_manager_event_venue_data_end', $thepostid);
		echo wp_kses_post('</div>');
	}

	/**
	 * save_venue_listing_data function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_venue_listing_data($post_id, $post)
	{
		global $wpdb;

		// get date and time setting defined in admin panel Event listing -> Settings -> Date & Time formatting
		$datepicker_date_format = WP_Event_Manager_Date_Time::get_datepicker_format();

		// covert datepicker format  into php date() function date format
		$php_date_format = WP_Event_Manager_Date_Time::get_view_date_format_from_datepicker_date_format($datepicker_date_format);

		update_post_meta($post_id, '_venue_name', sanitize_text_field($_POST['post_title']));
		update_post_meta($post_id, '_venue_description', sanitize_text_field($_POST['content']));

		// Save fields
		foreach ($this->venue_listing_fields() as $key => $field) {
			if ('_venue_author' === $key) {
				$wpdb->update($wpdb->posts, array('post_author' => $_POST[$key] > 0 ? absint($_POST[$key]) : 0), array('ID' => $post_id));
			}
			// Everything else
			else {
				$type = !empty($field['type']) ? $field['type'] : '';
				switch ($type) {
					case 'textarea':
						update_post_meta($post_id, $key, wp_kses_post(stripslashes($_POST[$key])));
						break;
					case 'checkbox':
						if (isset($_POST[$key])) {
							update_post_meta($post_id, $key, 1);
						} else {
							update_post_meta($post_id, $key, 0);
						}
						break;
					case 'date':
						if (isset($_POST[$key])) {
							$date = sanitize_text_field($_POST[$key]);

							// Convert date and time value into DB formatted format and save eg. 1970-01-01
							$date_dbformatted = WP_Event_Manager_Date_Time::date_parse_from_format($php_date_format, $date);
							$date_dbformatted = !empty($date_dbformatted) ? $date_dbformatted : $date;
							update_post_meta($post_id, $key, sanitize_text_field(trim($date_dbformatted)));
						}
						break;
						case 'wp-editor':
							if (!empty($_POST[$key])) {
								$v_text = wp_kses_post(stripslashes($_POST[$key]));
								update_post_meta($post_id, $key, $v_text);
							}
							break;
					default:
						if (!isset($_POST[$key])) {
							continue 2;
						} elseif (is_array($_POST[$key])) {
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
	 * wpem_delete_event_with_attachment function.
	 *
	 * @param $post_id
	 * @access public
	 * @return void
	 */
	public function delete_event_with_attachment($post_id)
	{
		if (!in_array(get_post_type($post_id), array('event_listing', 'event_organizer'))) {
			return;
		}

		$args = array(
			'posts_per_page' => -1,
			'post_parent'    => $post_id,
			'post_type' => 'event_listing'
		);
		$children = get_children($args, ARRAY_A);
		if (sizeof($children) == 0) {
			$event_banner = get_event_banner($post_id);

			if (!empty($event_banner)) {
				$wp_upload_dir = wp_get_upload_dir();

				$baseurl = $wp_upload_dir['baseurl'] . '/';

				if (is_array($event_banner)) {
					foreach ($event_banner as $banner) {
						$wp_attached_file = str_replace($baseurl, '', $banner);

						$args = array(
							'meta_key'       => '_wp_attached_file',
							'meta_value'     => $wp_attached_file,
							'post_type'      => 'attachment',
							'posts_per_page' => 1,
						);

						$attachments = get_posts($args);

						if (!empty($attachments)) {
							foreach ($attachments as $attachment) {

								wp_delete_attachment($attachment->ID, true);
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

					if (!empty($attachments)) {
						foreach ($attachments as $attachment) {
							wp_delete_attachment($attachment->ID, true);
						}
					}
				}
			}

			$thumbnail_id = get_post_thumbnail_id($post_id);
			if (!empty($thumbnail_id)) {
				wp_delete_attachment($thumbnail_id, true);
			}
		}
	}
}
WP_Event_Manager_Writepanels::instance();
