<?php
wp_register_script( 'wp-event-manager-zoomquestions', EVENT_MANAGER_PLUGIN_URL . '/assets/js/zoomquestions.min.js', array( 'jquery', 'chosen' ), EVENT_MANAGER_VERSION, true );
wp_enqueue_script('wp-event-manager-zoomquestions'); 
$event_zoom_id = !empty($_REQUEST['event_zoom_id']) ? absint($_REQUEST['event_zoom_id']) : 0;
$event_zoom = get_post($event_zoom_id);
$post_id = $event_zoom->ID;
$meeting_questions = get_post_meta($post_id,'_custom_questions',true);
global $post_id;
if(!isset($field['value']) || empty($field['value'])) {
    $field['value'] = esc_attr(get_post_meta($post_id, stripslashes($key), true));
}
if(!empty($field['name'])) {
    $name = $field['name'];
} else {
    $name = $key;
} 
if ( get_option( 'allow_questions' ) == '1' ){

?>
<p class="form-field">
<div class="form-field">
    <div id="meeting-questions-wrapper">
        <?php if (!empty($meeting_questions)) : 
            foreach ($meeting_questions as $index => $question) : ?>
                <div class="meeting-question">
                    <p>
                        <label><?php _e('Question Title', 'wp-event-manager-zoom'); ?>:</label>
                        <input type="text" name="custom_questions[<?php echo $index; ?>][title]" value="<?php echo esc_attr($question['title']); ?>" placeholder="<?php _e('Enter question title', 'wp-event-manager-zoom'); ?>">
                    </p>
                    <p>
                        <label><?php _e('Required', 'wp-event-manager-zoom'); ?>:</label>
                        <input type="checkbox" class="checkbox" name="custom_questions[<?php echo $index; ?>][required]" <?php checked(!empty($question['required'])); ?>>
                        <span class="description"><?php _e('Please check if this field is required', 'wp-event-manager-zoom'); ?></span>
                    </p>
                    <p>
                        <label><?php _e('Question Type', 'wp-event-manager-zoom'); ?>:</label>
                        <select name="custom_questions[<?php echo $index; ?>][type]" class="question-type-selector">
                            <option value="short" <?php selected($question['type'], 'short'); ?>><?php _e('Short', 'wp-event-manager-zoom'); ?></option>
                            <option value="single" <?php selected($question['type'], 'single'); ?>><?php _e('Single', 'wp-event-manager-zoom'); ?></option>
                        </select>
                    </p>
                    <div class="question-choices-wrapper" <?php echo $question['type'] == 'single' ? '' : 'style="display:none;"'; ?>>
                        <p><?php _e('Choices', 'wp-event-manager-zoom'); ?>:</p>
                        <?php if (!empty($question['answers']) && is_array($question['answers'])) : ?>
                            <?php foreach ($question['answers'] as $choice_index => $choice) : ?>
                                <p>
                                    <input type="text" name="custom_questions[<?php echo $index; ?>][answers][<?php echo $choice_index; ?>][choice]" value="<?php echo esc_attr($choice['choice']); ?>" placeholder="<?php _e('Enter choice', 'wp-event-manager-zoom'); ?>">
                                </p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <button type="button" class="add-choice button"><?php _e('Add Choice', 'wp-event-manager-zoom'); ?></button>
                    </div>
                </div>
            <?php endforeach;
        else : ?>
            <div class="meeting-question">
                <p>
                    <label><?php _e('Question Title', 'wp-event-manager-zoom'); ?>:</label>
                    <input type="text" name="custom_questions[0][title]" placeholder="<?php _e('Enter question title', 'wp-event-manager-zoom'); ?>">
                </p>
                <p>
                    <label><?php _e('Required', 'wp-event-manager-zoom'); ?>:</label>
                    <input type="checkbox" class="checkbox" name="custom_questions[0][required]">
                    <span class="description"><?php _e('Please check if this field is required', 'wp-event-manager-zoom'); ?></span>
                </p>
                <p>
                    <label><?php _e('Question Type', 'wp-event-manager-zoom'); ?>:</label>
                    <select name="custom_questions[0][type]" class="question-type-selector">
                        <option value="short"><?php _e('Short', 'wp-event-manager-zoom'); ?></option>
                        <option value="single"><?php _e('Single', 'wp-event-manager-zoom'); ?></option>
                    </select>
                </p>
                <div class="question-choices-wrapper" style="display:none;">
                    <p><?php _e('Choices', 'wp-event-manager-zoom'); ?>:</p>
                    <p>
                        <input type="text" name="custom_questions[0][answers][0][choice]" placeholder="<?php _e('Enter choice', 'wp-event-manager-zoom'); ?>">
                    </p>
                    <button type="button" class="add-choice wpem-theme-button"><?php _e('Add Choice', 'wp-event-manager-zoom'); ?></button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <button type="button" class="wpem-theme-button" id="add-question"><?php _e('Add Another Question', 'wp-event-manager-zoom'); ?></button>
        </div>
        </p>	
        <?php
}
?>