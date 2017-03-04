<?php
/*
Plugin Name: Custom Star Ratings
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

require 'includes/CustomStarRatings.php';
require 'includes/CustomStarRatingsMetaUpdate.php';
require 'includes/CustomStarRatingsShortcodes.php';
require 'includes/CustomStarRatingsCommentsList.php';

new CustomStarRatings();
new CustomStarRatingsMetaUpdate();
new CustomStarRatingsShortcodes();
new CustomStarRatingsCommentsList();
	
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




?>