<?php
/**
 * Event Submission Form
 */
if ( ! defined( 'ABSPATH' ) ) exit;

global $event_manager;
?>
<form action="<?php echo esc_url( $action ); ?>" method="post" id="submit-event-form" class="wpem-form-wrapper wpem-main event-manager-form" enctype="multipart/form-data">
	<?php if ( apply_filters( 'submit_event_form_show_signin', true ) ) : ?>
		<?php get_event_manager_template( 'account-signin.php' ); ?>
	<?php endif; ?>
	<?php if ( event_manager_user_can_post_event() || event_manager_user_can_edit_event( $event_id )   ) : ?>
		<!-- Event Information Fields -->
    	<h2 class="wpem-form-title wpem-heading-text"><?php _e( 'Event Details', 'wp-event-manager' ); ?></h2>
    <?php
	if ( isset( $resume_edit ) && $resume_edit ) {
		printf( '<p><strong>' . __( "You are editing an existing event. %s","wp-event-manager" ) . '</strong></p>', '<a href="?new=1&key=' . $resume_edit . '">' . __( 'Create A New Event','wp-event-manager' ) . '</a>' );
	}
	?>
	
		<?php do_action( 'submit_event_form_event_fields_start' ); ?>
		<?php foreach ( $event_fields as $key => $field ) : ?>
			<fieldset class="wpem-form-group fieldset-<?php esc_attr_e( $key ); ?>">
				<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . apply_filters( 'submit_event_form_required_label', $field['required'] ? '<span class="require-field">*</span>' : ' <small>' . __( '(optional)', 'wp-event-manager' ) . '</small>', $field ); ?></label>
				<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
					<?php get_event_manager_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
				</div>
			</fieldset>
		<?php endforeach; ?>
		<?php do_action( 'submit_event_form_event_fields_end' ); ?>

		<!-- Organizer Information Fields -->
		<?php if ( $organizer_fields ) : ?>
			<h2 class="wpem-form-title wpem-heading-text"><?php _e( 'Organizer Details', 'wp-event-manager' ); ?></h2>
			<?php do_action( 'submit_event_form_organizer_fields_start' ); ?>
			<?php foreach ( $organizer_fields as $key => $field ) : ?>
				<fieldset class="wpem-form-group fieldset-<?php esc_attr_e( $key ); ?>">
					<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . apply_filters( 'submit_event_form_required_label', $field['required'] ?'<span class="require-field">*</span>' : ' <small>' . __( '(optional)', 'wp-event-manager' ) . '</small>', $field ); ?></label>
					<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
						<?php get_event_manager_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
					</div>
				</fieldset>
			<?php endforeach; ?>
			<?php do_action( 'submit_event_form_organizer_fields_end' ); ?>
		<?php endif; ?>
		<div class="wpem-form-footer">
			<input type="hidden" name="event_manager_form" value="<?php echo $form; ?>" />
			<input type="hidden" name="event_id" value="<?php echo esc_attr( $event_id ); ?>" />
			<input type="hidden" name="step" value="<?php echo esc_attr( $step ); ?>" />
			<input type="submit" name="submit_event" class="btn" value="<?php esc_attr_e( $submit_button_text ); ?>" />
		</div>
	<?php else : ?>
	
	  <?php do_action( 'submit_event_form_disabled' ); ?>
	  
	<?php endif; ?>
</form>