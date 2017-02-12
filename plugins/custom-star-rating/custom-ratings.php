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

//if ( is_single() ) {


const RATING = array( 
		'id'=>'rating',
		'title'=>'Overall rating',
		'question'=>'How did you like this dish ?',
);

const CLARITY = array( 
		'id'=>'clarity',
		'title'=>'Clarity',
		'question'=>'How clear was the recipe ?',
);
		
const RATED_POST_TYPES = array('recipe');

if ( true ) {

	define( 'PLUGIN_PATH', plugins_url( '', __FILE__ ) );

	require 'helpers/functions.php';
	require 'templates/comments-list.php';
	require 'templates/comment-form.php';
	require 'shortcodes/shortcodes.php';
	
	$RatingCritera = array( RATING, CLARITY);

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

}


//*************************************************************************
//**               POST & COMMENTS UPDATE
//*************************************************************************

/* Add field 'rate' to the comments meta on submission using PHP
------------------------------------------------------------ */
add_action('comment_post','update_comment_post_meta_php',10,3);

function update_comment_post_meta_php($comment_id,$comment_approved,$comment) {
	
	PC::debug('In comment post !');
	$post_id = $comment['comment_post_ID'];

	if ( isset( $_POST[ 'rating-' . '1' ] ) ) {
		
		$rating = $_POST[ 'rating-' . '1' ];
		PC::debug(array('Rating :'=>$rating));
		
		add_comment_meta($comment_id, 'user_rating', $rating);
		// Update post meta with new rating table & rating stats
		$user_ratings = get_post_meta( $post_id, 'user_ratings' );
		PC::debug(array('User Ratings Table :'=>$user_ratings));

		if ( !empty($user_ratings) )
			$new_user_id = count( $user_ratings ) + 1;
		else {
			$new_user_id = 1;
		}
		$user_ip = get_user_ip();
		PC::debug(array('User IP :'=>$user_ip));

		$new_user_rating = array(
			'user' 	=>$new_user_id,
			'ip'		=>$user_ip,
			'rating'=>$rating,
		);
		//PC::debug(array('New User Rating :'=>$new_user_rating ) );
		add_post_meta($post_id, 'user_ratings', $new_user_rating);
		
		$user_ratings[]=$new_user_rating;
		$stats = get_rating_stats( $user_ratings );
		//PC:debug(array('Stats :'=>$stats) );
		
		update_post_meta($post_id, 'user_rating', $stats['rating']);

	}
}


/* Add ratings default value on post save 
-------------------------------------------------------------*/ 
add_action( 'save_post', 'wpurp_add_default_rating', 10, 2 );
function wpurp_add_default_rating( $id, $post ) {
 	if ( ! wp_is_post_revision($post->ID) ) {
 		//PC:debug('Default rating add');
		update_post_meta($post->ID, 'user_rating', '0');
 	}
}


//*************************************************************************
//**               COMMENTS LIST
//*************************************************************************

// Add our own custom list and specify our custom callback
add_action( 'genesis_list_comments', 'custom_star_rating_list_comments' );
function custom_star_rating_list_comments() {

	if ( is_singular( RATED_POST_TYPES ) ) {
		$args = array(
		    'type'          => 'comment',
		    'avatar_size'   => 54,
		    'callback'      => 'custom_star_rating_comment',
		);
		$args = apply_filters( 'genesis_comment_list_args', $args );		
	}
	wp_list_comments( $args );
}

/* Change the comment reply link to display our own comment form */
function remove_nofollow($link, $args, $comment, $post){
  return str_replace("rel='nofollow'", "", $link);
}

add_filter('comment_reply_link', 'remove_nofollow', 420, 4);


?>