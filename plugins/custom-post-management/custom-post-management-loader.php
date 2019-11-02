<?php
/*
Plugin Name: Custom Post Management
Plugin URI: http://goutu.org/
Description: Customized templates & front-end submission for any type of posts
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* Includes   */
require_once 'includes/class-custom-post-management.php';
require_once 'includes/class-cpm-assets.php';

require_once 'custom-post-template/class-custom-post-template.php';

require_once 'custom-post-submission/class-cpm-submission.php';
require_once 'custom-post-submission/helpers/CPM_Submission_Shortcodes.php';

require_once 'custom-post-list/CPM_List.php';
require_once 'custom-post-list/helpers/CPM_List_Shortcodes.php';

/* Start */
add_action( 'wp_loaded', 'cpm_start_plugin' );
function cpm_start_plugin() {
	new Custom_Post_Management();
}
