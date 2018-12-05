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
	

require_once 'includes/CustomSiteMails.php';
new CustomSiteMails();


