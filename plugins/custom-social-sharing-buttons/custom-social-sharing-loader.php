<?php
/*
Plugin Name: Custom Social Sharing Buttons
Plugin URI: http://goutu.org/
Description: Custom shortcodes & widgets for social sharing purposes
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

//if (! class_exists( 'PHP_Debug')) {
//	class PHP_Debug {
//		public function log( $msg, $var=false) {}
//		public function trace( $msg, $var=false) {}
//	}
//}

require_once 'includes/CustomSocialButtons.php';
require_once 'includes/CustomSocialButtonsShortcodes.php';
$customSocialButtons = new CustomSocialButtons;
$customSocialButtonsShortcodes = new CustomSocialButtonsShortcodes;

require_once 'widgets/social-share-buttons-widget.php';
