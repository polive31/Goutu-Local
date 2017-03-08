<?php
/*
Plugin Name: Batch Manage Posts
Plugin URI: http://goutu.org/
Description: Shortcodes for post & comments batch processing
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



/* =================================================================*/
/* =               PLUGIN INITIALIZATION
/* =================================================================*/

require 'helpers/form.php';
require 'functions/common.php';
require 'functions/delete_comments_ajax.php';
require 'functions/manage_posts_meta_ajax.php';

add_action( 'wp_enqueue_scripts', 'bupm_init_scripts' );
function bupm_init_scripts() {
	wp_register_script( 'ajax_call_batch_manage', plugins_url( 'js/ajax_call_on_button_press.js', __FILE__ ) , array( 'jquery' ), '1.0', true );
}

/* =================================================================*/
/* =               BATCH UPDATE POST META
/* =================================================================*/
add_action("wp_ajax_ManageMeta", "ajax_batch_manage_meta");
add_action("wp_ajax_nopriv_ManageMeta", "ajax_batch_manage_meta");
add_shortcode('batch-manage-meta', 'batch_manage_meta');

/* Batch update user_ratings_ratings custom field */
function batch_manage_meta($atts) {
	$a = shortcode_atts( array(
		'post-type' => 'recipe',
		'include' => '',
		'key' => 'user_rating',
		'new-key' => '',
		'value' => '0',//can be scalar or array of space-separated $key/$value pairs
		'cmd' => 'read',//add, replace, delete, rename
	), $atts );
	
	static $script_id; // allows several shortcodes on the same page
	++$script_id;
	
	$script_name = 'ManageMeta';

	echo "<h3>BATCH MANAGE META SHORTCODE#" . $script_id . "</h3>";
	
	$jsargs= create_ajax_arg_array($a, $script_name, $script_id);
	
	wp_enqueue_script( 'ajax_call_batch_manage' );	
	wp_localize_script( 'ajax_call_batch_manage', 'script' . $script_name . $script_id , $jsargs );
	
	echo batch_manage_form($script_id, $script_name, $a['cmd']);
	
}


/* =================================================================*/
/* =               BATCH DELETE COMMENTS
/* =================================================================*/
add_action("wp_ajax_DeleteComment", "ajax_batch_delete_comments");
add_action("wp_ajax_nopriv_DeleteComment", "ajax_batch_delete_comments");
add_shortcode('batch-delete-comments', 'batch_delete_comments');

/* Batch update user_ratings_ratings custom field */
function batch_delete_comments($atts) {
	$a = shortcode_atts( array(
		'post-type' => 'recipe',
		'include' => '', // Post ids list, separated by commas
	), $atts );
	$a['cmd']='delete';

	static $script_id; // allows several shortcodes on the same page
	++$script_id;
	
	$script_name = 'DeleteComment';
	
	echo "<h3>BATCH DELETE COMMENTS SHORTCODE#" . $script_id . "</h3>";

	$jsargs= create_ajax_arg_array($a, $script_name, $script_id);
	
	wp_enqueue_script( 'ajax_call_batch_manage' );	
	wp_localize_script( 'ajax_call_batch_manage', 'script' . $script_name . $script_id , $jsargs );
	
	echo batch_manage_form($script_id, $script_name, 'delete');

}

/* =================================================================*/
/* =               BATCH MIGRATE RATINGS
/* =================================================================*/
add_action("wp_ajax_MigrateRatings", "ajax_migrate_ratings");
add_action("wp_ajax_nopriv_MigrateRatings", "ajax_migrate_ratings");
add_shortcode('batch-migrate-ratings', 'batch_migrate_ratings');

/* Batch update user_ratings_ratings custom field */
function batch_migrate_ratings($atts) {
	$a = shortcode_atts( array(
		'post-type' => 'recipe',
		'include' => '',
	), $atts );
	
	static $script_id; // allows several shortcodes on the same page
	++$script_id;
	
	$script_name = 'MigrateRatings';

	echo "<h3>BATCH MANAGE META SHORTCODE#" . $script_id . "</h3>";
	
	$jsargs= create_ajax_arg_array($a, $script_name, $script_id);
	
	wp_enqueue_script( 'ajax_call_batch_manage' );	
	wp_localize_script( 'ajax_call_batch_manage', 'script' . $script_name . $script_id , $jsargs );
	
	echo batch_manage_form($script_id, $script_name, $a['cmd']);
	
}




