<?php
/*
Plugin Name: WP Scripts & Styles Manager
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







