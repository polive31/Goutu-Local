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

		$query = (!empty($instance['query'])) ? $instance['query'] : 'latest';
		$allow_empty = (isset($instance['allow_empty'])) ? !empty($instance['allow_empty']) && is_user_logged_in() : false;

		$mutual = (isset($instance['mutual'])) ? !empty($instance['mutual']) : true;
		$mutual = $mutual && is_user_logged_in();

		// $size = (!empty($instance['thumbnail']))? $instance['thumbnail']:'full';
		$size = 'small';
		$link = false;

		if ($query == 'latest') {

			if (!empty($instance['title'])) {
				echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
			}

			$query_args = array(
				'role__in'     => array('author', 'contributor'),
				'orderby'      => 'ID',
				'order'        => 'DESC',
				'number'       => $instance['limit'],
			);
			$users = get_users($query_args);
			$link = foodiepro_get_permalink(array(
				'community'	=> 'members',
				'text'	=>  __('All the members', 'foodiepro'),
			));
		} elseif ( foodiepro_contains($query, 'friends') ) {

			// Don't display widget if some conditions aren't met
			if ( !is_user_logged_in() && foodiepro_contains($query, 'current') )
				return;

			$display_params = $this->get_friends_display_params($query, $instance, $mutual);

			if ($display_params['count'] == 0) {
				if ($allow_empty ) {
					echo $args['before_title'] . apply_filters('widget_title', sprintf($instance['title'], $display_params['username'])) . $args['after_title'];
					echo '<p class="aligncenter">' . $display_params['nofriends'] . '</p>';
					return;
				}
				else
					return;
			}
			echo $args['before_title'] . apply_filters('widget_title', sprintf($instance['title'], $display_params['username'])) . $args['after_title'];

			$users = $display_params['users'];

		} else
			return;


?>
		<div class="ps-widget__members">
			<?php

			foreach ($users as $user) {
				if (isset($user->ID))
					$user_ID = $user->ID;
				elseif ( isset($user['friendID']) )
					$user_ID = $user['friendID'];
				elseif ( is_numeric($user))
					$user_ID=$user;
				else
					return '';


				$peepsoUser = PeepSoUser::get_instance($user_ID);


			?>
				<div class="ps-widget__members-item">
					<?= PeepsoHelpers::get_avatar(array(
						'user'		=> $peepsoUser,
						'imgclass'	=> 'ps-name-tips',
						'imgid'		=> 'square',
						'link'		=> 'profile',
						'size'		=> $size,
						'title'		=> ucfirst($peepsoUser->get_nicename()),
					)); ?>
				</div>
			<?php } ?>
		</div>
		<div class="clear"></div>
		<?php

		if ($link) { ?>
			<p class="more-from-category"><?= $link; ?></p>
		<?php }

		echo $args['after_widget'];
	}

	public function get_friends_display_params($query, &$instance, $mutual)
	{
		// Params template
		$params = array(
			'users'		=> array(),
			'username'	=> '',
			'morelink'	=> false,
			'nofriends'	=> false,
			'count'	=> 0,
		);
		if (!class_exists('PeepSoFriends')) return $params;

		$PeepSoFriends = PeepSoFriends::get_instance();

		/* Check if showing My Friends */
		if ( foodiepro_contains($query, 'current') || ( foodiepro_contains($query, 'auto') && ( PeepsoHelpers::is_current_user_profile() || is_front_page() ) ) && is_user_logged_in() ) {
			$owner_id = get_current_user_id();

			/* Morelink
			----------------------------------------*/
			$params['morelink'] = foodiepro_get_permalink(array(
				'user'		=> $owner_id,
				'display'	=> 'profile',
				'type'		=> 'friends',
				'text'		=>  __('All my friends', 'foodiepro'),
			));

			/* Username
			----------------------------------------*/
			$params['username'] = '';

			/* Title
			----------------------------------------*/
			if (empty($instance['title'])) {
				$instance['title'] = __('Friends of mine', 'foodiepro');
			}

			/* No friends label
			----------------------------------------*/
			$permalink_args = array(
				'peepso'	=> 'members',
				'text'		=> __('make new connections', 'foodiepro'),
			);
			$params['nofriends'] = sprintf(__('No friend yet, <u>%s</u>', 'foodiepro'), foodiepro_get_permalink($permalink_args));

			/* Friends count
			----------------------------------------*/
			$params['count'] = $PeepSoFriends->get_num_friends($owner_id);

			/* Users
			----------------------------------------*/
			$friendsModel = PeepSoFriendsModel::get_instance();
			$search_args = array(
				'number' => $instance['limit'],
			);
			$params['users'] = $friendsModel->get_friends($owner_id, $search_args);

		} else { // viewed or author, nearly the same so treated together

			if ( foodiepro_contains($query, 'viewed|auto') &&  PeepSoProfileShortcode::get_instance()->get_view_user_id())
				$owner_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
			elseif ( preg_match('(author|auto)', $query) && ( is_single() || is_author() ) ) {
				$owner_id = get_the_author_meta('ID');
				if ( $owner_id==get_current_user_id() )
					return false;
			}
			else
				return false;
			if (!$owner_id) return false;

			/* Username
			----------------------------------------*/
			$user = PeepsoHelpers::get_user($owner_id);
			if (!is_object($user)) return false;
			$params['username'] = ucfirst(PeepsoHelpers::get_field($user, 'nicename'));

			/* Title
			----------------------------------------*/
			if (empty($instance['title'])) {
				$instance['title'] = $mutual ? __('Mutual Friends with %s', 'foodiepro') : __('%s\'s Friends', 'foodiepro');
			}

			/* Morelink
			----------------------------------------*/
			$text = $mutual ? __('All %s\'s friends', 'foodiepro') : __('All mutual friends', 'foodiepro');

			if (is_user_logged_in()) {
				$params['morelink'] = foodiepro_get_permalink(array(
					'user'		=> $owner_id,
					'display'	=> 'profile',
					'type'		=> 'friends',
					'text'		=>  $text,
				));
			}

			/* No friends label
			----------------------------------------*/
			$permalink_args = array(
				'community'	=> 'members',
				'text'		=> __('make new connections', 'foodiepro'),
			);
			if ($mutual)
				$params['nofriends'] = sprintf(__('No mutual friends yet with %s, <u>%s</u>', 'foodiepro'), $params['username'], foodiepro_get_permalink($permalink_args));
			else
				$params['nofriends'] = sprintf(__('%s doesn\'t have any friend yet', 'foodiepro'), $params['username']);

			/* Users
			----------------------------------------*/
			$friendsModel = PeepSoFriendsModel::get_instance();
			$search_args = array(
				'number' => $instance['limit'],
			);
			if ($mutual)
				$params['users'] = $friendsModel->get_mutual_friends(get_current_user_id(), $owner_id, $search_args);
			else
				$params['users'] = $friendsModel->get_friends($owner_id, $search_args);

			/* Friends count
			----------------------------------------*/
			// $params['count'] = $PeepSoFriends->get_num_friends($owner_id);
			$params['count'] = count($params['users']);
		}

		return $params;
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

		if (isset($instance['mutual']))
			$mutual = $instance['mutual'];
		else
			$mutual = 'on';

		if (isset($instance['allow_empty']))
			$allow_empty = $instance['allow_empty'];
		else
			$allow_empty = '';

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
				<option value="friends-auto" <?php selected($query, 'friends-auto'); ?>><?php _e('Friends (automatic)', 'foodiepro') ?></option>
				<option value="friends-current" <?php selected($query, 'friends-current'); ?>><?php _e('Friends (from logged-in user)', 'foodiepro') ?></option>
				<option value="friends-viewed" <?php selected($query, 'friends-viewed'); ?>><?php _e('Friends (from viewed member profile)', 'foodiepro') ?></option>
				<option value="friends-author" <?php selected($query, 'friends-author'); ?>><?php _e('Friends (from post author)', 'foodiepro') ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('allow_empty'); ?>"><?php _e('Display if no friends found ?', 'foodiepro'); ?></label>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('allow_empty'); ?>" name="<?php echo $this->get_field_name('allow_empty'); ?>" <?php checked($allow_empty, 'on'); ?>>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('mutual'); ?>"><?php _e('Limit to mutual friends ?', 'foodiepro'); ?></label>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('mutual'); ?>" name="<?php echo $this->get_field_name('mutual'); ?>" <?php checked($mutual, 'on'); ?>>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('thumbnail'); ?>">
				<?php _e('Thumbnail size', 'foodiepro'); ?>
			</label>
			<select class="widefat" id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name('thumbnail'); ?>" style="width:100%;">
				<?php
				$default_image_sizes = $this->get_image_sizes();
				foreach ($default_image_sizes as $name => $values) { ?>
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
		$instance['mutual'] = (!empty($new_instance['mutual'])) ? strip_tags($new_instance['mutual']) : '';
		$instance['allow_empty'] = (!empty($new_instance['allow_empty'])) ? strip_tags($new_instance['allow_empty']) : '';
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
