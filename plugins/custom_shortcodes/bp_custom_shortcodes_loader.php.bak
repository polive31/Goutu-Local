<?php
/*
Plugin Name: BP Custom Shortcodes
Plugin URI: http://goutu.org/
Description: Buddypress-related shortcodes for Goutu
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	

/*** Make sure BuddyPress is loaded ********************************/
if ( class_exists( 'BuddyPress' ) ) {
	add_action( 'bp_include', 'bp_custom_shortcodes_load' );
} else {
	add_action( 'admin_notices', 'bp_custom_shortcodes_install_notice' );
}

function bp_custom_shortcodes_load() {
	require( dirname( __FILE__ ) . '/bp-custom-shortcodes.php' );
}

function bp_custom_shortcodes_install_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>BP Custom Shortcodes</strong></a> requires the BuddyPress plugin to work. Please <a href="https://buddypress.org/download">install BuddyPress</a> first, or <a href="plugins.php">deactivate BP Custom Shortcodes</a>.');
	echo '</p></div>';
}

?>