<?php
/*
Plugin Name: Custom Structured Data
Plugin URI: http://goutu.org/
Description: Outputs structured JSON+LD data in accordance with custom post & taxonomy types
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


// require_once 'includes/class-custom-structured-data.php';
require_once 'includes/class-csd-assets.php';
require_once 'public/class-csd-meta.php';

// add_action('plugins_loaded', 'custom_structured_data_start');
// function custom_structured_data_start()
// {
// 	new Custom_Structured_Data();
// }
