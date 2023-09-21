<?php

/**
 * This file is used to create widget for WP Event Manager.
 *
 * These widget can be used to show recent events, upcomming events, featured events.
 */

if (!defined('ABSPATH')) exit;

/**
 * WP Manager Widget base
 */

class WP_Event_Manager_Widget extends WP_Widget
{

	public $widget_cssclass;

	public $widget_description;

	public $widget_id;

	public $widget_name;

	public $settings;

	/**
	 * Constructor
	 */

	public function __construct()
	{

		$this->register();
	}

	/**
	 * Register Widget
	 */

	public function register()
	{

		$widget_ops = array(

			'classname'   => $this->widget_cssclass,

			'description' => $this->widget_description
		);

		parent::__construct($this->widget_id, $this->widget_name, $widget_ops);

		add_action('save_post', array($this, 'flush_widget_cache'));

		add_action('deleted_post', array($this, 'flush_widget_cache'));

		add_action('switch_theme', array($this, 'flush_widget_cache'));
	}

	/**
	 * get_cached_widget function.
	 */

	function get_cached_widget($args)
	{

		$cache = wp_cache_get($this->widget_id, 'widget');

		if (!is_array($cache))

			$cache = array();

		if (isset($cache[$args['widget_id']])) {

			echo esc_html($cache[$args['widget_id']]);

			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 */

	public function cache_widget($args, $content)
	{

		$cache[$args['widget_id']] = $content;

		wp_cache_set($this->widget_id, $cache, 'widget');
	}

	/**
	 * Flush the cache
	 * @return [type]
	 */

	public function flush_widget_cache()
	{

		wp_cache_delete($this->widget_id, 'widget');
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */

	function update($new_instance, $old_instance)
	{

		$instance = $old_instance;

		if (!$this->settings)

			return $instance;

		foreach ($this->settings as $key => $setting) {

			$instance[$key] = sanitize_text_field($new_instance[$key]);
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */

	function form($instance)
	{

		if (!$this->settings)

			return;

		foreach ($this->settings as $key => $setting) {

			$value = isset($instance[$key]) ? $instance[$key] : $setting['std'];

			switch ($setting['type']) {

				case 'text':
?>
					<p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php echo esc_attr($setting['label']); ?></label>

						<input class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="text" value="<?php echo esc_attr($value); ?>" />
					</p>
				<?php
					break;
				case 'number':
				?><p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php echo esc_attr($setting['label']); ?></label>

						<input class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="number" step="<?php echo esc_attr($setting['step']); ?>" min="<?php echo esc_attr($setting['min']); ?>" max="<?php echo esc_attr($setting['max']); ?>" value="<?php echo esc_attr($value); ?>" />
					</p>
				<?php
					break;
				case 'select':
				?><p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php echo esc_attr($setting['label']); ?> </label>
						<select class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>">
							<?php
							if (isset($setting['options'])) {
								foreach ($setting['options'] as $option_key => $option_value) { ?>
									<option value="<?php echo wp_kses_post($option_key); ?>" <?php if ($option_key ==  $value) echo wp_kses_post('selected'); ?>><?php echo esc_html($option_value); ?></option>
							<?php
								}
							}
							?>
						</select>
					</p>
			<?php
					break;
			}
		}
	}
}

/**
 * Recent Events Widget
 */

class WP_Event_Manager_Widget_Recent_Events extends WP_Event_Manager_Widget
{

	/**
	 * Constructor
	 */

	public function __construct()
	{

		global $wp_post_types;

		$this->widget_cssclass    = 'event_manager widget_recent_events';

		$this->widget_description = __('Display a list of recent listings on your site, optionally matching a keyword and location.', 'wp-event-manager');

		$this->widget_id          = 'widget_recent_events';

		$this->widget_name        = sprintf(wp_kses('Recent %s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->name);

		$this->settings           = array(

			'title' => array(

				'type'  => 'text',

				'std'   => sprintf(wp_kses('Recent %s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->name),

				'label' => __('Title', 'wp-event-manager')
			),

			'keyword' => array(

				'type'  => 'text',

				'std'   => '',

				'label' => __('Keyword', 'wp-event-manager')
			),

			'location' => array(

				'type'  => 'text',

				'std'   => '',

				'label' => __('Location', 'wp-event-manager')
			),

			'number' => array(

				'type'  => 'number',

				'step'  => 1,

				'min'   => 1,

				'max'   => '',

				'std'   => 10,

				'label' => __('Number of listings to show', 'wp-event-manager')
			),
			'order' => array(

				'type'  => 'select',

				'std'   => 10,

				'label' => __('Order by', 'wp-event-manager'),
				'options' => array(
					'ASC' => __('Ascending (ASC)', 'wp-event-manager'),
					'DESC' => __('Descending  (DESC)', 'wp-event-manager')
				)
			)
		);

		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */

	public function widget($args, $instance)
	{

		if ($this->get_cached_widget($args)) {

			return;
		}

		ob_start();

		extract($args);

		if (!empty($instance['title']))
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		else
			$title = sprintf(wp_kses('Recent Events', 'wp-event-manager'));

		if (!empty($instance['number']))
			$number = absint($instance['number']);
		else
			$number = 4;

		$events   = get_event_listings(array(

			'search_location'   => isset($instance['location']) ? $instance['location'] : '',

			'search_keywords'   => isset($instance['keyword']) ? $instance['keyword'] : '',

			'posts_per_page'    => $number,

			'orderby'           => 'event_start_date',

			'order'             => isset($instance['order']) ? $instance['order'] : 'ASC',
		));

		if ($events->have_posts()) : ?>

			<?php echo wp_kses_post($before_widget); ?>

			<?php if ($title) echo wp_kses_post($before_title . $title . $after_title); ?>

			<ul class="event_listings">

				<?php while ($events->have_posts()) : $events->the_post(); ?>

					<?php get_event_manager_template_part('content-widget', 'event_listing'); ?>

				<?php endwhile; ?>

			</ul>

			<?php echo wp_kses_post($after_widget); ?>

		<?php else : ?>

			<?php get_event_manager_template_part('content-widget', 'no-events-found'); ?>

		<?php endif;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo wp_kses_post($content);

		$this->cache_widget($args, $content);
	}
}

/**
 * Featured Events Widget
 */

class WP_Event_Manager_Widget_Featured_Events extends WP_Event_Manager_Widget
{

	/**
	 * Constructor
	 */

	public function __construct()
	{

		global $wp_post_types;

		$this->widget_cssclass    = 'event_manager widget_featured_events';

		$this->widget_description = __('Display a list of featured listings on your site.', 'wp-event-manager');

		$this->widget_id          = 'widget_featured_events';

		$this->widget_name        = sprintf(wp_kses('Featured %s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->name);

		$this->settings           = array(

			'title' => array(

				'type'  => 'text',

				'std'   => sprintf(wp_kses('Featured %s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->name),

				'label' => __('Title', 'wp-event-manager')
			),

			'number' => array(

				'type'  => 'number',

				'step'  => 1,

				'min'   => 1,

				'max'   => '',

				'std'   => 10,

				'label' => __('Number of listings to show', 'wp-event-manager')
			),
			'order' => array(

				'type'  => 'select',

				'std'   => 10,

				'label' => __('Order by', 'wp-event-manager'),
				'options' => array(
					'ASC' => __('Ascending (ASC)', 'wp-event-manager'),
					'DESC' => __('Descending  (DESC)', 'wp-event-manager')
				)
			)

		);

		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */

	public function widget($args, $instance)
	{

		if ($this->get_cached_widget($args)) {
			return;
		}

		ob_start();

		extract($args);

		if (!empty($instance['title']))
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		else
			$title = sprintf(wp_kses('Featured Events', 'wp-event-manager'));

		if (!empty($instance['number']))
			$number = absint($instance['number']);
		else
			$number = 4;

		$featured_events   = get_event_listings(
			array(

				'posts_per_page'    => $number,

				'orderby'           => 'event_start_date',

				'order'             => isset($instance['order']) ? $instance['order'] : 'ASC',

				'featured'	    =>  true
			)
		);

		if ($featured_events->have_posts()) : ?>

			<?php echo wp_kses_post($before_widget); ?>

			<?php if ($title) echo wp_kses_post($before_title . $title . $after_title);   ?>

			<ul class="event_listings">

				<?php while ($featured_events->have_posts()) : $featured_events->the_post(); ?>

					<?php get_event_manager_template_part('content-widget', 'event_listing'); ?>

				<?php endwhile; ?>

			</ul>

			<?php echo wp_kses_post($after_widget); ?>

		<?php else : ?>

			<?php get_event_manager_template_part('content-widget', 'no-events-found'); ?>

		<?php endif;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo wp_kses_post($content);

		$this->cache_widget($args, $content);
	}
}

/** 
 * This widget display Upcoming Events.
 * @since : 1.0.0
 */

class WP_Event_Manager_Widget_Upcoming_Events extends WP_Event_Manager_Widget
{

	/**
	 * Sets up the widgets name etc
	 * Upcoming events Widget class.
	 * This class handles everything that needs to be handled with the widget:
	 * the settings, form, display, and update.  Nice!
	 */

	public function __construct()
	{

		global $wp_post_types;

		$this->widget_cssclass    = 'event_manager widget_upcoming_events';

		$this->widget_description = __('Display a list of upcoming listings on your site.', 'wp-event-manager');

		$this->widget_id          = 'widget_upcoming_events';

		$this->widget_name        = sprintf(wp_kses('Upcoming %s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->name);

		$this->settings           = array(

			'title' => array(

				'type'  => 'text',

				'std'   => sprintf(wp_kses('Upcoming %s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->name),

				'label' => __('Title', 'wp-event-manager')
			),

			'number' => array(

				'type'  => 'number',

				'step'  => 1,

				'min'   => 1,

				'max'   => '',

				'std'   => 10,

				'label' => __('Number of listings to show', 'wp-event-manager')
			),
			'order' => array(

				'type'  => 'select',

				'std'   => 10,

				'label' => __('Order', 'wp-event-manager'),
				'options' => array(
					'ASC' => __('Ascending (ASC)', 'wp-event-manager'),
					'DESC' => __('Descending  (DESC)', 'wp-event-manager')
				)
			),
			'orderby' => array(

				'type'  => 'select',

				'std'   => 1,

				'label' => __('Order by', 'wp-event-manager'),
				'options' => array(
					'title' => __('Title', 'wp-event-manager'),
					'ID' => __('ID', 'wp-event-manager'),
					'name' => __('Name', 'wp-event-manager'),
					'modified' => __('Modified', 'wp-event-manager'),
					'parent' => __('Parent', 'wp-event-manager'),
					'event_start_date' => __('Event Start Date', 'wp-event-manager'),
					'rand' => __('Random', 'wp-event-manager')
				)
			)
		);

		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */

	public function widget($args, $instance)

	{
		// 		if ( $this->get_cached_widget( $args ) ) 
		// 		{
		// 			return;
		// 		}

		ob_start();

		extract($args);

		if (!empty($instance['title']))
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		else
			$title = sprintf(wp_kses('Upcoming Events', 'wp-event-manager'));

		if (!empty($instance['number']))
			$number = absint($instance['number']);
		else
			$number = 4;

		//$today_date=date('Y-m-d g:i:s');
		$today_date = current_time('Y-m-d H:i:s');

		$args = array(
			'post_type'   => 'event_listing',
			'post_status' => 'publish',
			'posts_per_page'    => $number,
			'orderby'           => isset($instance['orderby']) ? $instance['orderby'] : 'event_start_date',
			'order'             => isset($instance['order']) ? $instance['order'] : 'ASC',

		);

		$args['meta_query'] = array(
			array(
				'key'     => '_event_start_date',
				'value'   => $today_date,
				'type'    => 'DATETIME',
				'compare' => '>'
			),
			array(
				'key'     => '_cancelled',
				'value'   => '1',
				'compare' => '!='
			),
		);

		if ('event_start_date' === $args['orderby']) {
			$args['orderby'] = 'meta_value';
			$args['meta_key'] = '_event_start_date';
			$args['meta_type'] = 'DATETIME';
		}

		$events = new WP_Query($args);

		if ($events->have_posts()) : ?>

			<?php echo wp_kses_post($before_widget); ?>

			<?php if ($title) echo wp_kses_post($before_title . $title . $after_title); ?>

			<div class="event_listings_class" id="event-manager-owl-carousel-slider-widget">

				<?php while ($events->have_posts()) : $events->the_post(); ?>

					<?php get_event_manager_template_part('content-widget', 'event_listing'); ?>

				<?php endwhile; ?>

			</div>

			<?php echo wp_kses_post($after_widget); ?>

		<?php else : ?>

			<?php get_event_manager_template_part('content-widget', 'no-events-found'); ?>

		<?php endif;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo wp_kses_post($content);

		// 		$this->cache_widget( $args, $content );
	}
} //end of widget class

/** 
 * This widget display Upcoming Events.
 * @since : 1.0.0
 */

class WP_Event_Manager_Widget_Past_Events extends WP_Event_Manager_Widget
{

	/**
	 * Sets up the widgets name etc
	 * Upcoming events Widget class.
	 * This class handles everything that needs to be handled with the widget:
	 * the settings, form, display, and update.  Nice!
	 */

	public function __construct()
	{

		global $wp_post_types;

		$this->widget_cssclass    = 'event_manager widget_past_events';

		$this->widget_description = __('Display a list of Past listings on your site.', 'wp-event-manager');

		$this->widget_id          = 'widget_past_events';

		$this->widget_name        = sprintf(wp_kses('Past %s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->name);

		$this->settings           = array(

			'title' => array(

				'type'  => 'text',

				'std'   => sprintf(wp_kses('Past %s', 'wp-event-manager'), $wp_post_types['event_listing']->labels->name),

				'label' => __('Title', 'wp-event-manager')
			),

			'number' => array(

				'type'  => 'number',

				'step'  => 1,

				'min'   => 1,

				'max'   => '',

				'std'   => 10,

				'label' => __('Number of listings to show', 'wp-event-manager')
			),
			'order' => array(

				'type'  => 'select',

				'std'   => 10,

				'label' => __('Order', 'wp-event-manager'),
				'options' => array(
					'ASC' => __('Ascending (ASC)', 'wp-event-manager'),
					'DESC' => __('Descending  (DESC)', 'wp-event-manager')
				)
			),
			'orderby' => array(

				'type'  => 'select',

				'std'   => 1,

				'label' => __('Order by', 'wp-event-manager'),
				'options' => array(
					'title' => __('Title', 'wp-event-manager'),
					'ID' => __('ID', 'wp-event-manager'),
					'name' => __('Name', 'wp-event-manager'),
					'modified' => __('Modified', 'wp-event-manager'),
					'parent' => __('Parent', 'wp-event-manager'),
					'event_start_date' => __('Event Start Date', 'wp-event-manager'),
					'rand' => __('Random', 'wp-event-manager')
				)
			)
		);

		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */

	public function widget($args, $instance)

	{
		// 		if ( $this->get_cached_widget( $args ) ) 
		// 		{
		// 			return;
		// 		}

		ob_start();

		extract($args);

		if (!empty($instance['title']))
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		else
			$title = sprintf(wp_kses('Past Events', 'wp-event-manager'));

		if (!empty($instance['number']))
			$number = absint($instance['number']);
		else
			$number = 4;

		//$today_date=date('Y-m-d g:i:s');
		$today_date = current_time('Y-m-d H:i:s');

		$args = array(
			'post_type'   => 'event_listing',
			'post_status' => 'publish',
			'posts_per_page'    => $number,
			'orderby'           => isset($instance['orderby']) ? $instance['orderby'] : 'event_start_date',
			'order'             => isset($instance['order']) ? $instance['order'] : 'ASC',

		);

		$args['meta_query'] = array(
			array(
				'key'     => '_event_start_date',
				'value'   => $today_date,
				'type'    => 'DATETIME',
				'compare' => '<'
			),
			array(
				'key'     => '_cancelled',
				'value'   => '1',
				'compare' => '!='
			),
		);

		if ('event_start_date' === $args['orderby']) {
			$args['orderby'] = 'meta_value';
			$args['meta_key'] = '_event_start_date';
			$args['meta_type'] = 'DATETIME';
		}

		$events = new WP_Query($args);

		if ($events->have_posts()) : ?>

			<?php echo wp_kses_post($before_widget); ?>

			<?php if ($title) echo wp_kses_post($before_title . $title . $after_title); ?>

			<div class="event_listings_class" id="event-manager-owl-carousel-slider-widget">

				<?php while ($events->have_posts()) : $events->the_post(); ?>

					<?php get_event_manager_template_part('content-widget', 'event_listing'); ?>

				<?php endwhile; ?>

			</div>

			<?php echo wp_kses_post($after_widget); ?>

		<?php else : ?>

			<?php get_event_manager_template_part('content-widget', 'no-events-found'); ?>

<?php endif;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo wp_kses_post($content);

		// 		$this->cache_widget( $args, $content );
	}
} //end of widget class

register_widget('WP_Event_Manager_Widget_Recent_Events');

register_widget('WP_Event_Manager_Widget_Featured_Events');

register_widget('WP_Event_Manager_Widget_Upcoming_Events');

register_widget('WP_Event_Manager_Widget_Past_Events');
