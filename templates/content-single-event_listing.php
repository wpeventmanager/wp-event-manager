<?php global $post;
      $start_date = get_event_start_date();
      $end_date   = get_event_end_date();
      ?>
<div class="single_event_listing" itemscope itemtype="http://schema.org/EventPosting">
	<meta itemprop="title" content="<?php echo esc_attr( $post->post_title ); ?>" />
	<div class="wpem-main wpem-single-event-page">
		<?php if ( get_option( 'event_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
		<div class="event-manager-info"><?php _e( 'This listing has been expired.', 'wp-event-manager' ); ?></div>
		<?php else : ?>
		<?php
			/**
			 * single_event_listing_start hook
			 */
			do_action( 'single_event_listing_start' );
		?>
		<div class="wpem-single-event-wrapper">
		<div class="wpem-single-event-header-top">
			<div class="wpem-row">
				<?php $event_banners = get_event_banner();?>
				 <div class="wpem-col-xs-12 wpem-col-sm-7 wpem-col-md-8 wpem-single-event-images">
				 <?php if(is_array($event_banners) && sizeof($event_banners) > 1 ) : ?>
				 <div class="wpem-single-event-slider-wrapper">
				 	<div class="wpem-single-event-slider">
				 		<?php foreach( $event_banners as $banner_key => $banner_value ) :  ?>
				 			<div class="wpem-slider-items">
				 			<img src="<?php echo $banner_value;?>" alt="<?php echo esc_attr(get_organizer_name());?>" />
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
        					<?php if (isset($start_date) && isset($end_date)) :  ?>
                          <div class="wpem-from-date">
                            <div class="wpem-date"><?php echo date_i18n('d',strtotime($start_date));?></div>
                            <div class="wpem-month"><?php echo date_i18n('M',strtotime($start_date));?></div>
                          </div>
                          <div class="wpem-to-date">
                            <div class="wpem-date-separator">-</div>
                            <div class="wpem-date"><?php echo date_i18n('d',strtotime($end_date));?></div>
                            <div class="wpem-month"><?php echo date_i18n('M',strtotime($end_date));?></div>
                          </div>
        					<?php endif;?>
                        </div>
                     </div>
                     <div class="wpem-event-details">
                        <div class="wpem-event-title"><h3 class="wpem-heading-text"><?php the_title();?></h3></div>
                        <div class="wpem-event-organizer"><div class="wpem-event-organizer-name"><a><?php printf( __('by %s','wp-event-manager'),get_organizer_name() );?></a></div></div>
                        <div class="wpem-event-ticket"><span class="wpem-event-ticket-text"><?php display_event_ticket_price( '',  '', true, $post );?></span></div>
                      </div>
				 </div>
			</div>
		</div>
		<div class="wpem-single-event-body">  
        <div class="wpem-row">
            <div class="wpem-col-xs-12 wpem-col-sm-7 wpem-col-md-8 wpem-single-event-left-content">
               <?php do_action('single_event_overview_before');?>
              <div class="wpem-single-event-body-content">
               <?php do_action('single_event_overview_start');?>
               	<?php echo apply_filters( 'display_event_description', get_the_content() ); ?>
               <?php do_action('single_event_overview_end');?>
              </div>
               <?php do_action('single_event_overview_after');?>
            </div>
            
            <div class="wpem-col-xs-12 wpem-col-sm-5 wpem-col-md-4 wpem-single-event-right-content">
              <div class="wpem-single-event-body-sidebar">
				
                <div class="wpem-single-event-sidebar-info">
                  
                  <h3 class="wpem-heading-text"><?php _e('Date And Time','wp-event-manager')?></h3>
                  <?php printf( __('%s'),$start_date);?> – <?php printf(__('%s','wp-event-manager'),$end_date);?>
                  <div class="clearfix">&nbsp;</div>

                  <h3 class="wpem-heading-text"><?php _e('Location','wp-event-manager');?></h3>
                  <?php display_event_location();?>
                  <div class="clearfix">&nbsp;</div>
                  
                </div>
				<?php do_action('single_event_sidebar_start');?>
                <?php
			        $date_format = WP_Event_Manager_Date_Time::get_event_manager_view_date_format();
			        $registration_end_date = get_event_registration_end_date();
			        $registration_end_date = WP_Event_Manager_Date_Time::date_parse_from_format($date_format, $registration_end_date);
			        
			        $registration_addon_form = apply_filters('event_manager_registration_addon_form',true);
			        $event_timezone = get_event_timezone();
			        
			        //check if timezone settings is enabled as each event then set current time stamp according to the timezone
			        // for eg. if each event selected then Berlin timezone will be different then current site timezone.
			        if( WP_Event_Manager_Date_Time::get_event_manager_timezone_setting() == 'each_event'  )
			        	$current_timestamp = WP_Event_Manager_Date_Time::current_timestamp_from_event_timezone( $event_timezone );
			        else
			        	$current_timestamp = current_time( 'timestamp' ); // If site wise timezone selected
			      
			        if ( attendees_can_apply()  &&  ((strtotime($registration_end_date) >  $current_timestamp) || empty($registration_end_date)) && $registration_addon_form)
			        	get_event_manager_template( 'event-registration.php' );
			     ?>
			     
			       
                      
			     <?php do_action('single_event_sidebar_end');?>
		     
		         <!-- Event Registration End Date start-->
                 <?php if(get_event_registration_end_date()): ?>                                             
                     	<h3 class="wpem-heading-text"><?php _e('Registration End Date','wp-event-manager');?></h3>
                        <?php display_event_registration_end_date();?>
                 <?php endif; ?>
                 <!-- Registration End Date End-->
                <div class="clearfix">&nbsp;</div>
                <h3 class="wpem-heading-text">Share With Friends</h3>
				
				 
				 <div class="wpem-share-this-event">
                  <div class="wpem-event-share-lists">
                    <?php do_action('single_event_listing_social_share_start');?> 
                    <div class="wpem-social-icon wpem-facebook"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php display_event_permalink();?>" title="Share this page on Facebook">Facebook</a></div>
                    <div class="wpem-social-icon wpem-twitter"><a href="https://twitter.com/share?text=twitter&url=<?php display_event_permalink();?>" title="Share this page on Twitter">Twitter</a></div>
                    <div class="wpem-social-icon wpem-linkedin"><a href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source=<?php display_event_permalink();?>" title="Share this page on Linkedin">Linkedin</a></div>
                    <div class="wpem-social-icon wpem-xing"><a href="https://www.xing.com/spi/shares/new?url=<?php display_event_permalink();?>" title="Share this page on Xing">Xing</a></div>
                    <div class="wpem-social-icon wpem-pinterest"><a href="https://pinterest.com/pin/create/button/?url=<?php display_event_permalink();?>" title="Share this page on Pinterest">Pinterest</a></div>
                    <?php do_action('single_event_listing_social_share_end');?>
                  </div>
                </div>
              </div>

            </div>
          </div>
          
         </div>    
         	  
	<?php  get_event_manager_template_part( 'content', 'single-event_listing-organizer' ); 	
    /**
     * single_event_listing_end hook
     */
    	do_action( 'single_event_listing_end' );
    ?>
  <?php endif; ?><!-- Main if condition end -->
	  </div> <!-- / wpem-wrapper end  --> 

  </div> <!-- / wpem-main end  --> 
</div>
