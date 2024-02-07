<div class="wpem-main wpem-single-event-widget">
	<div <?php event_listing_class(''); ?>>
		<a href="<?php the_permalink(); ?>" class="wpem-event-action-url event-widget">
			<div class="wpem-event-banner">
				<div class="wpem-event-banner-img"><?php display_event_banner(); ?></div>
			</div>
			<div class="wpem-event-infomation">
				<div class="wpem-event-details">
					<div class="wpem-event-title">
						<h3 class="wpem-heading-text" title="<?php the_title(); ?>"> <?php the_title(); ?></h3>
					</div>
					<div class="wpem-event-date-time">
						<span class="wpem-event-date-time-text"><?php display_event_start_date();?></span>
					</div>
					<div class="wpem-event-location">
						<span class="wpem-event-location-text">
							<?php if(get_event_location()=='Online Event'): esc_html_e('Online Event','wp-event-manager'); else:  display_event_location(false); endif; ?>
						</span>
					</div>                        
				    <div class="wpem-event-type"><?php display_event_type(); ?></div>

				    <?php if (get_event_ticket_option()) : ?>
				    <div class="wpem-event-ticket-type">
						<span class="wpem-event-ticket-type-text">
							<?php echo esc_attr('#').esc_html(get_event_ticket_option()); ?>
						</span>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</a>
	</div>
</div>
