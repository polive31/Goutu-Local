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
	
	
	
/* Main
------------------------------------------------------------*/

//if (! class_exists( 'PHP_Debug')) {
//	class PHP_Debug {
//		public function log( $msg, $var=false) {}
//		public function trace( $msg, $var=false) {}
//	}
//}

require_once 'includes/ContactFormPostType.php';
require_once 'includes/CustomContactFormShortcode.php';

// new ContactFormPostType();
new CustomContactFormShortcode();

