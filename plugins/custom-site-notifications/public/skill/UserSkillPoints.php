<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class UserSkillPoints {

	protected $points;

	public function __construct() {
		add_action( 'updated_user_meta', 'increment_author_level', 10, 4 );
	}

	public function hydrate() {
	}


}
