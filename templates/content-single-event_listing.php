<?php
global $post;
$start_date = get_event_start_date ();
$end_date = get_event_end_date ();
wp_enqueue_script('wp-event-manager-slick-script');
wp_enqueue_style( 'wp-event-manager-slick-style');
do_action('set_single_listing_view_count');
?>
<div class="single_event_listing" itemscope
	itemtype="http://schema.org/Event">
	<meta itemprop="title"
		content="<?php echo esc_attr( $post->post_title ); ?>" />

	<div class="wpem-main wpem-single-event-page">
		<?php if ( get_option( 'event_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
		<div class="event-manager-info wpem-alert wpem-alert-danger" ><?php _e( 'This listing has been expired.', 'wp-event-manager' ); ?></div>
		<?php else : ?>
			<?php if ( is_event_cancelled() ) : ?>
              <div class="wpem-alert wpem-alert-danger">
              	<span class="event-cancelled" itemprop="eventCancelled"><?php _e( 'This event has been cancelled', 'wp-event-manager' ); ?></span>
			  </div>	               
            <?php elseif ( ! attendees_can_apply() && 'preview' !== $post->post_status ) : ?>		       
               <div class="wpem-alert wpem-alert-danger">
               	<span class="listing-expired" itemprop="eventExpired"><?php _e( 'Registrations have closed', 'wp-event-manager' ); ?></span>
               </div>
	        <?php endif; ?>
		<?php
			/**
			 * single_event_listing_start hook
			 */
			do_action ( 'single_event_listing_start' );
			?>
		<div class="wpem-single-event-wrapper">
			<div class="wpem-single-event-header-top">
				<div class="wpem-row">
		
				 <div class="wpem-col-xs-12 wpem-col-sm-7 wpem-col-md-8 wpem-single-event-images">
				 <?php
			$event_banners = get_event_banner ();
			if (is_array ( $event_banners ) && sizeof ( $event_banners ) > 1) :
				?>
				 <div class="wpem-single-event-slider-wrapper">
							<div class="wpem-single-event-slider">
				 		<?php foreach( $event_banners as $banner_key => $banner_value ) :  ?>
				 			<div class="wpem-slider-items">
									<img src="<?php echo $banner_value;?>" alt="<?php the_title();?>" />
								</div>
				 		<?php endforeach;?>
				 	</div>
						</div>
				 <?php else : ?>
				 	<div class="wpem-event-single-image-wrapper">
							<div class="wpem-event-single-image"><?php display_event_banner();?></div>
						</div>
				 <?php endif;?>
				 </div>
					<div class="wpem-col-xs-12 wpem-col-md-4 wpem-single-event-short-info">
						<div class="wpem-event-date">
							<div class="wpem-event-date-type">
        					<?php if (isset($start_date) && isset($end_date) && $start_date != $end_date ) :  ?>
                          		<?php if(!empty($start_date)){ ?>
                          		<div class="wpem-from-date">
									<div class="wpem-date"><?php echo date_i18n('d',strtotime($start_date));?></div>
									<div class="wpem-month"><?php echo date_i18n('M',strtotime($start_date));?></div>
								</div>
								<?php } ?>
								<?php if(!empty($end_date)){ ?>
								<div class="wpem-to-date">
									<div class="wpem-date-separator">-</div>
									<div class="wpem-date"><?php echo date_i18n('d',strtotime($end_date));?></div>
									<div class="wpem-month"><?php echo date_i18n('M',strtotime($end_date));?></div>
								</div>
								<?php } ?>
								<?php else :?>
								<div class="wpem-from-date">
									<div class="wpem-date"><?php echo date_i18n('d',strtotime($start_date));?></div>
									<div class="wpem-month"><?php echo date_i18n('M',strtotime($start_date));?></div>
								</div>
        					<?php endif;?>
                        </div>
						</div>
						<div class="wpem-event-details">
							<div class="wpem-event-title">
								<h3 class="wpem-heading-text"><?php the_title();?></h3>
							</div>
							<div class="wpem-event-organizer">
								<div class="wpem-event-organizer-name">
									<?php do_action('single_event_organizer_name_start');?>
									<?php printf( __('by %s','wp-event-manager'),get_organizer_name() );?>
									<?php do_action('single_event_organizer_name_end');?>
								</div>
							</div>
							<?php 
							$view_count = get_post_views_count($post);
							if( $view_count ){  ?>
							<div class="clearfix">&nbsp;</div>
							<div><i class="wpem-icon-eye"></i> <?php printf(__('%d people viewed this event.','wp-event-manager'),$view_count); ?></div>
							<?php } ?>
							<?php if(get_event_ticket_price()){ ?>
								<div class="clearfix">&nbsp;</div>
								<div><i class="wpem-icon-ticket"></i> <?php display_event_ticket_price( '',  '', true, $post );?></div>
							<?php } ?>
							<?php if(get_event_ticket_option()){  ?>
							<div class="clearfix">&nbsp;</div>
							<div class="wpem-event-ticket-type"><span class="wpem-event-ticket-type-text"><?php display_event_ticket_option();?></span></div>
							<?php } ?>
							
						</div>
					</div>
				</div>
			</div>
			<div class="wpem-single-event-body">
				<div class="wpem-row">
					<div
						class="wpem-col-xs-12 wpem-col-sm-7 wpem-col-md-8 wpem-single-event-left-content">
               <?php do_action('single_event_overview_before');?>
              <div class="wpem-single-event-body-content">
               <?php do_action('single_event_overview_start');?>
               	<?php echo apply_filters( 'display_event_description', get_the_content() ); ?>
               <?php do_action('single_event_overview_end');?>
              </div>
               <?php do_action('single_event_overview_after');?>
            </div>
					<div
						class="wpem-col-xs-12 wpem-col-sm-5 wpem-col-md-4 wpem-single-event-right-content">
						<div class="wpem-single-event-body-sidebar">
							<?php do_action( 'single_event_listing_button_start' ); ?>
							
							<?php
						$date_format = WP_Event_Manager_Date_Time::get_event_manager_view_date_format ();
						$registration_end_date = get_event_registration_end_date ();
						$registration_end_date = WP_Event_Manager_Date_Time::date_parse_from_format ( $date_format, $registration_end_date );
	
						$registration_addon_form = apply_filters ( 'event_manager_registration_addon_form', true );
						$event_timezone = get_event_timezone ();
	
						// check if timezone settings is enabled as each event then set current time stamp according to the timezone
						// for eg. if each event selected then Berlin timezone will be different then current site timezone.
						if (WP_Event_Manager_Date_Time::get_event_manager_timezone_setting () == 'each_event')
							$current_timestamp = WP_Event_Manager_Date_Time::current_timestamp_from_event_timezone ( $event_timezone );
						else
							$current_timestamp = current_time ( 'timestamp' ); // If site wise timezone selected
	
						if (attendees_can_apply () && ((strtotime ( $registration_end_date ) > $current_timestamp) || empty ( $registration_end_date )) && $registration_addon_form)
							get_event_manager_template ( 'event-registration.php' );
						?>
				
							<?php do_action( 'single_event_listing_button_end' ); ?>
							
							<div class="wpem-single-event-sidebar-info">
									
					<?php do_action('single_event_sidebar_start');?>
					<div class="clearfix">&nbsp;</div>
				    <h3 class="wpem-heading-text"><?php _e('Date And Time','wp-event-manager')?></h3>
            <div class="wpem-event-date-time" >
	            <span class="wpem-event-date-time-text" itemprop="startDate" content="<?php echo $start_date;?>"><?php display_event_start_date();?> <?php if(get_event_start_time()){ display_date_time_separator(); ?> <?php display_event_start_time(); }?></span>
	            <br/>
	            <span class="wpem-event-date-time-text" itemprop="endDate" content="<?php echo $end_date;?>">
	            <?php if( get_event_start_date() != get_event_end_date() ) {  display_event_end_date(); } ?>
	            <?php if(get_event_end_time()){ display_date_time_separator() ?> <?php display_event_end_time(); } ?>
	            </span>
            </div>
            <div itemprop="location" itemscope itemtype="http://schema.org/Place">
                  <div class="clearfix">&nbsp;</div>
				  <h3 class="wpem-heading-text"><?php _e('Location','wp-event-manager');?></h3>
				  <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                  	<?php if(get_event_address()){ display_event_address(); echo ',';} ?> <?php display_event_location();?> 
              	  </div>
                  <?php if(get_event_venue_name()){ ?>
                  <div class="clearfix">&nbsp;</div>
				  <h3 class="wpem-heading-text"><?php _e('Venue','wp-event-manager');?></h3>
                  <?php  display_event_venue_name(); 
                  } ?> 
            </div>      
                  
                  <?php if(get_option( 'event_manager_enable_event_types' ) && get_event_type()) { ?>
                  <div class="clearfix">&nbsp;</div>
                  <h3 class="wpem-heading-text"><?php _e('Event Types','wp-event-manager');?></h3>
				  <div class="wpem-event-type"><?php  display_event_type();  ?></div>
            	<?php } ?>
            	
				<?php if(get_option( 'event_manager_enable_categories' ) && get_event_category()) { ?>
					<div class="clearfix">&nbsp;</div>
                   <h3 class="wpem-heading-text"><?php _e('Event Category','wp-event-manager');?></h3>
				   <div class="wpem-event-category"><?php display_event_category(); ?></div>
				   
            	<?php } ?>
		   <div class="clearfix">&nbsp;</div>
		   <!-- Event Registration End Date start-->
		   <?php if(get_event_registration_end_date()): ?>                                             
					<h3 class="wpem-heading-text"><?php _e('Registration End Date','wp-event-manager');?></h3>
					<?php display_event_registration_end_date();?>
		  <?php endif; ?>
		  <!-- Registration End Date End-->
		  <div class="clearfix">&nbsp;</div>
		  <?php if( get_organizer_youtube()){ ?>
		    <button id="event-youtube-button" data-modal-id="wpem-youtube-modal-popup" class="wpem-theme-button wpem-modal-button"><?php _e('Watch video','wp-event-manager');?></button>
		    <div id="wpem-youtube-modal-popup" class="wpem-modal" role="dialog" aria-labelledby="<?php _e('Watch video','wp-event-manager');?>">
        		<div class="wpem-modal-content-wrapper">
        			<div class="wpem-modal-header">
        				<div class="wpem-modal-header-title"><h3 class="wpem-modal-header-title-text"><?php _e('Watch video','wp-event-manager');?></h3></div>
        				<div class="wpem-modal-header-close"><a href="javascript:void(0)" class="wpem-modal-close" id="wpem-modal-close">x</a></div>
        			</div>
        			<div class="wpem-modal-content">
        			    <?php echo wp_oembed_get(get_organizer_youtube() , array( 'autoplay' => 1, 'rel' => 0) );?>
                    </div>
                </div>
                <a href="#"><div class="wpem-modal-overlay"></div></a>
            </div>
      		<div class="clearfix">&nbsp;</div>
	  		<?php } ?>			
		   <?php do_action('single_event_sidebar_end');?>
                </div>
				
                
							<div class="clearfix">&nbsp;</div>		
							<h3 class="wpem-heading-text"><?php _e('Share With Friends','wp-event-manager');?></h3>
							<div class="wpem-share-this-event">
								<div class="wpem-event-share-lists">
                    <?php do_action('single_event_listing_social_share_start');?> 
                    <div class="wpem-social-icon wpem-facebook">
										<a
											href="https://www.facebook.com/sharer/sharer.php?u=<?php display_event_permalink();?>"
											title="Share this page on Facebook">Facebook</a>
									</div>
									<div class="wpem-social-icon wpem-twitter">
										<a
											href="https://twitter.com/share?text=twitter&url=<?php display_event_permalink();?>"
											title="Share this page on Twitter">Twitter</a>
									</div>
									<div class="wpem-social-icon wpem-linkedin">
										<a
											href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source=<?php display_event_permalink();?>"
											title="Share this page on Linkedin">Linkedin</a>
									</div>
									<div class="wpem-social-icon wpem-xing">
										<a
											href="https://www.xing.com/spi/shares/new?url=<?php display_event_permalink();?>"
											title="Share this page on Xing">Xing</a>
									</div>
									<div class="wpem-social-icon wpem-pinterest">
										<a
											href="https://pinterest.com/pin/create/button/?url=<?php display_event_permalink();?>"
											title="Share this page on Pinterest">Pinterest</a>
									</div>
                    <?php do_action('single_event_listing_social_share_end');?>
                  </div>
							</div>
						</div>

					</div>
				</div>

			</div>    
         	  
	<?php

			get_event_manager_template_part( 'content', 'single-event_listing-organizer' );
			/**
			 * single_event_listing_end hook
			 */
			do_action ( 'single_event_listing_end' );
			?>
  <?php endif; ?>
			<!-- Main if condition end -->
		</div>
		<!-- / wpem-wrapper end  -->

	</div>
	<!-- / wpem-main end  -->
</div>
<!-- override the script if needed -->
<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery('.wpem-single-event-slider').slick({
      dots: true,
      infinite: true,
      speed: 500,
      fade: true,
      cssEase: 'linear',
      responsive: [{
	      breakpoint: 992,
	      settings: {
	        dots: true,
	        infinite: true,
	        speed: 500,
	        fade: true,
	        cssEase: 'linear',
	        adaptiveHeight: true
	      }
	    }]
    });

  });
</script>

