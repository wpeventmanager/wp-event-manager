<p>
    <?php esc_html_e('To register for this event please visit the following URL:', 'wp-event-manager'); ?> 
    <a href="<?php echo esc_url($register->url); ?>" target="_blank" rel="nofollow">
        <?php echo esc_url($register->url); ?> &rarr;
    </a>
</p>