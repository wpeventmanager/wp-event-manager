<div  class="line-layout">

<li  <?php event_listing_class(); ?> data-longitude="<?php echo esc_attr( $post->geolocation_lat ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_long ); ?>">
  <div class="event-info-row-listing"> 
	<a href="<?php display_event_permalink(); ?>">
	   <div class="row">
            <div class="col-md-1">
                <div class="organizer-logo">         
                    <?php  display_event_banner(); ?>                       
                </div>               
            </div>            
            <div class="col-md-4">                
                <div class="event-title">                   
                    <h4><?php echo esc_html( get_the_title() ); ?></h4>
                    <div class="boxes-view-listing-registered-code">
                        <?php do_action('event_already_registered_title');?>
                    </div>  
                </div>
                <div class="event-organizer-name">
                    <?php display_organizer_name( '<normal>', '<normal>' ); ?>
                </div>                
            </div>
            <div class="col-md-2">		        
    			   <div class="date">
                        <date><?php  echo get_event_start_date();?></date>    			        
    			   </div>       			  
	        </div>
	        <div class="col-md-3">		
		        <div class="event-location"><i class="glyphicon glyphicon-map-marker"></i>
		          <?php if(get_event_location()=='Anywhere' || get_event_location() == ''): echo __('Online Event','wp-event-manager'); else:  display_event_location(false); endif; ?>
		       </div>		
	        </div>
	        <div class="col-md-2">
                <div class="event-ticket"><?php echo '#'.get_event_ticket_option(); ?></div>            
            </div>
            <div class="col-md-3"> <?php if ( get_option( 'event_manager_enable_event_types' ) ) { display_event_type(); } ?></div>
        </div>
      </a> 
     </div>
   </li>
</div>

<!-- Box Layout -->
<a <?php event_listing_class(); ?> href="<?php display_event_permalink(); ?>">
 <div class="box-layout"> 
    <div class="event-img"><?php  display_event_banner(); ?></div>     
       <div class="boxes-view-box-registered-code">
                <?php do_action('event_already_registered_title');?>
        </div>      
    <div class="event-title">
        <?php the_title(); ?>
    </div>
    <div class="event-start-date"><?php display_event_start_date();?></div>   
    <div class="event-location">
	 <i class="glyphicon glyphicon-map-marker"></i>
     <?php if(get_event_location()=='Anywhere'): echo __('Online Event','wp-event-manager'); else:  display_event_location(false); endif; ?>
    </div>
    <div class="box-footer">
     <?php if ( get_option( 'event_manager_enable_event_types' ) ) {  ?>
      <div class=""> <?php display_event_type(); ?> </div>    
      <?php } ?>  
      <div class="event-ticket"><?php echo '#'.get_event_ticket_option(); ?></div>
    </div> 
 </div> 
</a>     
<!-- Box Layout end-->

<script> 
jQuery(document).ready(function($) 
{   
   ContentEventListing.init();
});</script>

