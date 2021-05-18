<?php
/**
 * Template Name: Widget Showroom Page
 *
 * @package     FoodiePro
 * @subpackage  Genesis
 * @copyright   Copyright (c) 2013, Shay Bocks
 * @license     GPL-2.0+
 * @since       1.0.1
 */

add_action( 'genesis_meta', 'custom_showroom_genesis_meta' );

function custom_showroom_genesis_meta() {
	add_action( 'genesis_after_loop', 'foodiepro_showroom' );
}


function foodiepro_showroom() {
	genesis_widget_area( 'widget-showroom', array(
	   'before' => '<div class="after-content widget-area showroom" id="">',
	   'after'  => '</div>',
	));
}


genesis();
