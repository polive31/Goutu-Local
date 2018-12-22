<?php
/**
 * Template Name: Post List
 *
 * @package     FoodiePro
 * @subpackage  Genesis
 * @copyright   Copyright (c) 2013, Shay Bocks
 * @license     GPL-2.0+
 * @since       1.0.1
 */

add_action( 'genesis_meta', 'foodie_pro_postlist_genesis_meta' );
/**
 * Add widget support for post list page.
 * If no widgets active, display the default loop.
 *
 * @since 1.0.1
 */
function foodie_pro_postlist_genesis_meta() {
	if ( is_active_sidebar( 'post-list-top' ) || is_active_sidebar( 'post-list-middle' ) ) {
		// Remove the default Genesis loop.
		// remove_action( 'genesis_loop', 'genesis_do_loop' );
		// Add a custom loop for the home page.
		add_action( 'genesis_loop', 'foodie_pro_postlist_loop_helper' );
		// add_action( 'genesis_after_content_sidebar_wrap', 'foodie_pro_home_bottom' );
	}
}

/**
 * Display the home page widgeted sections.
 *
 * @since 1.0.0
 */
function foodie_pro_postlist_loop_helper() {
	// Add the post list top section if it has content.
	genesis_widget_area( 'post-list-top', array(
		'before' => '<div class="top archives-top widget-area">',
		'after'  => '</div> <!-- end .post-list-top -->',
	) );

	// Add the post list middle section if it has content.
	genesis_widget_area( 'post-list-middle', array(
		'before' => '<div class="widget-area home-middle">',
		'after'  => '</div> <!-- end .home-middle -->',
	) );

}


genesis();
