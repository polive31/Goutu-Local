<?php
/*
General manager for debug tags
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//*************************************************************************
//**               INITIALIZATION
//*************************************************************************


class DBG {
		
	private static $ON = true;

	public static function log($msg, $var=false) {
		if (!class_exists( 'PC' )) return;
		if (!self::$ON) return;		
		if ($var==false)
			PC::debug($msg);
		else 	
			PC::debug(array($msg=>$var));
	}
	
}
