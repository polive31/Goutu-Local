<?php
/*
Plugin Name: Buddypress Custom Widgets
Plugin URI: http://goutu.org
Description: Provides additional social widgets based on Buddypress  
Author: Pascal Olive 
Version: 1.0
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



/*** Make sure BuddyPress is loaded ********************************/
if ( class_exists( 'BuddyPress' ) ) {
	add_action( 'bp_include', 'bp_custom_widgets_load' );
} else {
	add_action( 'admin_notices', 'bp_custom_widgets_install_notice' );
}

function bp_custom_widgets_load() {
	require( dirname( __FILE__ ) . '/BP_custom_widgets.php' );
}

function bp_custom_widgets_install_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>BP Custom Widgets</strong></a> requires the BuddyPress plugin to work. Please <a href="https://buddypress.org/download">install BuddyPress</a> first, or <a href="plugins.php">deactivate BP Custom Widgets</a>.');
	echo '</p></div>';
}
