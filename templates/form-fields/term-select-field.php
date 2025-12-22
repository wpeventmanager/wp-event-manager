<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$wpem_field['default'] = isset($wpem_field['default']) ? $wpem_field['default'] : '';
$taxonomy = isset($wpem_field['taxonomy']) ? $wpem_field['taxonomy'] : '';
// Get selected value
if (isset($wpem_field['value'])) {
	$wpem_selected = $wpem_field['value'];
} elseif (is_int($wpem_field['default'])) {
	$wpem_selected = $wpem_field['default'];
} elseif (!empty($wpem_field['default']) && !empty($taxonomy) && ($term = get_term_by('slug', $wpem_field['default'], $taxonomy))) {
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
}elseif(isset($wpem_field['value'])){
	$wpem_selected = $wpem_field['value'];
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
	'show_option_none' => $wpem_field['required'] ? '' : '-',
	'name'             => isset($wpem_field['name']) ? $wpem_field['name'] : $key,
	'orderby'          => 'name',
	'selected'         => $wpem_selected,
	'hide_empty'       => false
), $key, $wpem_field));

if (!empty($wpem_field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($wpem_field['description']); ?>
	</small>
<?php endif; ?>