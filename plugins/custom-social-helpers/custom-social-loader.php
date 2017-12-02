<?php
/*
Plugin Name: Custom Social Helpers
Plugin URI: http://goutu.org/
Description: Custom BP-related shortcodes & widgets for Goutu
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
	
	
/* Main
------------------------------------------------------------*/

if ( class_exists( 'BuddyPress' ) ) {
	add_action( 'bp_include', 'bp_custom_shortcodes_load' );
} else {
	add_action( 'admin_notices', 'bp_custom_shortcodes_install_notice' );
}



/* Support functions 
------------------------------------------------------------*/
function bp_custom_shortcodes_load() {
	require_once 'widgets/BP-custom-login-widget.php';
	require_once 'widgets/BP-latest-registered-members-widget.php';
	require_once 'widgets/BP-member-profile-widget.php';
	require_once 'widgets/BP-my-friends-widget.php';
	require_once 'widgets/BP-welcome-widget.php';
	require_once 'widgets/BP-profile-completion-widget.php';
	require_once 'shortcodes/BP-custom-shortcodes.php';
}

function bp_custom_shortcodes_install_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>BP Social Helpers</strong></a> requires the BuddyPress plugin to work. Please <a href="https://buddypress.org/download">install BuddyPress</a> first, or <a href="plugins.php">deactivate BP Custom Shortcodes</a>.');
	echo '</p></div>';
}

