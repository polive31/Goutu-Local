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


require_once 'includes/class-custom-admin-helpers.php';

require_once 'admin/class-cah-assets.php';
require_once 'admin/class-cah-foodiepro-settings.php';
require_once 'admin/class-cah-tax-columns.php';
require_once 'admin/class-cah-post-filter.php';
require_once 'admin/class-cah-post-metabox.php';

require_once 'public/class-cah-adminbar.php';

add_action('plugins_loaded', 'custom_admin_helpers_start');

function custom_admin_helpers_start() {
	Custom_Admin_Helpers::get_instance();
	// new Custom_Admin_Helpers();
}
