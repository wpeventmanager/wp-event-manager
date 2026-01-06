<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Get selected value
if (isset($wpem_field['value'])) {
	$wpem_selected = $wpem_field['value'];
} elseif (!empty($wpem_field['default']) && is_int($wpem_field['default'])) {
	$wpem_selected = $wpem_field['default'];
} elseif (!empty($wpem_field['default']) && ($term = get_term_by('slug', $wpem_field['default'], $wpem_field['taxonomy']))) {
	$wpem_selected = $term->term_id;
} else {
	$wpem_selected = '';
}
// Ensure DOMPurify is available before term-multiselect initializes
if (!wp_script_is('wpem-dompurify', 'registered')) {
    wp_register_script('wpem-dompurify', EVENT_MANAGER_PLUGIN_URL . '/assets/js/dom-purify/dompurify.min.js', array(), '3.0.5', true);
}
wp_enqueue_script('wpem-dompurify');
wp_enqueue_script('chosen');
wp_enqueue_style('chosen'); 
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
wp_enqueue_script('wp-event-manager-multiselect');

wp_enqueue_script('wp-event-manager-term-multiselect');
$wpem_args = array(
	'taxonomy'     => $wpem_field['taxonomy'],
	'hierarchical' => 1,
	'name'         => isset($wpem_field['name']) ? $wpem_field['name'] : $wpem_key,
	'orderby'      => 'name',
	'selected'     => $wpem_selected,
	'hide_empty'   => false
);
if (isset($wpem_field['placeholder']) && !empty($wpem_field['placeholder'])) 
	$wpem_args['placeholder'] = $wpem_field['placeholder'];
if(isset( $wpem_field['taxonomy'] )){
	if($wpem_field['taxonomy'] === 'event_listing_type'):
		$wpem_args['placeholder'] = isset($wpem_field['placeholder']) ? $wpem_field['placeholder'] : __('Choose an event type', 'wp-event-manager');
		$wpem_args['multiple_text'] = isset($wpem_field['multiple_text']) ? $wpem_field['multiple_text'] :  __('Choose event types', 'wp-event-manager');;
	else:
		$wpem_args['placeholder'] = isset($wpem_field['placeholder']) ? $wpem_field['placeholder'] : __('Choose an event type', 'wp-event-manager');
		$wpem_args['multiple_text'] = isset($wpem_field['multiple_text']) ? $wpem_field['multiple_text'] : $wpem_args['placeholder'];
	endif;
}
$wpem_args['selected'] = is_array($wpem_selected) ? $wpem_selected : array($wpem_selected);
event_manager_dropdown_selection(apply_filters('event_manager_term_multiselect_field_args', $wpem_args));

if (!empty($wpem_field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($wpem_field['description']); ?>
	</small>
<?php endif; ?>