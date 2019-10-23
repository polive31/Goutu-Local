<?php
/*
Plugin Name: Custom Admin Management
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


require_once 'includes/class-custom-admin-management.php';
require_once 'admin/class-cam-admin-helpers.php';
require_once 'admin/class-cam-post-filter.php';

new Custom_Admin_Management();
