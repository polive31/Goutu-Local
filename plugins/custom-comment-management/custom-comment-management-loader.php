<?php
/*
Plugin Name: Custom Comment Management
Plugin URI: http://goutu.org/custom-comment-management
Description: Provide an improved commenting experience by replacing the embedded WP comment system
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
Text Domain: custom-comment-management
Domain Path: ./lang
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*************************************************************************/
/************               INITIALIZATION		         *****************/
/*************************************************************************/

require 'includes/class-custom-comment-management.php';
require 'includes/class-ccm-assets.php';

require 'public/class-ccm-comments-list.php';
require 'public/class-ccm-form.php';


/* Chargement du text domain */
// function custom_comment_management_load_textdomain() {
// 	load_plugin_textdomain( 'custom-comment-management', false, 'custom-comment-management/lang/' );
// }
// add_action('plugins_loaded', 'custom_comment_management_load_textdomain');

/* Start plugin */
add_action( 'init', 'ccm_start_plugin' );
function ccm_start_plugin() {
	new Custom_Comment_Management();
}
