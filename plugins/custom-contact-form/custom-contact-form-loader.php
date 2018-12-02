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

require_once 'includes/CustomContactForm.php';
require_once 'includes/CustomContactFormShortcode.php';

new CustomContactForm();
new CustomContactFormShortcode();

