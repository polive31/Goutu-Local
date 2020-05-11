<?php
/*
Plugin Name: Custom Site Interactions
Plugin URI: http://goutu.org/
Description: Support for customized mails, notifications and other interactions
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


require_once 'includes/class-custom-site-interactions.php';
require_once 'includes/class-csn-assets.php';

require_once 'public/mails/class-csn-mails.php';
require_once 'public/popups/class-csn-popups.php';
require_once 'public/notifications/class-csn-notifications.php';



/* Start plugin */
add_action( 'wp_loaded', 'csn_start_plugin' );
function csn_start_plugin() {
	new Custom_Site_Interactions();
}
