<?php
$location = get_event_location($post_id);
$event_type = get_event_type($post_id);
$ticket_price  = get_event_ticket_option($post_id);
$organizer  = get_organizer_name($post_id);

do_action('event_fee_item_start'); ?>
<item>
	<title><?php esc_attr(the_title_rss()); ?></title>
	<link><?php esc_url(the_permalink_rss()); ?></link>
	<dc:creator><?php esc_attr(the_author()); ?></dc:creator>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
	<guid isPermaLink="false"><?php esc_attr(the_guid()); ?></guid>
	<description><![CDATA[<?php wp_kses_post(the_excerpt_rss()); ?>]]></description>
	<content:encoded><![CDATA[<?php wp_kses_post(the_content_feed()); ?>]]></content:encoded>
	<?php
	if($location) {
		echo "<event_listing:location><![CDATA[" . esc_attr($location) . "]]></event_listing:location>\n";
	}

	if(isset($event_type->name)) {
		echo "<event_listing:event_type><![CDATA[" . esc_attr($event_type->name) . "]]></event_listing:event_type>\n";
	}

	if($ticket_price){
		echo "<event_listing:ticket_price><![CDATA[" . esc_attr($ticket_price) . "]]></event_listing:ticket_price>\n";
	}

	if($organizer) {
		echo "<event_listing:organizer><![CDATA[" . esc_attr($organizer) . "]]></event_listing:organizer>\n";
	}
	do_action('event_fee_item_end');  ?>
</item> 