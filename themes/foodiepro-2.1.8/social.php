<?php
/**
 * Template Name: Social Page
 *
 * @package     FoodiePro
 * @subpackage  Genesis
 * @copyright   Copyright (c) 2013, Shay Bocks
 * @license     GPL-2.0+
 * @since       1.0.1
 */

add_action( 'genesis_meta', 'custom_social_genesis_meta' );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

function custom_social_genesis_meta() {
	add_action( 'genesis_before_content_sidebar_wrap', 'foodiepro_social_before_content' );
	add_action( 'genesis_after_content', 'foodiepro_social_bottom' );

	// remove_action( 'genesis_loop', 'genesis_do_loop' );
	
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
	add_action( 'genesis_sidebar', 'foodiepro_do_social_sidebar' );
}


function foodiepro_social_before_content() {
	genesis_widget_area( 'social-before-content', array(
	   'before' => '<div class="top before-content widget-area" id="buddypress">',
	   'after'  => '</div>',
	));  
}


function foodiepro_social_bottom() {
	genesis_widget_area( 'social-bottom', array(
	  'before' => '<div class="bottom after-content social-bottom widget-area">',
	  'after'  => '</div>',
	));
}


// Display the social sidebar
function foodiepro_do_social_sidebar() {
	dynamic_sidebar( 'social-sidebar' );
}



genesis();
