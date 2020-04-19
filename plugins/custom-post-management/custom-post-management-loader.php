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

require_once 'like/class-cpm-like.php';

require_once 'includes/class-custom-post-management.php';
require_once 'includes/class-cpm-assets.php';
require_once 'includes/class-cpm-post-status.php';

require_once 'post-output/class-cpm-output.php';
require_once 'post-output/class-cpm-list.php';
require_once 'post-output/helpers/class-cpm-output-shortcodes.php';

require_once 'widgets/cpm_list_dropdown_widget.php';

require_once 'post-submission/class-cpm-submission.php';
require_once 'post-submission/helpers/class-cpm-save.php';
require_once 'post-submission/helpers/class-cpm-submission-shortcode.php';

/* Start */
add_action( 'plugins_loaded', 'cpm_start_plugin' );
function cpm_start_plugin() {
	new Custom_Post_Management();
}
