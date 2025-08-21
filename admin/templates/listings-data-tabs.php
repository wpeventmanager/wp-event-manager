<?php $event_fields = $this->event_listing_fields(); ?>

<div class="panel-wrap">
    <ul class="wpem-tabs">
        <?php foreach ($this->get_event_data_tabs() as $key => $tab) : ?>
            <li class="<?php echo esc_attr($key); ?>_options <?php echo esc_attr($key); ?>_tab <?php echo esc_attr(isset($tab['class']) ? implode(' ', (array) $tab['class']) : ''); ?>">
                <a href="#<?php echo isset($tab['target']) ? esc_attr($tab['target']) : ''; ?>" class="">
                    <span><?php echo esc_html($tab['label']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
        <?php do_action('wpem_event_write_panel_tabs'); ?>
    </ul>

    <?php foreach ($this->get_event_data_tabs() as $key => $tab) : ?>
        <div id="<?php echo isset($tab['target']) ? esc_attr($tab['target']) : ''; ?>" class="panel wpem_panel wpem-metaboxes-wrapper">
            <div class="wp_event_manager_meta_data">
                <div class="wpem-variation-wrapper wpem-metaboxes">
                    <?php do_action('event_manager_event_data_start', $thepostid);
                    if (isset($event_fields)) {
                        foreach ($event_fields as $key => $field) {
                            if (!isset($field['value'])) {
                                $field['value'] = get_post_meta($thepostid, '_' . $key, true);
                            }
                            $field['required'] = false;
                            $field['tabgroup'] = isset($field['tabgroup']) ? $field['tabgroup'] : 1;
                            if ($field['tabgroup'] == $tab['priority']) {
                                $type = !empty($field['type']) ? $field['type'] : 'text';
                                if ($type == 'wp-editor') {
                                    $type = 'wp_editor';
                                } elseif ($type == 'media-library-image') {
                                    $type = 'media_library_image';
                                }
                                
                                if (has_action('event_manager_input_' . $type)) {
                                    do_action('event_manager_input_' . $type, $key, $field);
                                } elseif (method_exists($this, 'input_' . $type)) {
                                    call_user_func(array($this, 'input_' . $type), $key, $field);
                                }
                            }
                        }
                    }
                    do_action('event_manager_event_data_end', $thepostid); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="clear"></div>
</div>
