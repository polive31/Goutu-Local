<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}



/* =================================================================*/
/* = SITE LOGO
/* =================================================================*/
add_shortcode('site-logo', 'foodiepro_get_site_logo_path');
function foodiepro_get_site_logo_path( $atts=array() ) {
	$url = CHILD_THEME_URL;
	$url = $url . '\images\favicon\fb-app-icon-512x512.png';
	return $url;
}


/* =================================================================*/
/* = WIDGET AREA CONTROL SHORTCODE
/* =================================================================*/
add_shortcode('widget_area', 'foodiepro_widget_area_shortcode');
/**
 * Display widget area with shortcode.
 *
 * @since  1.0.0
 *
 * @return string
 */
function foodiepro_widget_area_shortcode($atts)
{
	$atts = shortcode_atts(
		array(
			'id' 	=> '',
			'class' => '',
		),
		$atts,
		'widget_area'
	);
	ob_start();
	genesis_widget_area($atts['id'], array(
		'before' => '<div class="' . $atts['class'] . ' widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	));
	return ob_get_clean();
}

/* =================================================================*/
/* = IF SHORTCODE
/* =================================================================*/
add_shortcode('if', 'foodiepro_display_if');
function foodiepro_display_if( $atts, $content ) {
	$atts = shortcode_atts( array(
		'user' => '', //logged-in, logged-out
	), $atts );

	$display=true;
	$user=$atts['user'];

	if ( $user=='logged-out' )
	$display=$display && !is_user_logged_in();
	elseif ( $user=='logged-in' )
	$display=$display && is_user_logged_in();

	return $display?do_shortcode($content):'';
}

/* =================================================================*/
/* = POSTS CREATION HELPERS SHORTCODE
/* =================================================================*/
add_shortcode('glossary', 'foodiepro_glossary_search');
function foodiepro_glossary_search( $atts, $content ) {
	$atts = shortcode_atts( array(
		'glossaryslug' => 'lexique-de-cuisine',
		'searchkey' => 'name-directory-search-value',
		'dir' => '2',
		'term' => '',
	), $atts );
	$term = !empty($atts['term'])? $atts['term']:$content;
	$glossary_url = foodiepro_get_page_by_slug($atts['glossaryslug']);
	$url=add_query_arg(
		array(
			$atts['searchkey'] 	=> strip_tags($term),
			'dir'				=> $atts['dir'],
		),
		$glossary_url
	);
	$html='<a href="' . $url . '">' . $content . '</a>';
	return $html;
}

add_shortcode('search', 'foodiepro_search_posts');
function foodiepro_search_posts( $atts, $content ) {
	$atts = shortcode_atts( array(
		'searchkey' => 's',
	), $atts );
	$html=add_query_arg( $atts['searchkey'], $content, get_site_url());
	$html='<a href="' . $html . '">' . $content . '</a>';
	return $html;
}


/* =================================================================*/
/* = PERMALINK SHORTCODE
/* =================================================================*/
add_shortcode('permalink', 'foodiepro_get_permalink_shortcode');
function foodiepro_get_permalink_shortcode($atts, $content='') {
	$atts = shortcode_atts(array(
		/* Source parameters */
		'id' 		=> '',
		'slug' 		=> false,
		'tax' 		=> false,
		'wp' 		=> false, // home, login, register
		'user' 		=> false, // current, view, author, any user ID
		'community' => '', // members, register
		'google' 	=> '', // search query

		/* Display parameters */
		'class'	 	=> '',
		'display' 	=> false, // archive, profile
		'type' 		=> 'post', // post type : post, recipe OR peepso profile tab : about, activity, friends...
		'text' 		=> '',  // html link is output if not empty
		'target' 	=> '',  // link target

		/* Google Analytics parameters */
		'data' 		=> false, // "attr1 val1 attr2 val2  ..." separate with spaces
		'ga' 		=> false, // ga('send', 'event', [eventCategory], [eventAction], [eventLabel], [eventValue] ); separate by spaces
	), $atts);
	$atts['text']= $atts['text']. $content;

	return foodiepro_get_permalink($atts);
}


/* =================================================================*/
/* = PERMALINK HELPERS
/* =================================================================*/

function foodiepro_get_ga( $ga ) {
	if ( !$ga || !is_array($ga) ) return;
	$html = "ga('send', 'event' ";
	foreach ( $ga as $field ) {
		$html .= ",'$field' ";
	}
	$html .= ");";
	return $html;
}

function foodiepro_get_data( $data ) {
	if ( !$data || ( count($data) % 2 != 0) ) return;
	$html = '';
	$i = 0;
	while ( isset($data[$i]) ) {
		$html .= 'data-' . $data[$i] . '="' . $data[$i+1] . '" ';
		$i=$i+2;
	}
	return $html;
}

function foodiepro_get_page_by_slug($page_slug ) {
	global $wpdb;
	$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND (post_status = 'publish' OR post_status = 'private')", $page_slug ) );
	if ( $page )
	return get_permalink($page);
	return null;
}


/* =================================================================*/
/* = DEBUG SHORTCODE
/* =================================================================*/
add_shortcode('debug', 'foodiepro_show_debug_html');
function foodiepro_show_debug_html( $atts, $content ) {
	return WP_DEBUG?$content:'';
}


/* =================================================================*/
/* = POST COUNT SHORTCODE
/* =================================================================*/
add_shortcode('post-count', 'foodiepro_get_post_count');
function foodiepro_get_post_count( $atts ) {
	//Let's not loose time if user doesn't have the rights
	if( !current_user_can('editor') && !current_user_can('administrator') ) return;

	$atts = shortcode_atts( array(
		'status' => 'pending', //draft, publish, auto-draft, private, separated by " "
		'type' => 'post', //recipe
	), $atts );

	$post_type=$atts['type'];
	$status=$atts['status'];

	if ( !in_array($status, get_post_statuses() )) return;

	$count = wp_count_posts($post_type );
	if (isset($count->$status)) {
		$html = ($count->$status>0)?'<span class="post-count-indicator">('.$count->$status.')</span>':'';
	}

	return $html;
}
