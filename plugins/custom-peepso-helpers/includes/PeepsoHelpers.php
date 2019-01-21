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

class PeepsoHelpers {

	private static $TABS=array(
		'about',
		'friends',
		'groups',
		'photos',
		'media',
	);

	static function get_nav_tab() {
		$current='stream';
		foreach (self::$TABS as $tab) {
			$match=strpos( $_SERVER['REQUEST_URI'], '/' . $tab );
			if ($match) {
				$current=$tab;
				break;
			}
		}
		return $current;
	}

}
