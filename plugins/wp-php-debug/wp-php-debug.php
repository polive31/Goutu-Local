<?php
/*
Plugin Name: WP PHP Debug
Plugin URI: http://goutu.org/wp-scripts-styles-manager
Description: custom enqueue, minimize, and concatenate your css & js code
Version: 1.0.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class PHP_Debug {
		
	const PHP_CONSOLE = false; // Outputs debug messages via WP_PHP_Console if it exists
	const DISP_TRACE = false;
	const DISP_TRACE1 = true;
	const COLORS = array( '#e912ea', '#27f3cf', '#fd6d03', '#9c27b0', '#607d8b', '#ffc107', '#795548', '#9c27b0', 'green' );
	
	private static $classes = array();
	
	public function __construct() {
		set_error_handler( 'WPSSM_Debug::error' );	
	}
	
	public static function error($code, $str, $file, $line) {
		$msg = $str . ' in ' . $file . ' line ' . $line;
		self::log($msg, false, self::errstr($code), 'red');
	}
	
	public static function trace($msg, $var=false ) {	
		if ( !self::DISP_TRACE ) return;
		$trace = debug_backtrace();
		$class = $trace[1]['class'];
		$color = self::get_class_color($class);
		self::log($msg, $var, $class?$class:'TRACE', $color?$color:'#CCCCCC');
	}
	
	public static function trace1($msg, $var=false ) {	
		if ( !self::DISP_TRACE1 ) return;
		$trace = debug_backtrace();
		$class = $trace[1]['class'];
		$color = self::get_class_color($class);
		self::log($msg, $var, $class?$class:'TRACE', $color?$color:'#CCCCCC');
	}	
	
	private static function get_class_color( $class ) {
		if (!$class) return false;
		if (! in_array($class, self::$classes) ) {
			self::$classes[]=$class;
 		}
		$key = array_search($class, self::$classes);
		$nbcols = count(self::COLORS);
		//$color = self::COLORS[$key % $nbcols];
		$color = self::COLORS[$key];
		return $color;
	}

	public static function log($msg, $var=false, $type='DEBUG', $color='blue' ) {	
		if ( self::PHP_CONSOLE && class_exists( 'PC' ) ) {
			if ($var==false) PC::debug($msg);
			else PC::debug(array($msg=>$var));
		}
		else {
			//if ( defined( 'DOING_AJAX' ) || strstr($_SERVER['REQUEST_URI'], 'admin-post.php') ) return; //Prevents blocking of post submissions/ajax requests
			if ( !is_array($var) ) $var = '"' . $var . '"';
			if ( is_array($var) ) $var = json_encode($var);
			$output='console.log("%c' . $type . '%c ' . str_replace('\\', '\\\\', $msg) . '", "border-radius:4px;padding:2px 4px;background:' . $color . ';color:white", "color:' . $style . '");';  
			$output.='console.log(' . $var . ');';
			self::output_debug_buffer( $output );
		}
	}

	public static function output_debug_buffer($output) {
		ob_start();
		?>
		<script id="DBGlog" type="text/javascript">
			<?php echo $output;?>
		</script>
		<?php
		//PC::debug(array('In wp head output_buffer :'=>self::$output));
	}	
	
	public static function errstr($type) { 
    $return =""; 
    if($type & E_ERROR) // 1 // 
        $return.='& E_ERROR '; 
    if($type & E_WARNING) // 2 // 
        $return.='& E_WARNING '; 
    if($type & E_PARSE) // 4 // 
        $return.='& E_PARSE '; 
    if($type & E_NOTICE) // 8 // 
        $return.='& E_NOTICE '; 
    if($type & E_CORE_ERROR) // 16 // 
        $return.='& E_CORE_ERROR '; 
    if($type & E_CORE_WARNING) // 32 // 
        $return.='& E_CORE_WARNING '; 
    if($type & E_COMPILE_ERROR) // 64 // 
        $return.='& E_COMPILE_ERROR '; 
    if($type & E_COMPILE_WARNING) // 128 // 
        $return.='& E_COMPILE_WARNING '; 
    if($type & E_USER_ERROR) // 256 // 
        $return.='& E_USER_ERROR '; 
    if($type & E_USER_WARNING) // 512 // 
        $return.='& E_USER_WARNING '; 
    if($type & E_USER_NOTICE) // 1024 // 
        $return.='& E_USER_NOTICE '; 
    if($type & E_STRICT) // 2048 // 
        $return.='& E_STRICT '; 
    if($type & E_RECOVERABLE_ERROR) // 4096 // 
        $return.='& E_RECOVERABLE_ERROR '; 
    if($type & E_DEPRECATED) // 8192 // 
        $return.='& E_DEPRECATED '; 
    if($type & E_USER_DEPRECATED) // 16384 // 
        $return.='& E_USER_DEPRECATED '; 
    return substr($return,2); 
	} 	
	
	
}
