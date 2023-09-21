<?php

/**
 * Repeated fields is generated from this page .
 * Repeated fields for the paid and free tickets.
 * This field is used in submit event form.
 * */
?>
<?php if (!empty($field['value']) && is_array($field['value'])) : ?>

    <?php foreach ($field['value'] as $index => $value) : ?>

        <div class="repeated-row-<?php echo esc_attr($key); ?>">
            <input type="hidden" class="repeated-row" name="repeated-row-<?php echo esc_attr($key); ?>[]" value="<?php echo absint($index); ?>" />

            <div class="wpem-tabs-wrapper wpem-add-tickets-tab-wrapper">

                <div class="wpem-tabs-action-buttons">
                    <div class="wpem-ticket-counter-wrapper">
                        <div class="wpem-ticket-counter"><?php echo esc_attr(absint($index)); ?></div>
                    </div>
                    <div class="wpem-ticket-notice-info"><a class="ticket-notice-info" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?php _e('You can\'t delete ticket once it is added.You can make it private from settings tab.', 'wp-event-manager'); ?>"><i class="wpem-icon-blocked"></i></a></div>
                </div>

                <ul class="wpem-tabs-wrap">
                    <li class="wpem-tab-link active" data-tab="sell-ticket-details_<?php echo esc_attr($index); ?>"><?php _e('Ticket details', 'wp-event-manager'); ?></li>
                    <li class="wpem-tab-link" data-tab="<?php echo esc_attr($key); ?>_<?php echo esc_attr(absint($index)); ?>"><?php _e('Settings', 'wp-event-manager'); ?></li>
                </ul>

                <div id="sell-ticket-details-<?php echo esc_attr($key) . '-' . $index; ?>" class="wpem-tab-content current">
                    <div id="sell-ticket-details_<?php echo esc_attr(absint($index)); ?>" class="wpem-tab-pane active">
                        <?php
                        if (isset($field['fields'])) {
                            foreach ($field['fields'] as $subkey => $subfield) :
                                if ($subkey == 'ticket_description') : ?>
                    </div>
                    <!------------end ticket details tab------>
                    <div id="<?php echo esc_attr($key) . '_' . $index; ?>" class="wpem-tab-pane">
                    <?php endif; ?>
                    <fieldset class="wpem-form-group fieldset-<?php esc_attr_e($subkey); ?>">
                        <?php if (!empty($subfield['label'])) : ?>
                            <label for="<?php esc_attr_e($subkey); ?>" class="wpem-form-label-text"><?php echo esc_attr($subfield['label']) . ($subfield['required'] ? '' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>'); ?></label>
                        <?php endif; ?>

                        <div class="field">
                            <?php
                                $subfield['name']  = $key . '_' . $subkey . '_' . $index;
                                $subfield['id']    = $key . '_' . $subkey . '_' . $index;
                                $subfield['value'] = isset($value[$subkey]) ? $value[$subkey] : '';
                                if ($subkey === 'ticket_quantity' && isset($value['product_id'])) {
                                    $stock = get_post_meta($value['product_id'], '_stock', true);
                                    if (isset($stock) && !empty($stock)) {
                                        $subfield['value'] = $stock;
                                    }
                                }

                                get_event_manager_template('form-fields/' . $subfield['type'] . '-field.php', array('key' => $subkey, 'field' => $subfield));
                            ?>
                        </div>
                    </fieldset>
            <?php endforeach;
                        } ?>
                    </div>
                </div><!-- / wpemtab wraper  -->
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <a href="#" class="wpem-theme-text-button event_ticket_add_link" data-row="<?php
                                                                                ob_start();
                                                                                ?>
       <div class=" repeated-row-<?php echo esc_attr($key . '_%%repeated-row-index%%'); ?>">

        <input type="hidden" class="repeated-row" name="repeated-row-<?php echo esc_attr($key); ?>[]" value="%%repeated-row-index%%" />

        <div class="wpem-tabs-wrapper wpem-add-tickets-tab-wrapper">

            <div class="wpem-tabs-action-buttons">

                <div class="wpem-ticket-counter-wrapper">
                    <div class="wpem-ticket-counter"><?php echo wp_kses_post('%%repeated-row-index%%'); ?></div>
                </div>

                <div class="wpem-ticket-close-button"><a href="#remove" class="remove-row" title="<?php _e('Remove', 'wp-event-manager'); ?>" id="repeated-row-<?php echo esc_attr($key . '_%%repeated-row-index%%'); ?>"><i class="wpem-icon-cross"></i></a></div>
            </div>

            <ul class="wpem-tabs-wrap">
                <li class="wpem-tab-link active" data-tab="sell-ticket-details_%%repeated-row-index%%"><?php _e('Ticket details', 'wp-event-manager'); ?></li>
                <li class="wpem-tab-link" data-tab="<?php echo esc_attr($key); ?>_%%repeated-row-index%%"><?php _e('Settings', 'wp-event-manager'); ?></li>
            </ul>
            <div id="sell-ticket-details-<?php echo esc_attr($key) . '-' . '%%repeated-row-index%%'; ?>" class="wpem-tab-content current">
                <div id="sell-ticket-details_%%repeated-row-index%%" class="wpem-tab-pane active">
                    <?php
                    foreach ($field['fields'] as $subkey => $subfield) :
                        if ($subkey == 'ticket_description') :
                    ?>
                </div>
                <!------------end ticket details tab------>
                <div id="<?php echo esc_attr($key); ?>_%%repeated-row-index%%" class="wpem-tab-pane">
                <?php endif; ?>

                <fieldset class="wpem-form-group fieldset-<?php esc_attr_e($subkey); ?>">
                    <?php if (!empty($subfield['label'])) : ?>
                        <label for="<?php esc_attr_e($subkey); ?>" class="wpem-form-label-text"><?php echo esc_attr($subfield['label']) . ($subfield['required'] ? '' : ' <small>' . __('(optional)', 'wp-event-manager') . '</small>'); ?></label>
                    <?php endif; ?>

                    <div class="field">
                        <?php
                        $subfield['name'] = $key . '_' . $subkey . '_%%repeated-row-index%%';
                        $subfield['id']   = $key . '_' . $subkey . '_%%repeated-row-index%%';
                        get_event_manager_template('form-fields/' . $subfield['type'] . '-field.php', array('key' => $subkey, 'field' => $subfield));
                        ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>
                </div>
            </div>
            <?php
            echo esc_attr(ob_get_clean());
            ?>">+ <?php
                    if (!empty($field['label'])) {
                        echo esc_attr($field['label']);
                    };
                    ?>
    </a>
    <?php if (!empty($field['description'])) : ?><small class="description"><?php echo esc_attr($field['description']); ?></small><?php endif; ?>