<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}


/* =================================================================*/
/* =              SECURITY
/* =================================================================*/

// Secures translation strings outputs, while allowing some html attributes to be displayed
function esc($text)
{
	return wp_kses($text, ALLOWED_TAGS);
}


/* =================================================================*/
/* =    IMAGE OUTPUT
/* =================================================================*/

function picture($url, $id = '', $class = '')
{
	/* Generates a picture tag including .webp format, based the specified original image file (jpg, png, or other non-webp standard format) url */

	$image = pathinfo($url);
	$basename = $image['basename'];
	$extension = $image['extension'];
	$srcext = ($extension=='jpg'|| $extension == 'jpeg')?'jpeg':$extension;
	$dirname = $image['dirname'];

	ob_start();
?>

	<picture id="<?= $id; ?>" class="<?= $class; ?>">
		<source srcset="<?= $dirname . $basename . '.webp'; ?>" type="image/webp">
		<source srcset="<?= $dirname . $basename . '.' . $extension; ?>" type="image/<?= $srcext ?>">
		<img src="<?= $dirname . $basename . '.' . $extension; ?>">
	</picture>

<?php
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}

/* =================================================================*/
/* =              CUSTOM SCRIPTS HELPERS
/* =================================================================*/

function custom_register_script($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $footer = false, $data = array())
{
	if (is_array($handle)) {
		$uri = CHILD_THEME_URL;
		$dir = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$footer = false;
		extract($handle);
	}

	if (!strpos($file, '.min.js')) {
		$minfile = str_replace('.js', '.min.js', $file);
		if (file_exists($dir . $minfile) && WP_MINIFY) {
			$file = $minfile;
		}
	}
	wp_register_script($handle, $uri . $file, $deps, $version, $footer);

	if (!empty($data)) {
		$name = $data['name'];
		unset($data['name']);
		wp_localize_script($handle, $name, $data);
	}
}

function custom_enqueue_script($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $footer = false)
{
	if (is_array($handle)) {
		$uri = CHILD_THEME_URL;
		$dir = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$footer = false;
		extract($handle);
	}

	if (!strpos($file, '.min.js')) {
		$minfile = str_replace('.js', '.min.js', $file);
		if (file_exists($dir . $minfile) && WP_MINIFY) {
			$file = $minfile;
		}
	}
	wp_enqueue_script($handle, $uri . $file, $deps, $version, $footer);

	if (!empty($data)) {
		$name = $data['name'];
		unset($data['name']);
		wp_localize_script($handle, $name, $data);
	}
}


function remove_script($script)
{
	wp_deregister_script($script);
	wp_dequeue_script($script);
}


/* =================================================================*/
/* =              CUSTOM STYLES HELPERS
/* =================================================================*/

function custom_register_style($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $media = 'all')
{
	if (is_array($handle)) {
		$uri = CHILD_THEME_URL;
		$dir = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$media = 'all';
		extract($handle);
	}

	if (!strpos($file, '.min.css')) {
		$minfile = str_replace('.css', '.min.css', $file);
		if (file_exists($dir . $minfile) && WP_MINIFY) {
			$file = $minfile;
		}
	}
	wp_register_style($handle, $uri . $file, $deps, $version, $media);
}

function custom_enqueue_style($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $media = 'all')
{
	if (is_array($handle)) {
		$uri = CHILD_THEME_URL;
		$dir = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$media = 'all';
		extract($handle);
	}

	if (!strpos($file, '.min.css')) {
		$minfile = str_replace('.css', '.min.css', $file);
		if (file_exists($dir . $minfile) && WP_MINIFY) {
			$file = $minfile;
		}
	}
	wp_enqueue_style($handle, $uri . $file, $deps, $version, $media);
}


/* Optimize page loading by dequeuing specific CSS stylesheets loading actions */
function remove_style($style)
{
	global $wp_scripts;

	wp_dequeue_style($style);
	wp_deregister_style($style);
}

/* =================================================================*/
/* =         GENERATE PICTURE MARKUP FOR .WEbp SUPPORT
/* =================================================================*/

function output_picture_markup($url, $path, $name, $ext = null)
{
	echo '<picture>';
	if (file_exists($path . $name . '.webp'))
		echo '<source srcset="' . $url . $name . '.webp" ' . 'type="image/webp">';
	if (isset($ext)) {
		echo '<img src="' . $url . $name . '.' . $ext . '">';
	} else {
		if (file_exists($path . $name . '.jpg')) {
			echo '<img src="' . $url . $name . '.jpg' . '">';
		} elseif (file_exists($path . $name . '.png')) {
			echo '<img src="' . $url . $name . '.png' . '">';
		}
	}
	echo '</picture>';
}


/* =================================================================*/
/* =              MISC HELPERS
/* =================================================================*/

function url_exists($url)
{
	$headers = @get_headers($url);
	return (strpos($headers[0], '404') === false);
}

function initial_is_vowel($expression)
{
	if (empty($expression)) return false;

	$vowels = array('a', 'e', 'i', 'o', 'u');
	$exceptions = array('huile', 'herbes', 'hiver');

	$name = remove_accents($expression);
	$first_letter = strtolower($name[0]);
	$first_word = strtolower(explode(' ', trim($name))[0]);
	return (in_array($first_letter, $vowels) || in_array($first_word, $exceptions));
}
