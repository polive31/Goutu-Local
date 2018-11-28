<?php
/*
Plugin Name: Custom Admin Helpers
Plugin URI: http://goutu.org/
Description: Custom utilities for admin area
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
	
	
require_once 'includes/Custom_Admin_Post_Filter.php';
// new CustomArchiveMeta();
new CustomAdminPostFilter();