<?php
/*
Plugin Name: WP Scripts & Styles Manager
Plugin URI: http://goutu.org/scripts-styles-manager
Description: custom enqueue, minimize, and concatenate your css & js code
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//*************************************************************************
//**               INITIALIZATION
//*************************************************************************

require_once 'includes/WPSSM_Settings.php';
//require_once 'includes/WPSSM_Optimize.php';

new WPSSM_Settings();
//new WPSSM_Optimize();







