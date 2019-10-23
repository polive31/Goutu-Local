<?php
/*
Plugin Name: Custom Social Interactions
Plugin URI: http://goutu.org/
Description: Social Interactions Helpers for Goutu
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


define( 'CSI_PLUGIN_VERSION', '1.1.0' );


/* Main
------------------------------------------------------------*/
require_once 'includes/CustomSocialInteractions.php';
require_once 'includes/CustomSocialInteractionsShortcodes.php';
require_once 'includes/CustomSocialLikePost.php';
