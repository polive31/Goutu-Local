<?php
/**
 * Template Name: Contact Page
 *
 * @package     FoodiePro
 * @subpackage  Genesis
 * @copyright   Copyright (c) 2013, Shay Bocks
 * @license     GPL-2.0+
 * @since       1.0.1
 */

add_action( 'genesis_meta', 'custom_contact_genesis_meta' );
// remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

function custom_contact_genesis_meta() {
	add_action( 'genesis_before_content_sidebar_wrap', 'foodiepro_contact_before_content' );
}


function foodiepro_contact_before_content() {
	genesis_widget_area( 'contact-before-content', array(
	   'before' => '<div class="top before-content widget-area contact" id="">',
	   'after'  => '</div>',
	));
}

// Display the contact sidebar
function foodiepro_do_contact_sidebar() {
	dynamic_sidebar( 'contact-sidebar' );
}


genesis();
