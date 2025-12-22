<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Pagination - Show numbered pagination for the [events] shortcode
 */
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if($max_num_pages <= 1) {
	return;
}

// Calculate pages to output 
$wpem_end_size    = 3;
$wpem_mid_size    = 3;
$wpem_start_pages = range(1, $wpem_end_size);
$wpem_end_pages   = range($max_num_pages - $wpem_end_size + 1, $max_num_pages);
$wpem_mid_pages   = range($current_page - $wpem_mid_size, $current_page + $wpem_mid_size);
$pages       = array_intersect(range(1, $max_num_pages), array_merge($wpem_start_pages, $wpem_end_pages, $wpem_mid_pages));
$wpem_prev_page   = 0; ?>

<nav class="event-manager-pagination">
	<ul class="page-numbers">
		<?php if($current_page && $current_page > 1) : ?>
			<li><a href="#" data-page="<?php echo esc_attr($current_page - 1); ?>" class="page-numbers">&larr;</a></li>
		<?php endif; 
		
			foreach ($pages as $page) {

				if($wpem_prev_page != $page - 1) { ?>
					<li><span class="gap">...</span></li>
				<?php }

				if($current_page == $page) { ?>
					<li><span  data-page="<?php echo esc_attr($page);?>" class="page-numbers current"><?php echo esc_attr($page);?></span></li>
				<?php } else { ?>
					<li><a href="#" data-page="<?php echo esc_attr($page);?>" class="page-numbers"><?php echo esc_attr($page);?></a></li>
				<?php }

				$wpem_prev_page = $page;
			}
		if($current_page && $current_page < $max_num_pages) : ?>
			<li><a href="#" data-page="<?php echo esc_attr($current_page + 1); ?>" class="page-numbers">&rarr;</a></li>
		<?php endif; ?>
	</ul>
</nav>