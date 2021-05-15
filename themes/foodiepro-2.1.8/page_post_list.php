<?php
/**
 * Template Name: Post List Page
 *
 * @package     FoodiePro
 * @subpackage  Genesis
 * @copyright   Copyright (c) 2013, Shay Bocks
 * @license     GPL-2.0+
 * @since       1.0.1
 */


function foodiepro_contact_before_content() {
	genesis_widget_area( 'post-list-top', array(
	   'before' => '<div class="top before-content widget-area post-list" id="post-list-top">',
	   'after'  => '</div>',
	));
}


genesis();
