<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<div class="error">
	<p><?php echo wp_kses_post( $error ); ?></p>
</div>