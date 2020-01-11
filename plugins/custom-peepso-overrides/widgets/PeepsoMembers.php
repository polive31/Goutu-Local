<?php

// Block direct requests
if (!defined('ABSPATH'))
	die('-1');



/* =================================================================*/
/* =                CONFIGURABLE MEMBERS WIDGET               =*/
/* =================================================================*/

add_action('widgets_init', function () {
	register_widget('CustomPeepsoMembers');
});

/**
 * Adds Configurable Members widget.
 */
class CustomPeepsoMembers extends WP_Widget
{

	/**
	 * Register widget with WordPress.
	 */
	function __construct()
	{
		parent::__construct(
			'custom_peepso_members', // Base ID
			__('(Peepso) Configurable Members Widget', 'foodiepro'), // Name
			array(
				'description' => __('Displays a list of members according to different criteria and markups', 'foodiepro'),
			) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance)
	{

		echo $args['before_widget'];
		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

		$query = (!empty($instance['query']))? $instance['query']:'latest';
		// $size = (!empty($instance['thumbnail']))? $instance['thumbnail']:'full';
		$size = 'full';

		if ($query=='latest') {
			$query_args = array(
				// 'blog_id'      => $GLOBALS['blog_id'],
				// 'role'         => '',
				'role__in'     => array('author', 'contributor'),
				// 'role__not_in' => array('administrator','editor','pending'),
				// 'meta_key'     => 'registered',
				// 'meta_value'   => '',
				// 'meta_compare' => '',
				// 'meta_query'   => array(),
				// 'date_query'   => array(),
				// 'include'      => array(),
				// 'exclude'      => array(),
				'orderby'      => 'ID',
				'order'        => 'DESC',
				// 'offset'       => '',
				// 'search'       => '',
				'number'       => $instance['limit'],
				// 'count_total'  => false,
				// 'fields'       => 'all',
				// 'who'          => '',
			);
			$users = get_users($query_args);
		}
		else {
			$users = array();
		}


		echo '<div class="ps-widget__members">';
		foreach ($users as $user) {
			echo '<div class="ps-widget__members-item">';
			$peepsoUser = PeepSoUser::get_instance($user->ID);
			echo '<a class="ps-avatar ps-avatar--' . $size . '" href="' . $peepsoUser->get_profileurl() . '" title="' . ucfirst($peepsoUser->get_nicename()) . '">';
			echo '<img class="ps-name-tips" id="square" src="' . $peepsoUser->get_avatar($size) . '" alt="' . $peepsoUser->get_nicename() . '">';
			echo '</a>';
			echo '</div>';
		}
		echo '</div>';

		echo '<div class="clear"></div>';

		if (is_user_logged_in()) {
			echo '<p class="more-from-category">' . do_shortcode('[permalink peepso="members" text="' . __('All the members', 'foodiepro') . '"]') . '</p>';
		}

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance)
	{

		if (isset($instance['title']))
			$title = $instance['title'];
		else
			$title = __('New title', 'text_domain');

		if (isset($instance['query']))
			$query = $instance['query'];
		else
			$query = 'latest';

		if (isset($instance['thumbnail']))
			$thumbnail = $instance['thumbnail'];
		else
			$thumbnail = 'thumbnail';

		if (isset($instance['limit']))
			$limit = $instance['limit'];
		else
			$limit = 8;

		// if (isset($instance['width']))
		// 	$width = $instance['width'];
		// else
		// 	$width = 80;

		// if (isset($instance['height']))
		// 	$height = $instance['height'];
		// else
		// 	$height = 80;

?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('query'); ?>">
				<?php _e('Query', 'foodiepro'); ?>
			</label>
			<select class="widefat" id="<?php echo $this->get_field_id('query'); ?>" name="<?php echo $this->get_field_name('query'); ?>" style="width:100%;">
				<option value="latest" <?php selected($query, 'latest'); ?>><?php _e('Latest Registered', 'foodiepro') ?></option>
				<option value="friends" <?php selected($query, 'friends'); ?>><?php _e('Friends', 'foodiepro') ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('thumbnail'); ?>">
				<?php _e('Thumbnail size', 'foodiepro'); ?>
			</label>
			<select class="widefat" id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name('thumbnail'); ?>" style="width:100%;">
			<?php
				$default_image_sizes = $this->get_image_sizes();
				foreach ( $default_image_sizes as $name => $values ) { ?>
					<option value="<?= $name; ?>" <?php selected($thumbnail, $name); ?>><?= $name . ' (' . $values['width'] . 'x' . $values['height'] . ')'; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>">
				<?php _e('Number of users to show', 'foodiepro'); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" step="1" min="-1" value="<?php echo (int) ($limit); ?>" />
		</p>

		<?php /*
		<p>
			<label for="<?php echo $this->get_field_id('width'); ?>">
				<?php _e('Thumbnail size (width,height)', 'foodiepro'); ?>
			</label>
			<div>
				<input class="small-input" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="number" step="1" min="0" value="<?php echo (int) ($width); ?>" />
				<input class="small-input" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="number" step="1" min="0" value="<?php echo (int) ($height); ?>" />
			</div>
		</p>
		*/ ?>

<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['limit'] = (!empty($new_instance['limit'])) ? strip_tags($new_instance['limit']) : '';
		$instance['query'] = (!empty($new_instance['query'])) ? strip_tags($new_instance['query']) : '';
		$instance['thumbnail'] = (!empty($new_instance['thumbnail'])) ? strip_tags($new_instance['thumbnail']) : '';
		// $instance['width'] = (!empty($new_instance['width'])) ? strip_tags($new_instance['width']) : '';
		// $instance['height'] = (!empty($new_instance['height'])) ? strip_tags($new_instance['height']) : '';

		return $instance;
	}

	/**
	 * Get information about available image sizes
	 */
	public function get_image_sizes($size = '')
	{
		$wp_additional_image_sizes = wp_get_additional_image_sizes();

		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop info
		foreach ($get_intermediate_image_sizes as $_size) {
			if (in_array($_size, array('thumbnail', 'medium', 'large'))) {
				$sizes[$_size]['width'] = get_option($_size . '_size_w');
				$sizes[$_size]['height'] = get_option($_size . '_size_h');
				$sizes[$_size]['crop'] = (bool) get_option($_size . '_crop');
			} elseif (isset($wp_additional_image_sizes[$_size])) {
				$sizes[$_size] = array(
					'width' => $wp_additional_image_sizes[$_size]['width'],
					'height' => $wp_additional_image_sizes[$_size]['height'],
					'crop' =>  $wp_additional_image_sizes[$_size]['crop']
				);
			}
		}

		// Get only 1 size if found
		if ($size) {
			if (isset($sizes[$size])) {
				return $sizes[$size];
			} else {
				return false;
			}
		}
		return $sizes;
	}
} // class CustomPeepsoMembers
