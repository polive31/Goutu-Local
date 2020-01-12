<?php
/*
Plugin Name: Custom Color Theme
Plugin URI: http://goutu.org/
Description: Pilot the color theme from WP backend
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


require_once 'includes/class-custom-color-theme.php';
// require_once 'public/class-cct-public.php';
require_once 'admin/class-cct-admin.php';

/* Start */
add_action('wp_loaded', 'cct_start_plugin');
function cct_start_plugin()
{
	new Custom_Color_Theme();
}
