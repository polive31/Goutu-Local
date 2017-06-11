<?Php
/*
Plugin Name: Wp Scripts & Styles Manager
Plugin Uri: Http://Goutu.Org/Wp-Scripts-Styles-Manager
Description: Custom Enqueue, Minimize, And Concatenate Your Css & Js Code
Version: 1.0.0
Author: Pascal Olive
Author Uri: Http://Goutu.Org
License: Gpl
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (! class_exists( 'PHP_Debug')) {
	class PHP_Debug {
		public function log( $msg, $var=false) {}
		public function trace( $msg, $var=false) {}
	}
}

/* Plugin Management hooks
---------------------------------------------------------*/
function uninstall_wpss() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpssm-uninstall.php';
	WPSSM_Uninstall::uninstall();
}

register_deactivation_hook( __FILE__, 'uninstall_wpssm' );



/* Plugin Management hooks
---------------------------------------------------------*/
require plugin_dir_path( __FILE__ ) . 'includes/class-wpssm.php';

function run_wpssm() {
	$plugin = new WPSSM();
	$plugin->run();
}
run_wpssm();







