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

//add_action( 'genesis_before', 'foodie_pro_before_header' );
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

//add_action( 'genesis_before_comments', 'foodie_pro_after_entry' );
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

/* General widgeted areas.
----------------------------------------------------*/
// genesis_register_sidebar( array(
// 	'id'			=> 'before-header',
// 	'name'			=> __( 'Before Header', 'foodiepro' ),
// 	'description'	=> __( 'This is the section before the header.', 'foodiepro' ),
// ) );

//* Post Widgeted areas
// genesis_register_sidebar( array(
// 	'id'			=> 'post-bottom',
// 	'name'			=> __( 'Post Bottom', 'foodiepro' ),
// 	'description'	=> __( 'This is the section below content and sidebar, just before the footer.', 'foodiepro' ),
// ) );


/* Common widgeted areas
----------------------------------------------------*/
genesis_register_sidebar( array(
	'id'			=> 'before-header',
	'name'			=> __( 'Before Header', 'foodiepro' ),
	'description'	=> __( 'This is the section before the header.', 'foodiepro' ),
) );

/* Home Page widgeted areas
----------------------------------------------------*/
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
	'id'			=> 'after-content',
	'name'			=> __( 'After Content', 'foodiepro' ),
	'description'	=> __( 'This is the last section after content and sidebars.', 'foodiepro' ),
) );

/* Social (Buddypress) widgeted areas
----------------------------------------------------*/
genesis_register_sidebar( array(
	'id'			=> 'social-before-content',
	'name'			=> __( 'Social Before Content', 'foodiepro' ),
	'description'	=> __( 'On social pages, this is the first section before content and sidebars.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'social-sidebar',
	'name'			=> __( 'Social Sidebar', 'foodiepro' ),
	'description'	=> __( 'This is the sidebar for social pages.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
    'id'          => 'social-content',
    'name'        => __( 'Social Content', 'foodiepro' ),
    'description' => __( 'This is the widget section of buddypress pages.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
    'id'          => 'social-bottom',
    'name'        => __( 'Social Bottom', 'foodiepro' ),
    'description' => __( 'This goes just before the footer, and occupies the whole page width.', 'foodiepro' ),
) );

/* Archive pages widgeted areas
----------------------------------------------------*/
genesis_register_sidebar( array(
    'id'          => 'archives-top',
    'name'        => __( 'Archives Top', 'foodiepro' ),
    'description' => __( 'This is the top widget section of archived pages.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
    'id'          => 'archives-bottom',
    'name'        => __( 'Archives Bottom', 'foodiepro' ),
    'description' => __( 'This is the bottom widget section of archived pages.', 'foodiepro' ),
) );

/* Post List pages widgeted areas
----------------------------------------------------*/
genesis_register_sidebar( array(
    'id'          => 'post-list-top',
    'name'        => __( 'Post List Top', 'foodiepro' ),
    'description' => __( 'This is the top widget section of Post List pages.', 'foodiepro' ),
) );
genesis_register_sidebar( array(
    'id'          => 'post-list-middle',
    'name'        => __( 'Post List Middle', 'foodiepro' ),
    'description' => __( 'This is the middle widget section of Post List pages.', 'foodiepro' ),
) );
