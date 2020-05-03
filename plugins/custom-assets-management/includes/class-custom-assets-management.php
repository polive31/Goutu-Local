<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Assets_Management {

	public function __construct() {

		$Assets = new CASM_Assets();

		$Enqueue = new CASM_Enqueue();
		add_action( 'wp_enqueue_scripts', 		array($Enqueue, 'build_styles_lists'), PHP_INT_MAX);
		add_action( 'wp_enqueue_scripts', 		array($Enqueue, 'build_scripts_lists'), PHP_INT_MAX);

		// Header hooks
		add_action( 'wp_print_styles', 			array($Enqueue, 'dequeue_styles'), 0);
		add_action( 'wp_print_scripts', 		array($Enqueue, 'dequeue_scripts'), 0);

		// Footer hooks
		add_action( 'wp_print_footer_scripts',	array($Enqueue, 'dequeue_styles'), 0);
		add_action( 'wp_print_footer_scripts',	array($Enqueue, 'dequeue_scripts'), 0);
		add_action( 'get_footer', 				array($Enqueue, 'enqueue_footer_styles'), 10);
		add_action( 'print_late_styles', 		'__return_true', 10);

		add_filter( 'script_loader_tag', 		array($Enqueue, 'async_load_js'), PHP_INT_MAX, 3 );
		add_filter( 'style_loader_tag', 		array($Enqueue, 'async_load_css'), PHP_INT_MAX, 4 );
		add_filter( 'style_loader_tag', 		array($Enqueue, 'preload_css'), PHP_INT_MAX, 4 );

		// add_action( 'wp_print_styles', 		array($this, 'megamenu_dequeue_google_fonts'), 100 );
		// add_action('init', 					'load_jquery_from_google');

		add_filter( 'stylesheet_uri', 			array($Enqueue, 'enqueue_minified_theme_stylesheet'), 10, 1 );
	}

}
