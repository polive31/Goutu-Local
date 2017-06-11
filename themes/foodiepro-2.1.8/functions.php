<?php
/**
 * Custom amendments for the theme.
 *
 * @package     FoodiePro
 * @subpackage  Genesis
 * @copyright   Copyright (c) 2014, Shay Bocks
 * @license     GPL-2.0+
 * @link        http://www.shaybocks.com/foodie-pro/
 * @since       1.0.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CHILD_THEME_NAME', 'Foodie Pro Theme' );
define( 'CHILD_THEME_VERSION', '2.1.8' );
define( 'CHILD_THEME_DEVELOPER', 'Shay Bocks' );
//define( 'CHILD_COLOR_THEME', 'autumn' ); 
define( 'CHILD_COLOR_THEME', 'white' ); 
define( 'CHILD_THEME_URL', get_stylesheet_directory_uri() );
define( 'CHILD_THEME_PATH', get_stylesheet_directory() );
define( 'PLUGINS_URL', plugins_url() );


add_action( 'after_setup_theme', 'foodie_pro_load_textdomain' );
/**
 * Loads the child theme textdomain.
 *
 * @since  2.1.0
 * @return void
 */
function foodie_pro_load_textdomain() {
	load_child_theme_textdomain(
		'foodiepro',
		trailingslashit( get_stylesheet_directory() ) . 'languages'
	);
}

add_action( 'genesis_setup', 'foodie_pro_theme_setup', 15 );


/* =================================================================*/
// =                   THEME SETUP & LOADING
/* =================================================================*/

/**
 * Theme Setup
 *
 * This setup function hooks into the Genesis Framework to allow access to all
 * of the core Genesis functions. All the child theme functionality can be found
 * in files located within the /includes/ directory.
 *
 * @since  1.0.1
 * @return void
 */
function foodie_pro_theme_setup() {
	//* Add viewport meta tag for mobile browsers.
	add_theme_support( 'genesis-responsive-viewport' );

	//* Add HTML5 markup structure.
	add_theme_support( 'html5' );

	//*	Set content width.
	$content_width = apply_filters( 'content_width', 610, 610, 980 );

	//* Add new featured image sizes.
	add_image_size( 'horizontal-thumbnail', 680, 450, true );
	//add_image_size( 'flexslider', 680, 400, true );
	add_image_size( 'vertical-thumbnail', 680, 900, true );
	add_image_size( 'square-thumbnail', 320, 320, true );
	add_image_size( 'medium-thumbnail', 450, 450, true );
	add_image_size( 'mini-thumbnail', 75, 75, true );

	//* Add Accessibility support
	add_theme_support(
		'genesis-accessibility',
		array(
			'headings',
			'search-form',
			'skip-links',
		)
	);

	/* Disables Genesis responsive menu toggle */
	remove_action( 'wp_enqueue_scripts', 'genesis_sample_enqueue_menu_scripts_styles' );

	//* Add support for custom background.
	add_theme_support( 'custom-background' );

	//* Add support for custom header.
	add_theme_support( 'genesis-custom-header', array(
			'width'  => 1400, /*P.O. Original 800 */
			'height' => 260, /*P.O. Original 340 */
			'header_callback' => 'goutu_custom_header_style',
		)
	);

	/* Original file : genesis/lib/structure/header.php */
	function goutu_custom_header_style() {
		$output = '';

		$header_image = get_header_image();
		$text_color   = get_header_textcolor();

		// If no options set, don't waste the output. Do nothing.
		if ( empty( $header_image ) && ! display_header_text() && $text_color === get_theme_support( 'custom-header', 'default-text-color' ) )
			return;

		$header_selector = get_theme_support( 'custom-header', 'header-selector' );
		$title_selector  = genesis_html5() ? '.custom-header .site-title'       : '.custom-header #title';
		$desc_selector   = genesis_html5() ? '.custom-header .site-description' : '.custom-header #description';

		// Header selector fallback.
		if ( ! $header_selector )
			$header_selector = genesis_html5() ? '.custom-header .site-header' : '.custom-header #header';

		// Header image CSS, if exists.
		//if ( $header_image )
		if ( is_front_page() && $header_image )
			$output .= sprintf( '%s { background: url(%s) no-repeat !important; }', $header_selector, esc_url( $header_image ) );

		// Header text color CSS, if showing text.
		if ( display_header_text() && $text_color !== get_theme_support( 'custom-header', 'default-text-color' ) )
			$output .= sprintf( '%2$s a, %2$s a:hover, %3$s { color: #%1$s !important; }', esc_html( $text_color ), esc_html( $title_selector ), esc_html( $desc_selector ) );

		if ( $output )
			printf( '<style type="text/css">%s</style>' . "\n", $output );

	}

	//* Add support for 4-column footer widgets.
		add_theme_support( 'genesis-footer-widgets', 4 );
}

/* =================================================================*/
/* =        LOAD OF FOODIE INCLUDES                                =*/
/* =================================================================*/

add_action( 'genesis_setup', 'foodie_pro_includes', 20 );
/**
 * Load additional functions and helpers.
 *
 * DO NOT MODIFY ANYTHING IN THIS FUNCTION.
 *
 * @since   2.0.0
 * @return  void
 */
function foodie_pro_includes() {
	$includes_dir = trailingslashit( get_stylesheet_directory() ) . 'includes/';

	// Load the customizer library.
	require_once $includes_dir . 'vendor/customizer-library/customizer-library.php';

	// Load all customizer files.
	require_once $includes_dir . 'customizer/customizer-display.php';
	require_once $includes_dir . 'customizer/customizer-settings.php';

	// Load everything in the includes root directory.
	require_once $includes_dir . 'helper-functions.php';
	require_once $includes_dir . 'compatability.php';
	require_once $includes_dir . 'simple-grid.php';
	require_once $includes_dir . 'widgeted-areas.php';
	require_once $includes_dir . 'widgets.php';

	// End here if we're not in the admin panel.
	if ( ! is_admin() ) {
		return;
	}

	// Load the TGM Plugin Activation class.
	//require_once $includes_dir . 'vendor/class-tgm-plugin-activation.php';

	// Load everything in the admin root directory.
	require_once $includes_dir . 'admin/functions.php';
	
}


/* =================================================================*/
/* =                       LOAD GENESIS      
/* =================================================================*/

//Child Theme Language override
define('GENESIS_LANGUAGES_DIR', STYLESHEETPATH.'/languages/genesis');
define('GENESIS_LANGUAGES_URL', STYLESHEETPATH.'/languages/genesis');

/**
 * Load Genesis
 *
 * This is technically not needed.
 * However, to make functions.php snippets work, it is necessary.
 */
require_once( get_template_directory() . '/lib/init.php' );


add_action( 'wp_enqueue_scripts', 'foodie_pro_enqueue_js' );
/**
 * Load all required JavaScript for the Foodie theme.
 *
 * @since   1.0.1
 * @return  void
 */
function foodie_pro_enqueue_js() {
	$js_uri = CHILD_THEME_URL . '/assets/js/';
	// Add general purpose scripts.
	wp_enqueue_script(
		'foodie-pro-general',
		$js_uri . 'general.js',
		array( 'jquery' ),
		CHILD_THEME_VERSION,
		true
	);
}


/**
 * Add the theme name class to the body element.
 *
 * @since  1.0.0
 *
 * @param  string $classes
 * @return string Modified body classes.
 */
add_filter( 'body_class', 'foodie_pro_add_body_class' );
function foodie_pro_add_body_class( $classes ) {
	$classes[] = 'foodie-pro';
	return $classes;
}

/* =================================================================*/
/* =              DEBUG
/* =================================================================*/



/* =================================================================*/
/* =              CUSTOM SCRIPTS ENQUEUE
/* =================================================================*/

/* Enqueue default WP jQuery in the footer rather than the header 
--------------------------------------------------------------------*/
//add_action( 'wp_default_scripts', 'move_jquery_into_footer' );
//function move_jquery_into_footer( $wp_scripts ) {
//    if( is_admin() ) return;
//    $wp_scripts->add_data( 'jquery', 'group', true );
//    $wp_scripts->add_data( 'jquery-core', 'group', true );
//    $wp_scripts->add_data( 'jquery-migrate', 'group', true );
//}


//Making jQuery Google API
//add_action('init', 'load_jquery_from_google');
function load_jquery_from_google() {
	if (!is_admin()) {
		// comment out the next two lines to load the local copy of jQuery
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js', false, '1.8.1');
		wp_enqueue_script('jquery');
	}
}


/* =================================================================*/
/* =              ADMIN
/* =================================================================*/


/* =================================================================*/
/* =              STYLING     
/* =================================================================*/

//* Load Custom Google Fonts
add_filter( 'foodie_pro_disable_google_fonts', '__return_true' );
add_action( 'wp_enqueue_scripts', 'foodie_pro_enqueue_syles' );
function foodie_pro_enqueue_syles() {
	//wp_enqueue_style( 'font-awesome', CHILD_THEME_URL . '/assets/fonts/font-awesome/css/font-awesome.min.css', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Amatic+SC:400|PT+Sans+Narrow|Roboto+Slab:100|Lato:300|Delius+Swash+Caps', array(), CHILD_THEME_VERSION );
}


/* Chargement des feuilles de style custom et polices */
add_action( 'wp_enqueue_scripts', 'load_custom_stylesheet' );
function load_custom_stylesheet() {
	if ( CHILD_COLOR_THEME=='autumn')
		wp_enqueue_style( 'color-theme-autumn', CHILD_THEME_URL . '/assets/css/color-theme-autumn.css', array(), CHILD_THEME_VERSION );
	else 
		wp_enqueue_style( 'color-theme-white', CHILD_THEME_URL . '/assets/css/color-theme-white.css', array(), CHILD_THEME_VERSION );		
}

/* Gestion des feuilles de style minifiées */
//add_filter( 'stylesheet_uri', 'use_minified_stylesheet', 10, 1 );
function use_minified_stylesheet( $default_stylesheet_uri ) {
//	$stylesheet_path = CHILD_THEME_PATH . '/style.min.css';
//	$default_stylesheet_path = CHILD_THEME_PATH . '/style.css';
//	$stylesheet_uri =  wp_make_link_relative( CHILD_THEME_URL . '/style.min.css');
//	$default_stylesheet_uri =  wp_make_link_relative( $default_stylesheet_uri );
//	$min_mod_date = filemtime( $stylesheet_path );
//	$orig_mod_date = filemtime( $default_stylesheet_path );
//	if ( file_exists( $stylesheet_path ) && ($min_mod_date >= $orig_mod_date) ) {
//		//PHP_Debug::log( 'Minified stylesheet exist and is valid' );
//		return $stylesheet_uri;	
//	}
//	//PHP_Debug::log( 'Minified stylesheet doesn t exist or too old' );
//	return $default_stylesheet_uri;	
}


/* Suppression de la feuille de style YARPP */
function yarpp_dequeue_footer_styles() {
  wp_dequeue_style('yarppRelatedCss');
  wp_dequeue_style('yarpp-thumbnails-yarpp-thumbnail');
}
add_action('get_footer','yarpp_dequeue_footer_styles');


/* =================================================================*/
/* =              CUSTOM LOGIN                                     =*/
/* =================================================================*/

/* Sets login page color theme */
function my_custom_login() {
	if ( CHILD_COLOR_THEME=='autumn')
		echo '<link rel="stylesheet" type="text/css" href="' . CHILD_THEME_URL . '/login/custom-login-styles-autumn.css" />';
	else 
		echo '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/login/custom-login-styles-white.css" />';
}
add_action('login_head', 'my_custom_login');


/* Sets login page logo & url */
function my_login_logo_url() {
	return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
	$output = __('Goûtu.org - Cuisiner, Découvrir, Partager', 'foodiepro');
	return $output;
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );


/* Disable admin bar for all users except admin */
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin())
  	show_admin_bar(false); 
}
add_action('after_setup_theme', 'remove_admin_bar');


/* Disable dashboard for non admin */
function blockusers_init() {
	if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_redirect( home_url() );
		exit;
	}
}
add_action( 'init', 'blockusers_init' );


/* Redirect towards homepage on logout */
function go_home() {
  wp_redirect( home_url() );
  exit;
}
add_action('wp_logout','go_home');


/* Prevent new users (not yet approved) to log in */
add_filter('wp_authenticate_user', 'block_new_users',10,1);
function block_new_users ($user) {
		$role=$user->roles[0];
    if ( $role=='pending' )
    	return new WP_Error( 'user_not_approved', __( '<strong>ERROR</strong>: User pending ', 'foodiepro') . '<a href="' . get_page_link(10066) . '"> ' . __('approval', 'foodiepro') . ' </a>');
		else
			return $user;
}





/* =================================================================*/
/* =              LAYOUT      
/* =================================================================*/

// Move pagination on all archive pages
remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action( 'genesis_after_content', 'genesis_posts_nav' );


/* =================================================================*/
/* =                 RECIPES
/* =================================================================*/


/* =================================================================*/
/* =                      WIDGETS
/* =================================================================*/

// Allow Text widgets to execute shortcodes
add_filter( 'widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

// Enable PHP in widgets
add_filter('widget_text','execute_php',100);
function execute_php($html){
	if(strpos($html,"<"."?php")!==false){
	    ob_start();
	    eval("?".">".$html);
	    $html=ob_get_contents();
	    ob_end_clean();
	}
	return $html;
}

// Add user rating to WPRPE widget
add_filter('rpwe_after_thumbnail', 'wprpe_add_rating', 10, 2);
function wprpe_add_rating($output, $args ) {
		$disp_rating = substr($args['cssID'],1,1);
		////PHP_Debug::log( array('WPRPE Output add rating'=>$output) );
		if ( $disp_rating == '1') {
			$output .= '<div class="rpwe-title">' . do_shortcode('[display-star-rating display="minimal" category="global"]') . '</div>';
		}
	return $output;
}


/* Modify WP Recent Posts ordering, depending on the orderby field value */
add_filter( 'rpwe_default_query_arguments', 'wprpe_orderby_rating' );
function wprpe_orderby_rating( $args ) {
	if ( $args['orderby'] == 'meta_value_num')
		//$args['meta_key'] = 'user_rating_global';
		$args['meta_key'] = 'user_rating_global';
	return $args;
}


/* =================================================================*/
/* =                      ARCHIVES
/* =================================================================*/

/* Hook category widget areas before post content and after archive title
-----------------------------------------------------------------------------*/
add_action( 'genesis_before_loop', 'add_archive_widgeted_area');
function add_archive_widgeted_area() {
  if ( is_archive() || is_search() ) {
  		genesis_widget_area( 'archives-top', array(
        'before' => '<div class="archives-top widget-area">',
        'after'  => '</div>',
  		));
  }     
}


/* Customize entry title in the archive pages
-----------------------------------------------------------------------------*/
function archive_title($title) {
	if ( is_tax() || is_search() ) :
			$title .= do_shortcode('[display-star-rating category="global" display="minimal"]');
	endif;

	if ( is_tax('cuisine', array('france', 'europe', 'asie', 'afrique', 'amerique-nord', 'amerique-sud') ) ) :
		$origin = wp_get_post_terms( get_the_ID(), 'cuisine', array("fields" => "names"));
		$title .= '<div class="origin">' . $origin[0] . '</div>';
	endif;

	return $title;
}
add_filter( 'genesis_post_title_output', 'archive_title', 15 );


/* =================================================================*/
/* =                      DEBUG
/* =================================================================*/
if (! class_exists( 'PHP_Debug')) {
	class PHP_Debug {
		public function log( $msg, $var=false) {}
		public function trace( $msg, $var=false) {}
	}
}


add_action( 'genesis_before_content', 'display_debug_info' );
function display_debug_info() {
	if (is_single()) {

		$post_id = get_the_id();

		//PHP_Debug::log( 'In foodiepro functions.php' );		
		$output = get_post_meta( $post_id , '' , true);
		//PHP_Debug::log( 'get_post_meta( $post_id ) : ',$output);

		//$output = get_post_meta( $post_id, 'user_ratings' );
		//PHP_Debug::log(array('user_ratings : '=> $output) );

		//$output = get_post_meta( $post_id, 'user_rating_stats' );
		//PHP_Debug::log(array('user_rating_stats : '=> $output) );
		
//		delete_post_meta( $post_id, 'recipe_user_ratings' );
//		$user_ratings_update = get_post_meta( $post_id, 'recipe_user_ratings' );
//		echo 'Recipe user ratings after deletion : ';
//		print_r($user_ratings_update);

		//echo '</pre>';			
	}
}


/* =================================================================*/
/* =                      POSTS
/* =================================================================*/

//* Remove the post meta display
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

/* Remove mention from private & protected titles */
function title_format($content) {
	return '%s';
}
add_filter('private_title_format', 'title_format');
add_filter('protected_title_format', 'title_format');


//* Add readmore links
add_filter( 'excerpt_more', 'foodie_pro_read_more_link' );
add_filter( 'get_the_content_more_link', 'foodie_pro_read_more_link' );
add_filter( 'the_content_more_link', 'foodie_pro_read_more_link' );


/* Modify the Genesis read more link
-------------------------------------------------------*/
function foodie_pro_read_more_link() {
	return '...</p><p><a class="more-link" href="' . get_permalink() . '">' . __( 'Read More', 'foodiepro' ) . ' &raquo;</a></p>';
}


//* Add social share icons
function add_share_icons() {
	if ( is_singular( 'post' ) )
		echo do_shortcode('[mashshare]');
}
//add_action( 'genesis_entry_footer', 'add_share_icons' , 10 ); /* Original genesis_after_entry_content */


/* Remove comment form unless it's a comment reply page
-------------------------------------------------------*/
add_action( 'genesis_comment_form', 'remove_recipe_comments_form', 0 );
function remove_recipe_comments_form() {
	if ( is_singular( 'recipe' ) ) {
		$url = $_SERVER["REQUEST_URI"];
		$is_comment_reply = strpos($url, 'replytocom');
		if ( ! $is_comment_reply )
			remove_action( 'genesis_comment_form', 'genesis_do_comment_form' );
	}
}

/* Customize comment section title 
------------------------------------------------------*/
add_filter('genesis_title_comments', 'custom_comment_text');
function custom_comment_text() {
	$title = __('Comments','genesis');
	return ('<h3>' . $title . '</h3>');
}


/* Customize navigation links 
------------------------------------------------------*/
add_filter('genesis_prev_comments_link_text', 'custom_comments_prev_link_text');
function custom_comments_prev_link_text() {
	$text = __('Previous comments','foodiepro');
	return $text;
}

add_filter('genesis_next_comments_link_text', 'custom_comments_next_link_text');
function custom_comments_next_link_text() {
	$text = __('Next comments','foodiepro');
	return $text;
}


/* Disable url input box in comment form unlogged users
------------------------------------------------------*/
add_filter('comment_form_default_fields','customize_comment_form');
function customize_comment_form($fields) { 
  unset($fields['url']);
  return $fields;
}

/* Disable logged in / logged out link
------------------------------------------------------*/
add_filter( 'comment_form_defaults', 'change_comment_form_defaults' );
function change_comment_form_defaults( $defaults ) {
  $defaults['logged_in_as'] = '';
  $defaults['id_form'] = 'respond';
  $defaults['title_reply_to'] = __('Your answer here','foodiepro');
  $defaults['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
  return $defaults;
}

/* =================================================================*/
/* =          AVATARS
/* =================================================================*/

// Moved to plugins/bp-custom.php


/* =================================================================*/
/* =                      FOOTER
/* =================================================================*/


//* Change the credits text
function sp_footer_creds_filter( $creds ) {
	$creds = '[footer_copyright before="' . __('All rights reserved','foodiepro') . ' " first="2015"] &middot; <a href="http://goutu.org">Goutu.org</a> &middot; <a href="http://goutu.org/contact">' . __('Contact us', 'foodiepro') . '</a> &middot; ' . __('Legal notice', 'foodiepro') . ' &middot; ' . __('Goûtu charter','foodiepro') . ' &middot; ' . __('Personal data','foodiepro') . ' &middot; ' . __('Terms of use','foodiepro') . ' &middot; [footer_loginout]';
	//$creds .= '<a href="http://www.beyondsecurity.com/vulnerability-scanner-verification/goutu.org"><img src="https://seal.beyondsecurity.com/verification-images/goutu.org/vulnerability-scanner-2.gif" alt="Website Security Test" border="0" /></a>';
	return $creds;
}

add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');