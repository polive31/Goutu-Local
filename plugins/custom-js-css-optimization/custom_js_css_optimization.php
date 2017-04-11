<?php
/*
Plugin Name: Custom JS & CSS Optimization
Plugin URI: http://goutu.org/custom-js-css-optimization
Description: custom enqueue, minimize, and concatenate your css & js code
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//*************************************************************************
//**               INITIALIZATION
//*************************************************************************

require_once 'includes/JS_CSS_Optimize.php';

new JS_CSS_Optimize();







