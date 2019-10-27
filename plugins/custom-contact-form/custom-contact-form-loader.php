<?php
/*
Plugin Name: Custom Contact Form
Plugin URI: http://goutu.org/
Description: Custom Shortcode for Contact Form
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

require_once 'includes/class-custom-contact-form.php';
require_once 'admin/class-ccf-admin.php';
require_once 'public/class-ccf-public.php';


/* Start plugin */
/* IMPORTANT : since the contacts post type is created at the init hook, the plugin's startup hook must be fired before ! */
add_action('after_setup_theme', 'ccf_start_plugin');
function ccf_start_plugin()
{
	new Custom_Contact_Form();
}
