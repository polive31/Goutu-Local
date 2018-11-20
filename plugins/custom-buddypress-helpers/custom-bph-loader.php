<?php
/*
Plugin Name: Custom Buddypress Helpers
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
	
	
define( 'CBPH_PLUGIN_VERSION', '1.1.0' );
	
	
/* Main
------------------------------------------------------------*/
if ( class_exists( 'BuddyPress' ) ) {
	add_action( 'bp_include', 'bp_social_helpers_load' );
} else {
	add_action( 'admin_notices', 'bp_social_helpers_install_notice' );
}


/* Support functions 
------------------------------------------------------------*/
function bp_social_helpers_load() {
	// Includes
	require_once 'includes/BP_Activity_Customizations.php';
	// Widgets
	require_once 'widgets/BP-custom-login-widget.php';
	require_once 'widgets/BP-latest-registered-members-widget.php';
	require_once 'widgets/BP-member-profile-widget.php';
	require_once 'widgets/BP-my-friends-widget.php';
	require_once 'widgets/BP-cover-image-header.php';
	require_once 'widgets/BP-welcome-widget.php';
	require_once 'widgets/BP-whats-new-widget.php';
	require_once 'widgets/BP-activity-feed-widget.php';
	if ( class_exists( 'Buddy_Progress_Bar' ) )
		require_once 'widgets/BP-profile-completion-widget.php';
	else
		add_action( 'admin_notices', 'bp_social_helpers_missing_plugin' );
	require_once 'shortcodes/CustomSocialHelpers.php';
	new CustomSocialHelpers();
}

function bp_social_helpers_install_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>BP Social Helpers</strong></a> requires the BuddyPress plugin to work. Please <a href="https://buddypress.org/download">install BuddyPress</a> first, or <a href="plugins.php">deactivate BP Custom Shortcodes</a>.');
	echo '</p></div>';
}

function bp_social_helpers_missing_plugin() {
	$class = 'notice notice-error';
	$message = 'The customized profile completion widget requires the <a href="https://fr.wordpress.org/plugins/buddy-progress-bar/">Buddy Progress Bar</a> plugin to be installed.';

	//printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
	echo '<div class="notice notice-error"><p>' . $message . '</p></div>'; 
}

