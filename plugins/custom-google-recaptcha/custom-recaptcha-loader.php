<?php
/*
Plugin Name: Custom Google Recaptcha
Plugin URI: http://goutu.org/
Description: Shortcode and Helpers for Google Recaptcha Integration
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


require_once 'includes/class-custom-recaptcha.php';
require_once 'public/class-cgr-public.php';
require_once 'public/class-crca-math.php';
require_once 'admin/class-cgr-admin.php';

/* Start */
add_action('wp_loaded', 'cgr_start_plugin');
function cgr_start_plugin()
{
	new Custom_Recaptcha();
}
