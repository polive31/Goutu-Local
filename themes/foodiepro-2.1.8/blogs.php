<?php
/**
 * Template Name: Blogs Index
 *
 * @package     FoodiePro
 * @subpackage  Genesis
 * @copyright   Copyright (c) 2013, Shay Bocks
 * @license     GPL-2.0+
 * @since       1.0.1
 */

add_action( 'genesis_meta', 'foodie_pro_blogs_genesis_meta' );
/**
 * Add widget support for blogs page.
 * If no widgets active, display the default page content.
 *
 * @since 1.0.1
 */
function foodie_pro_blogs_genesis_meta() {
	if ( is_active_sidebar( 'blogs-top' ) || is_active_sidebar( 'blogs-bottom' ) ) {
		// Remove the default Genesis loop.
		remove_action( 'genesis_loop', 'genesis_do_loop' );
		// Add a custom loop for the home page.
		add_action( 'genesis_loop', 'foodie_pro_blogs_loop_helper' );
	}
}

/**
 * Display the blogs page widgeted sections.
 *
 * @since 1.0.0
 */
function foodie_pro_blogs_loop_helper() {
	genesis_widget_area( 'blogs-top',  array(
		'before' => '<div class="widget-area blogs-top">',
		'after'  => '</div> <!-- end .blogs-top -->',
	) );

	genesis_widget_area( 'blogs-bottom', array(
		'before' => '<div class="widget-area blogs-bottom">',
		'after'  => '</div> <!-- end .blogs-bottom -->',
	) );
}

genesis();
