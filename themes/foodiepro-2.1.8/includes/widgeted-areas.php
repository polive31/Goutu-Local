<?php
/**
 * Widgeted areas and related functions.
 *
 * @package     FoodiePro
 * @subpackage  Genesis
 * @copyright   Copyright (c) 2014, Shay Bocks
 * @license     GPL-2.0+
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'genesis_before', 'foodie_pro_before_header' );
/**
 * Load an ad section before .site-inner.
 *
 * @since   1.0.0
 *
 * @return  null if the before-header sidebar isn't active.
 */
function foodie_pro_before_header() {
	//* Return early if we have no ad.
	if ( ! is_active_sidebar( 'before-header' ) ) {
		return;
	}

	echo '<div class="before-header">';
		dynamic_sidebar( 'before-header' );
	echo '</div>';
}

add_action( 'genesis_before_comments', 'foodie_pro_after_entry' );
/**
 * Load an after entry section before .entry-comments on single entries.
 *
 * @since   1.1.0
 *
 * @return  null if the after entry sidebar isn't active.
 */
function foodie_pro_after_entry() {
	//* Return early if we have no ad.
	if ( ! is_active_sidebar( 'after-entry' ) ) {
		return;
	}

	echo '<div class="after-entry">';
		dynamic_sidebar( 'after-entry' );
	echo '</div>';
}

//* Register widget areas.
genesis_register_sidebar( array(
	'id'			=> 'before-content',
	'name'			=> __( 'Before Content', 'foodiepro' ),
	'description'	=> __( 'This is the first section before content and sidebars.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'before-header',
	'name'			=> __( 'Before Header', 'foodiepro' ),
	'description'	=> __( 'This is the section before the header.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-top',
	'name'			=> __( 'Home Top', 'foodiepro' ),
	'description'	=> __( 'This is the home top section.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-middle',
	'name'			=> __( 'Home Middle', 'foodiepro' ),
	'description'	=> __( 'This is the home middle section.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-bottom',
	'name'			=> __( 'Home Bottom', 'foodiepro' ),
	'description'	=> __( 'This is the home bottom section.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'after-entry',
	'name'			=> __( 'Post Bottom', 'foodiepro' ),
	'description'	=> __( 'This is the after entry section.', 'foodiepro' ),
) );
//genesis_register_sidebar( array(
//	'id'			=> 'recipes-top',
//	'name'			=> __( 'Recipes Top', 'foodiepro' ),
//	'description'	=> __( 'This is the recipes top section.', 'foodiepro' ),
//) );
//genesis_register_sidebar( array(
//	'id'			=> 'recipes-bottom',
//	'name'			=> __( 'Recipes Bottom', 'foodiepro' ),
//	'description'	=> __( 'This is the recipes bottom section.', 'foodiepro' ),
//) );
///* P.O. Added */
//genesis_register_sidebar( array(
//	'id'			=> 'blogs-top',
//	'name'			=> __( 'Blogs Top', 'foodiepro' ),
//	'description'	=> __( 'This is the blogs top section.', 'foodiepro' ),
//) );
//genesis_register_sidebar( array(
//	'id'			=> 'blogs-bottom',
//	'name'			=> __( 'Blogs Bottom', 'foodiepro' ),
//	'description'	=> __( 'This is the blogs bottom section.', 'foodiepro' ),
//) );
genesis_register_sidebar( array(
	'id'			=> 'social-sidebar',
	'name'			=> __( 'Social sidebar', 'foodiepro' ),
	'description'	=> __( 'This is the sidebar for social pages.', 'foodiepro' ),
) );
//* Create custom category Social widget area
genesis_register_sidebar( array(
    'id'          => 'social-bottom',
    'name'        => __( 'Social Bottom', 'foodiepro' ),
    'description' => __( 'This is the widget section of buddypress pages.', 'foodiepro' ),
) );
//* Create custom category archive bottom widget area (lef)
genesis_register_sidebar( array(
    'id'          => 'archives-top',
    'name'        => __( 'Archives Top', 'foodiepro' ),
    'description' => __( 'This is the top widget section of archived pages.', 'foodiepro' ),
) );
//* Create custom category archive bottom widget area (lef)
genesis_register_sidebar( array(
    'id'          => 'archives-bottom',
    'name'        => __( 'Archives Bottom', 'foodiepro' ),
    'description' => __( 'This is the bottom widget section of archived pages.', 'foodiepro' ),
) );
