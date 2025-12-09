<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
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
	// Remove 'disabled' attributes safely
    $checklist = str_replace( "disabled='disabled'", '', $checklist );
	// Escape output while allowing safe HTML tags
    echo wp_kses(
        $checklist,
        array(
            'ul'    => array( 'id' => true, 'class' => true ),
            'li'    => array( 'id' => true, 'class' => true ),
            'input' => array(
                'type'    => true,
                'name'    => true,
                'value'   => true,
                'checked' => true,
                'id'      => true,
                'class'   => true,
            ),
            'label' => array(
                'for'   => true,
                'class' => true,
            ),
            'span'  => array(
                'class' => true,
            ),
        )
    );?>
</ul>

<?php if (!empty($field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($field['description']); ?>
	</small>
<?php endif; ?>