<?php
/*
Plugin Name: Custom Site Notifications
Plugin URI: http://goutu.org/
Description: Support for customized notifications including mails
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


require_once 'includes/class-custom-site-notifications.php';
require_once 'includes/class-csn-assets.php';

require_once 'public/class-csn-public.php';
require_once 'public/mails/CustomSiteMails.php';
require_once 'public/popups/CustomSitePopups.php';
require_once 'public/like/class-csn-like.php';



/* Start plugin */
add_action( 'wp_loaded', 'csn_start_plugin' );
function csn_start_plugin() {
	new CustomSiteNotifications();
}
