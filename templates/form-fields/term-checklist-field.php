<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<ul class="event-manager-term-checklist event-manager-term-checklist-<?php echo esc_attr($key) ?>">
	<?php require_once(ABSPATH . '/wp-admin/includes/template.php');

	if (empty($wpem_field['default'])) {
		$wpem_field['default'] = '';
	}

	$wpem_args = array(
		'descendants_and_self'  => 0,
		'selected_cats'         => isset($wpem_field['value']) ? $wpem_field['value'] : (is_array($wpem_field['default']) ? $wpem_field['default'] : array($wpem_field['default'])),
		'popular_cats'          => false,
		'taxonomy'              => $wpem_field['taxonomy'],
		'checked_ontop'         => false
	);
	ob_start();

	wp_terms_checklist(0, $wpem_args);

	$wpem_checklist = ob_get_clean();
	// Remove 'disabled' attributes safely
    $wpem_checklist = str_replace( "disabled='disabled'", '', $wpem_checklist );
	// Escape output while allowing safe HTML tags
    echo wp_kses(
        $wpem_checklist,
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

<?php if (!empty($wpem_field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($wpem_field['description']); ?>
	</small>
<?php endif; ?>