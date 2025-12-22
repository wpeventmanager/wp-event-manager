<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if (!isset($wpem_field['type']) || $wpem_field['type'] !== 'wp-editor') {
	return;
}
$wpem_editor = apply_filters('submit_event_form_wp_editor_args', array(
	'textarea_name' => isset($wpem_field['name']) ? $wpem_field['name'] : $key,
	'media_buttons' => false,
	'wpautop' 		=> false,
	'textarea_rows' => 8,
	'quicktags'     => false,
	'tinymce'       => array(
		'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
		'paste_as_text'                 => true,
		'paste_auto_cleanup_on_paste'   => true,
		'paste_remove_spans'            => true,
		'paste_remove_styles'           => true,
		'paste_remove_styles_if_webkit' => true,
		'paste_strip_class_attributes'  => true,
		'toolbar1'                      => 'formatselect,bold,italic,|,bullist,numlist,|,link,unlink,|,undo,redo',
		'toolbar2'                      => '',
		'toolbar3'                      => '',
		'toolbar4'                      => ''
	),
));

$wpem_placeholder_text = isset($wpem_field['placeholder']) ? $wpem_field['placeholder'] : '';
wp_editor(isset($wpem_field['value']) ? $wpem_field['value'] : $wpem_placeholder_text, $key, $wpem_editor);
if (!empty($wpem_field['description'])) : ?>
	<small class="description">
		<?php echo wp_kses_post($wpem_field['description']); ?>
	</small>
<?php endif; ?>
