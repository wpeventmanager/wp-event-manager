<?php

$location = get_event_location( $post_id );
$event_type = get_event_type( $post_id );

$ticket_price  = get_event_ticket_option( $post_id );

$organizer  = get_organizer_name( $post_id );
do_action('event_fee_item_start');

if ( $location ) {

	echo "<event_listing:location><![CDATA[" . esc_html( $location ) . "]]></event_listing:location>\n";
}

if (  isset($event_type->name)  ) {
	
	echo "<event_listing:event_type><![CDATA[" . esc_html( $event_type->name ) . "]]></event_listing:event_type>\n";
}

if ( $ticket_price ) {

	echo "<event_listing:ticket_price><![CDATA[" . esc_html( $ticket_price ) . "]]></event_listing:ticket_price>\n";
}

if ( $organizer ) {

	echo "<event_listing:organizer><![CDATA[" . esc_html( $organizer ) . "]]></event_listing:organizer>\n";
}
do_action('event_fee_item_end');