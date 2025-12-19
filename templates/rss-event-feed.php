<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$location = wpem_get_event_location($post_id);
$event_type = wpem_get_event_type($post_id);
$ticket_price  = wpem_get_event_ticket_option($post_id);
$organizer  = wpem_get_organizer_name($post_id);
$start_date   = get_post_meta($post_id, '_event_start_date', true);
$end_date     = get_post_meta($post_id, '_event_end_date', true);

do_action('event_fee_item_start'); ?>
<item>
	<title><?php esc_attr(the_title_rss()); ?></title>
	<link><?php echo esc_url(get_permalink($post_id)); ?></link>
	<dc:creator><?php esc_attr(the_author()); ?></dc:creator>
	<pubDate><?php echo esc_html(mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false)); ?></pubDate>
	<guid isPermaLink="false"><?php esc_attr(the_guid()); ?></guid>
	<description><![CDATA[<?php echo wp_kses_post(get_the_excerpt() ?? ''); ?>]]></description>
	<content:encoded><![CDATA[<?php echo wp_kses_post(get_the_content_feed('rss2') ?? ''); ?>]]></content:encoded>
	<?php
	if($location) {
		echo "<event_listing:location><![CDATA[" . esc_html($location) . "]]></event_listing:location>\n";
	}

	if(isset($event_type->name)) {
		echo "<event_listing:event_type><![CDATA[" . esc_html($event_type->name) . "]]></event_listing:event_type>\n";
	}

	if($ticket_price){
		echo "<event_listing:ticket_price><![CDATA[" . esc_html($ticket_price) . "]]></event_listing:ticket_price>\n";
	}

	if($organizer) {
		echo "<event_listing:organizer><![CDATA[" . esc_html($organizer) . "]]></event_listing:organizer>\n";
	}

	if ($start_date) {
		echo "<event_listing:start_date><![CDATA[" . esc_html($start_date) . "]]></event_listing:start_date>\n";
	}

	if ($end_date) {
		echo "<event_listing:end_date><![CDATA[" . esc_html($end_date) . "]]></event_listing:end_date>\n";
	}

	do_action('event_fee_item_end');  ?>
</item> 