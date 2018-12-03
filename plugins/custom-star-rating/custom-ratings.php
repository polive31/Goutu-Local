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

require 'includes/CustomStarRatings.php';
require 'includes/CustomStarRatingsMeta.php';
require 'includes/CustomStarRatingsShortcodes.php';
require 'includes/CustomStarRatingsCommentsList.php';

new CustomStarRatings();
new CustomStarRatingsMeta();
new CustomStarRatingsShortcodes();
new CustomStarRatingsCommentsList();
	
/* Chargement du text domain */
function custom_star_rating_load_textdomain() {
	load_plugin_textdomain( 'custom-star-rating', false, 'custom-star-rating/lang/' );
}
add_action('plugins_loaded', 'custom_star_rating_load_textdomain');


?>