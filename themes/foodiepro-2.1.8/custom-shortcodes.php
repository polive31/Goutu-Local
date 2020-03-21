<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/* =================================================================*/
/* = SITE LOGO
/* =================================================================*/
add_shortcode('site-logo', 'foodiepro_get_site_logo_path');
function foodiepro_get_site_logo_path( $atts ) {
	$url = get_stylesheet_directory_uri();
	$url = $url . '\images\fb-app-icon-512x512.png';
	return $url;
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
		'id' 	=> '',
		'slug' 	=> false,
		'tax' 	=> false,
		'wp' 	=> false, // home, login, register
		'user' 	=> false, // current, view, author, any user ID
		'peepso' => false, // members, register

		/* Display parameters */
		'class' => '',
		'display' => false, // archive, profile
		'type' => 'post', // post type : post, recipe OR peepso profile tab : about, activity...
		'text' 	=> false,  // html link is output if not empty
		'target' 	=> '',  // link target

		/* Google Analytics parameters */
		'data' 	=> false, // "attr1 val1 attr2 val2  ..." separate with spaces
		'ga' 	=> false, // ga('send', 'event', [eventCategory], [eventAction], [eventLabel], [eventValue] ); separate by spaces

	), $atts);

	return foodiepro_get_permalink($atts, $content);
}
function foodiepro_get_permalink( $atts, $content='' ) {

	extract( $atts );
	$text=$text?esc_html($text):'';
	$content=esc_html($content);
	$data=$data?explode(' ', $data):false;
	$ga=$ga?explode(' ', $ga):false;
	$rel='';

	$url='#';
	$token=''; /* Replacement token for display text */
	if ($id) {
		$url=get_permalink($id);
	}
	elseif ($tax) {
		if (!empty($slug))
			$url=get_term_link((string) $slug, (string) $tax);
	}
	elseif ($slug) {
		// $url=get_permalink(get_page_by_path($slug));
		$url=foodiepro_get_page_by_slug($slug);
	}
	elseif ($user) {
		// Define user
		if ($user=='current') {
			$user_id = get_current_user_id();
		}
		elseif ($user=='author') {
			$user_id = get_the_author_meta('ID');
		}
		elseif ($user=='view' && class_exists('Peepso') ) {
			$user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
		}
		else {
			$user_id = $user;
		}
		// Define display url
		if ($display=='archive') {
			$user = get_user_by('id', $user_id);
			if (!$user) return;
			$token = $user->data->user_nicename;
			// $url = get_site_url( null, foodiepro_get_author_base() . '/' . $token);
			$url = get_author_posts_url($user_id, $token);
			$url = esc_url(add_query_arg('post_type', $type, $url));
			$rel='author';
		}
		elseif ( $display=='profile' && class_exists('Peepso') ) {
			$peepso_user = PeepsoUser::get_instance( $user_id );
			$url = $peepso_user->get_profileurl();
			$url .= $type;
			$token = $peepso_user->get_nicename();
		}
	}
	elseif ($wp) {
		if ( $wp=='home' )
			$url = get_home_url();
		elseif ( $wp=='login' )
			$url = wp_login_url();
		elseif ( $wp=='register' )
			$url = wp_registration_url();
	}
	elseif ($peepso) {
		if (!class_exists('Peepso')) return;
		if ($peepso=='members' ) {
			$url = PeepSo::get_page('members');
		}
		elseif ($peepso=='register') {
			$url= PeepSo::get_page('register');
		}
	}
	else {
		// Current URL is supplied by default
		$url=$_SERVER['REQUEST_URI'];
	}

	if ( $content || $text )
		return '<a class="' . $class . '" rel="' . $rel . '" id="' . $id . '" ' . foodiepro_get_data( $data ) . ' href="' . $url . '" target="' . $target . '" onclik="' . foodiepro_get_ga( $ga ) . '">' . sprintf( $text . $content, $token ) . '</a>';
	else
		return $url;
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
	$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_status = 'publish'", $page_slug ) );
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
