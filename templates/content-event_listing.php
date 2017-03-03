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
                    <h4><?php the_title(); ?></h4>  
                    <div class="boxes-view-listing-registered-code">
                        <?php do_action('event_already_registered_title');?>
                    </div>  
                </div>
                <div class="event-organizer-name">
                    <?php display_organizer_name( '<normal>', '<normal>' ); ?>
                    <?php display_event_type(); ?>
                </div>                
            </div>
            <div class="col-md-2">		        
    			   <div class="date">
    			        <date><?php $timestamp = strtotime(get_event_start_date()); if($timestamp!= null): echo date("M j, Y",$timestamp); endif;?></date>
    			        <date><?php /* printf( __( '%s ago', 'wp-event-manager' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) ); */ ?></date>    			        
    			   </div>       			  
	        </div>
	        <div class="col-md-3">		
		        <div class="event-location"><i class="glyphicon glyphicon-map-marker"></i>
		          <?php if(get_event_location()=='Anywhere' || get_event_location() == ''): echo 'Online Event'; else:  display_event_location(false); endif; ?>
		       </div>		
	        </div>
	        <div class="col-md-2">
                <div class="event-ticket"><?php echo '#'.get_event_ticket_option(); ?></div>            
            </div>
        </div>
      </a> 
     </div>
   </li>
</div>

<!-- Box Layout -->
<a href="<?php display_event_permalink(); ?>">
 <div class="box-layout"> 
    <div class="event-img"><?php  display_event_banner(); ?></div>     
       <div class="boxes-view-box-registered-code">
                <?php do_action('event_already_registered_title');?>
        </div>      
    <div class="event-title">
        <?php the_title(); ?>
    </div>
    <div class="event-start-date"><?php $timestamp = strtotime(get_event_start_date()); if($timestamp!= null): echo date("M j, Y",$timestamp); endif;?></div>   
    <div class="event-location">
	 <i class="glyphicon glyphicon-map-marker"></i>
     <?php if(get_event_location()=='Anywhere'): echo 'Online Event'; else:  display_event_location(false); endif; ?>
    </div>
    <div class="box-footer">
      <div class="event-type"> <div><?php display_event_type(); ?></div> </div>      
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

