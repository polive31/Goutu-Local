<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* =================================================================*/
/* =              SECURITY
/* =================================================================*/

// Secures translation strings outputs, while allowing some html attributes to be displayed
function sec( $text ) {
	return wp_kses( $text, array( 'br'=>array() ) );
}


/* =================================================================*/
/* =    IMAGE OUTPUT
/* =================================================================*/

function custom_img( $dir, $url, $name ) {

	ob_start();
	?>

	<picture id="cover-image">
	
	<?php
	if (file_exists( $dir . $name . '.webp'))
	echo '<source srcset="' . $url . $name . '.webp" ' . 'type="image/webp">';
	if (file_exists( $dir . $name . '.jpg')) {
		echo '<img src="' . $url . $name . '.jpg' . '">';
	}
	elseif (file_exists( $dir . $name . '.jpeg')) {
		echo '<img src="' . $url . $name . '.jpeg' . '">';
	}				
	elseif (file_exists( $dir . $name . '.png')) {
		echo '<img src="' . $url . $name . '.png' . '">';
	}
	?>
	
	</picture>

	<?php 
	return $html;
}
	
/* =================================================================*/
/* =              CUSTOM SCRIPTS HELPERS
/* =================================================================*/

function custom_register_script( $handler, $file, $uri=CHILD_THEME_URL, $path=CHILD_THEME_PATH, $deps=array(), $version=CHILD_THEME_VERSION, $footer=false ) {	
	if (is_array($file) ) {
		$uri = CHILD_THEME_URL;
		$path = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$footer = false;
		extract($file);
	}
	
	if ( !strpos($file, '.min.js') ) {
		$minfile = str_replace( '.js', '.min.js', $file );
		if (file_exists( $path . $minfile) && WP_MINIFY ) {	
			$file=$minfile;
		}
	}
	wp_register_script( $handler, $uri . $file, $deps, $version, $footer );
}

function custom_enqueue_script( $handler, $file, $uri=CHILD_THEME_URL, $path=CHILD_THEME_PATH, $deps=array(), $version=CHILD_THEME_VERSION, $footer=false ) {	
	if (is_array($file) ) {
		$uri = CHILD_THEME_URL;
		$path = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$footer = false;
		extract($file);
	}

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

function custom_register_style( $handler, $file, $uri=CHILD_THEME_URL, $path=CHILD_THEME_PATH, $deps=array(), $version=CHILD_THEME_VERSION, $media='all' ) {	
	if (is_array($file) ) {
		$uri = CHILD_THEME_URL;
		$path = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$media = 'all';
		extract($file);
	}

	if ( !strpos($file, '.min.css') ) {
		$minfile = str_replace( '.css', '.min.css', $file );
		if (file_exists( $path . $minfile) && WP_MINIFY ) {	
			$file=$minfile;
		}
	}
    wp_register_style( $handler, $uri . $file, $deps, $version, $media );
}

function custom_enqueue_style( $handler, $file, $uri=CHILD_THEME_URL, $path=CHILD_THEME_PATH, $deps=array(), $version=CHILD_THEME_VERSION, $media='all' ) {	
	if (is_array($file) ) {
		$uri = CHILD_THEME_URL;
		$path = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$media = 'all';
		extract($file);
	}
	
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
	global $wp_scripts;
	
	wp_dequeue_style($style);
	wp_deregister_style($style);

}

/* =================================================================*/
/* =         GENERATE PICTURE MARKUP FOR .WEbp SUPPORT
/* =================================================================*/

function output_picture_markup($url, $path, $name, $ext=null) {
	echo '<picture>';
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
	echo '</picture>';
}


/* =================================================================*/
/* =              MISC HELPERS
/* =================================================================*/

function url_exists($url) {
	$headers = @get_headers($url);
	return (strpos($headers[0],'404') === false);
}
