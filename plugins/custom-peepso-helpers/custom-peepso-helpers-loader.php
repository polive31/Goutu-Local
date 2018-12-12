<?php
/*
Plugin Name: Custom Peepso Helpers
Plugin URI: http://goutu.org/
Description: Custom shortcodes & widgets for Peepso plugin
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
add_action('plugins_loaded','check_peepso_active');
function check_peepso_active() {
	if ( class_exists( 'PeepSo' ) ) {
		peepso_helpers_conditional_load();
	} else {
		add_action( 'admin_notices', 'peepso_helpers_install_notice' );
	}
}


/* Support functions 
------------------------------------------------------------*/
function peepso_helpers_conditional_load() {
	// Includes
	// require_once 'includes/BP_Activity_Customizations.php';

	// Shortcodes
	require_once 'shortcodes/CustomPeepsoShortcodes.php';

	// Widgets
	require_once 'widgets/PeepsoCoverImageHeader.php';
	require_once 'widgets/PeepsoLatestRegisteredMembers.php';
	require_once 'widgets/PeepsoCustomLoginWidget.php';

}

function peepso_helpers_install_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>Peepso Helpers</strong> requires the Peepso plugin to work.');
	echo '</p></div>';
}


