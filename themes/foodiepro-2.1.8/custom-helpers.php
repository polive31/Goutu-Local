<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/* =================================================================*/
/* =              CUSTOM SCRIPTS HELPERS
/* =================================================================*/

// function custom_register_script( $handler, $uri, $path, $file, $deps, $version, $footer=false ) {	
// 	$minfile = str_replace( '.js', '.min.js', $file );
// 	if (file_exists( $path . $minfile) && WP_MINIFY ) {
// 		wp_register_script( $handler, $uri . $minfile, $deps, $version, $footer );
// 	}
// 	else {
// 		wp_register_script( $handler, $uri . $file, $deps, $version, $footer );
// 	}
// }

function custom_enqueue_script( $handler, $uri, $path, $file, $deps, $version, $footer=false ) {	
	if ( !strpos($file, '.min.js') ) {
		$minfile = str_replace( '.js', '.min.js', $file );
		if (file_exists( $path . $minfile) && WP_MINIFY ) {	
			$file=$minfile;
		}
	}
	wp_enqueue_script( $handler, $uri . $file, $deps, $version, $footer );
}


function remove_script($script) {
	wp_deregister_script($script);
	wp_dequeue_script($script);
}


/* =================================================================*/
/* =              CUSTOM STYLES HELPERS     
/* =================================================================*/

// function custom_register_style( $handler, $uri, $path, $file, $deps=array(), $version=false, $media='all' ) {	
// 	$minfile = str_replace( '.css', '.min.css', $file );
// 	if (file_exists( $path . $minfile) && WP_MINIFY ) {
// 		wp_register_style( $handler, $uri . $minfile, $deps, $version, $media );
// 	}
// 	else {
// 		wp_register_style( $handler, $uri . $file, $deps, $version, $media );
// 	}
// }

function custom_enqueue_style( $handler, $uri, $path, $file, $deps=array(), $version=false, $media='all' ) {	
	if ( !strpos($file, '.min.css') ) {
		$minfile = str_replace( '.css', '.min.css', $file );
		if (file_exists( $path . $minfile) && WP_MINIFY ) {	
			$file=$minfile;
		}
	}
    wp_enqueue_style( $handler, $uri . $file, $deps, $version, $media );	
}


/* Optimize page loading by dequeuing specific CSS stylesheets loading actions */
function remove_style($style) {
	wp_dequeue_style($style);
	wp_deregister_style($style);
}

/* =================================================================*/
/* =         GENERATE PICTURE MARKUP FOR .WEBP SUPPORT
/* =================================================================*/

function output_picture_markup($url, $path, $name, $ext=null) {
	?>

	<picture><?php
	if (file_exists( $path . $name . '.webp'))
		echo '<source srcset="' . $url . $name . '.webp" ' . 'type="image/webp">';
	if (isset($ext)) {
		echo '<img src="' . $url . $name . '.' . $ext . '">';
	}
	else {
		if (file_exists( $path . $name . '.jpg')) {
			echo '<img src="' . $url . $name . '.jpg' . '">';
		}
		elseif (file_exists( $path . $name . '.png')) {
			echo '<img src="' . $url . $name . '.png' . '">';
		}
	}
?></picture>
	<?php
}


/* =================================================================*/
/* =              MISC HELPERS
/* =================================================================*/

function url_exists($url) {
	$headers = @get_headers($url);
	return (strpos($headers[0],'404') === false);
}
