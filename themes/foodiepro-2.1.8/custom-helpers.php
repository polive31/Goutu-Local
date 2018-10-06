<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =================================================================*/
/* =              CUSTOM SCRIPTS ENQUEUE
/* =================================================================*/


function url_exists($url) {
	$headers = @get_headers($url);
	return (strpos($headers[0],'404') === false);
}

function custom_enqueue_script( $handler, $uri, $path, $file, $deps, $version, $footer=false ) {	
	$minfile = str_replace( '.js', '.min.js', $file );
	//echo '<pre>' . "minpath = {$minpath}" . '</pre>';
	//echo '<pre>' . "path = {$path}" . '</pre>';
	
  //if ((url_exists($minpath)) && (WP_DEBUG==false)) {
  if (file_exists( $path . $minfile) && WP_MINIFY ) {
    wp_enqueue_script( $handler, $uri . $minfile, $deps, $version, $footer );
  }
  else {
    wp_enqueue_script( $handler, $uri . $file, $deps, $version, $footer );
  }
}

function remove_script($script) {
	wp_deregister_script($script);
	wp_dequeue_script($script);
}



/* =================================================================*/
/* =              CUSTOM STYLES ENQUEUE     
/* =================================================================*/

function custom_enqueue_style( $handler, $uri, $path, $file, $deps, $version ) {	
	$minfile = str_replace( '.css', '.min.css', $file );
	if (file_exists( $path . $minfile) && WP_MINIFY ) {
		wp_enqueue_style( $handler, $uri . $minfile, $deps, $version );
	}
	else {
		wp_enqueue_style( $handler, $uri . $file, $deps, $version );
	}
}

/* Optimize page loading by dequeuing specific CSS stylesheets loading actions */
function remove_style($style) {
	wp_deregister_style($style);
	wp_dequeue_style($style);
}
