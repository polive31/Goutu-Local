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

	const PROFILE_FIELDS = array(
		'blog_title'	=> 'Titre de votre Blog',
	);


	const TABS=array(
		'blogposts',
		'about',
		'friends',
		'groups',
		'photos',
		'media',
	);


	static function get_nav_tab() {
		$current='stream';
		foreach (self::TABS as $tab) {
			$match=strpos( $_SERVER['REQUEST_URI'], '/' . $tab );
			if ($match) {
				$current=$tab;
				break;
			}
		}
		return $current;
	}


	static function get_user( $user_type ) {
		$user_id=false;

		switch ( $user_type ) {
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
				$user_id = $user_type;
				break;
		}
		$user = $user_id?PeepsoUser::get_instance( $user_id ):false;

		return $user;
	}

	static function get_field( $user, $field ) {
		switch ($field) {
			case "pseudo" :
				$html=$user->get_nicename();
				break;
			case "firstname" :
				$html=$user->get_firstname();
				break;
			case "lastname" :
				$html=$user->get_lastname();
				break;
			case "nicename" :
				$html=$user->get_nicename();
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

	static function get_url( $user, $page='profile', $subpage='' ) {
		switch ( $page ) {
			case 'archive':
				$url = get_site_url();
				if ( !empty($subpage) )
					$url = add_query_arg( 'post_type', $subpage, $url);
				$url = add_query_arg( 'author_name', $user->get_nicename(), $url);
				break;

			case 'profile':
				$url = $user->get_profileurl();
				$url .= $subpage;
				break;

		}
		return $url;
	}

	static function get_avatar( $args ) {
		$user='current';
		$aclass='';
		$wraptag='';
		$wrapclass='';
		$link='profile';
		$size='full';
		$title='';
		extract( $args );

		$user = self::get_user( $user );
		if (!$user) return;

		$html = '<img class="avatar" src="' . $user->get_avatar( $size ) . '" alt="' . sprintf( __('Picture of %s','foodiepro') , ucfirst($user->get_nicename()) ) . '">';

		if ( !empty($link) ) {
			$html = '<a class="' . $aclass . '" href="' . self::get_url($user, 'profile') . '" title="' . sprintf( $title , ucfirst($user->get_nicename()) ) . '">' . $html . '</a>';
		}

		if ( !empty($wraptag) ) {
			$html = '<' . $wraptag . ' class="' . $wrapclass . '">' . $html . '</' . $wraptag . '>';
		}

		return $html;
	}

	static function get_profile_field( $fields, $handle ) {
		$value = false;
		foreach ($fields as $key=>$field) {
			if ($field->title==self::PROFILE_FIELDS[$handle] && !empty($field->value)) {
				$value = $field->render(false);
				break;
			}
		}
		return $value;
	}



}
