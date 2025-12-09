<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Get selected value
if (isset($field['value'])) {
	$selected = $field['value'];
} elseif (!empty($field['default']) && is_int($field['default'])) {
	$selected = $field['default'];
} elseif (!empty($field['default']) && ($term = get_term_by('slug', $field['default'], $field['taxonomy']))) {
	$selected = $term->term_id;
} else {
	$selected = '';
}
// Ensure DOMPurify is available before term-multiselect initializes
if (!wp_script_is('wpem-dompurify', 'registered')) {
    wp_register_script('wpem-dompurify', EVENT_MANAGER_PLUGIN_URL . '/assets/js/dom-purify/dompurify.min.js', array(), '3.0.5', true);
}
wp_enqueue_script('wpem-dompurify');
// Ensure the term-multiselect script depends on DOMPurify at runtime
if (wp_script_is('wp-event-manager-term-multiselect', 'registered')) {
    wp_deregister_script('wp-event-manager-term-multiselect');
}
wp_register_script(
    'wp-event-manager-term-multiselect',
    EVENT_MANAGER_PLUGIN_URL . '/assets/js/term-multiselect.min.js',
    array('jquery', 'chosen', 'wpem-dompurify'),
    defined('EVENT_MANAGER_VERSION') ? EVENT_MANAGER_VERSION : null,
    true
);

wp_enqueue_script('wp-event-manager-term-multiselect');
$args = array(
	'taxonomy'     => $field['taxonomy'],
	'hierarchical' => 1,
	'name'         => isset($field['name']) ? $field['name'] : $key,
	'orderby'      => 'name',
	'selected'     => $selected,
	'hide_empty'   => false
);
if (isset($field['placeholder']) && !empty($field['placeholder'])) 
	$args['placeholder'] = $field['placeholder'];
if(isset( $field['taxonomy'] )){
	if($field['taxonomy'] === 'event_listing_type'):
		$args['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : __('Choose an event type', 'wp-event-manager');
		$args['multiple_text'] = isset($field['multiple_text']) ? $field['multiple_text'] :  __('Choose event types', 'wp-event-manager');;
	else:
		$args['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : __('Choose an event type', 'wp-event-manager');
		$args['multiple_text'] = isset($field['multiple_text']) ? $field['multiple_text'] : $args['placeholder'];
	endif;
}
$args['selected'] = is_array($selected) ? $selected : array($selected);
event_manager_dropdown_selection(apply_filters('event_manager_term_multiselect_field_args', $args));

if (!empty($field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($field['description']); ?>
	</small>
<?php endif; ?>