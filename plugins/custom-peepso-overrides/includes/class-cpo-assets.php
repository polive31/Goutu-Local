<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CPO_Assets  {

	const PROFILE_FIELDS = array(
		'blog_title'	=> 'Titre de votre Blog',
		'user_bio'	=> 'Ma Bio',
	);

	const AVATAR_SIZE = array(
		'small'		=> array(
			'px'		=> 64,
			'suffix'	=> '',
		),
		'medium'	=> array(
			'px'		=> 250,
			'suffix'	=> 'full',
		),
		'full'		=> array(
			'px'		=> false, // max size, must be the last element of the array !
			'suffix'	=> 'orig',
		)
	);

	public static function get_profile_field($slug) {
		if (isset(self::PROFILE_FIELDS[$slug]))
			$value = self::PROFILE_FIELDS[$slug];
		else
			$value = false;
		return $value;
	}

	public static function get_avatar_suffix( $slug )
	{
		if ( is_numeric( $slug ) ) {
			foreach ( self::AVATAR_SIZE as $key => $values ) {
				if ( $slug <= $values['px'] || !$values['px'] ) {
					$slug=$key;
					break;
				}
			}
		}
		if ( isset(self::AVATAR_SIZE[$slug]) )
			$value = self::AVATAR_SIZE[$slug]['suffix'];
		else
			$value = false;
		return $value;
	}

}
