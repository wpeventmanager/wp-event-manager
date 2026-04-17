<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! isset( $GLOBALS['wpem_listing_instance_stack'] ) ) {
	$GLOBALS['wpem_listing_instance_stack'] = array();
}
$listing_instance_id = isset( $listing_instance_id ) && is_string( $listing_instance_id ) && '' !== $listing_instance_id
	? sanitize_key( $listing_instance_id )
	: sanitize_key( wp_unique_id( 'wpem-el-' ) );
array_push( $GLOBALS['wpem_listing_instance_stack'], $listing_instance_id );
wp_enqueue_script( 'wp-event-manager-content-event-listing' );
?>
<div class="wpem-event-listing-instance" data-wpem-listing-instance="<?php echo esc_attr( $listing_instance_id ); ?>">
<div class="wpem-main wpem-event-listings-header">
    <div class="wpem-row">
        <div class="wpem-col wpem-col-12 wpem-col-sm-6 wpem-col-md-6 wpem-col-lg-8">
            <div class="wpem-event-listing-header-title">
                <?php if ( isset( $title ) ) : ?>
                    <h2 class="wpem-heading-text"><?php
						// translators: %s is the title of the event.
						printf( esc_html( '%s', 'wp-event-manager' ), esc_attr( $title ) );
					?></h2>
                <?php endif; ?>
            </div>
        </div>
        <div class="wpem-col wpem-col-12 wpem-col-sm-6 wpem-col-md-6 wpem-col-lg-4">
            <div class="wpem-event-layout-action-wrapper">
                <div class="wpem-event-layout-action">
                    <?php if ( $layout_type == 'all' ) : ?>
                        <?php do_action( 'wpem_start_event_listing_layout_icon' ); ?>
                        <div class="wpem-event-layout-icon wpem-event-box-layout" title="<?php esc_attr_e( 'Events Box View', 'wp-event-manager' ); ?>"><i class="wpem-icon-stop2"></i></div>
                        <div class="wpem-event-layout-icon wpem-event-list-layout wpem-active-layout" title="<?php esc_attr_e( 'Events List View', 'wp-event-manager' ); ?>"><i class="wpem-icon-menu"></i></div>
                        <?php do_action( 'wpem_end_event_listing_layout_icon' ); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event listing view -->
<?php
if ( $layout_type == 'box' ) {
	$wpem_list_type_class = 'wpem-row wpem-event-listing-box-view';
} else {
	$wpem_list_type_class = 'wpem-event-listing-list-view';
}

$wpem_list_type_class = apply_filters( 'wpem_default_listing_layout_class', $wpem_list_type_class, $layout_type );

$wpem_html_before_event_list = get_option( 'enable_before_html' );
if ( $wpem_html_before_event_list ) {
	$wpem_html_content = get_option( 'event_content_html' );
	echo wp_kses_post( $wpem_html_content );
}
?>
<div class="event_listings_main">
    <div id="wpem-el-view-<?php echo esc_attr( $listing_instance_id ); ?>" class="wpem-main wpem-event-listings event_listings wpem-event-listing-view-root <?php echo esc_attr( $wpem_list_type_class ); ?>" data-id="<?php echo esc_attr( $layout_type ); ?>">
