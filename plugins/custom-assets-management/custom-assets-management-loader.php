<?php
/*
Plugin Name: Custom Assets Management
Plugin URI: http://goutu.org/
Description: Manage loading of all Scripts & Styles
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* Includes
------------------------------------*/
require_once 'includes/class-custom-assets-management.php';
require_once 'includes/class-casm-assets.php';
require_once 'includes/class-casm-enqueue.php';

add_action('plugins_loaded', 'casm_start_plugin');

function casm_start_plugin() {
	new Custom_Assets_Management();
}
