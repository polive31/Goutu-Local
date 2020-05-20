<?php
/*
Plugin Name: Custom SEO
Plugin URI: http://goutu.org/
Description: Custom SEO management
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


require_once 'includes/class-custom-seo.php';
require_once 'includes/class-cseo-assets.php';
require_once 'public/class-cseo-public.php';
require_once 'admin/class-cseo-admin.php';

add_action('plugins_loaded', 'custom_seo_start');
function custom_seo_start()
{
	new Custom_SEO();
}
