<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$field['default'] = isset($field['default']) ? $field['default'] : '';
$taxonomy = isset($field['taxonomy']) ? $field['taxonomy'] : '';
// Get selected value
if (isset($field['value'])) {
	$wpem_selected = $field['value'];
} elseif (is_int($field['default'])) {
	$wpem_selected = $field['default'];
} elseif (!empty($field['default']) && !empty($taxonomy) && ($term = get_term_by('slug', $field['default'], $taxonomy))) {
	$wpem_selected = $term->term_id;
} else {
	$wpem_selected = '';
}

$wpem_placeholder = '';

if($key == 'event_category'){
	$wpem_placeholder=__( 'Choose a Category', 'wp-event-manager' );
}else if($key == 'event_type'){
	$wpem_placeholder=__( 'Choose an Event Type', 'wp-event-manager' );
}

// Select only supports 1 value
if (is_array($wpem_selected)) {
	$wpem_selected = current($wpem_selected);
}elseif(isset($field['value'])){
	$wpem_selected = $field['value'];
}else{
	$wpem_selected = '';
}

if (empty($taxonomy)) {
    return;
}
wp_dropdown_categories(apply_filters('event_manager_term_select_field_wp_dropdown_categories_args', array(
	'taxonomy'         => $taxonomy,
	'hierarchical'     => 1,
	'show_option_all'  => $wpem_placeholder,
	'show_option_none' => $field['required'] ? '' : '-',
	'name'             => isset($field['name']) ? $field['name'] : $key,
	'orderby'          => 'name',
	'selected'         => $wpem_selected,
	'hide_empty'       => false
), $key, $field));

if (!empty($field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($field['description']); ?>
	</small>
<?php endif; ?>