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
require_once 'public/mails/CustomSiteMails.php';
require_once 'public/popups/CustomSitePopups.php';


new CustomSiteNotifications();
