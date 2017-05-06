<?php
/**
 * Plugin Name: PHP Output To Console
 * * Author: Better WP Solutions
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Debug {
		
	const PHP_CONSOLE = false;
  private static $output = '';
	
	public function __construct() {

		//add_action( 'print_footer_scripts', array($this, 'output_debug_buffer') );
		//add_action( 'admin_print_footer_scripts', array($this, 'output_debug_buffer') );
		set_error_handler( 'WPSSM_Debug::error' );	
	}
	
	public static function output_debug_buffer() {
		ob_start();
		?>
		<script id="DBGlog" type="text/javascript">
			<?php echo self::$output;?>
		</script>
		<?php
		//PC::debug(array('In wp head output_buffer :'=>self::$output));
	}
	
	public static function init_dbg() {
	}
	
	public static function error($code, $str, $file, $line) {
		$msg = $str . ' in ' . $file . ' line ' . $line;
		self::log($msg, false, self::errstr($code));
	}

	public static function log($msg, $var=false, $type='DEBUG' ) {	
		if ( self::PHP_CONSOLE && class_exists( 'PC' )) {
			if ($var==false) PC::debug($msg);
			else PC::debug(array($msg=>$var));
		}
		elseif ( !self::PHP_CONSOLE ) {
			if ( defined( 'DOING_AJAX' ) || strstr($_SERVER['REQUEST_URI'], 'admin-post.php') ) return; //Prevents blocking of post submissions/ajax requests
			if ( !is_array($var) ) $var = '"' . $var . '"';
			if ( is_array($var) ) $var = json_encode($var);
			$style=( $type=='DEBUG' )?'blue':'red';
			self::$output='console.log("%c' . $type . '%c ' . str_replace('\\', '\\\\', $msg) . '", "border-radius:4px;padding:2px 4px;background:' . $style . ';color:white", "color:' . $style . '");';  
			self::$output.='console.log(' . $var . ');';
			self::output_debug_buffer();
		}
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
