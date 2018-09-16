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
define( 'CHILD_THEME_VERSION', '2.2.14' );
define( 'CHILD_THEME_DEVELOPER', 'Shay Bocks' );
define( 'CHILD_THEME_URL', get_stylesheet_directory_uri() );
define( 'CHILD_THEME_PATH', get_stylesheet_directory() );

define( 'CHILD_COLOR_THEME', 'spring' ); // christmas, autumn, winter, summer
define( 'CHILD_COLOR_THEME_VERSION', '1.1.0' ); // triggers browser cache flush

define( 'PLUGINS_URL', plugins_url() );

/* =================================================================*/
/* =              ADMIN
/* =================================================================*/

/**
 * Show all parents, regardless of post status.
 *
 * @param   array  $args  Original get_pages() $args.
 *
 * @return  array  $args  Args set to also include posts with pending, draft, and private status.
 */
add_filter( 'page_attributes_dropdown_pages_args', 'my_slug_show_all_parents' );
add_filter( 'quick_edit_dropdown_pages_args', 'my_slug_show_all_parents' );
function my_slug_show_all_parents( $args ) {
	$args['post_status'] = array( 'publish', 'pending', 'draft', 'private' );
	return $args;
}

/* Chargement des feuilles de style admin */
add_action( 'wp_admin_enqueue_scripts', 'load_admin_stylesheet' );
function load_admin_stylesheet() {
	wp_enqueue_style( 'admin-css', CHILD_THEME_URL . '/assets/css/admin.css', array(), CHILD_THEME_VERSION );		
}

/* =================================================================*/
/* =              FOODIEPRO CHILD THEME SETUP
/* =================================================================*/

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

	/** Reposition header outside container */
	remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
	remove_action( 'genesis_header', 'genesis_do_header' );
	remove_action( 'genesis_header', 'genesis_header_markup_close', 15 ) ;

	add_action( 'genesis_before', 'custom_header_markup_open', 5 );
	add_action( 'genesis_before', 'genesis_do_header' );
	add_action( 'genesis_before', 'custom_header_markup_close', 15 );	

	// Custom Body wrap
	// add_action( 'genesis_before', 'custom_body_markup_open', 15 );	
	// add_action( 'wp_footer', 'custom_body_markup_close', 15 );	

	// function custom_body_markup_open() {
	// 	echo '<div class="body-wrap">';
	// }

	// function custom_body_markup_close() {
	// 	echo '</div>';
	// }
		
	//New Header functions
	function custom_header_markup_open() {
		genesis_markup( array(
			'html5'   => '<header %s>',
			'context' => 'site-header',
		) );
		// Added in content
		echo '<div class="header-inner">';
		genesis_structural_wrap( 'header' );
	}
	function custom_header_markup_close() {
		genesis_structural_wrap( 'header', 'close' ); // widgets area
		do_action('before_header_close');
		echo '</div>';// header-inner
		genesis_markup( array(
			'close'   => '</header>',
			'context' => 'site-header',
		) ); // <header> tag
	}	
	

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

	// P.O. Load the custom helpers
	require_once trailingslashit(CHILD_THEME_PATH) . 'custom-helpers.php';

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
	$classes[] = 'no-js';
	return $classes;
}

/* Add the theme name class to the body element. */
// add_filter('language_attributes', 'modernizr');
// function modernizr($output) {
// 	return $output . ' class="no-js"';
// }


/* =================================================================*/
/* =              FAVICON
/* =================================================================*/

remove_action('wp_head', 'genesis_load_favicon');

/** Adding custom Favicon */
add_action ('genesis_meta','custom_favicon_links');
 
function custom_favicon_links() {
	$path = CHILD_THEME_URL . '/images/favicon';
	echo sprintf('<link rel="apple-touch-icon" sizes="57x57" href="%s/apple-icon-57x57.png">',$path);
	echo sprintf('<link rel="apple-touch-icon" sizes="60x60" href="%s/apple-icon-60x60.png">',$path);
	echo sprintf('<link rel="apple-touch-icon" sizes="72x72" href="%s/apple-icon-72x72.png">',$path);
	echo sprintf('<link rel="apple-touch-icon" sizes="76x76" href="%s/apple-icon-76x76.png">',$path);
	echo sprintf('<link rel="apple-touch-icon" sizes="114x114" href="%s/apple-icon-114x114.png">',$path);
	echo sprintf('<link rel="apple-touch-icon" sizes="120x120" href="%s/apple-icon-120x120.png">',$path);
	echo sprintf('<link rel="apple-touch-icon" sizes="144x144" href="%s/apple-icon-144x144.png">',$path);
	echo sprintf('<link rel="apple-touch-icon" sizes="152x152" href="%s/apple-icon-152x152.png">',$path);
	echo sprintf('<link rel="apple-touch-icon" sizes="180x180" href="%s/apple-icon-180x180.png">',$path);
	echo sprintf('<link rel="icon" type="image/png" sizes="192x192" href="%s/android-icon-192x192.png">',$path);
	echo sprintf('<link rel="icon" type="image/png" sizes="32x32" href="%s/favicon-32x32.png">',$path);
	echo sprintf('<link rel="icon" type="image/png" sizes="96x96" href="%s/favicon-96x96.png">',$path);
	echo sprintf('<link rel="icon" type="image/png" sizes="16x16" href="%s/favicon-16x16.png">',$path);
	echo sprintf('<link rel="manifest" href="%s/manifest.json">',$path);
	echo sprintf('<meta name="msapplication-TileColor" content="#ffffff">',$path);
	echo sprintf('<meta name="msapplication-TileImage" content="%s/ms-icon-144x144.png">',$path);
	echo sprintf('<meta name="theme-color" content="#ffffff">',$path); 
}

/* =================================================================*/
/* =              CUSTOM SCRIPTS ENQUEUE
/* =================================================================*/

add_action( 'wp_enqueue_scripts', 'foodie_pro_enqueue_js' );
/**
 * Load all required JavaScript for the Foodie theme.
 *
 * @since   1.0.1
 * @return  void
 */
function foodie_pro_enqueue_js() {
	$js_uri = CHILD_THEME_URL . '/assets/js/';
	$js_path = CHILD_THEME_PATH . '/assets/js/';
	// Add general purpose scripts.
	custom_enqueue_script( 'foodie-pro-general', $js_uri, $js_path, 'general.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
	custom_enqueue_script( 'custom-js-helpers', $js_uri, $js_path, 'custom_helpers.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
	// .webp detection
	custom_enqueue_script( 'modernizr', $js_uri, $js_path, 'modernizr-custom.js', array(), CHILD_THEME_VERSION );
}


/* Enqueue default WP jQuery in the footer rather than the header 
--------------------------------------------------------------------*/
add_action( 'wp_enqueue_scripts', 'remove_header_scripts' );
function remove_header_scripts() {
  //if ( bp_is_user_change_avatar() ) remove_script( 'BJLL' ); //Prevent conflict between BP & BJ Lazy Load
}

add_action('get_footer','remove_footer_scripts');
function remove_footer_scripts() {
	if (!(is_single())) {
		remove_script('galleria');
		remove_script('galleria-fs');
		remove_script('galleria-fs-theme');		
	}
}

/* Defer Javascript parsing using <async> tag */
add_filter( 'script_loader_tag','add_async_tag_cb', PHP_INT_MAX, 3 );
function add_async_tag_cb( $tag, $handle, $src ) { 
	if ( is_admin() ) return $tag;
	$defer_js = array(
		'masterslider-core',
		'bp-confirm',
		'skip-links',
		'foodie-pro-general'
	);
	if ( in_array($handle,$defer_js) ) {
	  $tag='<script src="' . $src . '" async type="text/javascript"></script>' . "\n";
	}
	return $tag;
} 



/*  Making jQuery Google API  
--------------------------------------------------------*/
//add_action('init', 'load_jquery_from_google');   */
function load_jquery_from_google() {
	if (!is_admin()) {
		// comment out the next two lines to load the local copy of jQuery
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js', false, '1.8.1');
		wp_enqueue_script('jquery');
	}
}

// Prevent Max Mega Menu to load all google fonts
add_action( 'wp_print_styles', 'megamenu_dequeue_google_fonts', 100 );
function megamenu_dequeue_google_fonts() {
   wp_dequeue_style( 'megamenu-google-fonts' );
}


/* =================================================================*/
/* =              CUSTOM STYLES ENQUEUE     
/* =================================================================*/

//* Load Custom Styles & Fonts
add_filter( 'foodie_pro_disable_google_fonts', '__return_true' );
add_action( 'wp_enqueue_scripts', 'foodie_pro_enqueue_stylesheets' );
function foodie_pro_enqueue_stylesheets() {
	
	/* Google Fonts
	--------------------------------------------------- */
	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Amatic+SC:400,700|Oswald|Vollkorn:300,400', array(), CHILD_THEME_VERSION );
	//wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Amatic+SC:400,700|Oswald|Lato:300,400', array(), CHILD_THEME_VERSION );
	
	/* Font Awesome
	--------------------------------------------------- */
	wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
	//wp_enqueue_style( 'font-awesome', CHILD_THEME_URL . '/assets/fonts/font-awesome/css/font-awesome.min.css', array(), CHILD_THEME_VERSION );
	
	/* Theme stylesheet with varying name & version, forces cache busting at browser level
	--------------------------------------------------- */
	$color_theme_url = CHILD_THEME_URL . '/assets/css/';
	$color_theme_path = CHILD_THEME_PATH . '/assets/css/';
	$color_theme_handler = 'color-theme-' . CHILD_COLOR_THEME;
	custom_enqueue_style( $color_theme_handler , $color_theme_url, $color_theme_path, $color_theme_handler . '.css', array(), CHILD_COLOR_THEME . CHILD_COLOR_THEME_VERSION );
}

add_action('wp_enqueue_scripts','remove_header_styles');
function remove_header_styles() {	
	if (!(is_single())) {
  	remove_style('galleria-fs');
	}
  remove_style('popup-maker-site');
  remove_style('cookie-notice-front');
  remove_style('bppp-style');
  remove_style('wpurp_style5');
  remove_style('wpurp_style6');    
  remove_style('wpurp_style7');
  remove_style('wpurp_style11');  	
  remove_style('yarppWidgetCss');
  //remove_style('cnss_font_awesome_css');   
  remove_style('megamenu-fontawesome');
}

add_action('get_footer','remove_footer_styles');
function remove_footer_styles() {
  remove_style('yarpp-thumbnails-yarpp-thumbnail');
}

add_action('wp_enqueue_scripts','remove_bp_styles');
function remove_bp_styles() {
	if (!function_exists( 'bp_is_blog_page')) return;
	if (bp_is_blog_page()) {
  	remove_style('bp-child-css');
	}
}

/* Gestion des feuilles de style minifiées */
add_filter( 'stylesheet_uri', 'enqueue_minified_stylesheet', 10, 1 );

function enqueue_minified_stylesheet( $default_stylesheet_uri ) {
	$path_parts = pathinfo( $default_stylesheet_uri );
	$file = $path_parts['basename'];
	$min_file = str_replace( '.css', '.min.css', $file ); 
	$min_file_path = CHILD_THEME_PATH . '/' . $min_file;
	// echo '<pre>' . "Default stylesheet URI : {$default_stylesheet_uri}" . '</pre>';
	// echo '<pre>' . "Min file : {$min_file}" . '</pre>';
	// echo '<pre>' . "Min file path : { $min_file_path }" . '</pre>';

	if ( file_exists( $min_file_path ) && WP_MINIFY ) {
		$default_stylesheet_uri = CHILD_THEME_URL . '/' . $min_file;
	} 
	return $default_stylesheet_uri;
}


function use_minified_stylesheet( $default_stylesheet_uri ) {
	$stylesheet_path = CHILD_THEME_PATH . '/style.min.css';
	$default_stylesheet_path = CHILD_THEME_PATH . '/style.css';
	$stylesheet_uri =  wp_make_link_relative( CHILD_THEME_URL . '/style.min.css');
	$default_stylesheet_uri =  wp_make_link_relative( $default_stylesheet_uri );
	$min_mod_date = filemtime( $stylesheet_path );
	$orig_mod_date = filemtime( $default_stylesheet_path );
	if ( file_exists( $stylesheet_path ) && ($min_mod_date >= $orig_mod_date) ) {
		//foodiepro_log( 'Minified stylesheet exist and is valid' );
		return $stylesheet_uri;	
	}
	//foodiepro_log( 'Minified stylesheet doesn t exist or too old' );
	return $default_stylesheet_uri;	
}

/* =================================================================*/
/* =              CUSTOM LOGIN                                     =*/
/* =================================================================*/

/* Sets login page color theme */
function my_custom_login() {
	//echo '<link rel="stylesheet" type="text/css" href="' . CHILD_THEME_URL . '/login/custom-login-styles-' . CHILD_COLOR_THEME . '.css" />';
	echo '<link rel="stylesheet" type="text/css" href="' . CHILD_THEME_URL . '/login/custom-login-styles-default.css" />';
}
add_action('login_head', 'my_custom_login');


/* Sets login page logo & url */
function my_login_logo_url() {
	return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
	$output = __('Goûtu.org - La Communauté des Gourmets', 'foodiepro');
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
    if ( $role=='pending' ) {
    	//$approve_url=get_permalink(get_page_by_path('pending-approval'));
    	$approve_url=get_permalink('10066');
    	$msg=sprintf(__( '<strong>ERROR</strong>: User pending <a href="%s">approval</a>.', 'foodiepro' ),$approve_url);
    	return new WP_Error( 'user_not_approved', $msg);
    }
		else
			return $user;
}

/* =================================================================*/
/* =              CUSTOM URLS     
/* =================================================================*/

add_action('init', 'custom_author_base');
function custom_author_base() {
    global $wp_rewrite;
    $author_slug = __('author', 'foodiepro'); // the new slug name
    $wp_rewrite->author_base = $author_slug;
}


/* =================================================================*/
/* =              SEO 
/* =================================================================*/

/* Exclude Multiple Taxonomies From Yoast SEO Sitemap */
add_filter( 'wpseo_sitemap_exclude_taxonomy', 'sitemap_exclude_taxonomy', 10, 2 );
function sitemap_exclude_taxonomy( $value, $taxonomy ) {
	$taxonomy_to_exclude = array( 'slider' );
	if( in_array( $taxonomy, $taxonomy_to_exclude ) ) return true;
}

/* =================================================================*/
/* =              LAYOUT      
/* =================================================================*/

//* Adds custom inline Javascript
// add_action('wp_head','add_custom_js');
function add_custom_js(){
?>
<script>
	jQuery(document).ready(function() {
		// place your code here
	});
</script>
<?php
}

//* Reposition the primary navigation menu within header
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'before_header_close', 'genesis_do_subnav');
	
//* Reposition the primary navigation menu within header
remove_action( 'genesis_after_header', 'genesis_do_nav' );
//add_action( 'genesis_header', 'genesis_do_nav');
		
// Move pagination on all archive pages
remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action( 'genesis_after_content', 'genesis_posts_nav' );

// Move footer widget area (avoid "out of content" issue on buddypress pages)
remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );
add_action( 'genesis_after_content_sidebar_wrap', 'genesis_footer_widget_areas', 999 );


/* Hook widget areas 
-----------------------------------------------------------------------------*/

add_action( 'genesis_after_content', 'add_after_content_area');
function add_after_content_area() {
  //if ( is_page() ) {
  	genesis_widget_area( 'after-content', array(
		'before' => '<div class="bottom after-content widget-area">',
		'after'  => '</div>',
  	));
  //}     
}

// add_action( 'genesis_before_loop', 'add_archive_top_area', 15);
add_action( 'genesis_before_content', 'add_archive_top_area', 15);
function add_archive_top_area() {
	if ( is_archive() || is_search() ) {
		genesis_widget_area( 'archives-top', array(
		    'before' => '<div class="top archives-top widget-area">',
		    'after'  => '</div>',
		));
	}     
}

add_action( 'genesis_after_loop', 'add_archive_bottom_area');
function add_archive_bottom_area() {
	if ( is_archive() || is_search() ) {
		genesis_widget_area( 'archives-bottom', array(
		    'before' => '<div class="bottom archives-bottom widget-area">',
		    'after'  => '</div>',
		));
	}     
}

// add_action( 'genesis_after_content_sidebar_wrap', 'add_post_bottom_area');
// function add_post_bottom_area() {
// 	if ( is_single() ) {
// 	  genesis_widget_area( 'post-bottom', array(
// 	      'before' => '<div class="post-bottom widget-area page-bottom">',
// 	      'after'  => '</div>',
// 		));
// 	}
// }


/* =================================================================*/
/* =              WIDGETS / PLUGINS
/* =================================================================*/

//* Adds Mailchimp newsletter subscribe form
//add_action('wp_head','mailchimp_subscribe_form');
function mailchimp_subscribe_form(){
?>
<script type="text/javascript" src="//downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script><script type="text/javascript">require(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us17.list-manage.com","uuid":"86ca729ff9d0eb5dc6a0d0ff1","lid":"f2167601d1"}) })</script>
<?php
}

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

/* Recent Posts Widget Extended
-------------------------------------------------------------------*/
// Prevent redundant posts when several rpwe instances are called on the same page
$rpwe_exclude_posts=array();
add_action('rpwe_loop','rpwe_get_queried_posts');
add_filter('rpwe_default_query_arguments','rpwe_exclude_posts');

function rpwe_get_queried_posts( $post ) {
	// echo $post->ID;
	noglobal( 'collect', $post->ID);
}

function rpwe_exclude_posts( $query ) {
	// echo '<br>IN FILTER FUNCTION <br>';
	// echo '<br>$query before function : <br>';
	// echo print_r($query);
	$query = noglobal( 'exclude', '', $query);
	// echo '<br>$query after function : <br>';
	// echo print_r($query);
	return $query;
}

function noglobal( $action, $postId='', $query=array() ) {
	static $rpwe_queried_posts=array();
	if ($action=='collect') {
		// echo '<br>In RPWE LOOP ACTION ! <br>';
		// echo $postId;
		$rpwe_queried_posts[]=$postId;
		// echo print_r($rpwe_queried_posts);
		return;
	}
	else {
		// echo '<br>In RPWE DEFAULT QUERY ARGS FILTER ! <br>';
		// echo '$rpwe_queried_posts : <br>';
		// echo print_r($rpwe_queried_posts);
		// echo '<br>$query before merge : <br>';
		// echo print_r($query);
		if (isset($query['post__not_in']) && isset($rpwe_queried_posts)) {
			$query['post__not_in'] = array_merge( $query['post__not_in'], $rpwe_queried_posts );	
		} 
		// echo '<pre>' . '$query after merge : ' . print_r($query) . '</pre>';
		return $query;
	}
}

// Add user rating to RPWE widget
add_filter('rpwe_post_title', 'rpwe_add_rating', 10, 2);
function rpwe_add_rating($title, $args ) {
	$disp_rating = substr($args['cssID'],1,1);
	////foodiepro_log( array('WPRPE Output add rating'=>$output) );
	$output='';
	if ( $disp_rating == '1') {
		$output .= '<span class="entry-rating">';
		$output .= do_shortcode('[display-star-rating display="minimal" category="global" markup="span"]');
		$output .= do_shortcode('[like-count]');
		$output .= '</span>';
	}
	return $title . $output;
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
/* =               ARCHIVES
/* =================================================================*/


/* =================================================================*/
/* =               PAGES
/* =================================================================*/

//* Add icon before page title
add_action( 'genesis_entry_header', 'add_page_icon', 7 );

function add_page_icon() {
	if ( is_page() ) {
		$icon_url = trailingslashit( CHILD_THEME_URL ) . 'images/page-icons/';
		$icon_path = trailingslashit( CHILD_THEME_PATH ) . 'images/page-icons/';
		$key_val = get_post_meta( get_the_ID(), 'entry_header_image', true );
		if ( ! empty( $key_val ) ) {
			$ext = substr(strrchr($key_val, "."), 1);
			$filename = substr($key_val, 0 , (strrpos($key_val, ".")));
			echo '<div class="entry-header-image">';
			output_picture_markup($icon_url, $icon_path, $filename, $ext);
			echo '</div>';	
		}
	}
}

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
/* =               POSTS
/* =================================================================*/

//* Remove the post meta display from footer
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


/* Add social share icons
-------------------------------------------------------*/
add_action( 'genesis_entry_header', 'add_share_icons' , 10 ); /* Original genesis_after_entry_content */
function add_share_icons() {
	if ( is_singular( 'post' ) )
		echo do_shortcode('[mashshare]');
}

/* =================================================================*/
/* =               COMMENTS
/* =================================================================*/


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
/* =      FOOTER           
/* =================================================================*/


//* Change the credits text
function sp_footer_creds_filter( $creds ) {
	/*$creds = '[footer_copyright before="' . __('All rights reserved','foodiepro') . ' " first="2015"] &middot; <a href="https://goutu.org">Goutu.org</a> &middot; <a href="https://goutu.org/contact">' . __('Contact us', 'foodiepro') . '</a> &middot; ' . '<a href=" https://goutu.org/mentions-legales">' . __('Legal notice', 'foodiepro') . '</a>' . ' &middot; ' . __('Goûtu charter','foodiepro') . ' &middot; ' . __('Personal data','foodiepro') . ' &middot; ' . __('Terms of use','foodiepro') . ' &middot; [footer_loginout]';*/
	$creds = '[footer_copyright before="' . __('All rights reserved','foodiepro') . ' " first="2015"] &middot; <a href="\">Goutu.org</a> &middot; <a href="/plus/contact-form">' . __('Contact us', 'foodiepro') . '</a> &middot; ' . '<a href=/plus/mentions-legales">' . __('Legal notice', 'foodiepro') . '</a> &middot; [footer_loginout]';
	//$creds .= '<a href="http://www.beyondsecurity.com/vulnerability-scanner-verification/goutu.org"><img src="https://seal.beyondsecurity.com/verification-images/goutu.org/vulnerability-scanner-2.gif" alt="Website Security Test" border="0" /></a>';
	return $creds;
}

add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');