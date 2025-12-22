<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Repeated fields is generated from this page .
 * Repeated fields for the paid and free tickets.
 * This field is used in submit event form.
 * */
if (!empty($wpem_field['value']) && is_array($wpem_field['value'])) : 
    foreach ($wpem_field['value'] as $wpem_index => $wpem_value) : ?>
        <div class="repeated-row-<?php echo esc_attr($key); ?>">
            <input type="hidden" class="repeated-row" name="repeated-row-<?php echo esc_attr($key); ?>[]" value="<?php echo absint($wpem_index); ?>" />

            <div class="wpem-tabs-wrapper wpem-add-tickets-tab-wrapper">

                <div class="wpem-tabs-action-buttons">
                    <div class="wpem-ticket-counter-wrapper">
                        <div class="wpem-ticket-counter"><?php echo esc_attr(absint($wpem_index)+1); ?></div>
                    </div>
                    <div class="wpem-ticket-notice-info"><a class="ticket-notice-info" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?php esc_attr_e('You can\'t delete ticket once it is added.You can make it private from settings tab.', 'wp-event-manager'); ?>" title="<?php esc_attr_e('You can\'t delete ticket once it is added.You can make it private from settings tab.', 'wp-event-manager');?>"><i class="wpem-icon-blocked"></i></a></div>
                </div>

                <ul class="wpem-tabs-wrap">
                    <li class="wpem-tab-link active" data-tab="sell-ticket-details_<?php echo esc_attr($wpem_index); ?>"><?php esc_html_e('Ticket details', 'wp-event-manager'); ?></li>
                    <li class="wpem-tab-link" data-tab="<?php echo esc_attr($key); ?>_<?php echo esc_attr(absint($wpem_index)); ?>"><?php esc_html_e('Settings', 'wp-event-manager'); ?></li>
                </ul>

                <div id="sell-ticket-details-<?php echo esc_attr($key) . '-' . esc_attr($wpem_index); ?>" class="wpem-tab-content current">
                    <div id="sell-ticket-details_<?php echo esc_attr(absint($wpem_index)); ?>" class="wpem-tab-pane active">
                        <?php
                        if (isset($wpem_field['fields'])) {
                            foreach ($wpem_field['fields'] as $wpem_subkey => $wpem_subfield) :
                                if ($wpem_subkey == 'ticket_description') : ?>
                    </div>
                    <!------------end ticket details tab------>
                    <div id="<?php echo esc_attr($key) . '_' . esc_attr($wpem_index); ?>" class="wpem-tab-pane">
                    <?php endif; ?>
                    <fieldset class="wpem-form-group fieldset-<?php esc_attr($wpem_subkey, 'wp-event-manager'); ?>">
                        <?php if (!empty($wpem_subfield['label'])) : ?>
                            <label for="<?php esc_attr($wpem_subkey, 'wp-event-manager'); ?>" class="wpem-form-label-text"><?php echo esc_attr($wpem_subfield['label']) . ($wpem_subfield['required'] ? '' : ' <small>' . esc_attr('(optional)', 'wp-event-manager') . '</small>'); ?></label>
                        <?php endif; ?>

                        <div class="field">
                            <?php
                                $wpem_subfield['name']  = $key . '_' . $wpem_subkey . '_' . $wpem_index;
                                $wpem_subfield['id']    = $key . '_' . $wpem_subkey . '_' . $wpem_index;
                                $wpem_subfield['value'] = isset($wpem_value[$wpem_subkey]) ? $wpem_value[$wpem_subkey] : '';
                                if ($wpem_subkey === 'ticket_quantity' && isset($wpem_value['product_id'])) {
                                    $wpem_stock = esc_attr(get_post_meta($wpem_value['product_id'], '_stock', true));
                                    if (isset($wpem_stock) && !empty($wpem_stock)) {
                                        $wpem_subfield['value'] = $wpem_stock;
                                    }
                                }
                                wpem_get_event_manager_template('form-fields/' . $wpem_subfield['type'] . '-field.php', array('key' => $wpem_subkey, 'field' => $wpem_subfield));
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
    <a href="#" class="wpem-theme-text-button event_ticket_add_link" data-row="<?php ob_start();  ?>
       <div class=" repeated-row-<?php echo esc_attr($key . '_%%repeated-row-index%%'); ?>">

        <input type="hidden" class="repeated-row" name="repeated-row-<?php echo esc_attr($key); ?>[]" value="%%repeated-row-index%%" />

        <div class="wpem-tabs-wrapper wpem-add-tickets-tab-wrapper">

            <div class="wpem-tabs-action-buttons">

                <div class="wpem-ticket-counter-wrapper">
                    <div class="wpem-ticket-counter"><?php echo wp_kses_post('%%repeated-row-index%%'); ?></div>
                </div>
 
                <div class="wpem-ticket-close-button"><a href="#remove" class="remove-row" title="<?php esc_attr_e('Remove', 'wp-event-manager'); ?>" id="repeated-row-<?php echo esc_attr($key . '_%%repeated-row-index%%'); ?>"><i class="wpem-icon-cross"></i></a></div>
            </div>

            <ul class="wpem-tabs-wrap">
                <li class="wpem-tab-link active" data-tab="sell-ticket-details_%%repeated-row-index%%"><?php esc_attr_e('Ticket details', 'wp-event-manager'); ?></li>
                <li class="wpem-tab-link" data-tab="<?php echo esc_attr($key); ?>_%%repeated-row-index%%"><?php esc_attr_e('Settings', 'wp-event-manager'); ?></li>
            </ul>
            <div id="sell-ticket-details-<?php echo esc_attr($key) . '-' . '%%repeated-row-index%%'; ?>" class="wpem-tab-content current">
                <div id="sell-ticket-details_%%repeated-row-index%%" class="wpem-tab-pane active">
                    <?php
                    foreach ($wpem_field['fields'] as $wpem_subkey => $wpem_subfield) :
                        if ($wpem_subkey == 'ticket_description') :
                    ?>
                </div>
                <!------------end ticket details tab------>
                <div id="<?php echo esc_attr($key); ?>_%%repeated-row-index%%" class="wpem-tab-pane">
                <?php endif; ?>

                <fieldset class="wpem-form-group fieldset-<?php esc_attr($wpem_subkey, 'wp-event-manager'); ?>">
                    <?php if (!empty($wpem_subfield['label'])) : ?>
                        <label for="<?php esc_attr($wpem_subkey,'wp-event-manager'); ?>" class="wpem-form-label-text"><?php echo esc_attr($wpem_subfield['label']) . ($wpem_subfield['required'] ? '' : ' <small>' . esc_attr('(optional)', 'wp-event-manager') . '</small>'); ?></label>
                    <?php endif; ?>

                    <div class="field">
                        <?php
                        $wpem_subfield['name'] = $key . '_' . $wpem_subkey . '_%%repeated-row-index%%';
                        $wpem_subfield['id']   = $key . '_' . $wpem_subkey . '_%%repeated-row-index%%';
                        wpem_get_event_manager_template('form-fields/' . $wpem_subfield['type'] . '-field.php', array('key' => $wpem_subkey, 'field' => $wpem_subfield));
                        ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>
                </div>
            </div>
            <?php  echo esc_attr(ob_get_clean());
            ?>">+ <?php
                    if (!empty($wpem_field['label'])) {
                        echo esc_attr($wpem_field['label']);
                    };
                    ?>
    </a>
<?php if (!empty($wpem_field['description'])) : ?>
    <small class="description">
        <?php echo wp_kses_post($wpem_field['description']); ?>
    </small>
<?php endif; ?>