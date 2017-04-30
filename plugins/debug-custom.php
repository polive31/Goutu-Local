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
	
	public static function log($msg, $var=false ) { 
		if (!self::$ON) return;	
		if ( defined( 'DOING_AJAX' ) || strstr($_SERVER['REQUEST_URI'], 'admin-post.php') ) return; // Prevents blocking of post submissions/ajax requests
		if ( !is_array($var) ) {
			$var = '"' . $var . '"';
		}
		else {
			$var = json_encode($var);
		}
		echo('<script>console.log("%cDEBUG%c ' . $msg . '", "border-radius:4px;padding:2px 4px;background:blue;color:white", "color:blue");');  
		echo('console.log(' . $var . ');</script>');  
	}	

	public static function log2($msg, $var=false) {
		if (!class_exists( 'PC' )) return;
		if (!self::$ON) return;		
		if ($var==false)
			PC::debug($msg);
		else 	
			PC::debug(array($msg=>$var));
	}
	
}
