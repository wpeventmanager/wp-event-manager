<ul class="event-manager-term-checklist event-manager-term-checklist-<?php echo esc_attr($key) ?>">
	<?php require_once(ABSPATH . '/wp-admin/includes/template.php');

	if (empty($field['default'])) {
		$field['default'] = '';
	}

	$args = array(
		'descendants_and_self'  => 0,
		'selected_cats'         => isset($field['value']) ? $field['value'] : (is_array($field['default']) ? $field['default'] : array($field['default'])),
		'popular_cats'          => false,
		'taxonomy'              => $field['taxonomy'],
		'checked_ontop'         => false
	);
	ob_start();

	wp_terms_checklist(0, $args);

	$checklist = ob_get_clean();

	echo esc_attr(str_replace("disabled='disabled'", '', $checklist));?>
</ul>

<?php if (!empty($field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($field['description']); ?>
	</small>
<?php endif; ?>