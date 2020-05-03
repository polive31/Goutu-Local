<?php
/*
Plugin Name: Custom Recaptcha
Plugin URI: http://goutu.org/
Description: Shortcode and Helpers for Recaptcha Integration (Math, Google...)
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
require_once 'public/class-crca-google.php';
require_once 'public/class-crca-math.php';

require_once 'admin/class-cgr-admin.php';

/* Start */
add_action('wp_loaded', 'crca_start_plugin');
function crca_start_plugin()
{
	new Custom_Recaptcha();
}
