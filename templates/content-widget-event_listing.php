<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<!-- Events Display Widget-->
<div class="wpem-main wpem-single-event-widget <?php echo isset($widget_style) ? esc_attr($widget_style) : '';?>">
	<a href="<?php the_permalink(); ?>"
		class="wpem-event-action-url event-widget">
		<div class="wpem-event-banner">
			<div class="wpem-event-banner-img"><img src="<?php echo esc_url(wpem_get_event_thumbnail()); ?>" title="<?php echo esc_html(get_the_title()); ?>" /></div>
		</div>
		<div class="wpem-event-infomation">
			<div class="wpem-event-details">
				<div class="wpem-event-title">
					<h3 class="wpem-heading-text" title="<?php echo esc_html(get_the_title()); ?>">
						<?php echo esc_html(get_the_title()); ?>
					</h3>
				</div>				
				<div class="wpem-event-date-time">
					<span class="wpem-event-date-time-text"><?php wpem_display_event_start_date();?></span>
				</div>
				<div class="wpem-event-location">
					<span class="wpem-event-location-text"><?php if(wpem_get_event_location()=='Online Event'): echo esc_attr('Online Event','wp-event-manager'); else:  wpem_display_event_location(false); endif; ?></span>
				</div>				                
				<?php if (get_option('event_manager_enable_event_types')) : ?>      
					<div class="wpem-event-type">
						<?php wpem_display_event_type(); ?>
					</div>        
				<?php endif; 
				if (wpem_get_event_ticket_option()) : ?>
					<div class="wpem-event-ticket-type">
						<span class="wpem-event-ticket-type-text">
							<?php echo esc_attr('#').esc_html(wpem_get_event_ticket_option()); ?>
						</span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</a>
</div>