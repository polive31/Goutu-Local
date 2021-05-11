<?php
/*
Plugin Name: Custom Peepso Overrides
Plugin URI: http://goutu.org/
Description: Custom filters, shortcodes, helpers & widgets for Peepso plugin
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
add_action('plugins_loaded','cpo_check_peepso_active');
function cpo_check_peepso_active() {
  	if ( class_exists( 'PeepSo' ) ) {
		cpo_peepso_helpers_conditional_load();
	} else {
		add_action( 'admin_notices', 'cpo_peepso_helpers_install_notice' );
	}
}


/* Support functions
------------------------------------------------------------*/
function cpo_peepso_helpers_conditional_load() {
	// Includes
	require_once 'includes/class-custom-peepso-overrides.php';
	require_once 'includes/class-cpo-assets.php';

	// Public
	require_once 'public/class-cpo-customizations.php';
	require_once 'public/class-peepso-helpers.php';
	require_once 'public/class-cpo-shortcodes.php';

	// Widgets
	require_once 'widgets/PeepsoCoverImageHeader.php';
	require_once 'widgets/PeepsoMembers.php';
	require_once 'widgets/PeepsoCustomLoginWidget.php';
	// require_once 'widgets/PeepsoActivityStreamWidget.php';
	require_once 'widgets/PeepsoProfileCompletionWidget.php';
	require_once 'widgets/PeepsoAboutWidget.php';

	new Custom_Peepso_Overrides();

}

function cpo_peepso_helpers_install_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>Peepso Helpers</strong> requires the Peepso plugin to work.');
	echo '</p></div>';
}
