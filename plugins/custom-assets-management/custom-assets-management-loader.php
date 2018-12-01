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
require_once 'includes/Custom_Scripts_Styles_Enqueue.php';
require_once 'includes/Lazy_Load.php';

new CustomScriptsStylesEnqueue();
// new LazyLoad();









