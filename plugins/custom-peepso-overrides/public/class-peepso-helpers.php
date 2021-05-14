<?php

/* CustomPeepso class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Static functions to be used in the different locations requiring Peepso-dependent data :
	- Templates overrides
	- Actions & filters...
*/

class PeepsoHelpers  {

	const MODULE_ID = 1;// Blogposts module ID in Peepso


	/**
	 * get_avatar
	 *
	 * @param  mixed $args array
	 * * user => 'current', 'view, 'author', <user_id> or PeepsoUser object
	 * * aclass => link class
	 * * imgclass => image class
	 * * wraptag => wrapper html tag
	 * * wrapclass => wrapper css class
	 * * link => 'profile', ''
	 * * size => 'small', 'medium', 'full' or integer size in pixels
	 * * title => link title
	 * * (bool) lazy
	 * @return string formatted html avatar
	 */
	static function get_avatar($args)
	{
		$user = 'current';
		$aclass = '';
		$imgclass = '';
		$imgid = '';
		$wraptag = '';
		$wrapclass = '';
		$link = true;
		$size = 'small';
		$lazy = true;
		// $title= __('by %s', 'foodiepro');
		$title = '%s';
		extract($args);

		$suffix = CPO_Assets::get_avatar_suffix( $size );

		if (!is_object($user))
			$user = self::get_user($user);

		if (is_object($user)) {
			$src = $user->get_avatar( $suffix );
			$dir = $user->get_image_dir();
			$alt = sprintf(__('Picture of %s', 'foodiepro'), $user->get_nicename());
		} else {
			$src = CHILD_THEME_URL . '/images/social/avatars/user-neutral-thumb.png';
			$dir = CHILD_THEME_PATH . '/images/social/avatars/';
			$alt = __('User picture', 'foodiepro');
			$link = false;
		}

		$html = foodiepro_get_picture(array(
			'src' 		=> $src,
			'dir' 		=> $dir,
			'class'		=> $imgclass . ' ' . $size,
			'id'		=> $imgid,
			'alt' 		=> $alt,
			'width' 	=> $size,
			'height'	=> $size,
			'lazy'		=> $lazy,
		));

		if ($link) {
			$html = '<a class="' . $aclass . ' ' . $size . '" href="' . foodiepro_get_permalink(array('user' => $user->get_id(), 'display' => 'profile')) . '" title="' . sprintf($title, ucfirst($user->get_nicename())) . '">' . $html . '</a>';
		}

		if (!empty($wraptag)) {
			$html = '<' . $wraptag . ' class="' . $wrapclass . '">' . $html . '</' . $wraptag . '>';
		}

		return $html;
	}


	/**
	 * current_nav_tab
	 *
	 * @return void
	 */
	public static function current_nav_tab()
	{
		$user_id = get_current_user_id();
		$links = array('_user_id' => $user_id);
		$links = apply_filters('peepso_navigation_profile', $links);

		$current = 'stream';
		// foreach (self::TABS as $tab) {
		foreach ($links as $tab => $params) {
			$slug=!empty($params['href'])? $params['href']:$tab;
			$match = foodiepro_contains($_SERVER['REQUEST_URI'], '/' . $slug);
			if ($match) {
				$current = $tab;
				break;
			}
		}
		return $current;
	}

	/**
	 * is_current_user_profile
	 *
	 * @return void
	 */
	public static function is_current_user_profile() {
		if (!is_user_logged_in()) return false;

		$current_url = home_url($_SERVER['REQUEST_URI']);
		$messages_url = Peepso::get_page('messages');

		if ( foodiepro_contains($current_url, $messages_url) )
			return true;

		$user=self::get_user('current');
		$profile_url=$user->get_profileurl();
		$is_profile =  foodiepro_contains($current_url, $profile_url);

		return $is_profile;
	}

	/**
	 * get_nav_url
	 *
	 * Provides the Peepso page url corresponding to a specific user and nav tab
	 *
	 * @param  WP_User $user
	 * @param  string $nav_id identifier of the expected nav tab
	 * @param  string $nav_slug link of the corresponding nav tab
	 * @return void
	 */
	public static function get_nav_url($user, $nav_id, $nav_slug) {
		if (!is_user_logged_in()) return get_home_url();
		if (!is_object($user)) return get_home_url();

		if ($nav_id == 'messages')
			$url = Peepso::get_page('messages');
		else
			$url = $user->get_profileurl() . $nav_slug;

		if ('http' != substr($url, 0, 4)) {
			$url = $user->get_profileurl();
		}

		return $url;
	}

	/**
	 * get_user
	 *
	 * Returns the PeepsoUser corresponding to the specified user type (current, viewed, author) or id
	 *
	 * @param  mixed $user_type_or_id
	 * *
	 * * 'current'
	 * * 'view'
	 * * 'author'
	 * * <user_id>
	 * @return PeepsoUser
	 */
	static function get_user( $user_type_or_id ) {
		$user_id=false;

		switch ($user_type_or_id ) {
			case 'current':
				$user_id = get_current_user_id();
				break;
			case 'view':
				$user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
				break;
			case 'author':
				$user_id = get_the_author_meta('ID');
				break;
			default :
				$user_id = $user_type_or_id;
				break;
		}
		$user = $user_id?PeepsoUser::get_instance( $user_id ):false;

		return $user;
	}

	static function get_field( $user, $field ) {
		switch ($field) {
			case "pseudo" :
				// username is now randomized and kept secret for security reasons
				$html=$user->get_nicename();
				break;
			case "nicename" :
				$html=$user->get_nicename();
				break;
			case "firstname" :
				$html=$user->get_firstname();
				break;
			case "lastname" :
				$html=$user->get_lastname();
				break;
			case "fullname" :
				$html=$user->get_fullname();
				break;
			default :
				$html='';
				break;
		}
		return $html;
	}



	static function get_profile_field( $user_type_or_id, $handle ) {

		$user = is_object($user_type_or_id)?$user_type_or_id:self::get_user( $user_type_or_id );

		$field_args = array('post_status' => 'publish');
		if (empty($field_args)) return;

		$user->profile_fields->load_fields($field_args);
		$fields = $user->profile_fields->get_fields();

		$value = false;
		foreach ($fields as $key=>$field) {
			if ($field->title==CPO_Assets::get_profile_field($handle) && !empty($field->value)) {
				$value = $field->render(false);
				break;
			}
		}
		return $value;
	}


	public static function send_notification($action, $from_id, $to_id, $post_id) {
        $note = new PeepSoNotifications();
        $post = get_post($post_id);

        $msg = CPM_Assets::get_label($post->post_type, 'not_' . $action);
        $msg = sprintf( $msg, $post->post_title);

		$peepso_action = 'foodiepro_' . $action . '_' . $post->post_type;
		$peepso_module_id = self::MODULE_ID;

		// IMPORTANT : do not give value any  to $post_id & $act_id,they must remain to 0 in order for the notification to be shown
		// Otherwise it'll be filtered-out
        $note->add_notification($from_id, $to_id, $msg, $peepso_action, $peepso_module_id);
    }



}
