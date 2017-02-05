<?php
/*
Plugin Name: Custom Star Rating
Plugin URI: http://goutu.org/custom-star-rating
Description: Ratings via stars in comments
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
Text Domain: custom-star-rating
Domain Path: ./lang
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//*************************************************************************
//**               INITIALIZATION
//*************************************************************************

define( 'PLUGIN_PATH', plugins_url( '', __FILE__ ) );

require 'helpers/functions.php';
require 'templates/comments-list.php';
require 'templates/comment-form.php';
require 'shortcodes/shortcodes.php';


/* Chargement des feuilles de style custom et polices */
function load_custom_rating_style_sheet() {
	//wp_enqueue_style( 'custom-ratings',  plugins_url( '/assets/custom-star-rating.css', __FILE__ ), array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'custom-ratings', PLUGIN_PATH . '/assets/custom-star-rating.css' , array(), CHILD_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'load_custom_rating_style_sheet' );

/* Chargement du text domain */
function custom_star_rating_load_textdomain() {
	load_plugin_textdomain( 'custom-star-rating', false, 'custom-star-rating/lang/' );
}
add_action('plugins_loaded', 'custom_star_rating_load_textdomain');


//*************************************************************************
//**               POST & COMMENTS UPDATE
//*************************************************************************

/* Add field 'rate' to the comments meta on submission using PHP
------------------------------------------------------------ */
add_action('comment_post','comment_ratings_php');
function comment_ratings_php($comment_id) {
	// Retrieve new rating
	$rating = $_POST['rating'];
	reset($rating);
	$rating_val=key($rating);
	
	// Update comment meta with new rating
	add_comment_meta($comment_id, 'rating', $rating_val);

	// Update post meta with new rating table & rating stats
	$user_ratings = get_post_meta( get_the_id(), 'recipe_user_ratings' );
	
	$user_ip = get_user_ip();
	$nb_users = count( $user_ratings ) + 1;
	$user_ratings[] = array(
		'user' => $nb_users,
		'ip'=>$user_ip,
		'rating'=> $new_rating_val,
	);
	update_post_meta($post_id, 'recipe_user_ratings', $user_ratings);
	
	$user_ratings_rating = get_rating_stats( $user_ratings )['rating'];
	update_post_meta($post_id, 'recipe_user_ratings_rating', $user_ratings_rating);
}


/* Add ratings default value on recipe save 
-------------------------------------------------------------*/ 
add_action( 'save_post', 'wpurp_add_default_rating', 10, 2 );
function wpurp_add_default_rating( $id, $post ) {
 if ( $post->post_type == 'recipe' && !wp_is_post_revision($post->ID) )
    update_post_meta($post->ID, 'recipe_user_ratings_rating', '0');
}




//*************************************************************************
//**               COMMENTS LIST
//*************************************************************************

// Remove the genesis_default_list_comments function
remove_action( 'genesis_list_comments', 'genesis_default_list_comments' );

// Add our own and specify our custom callback
add_action( 'genesis_list_comments', 'custom_star_rating_list_comments' );
function custom_star_rating_list_comments() {
    $args = array(
        'type'          => 'comment',
        'avatar_size'   => 54,
        'callback'      => 'custom_star_rating_comment',
    );
    $args = apply_filters( 'genesis_comment_list_args', $args );
    wp_list_comments( $args );
}


?>