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
define( 'CHILD_THEME_VERSION', '2.2.67' );
define( 'CHILD_THEME_DEVELOPER', 'Shay Bocks' );
define( 'CHILD_THEME_URL', get_stylesheet_directory_uri() );
define( 'CHILD_THEME_PATH', get_stylesheet_directory() );

define( 'CHILD_COLOR_THEME', 'winter' ); // christmas, autumn, winter, summer

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
	if ( is_admin() ) {
		// Load everything in the admin root directory.
		require_once $includes_dir . 'admin/functions.php';
	}

	
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
/* =              CUSTOM LOGIN                                     =*/
/* =================================================================*/

/* Sets login page color theme */
add_action('login_head', 'my_custom_login');
function my_custom_login() {
	//echo '<link rel="stylesheet" type="text/css" href="' . CHILD_THEME_URL . '/login/custom-login-styles-' . CHILD_COLOR_THEME . '.css" />';
	echo '<link rel="stylesheet" type="text/css" href="' . CHILD_THEME_URL . '/login/custom-login-styles-default.css" />';
}

/* Sets login page logo & url */
add_filter( 'login_headerurl', 'my_login_logo_url' );
function my_login_logo_url() {
	return get_bloginfo( 'url' );
}

add_filter( 'login_form_defaults', 'my_login_page' );
function my_login_page() {
	$defaults = array(
		'label_username' => __('Enter Username', 'foodiepro'),
		'label_password' => __('Enter Password', 'foodiepro'),
		'label_remember' => __('Remember Login State', 'foodiepro'),
		'label_log_in'   => __('Please Log In', 'foodiepro'),
	);
	return $defaults;
}

add_filter( 'login_headertitle', 'my_login_logo_url_title' );
function my_login_logo_url_title() {
	$output = __('Goûtu.org - La Communauté des Gourmets', 'foodiepro');
	return $output;
}

/* Disable admin bar for all users except admin */
// add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin())
  	show_admin_bar(false); 
}

/* Disable dashboard for non admin */
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
	if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_redirect( home_url() );
		exit;
	}
}

/* Redirect towards homepage on logout */
add_action('wp_logout','go_home');
function go_home() {
  wp_redirect( home_url() );
  exit;
}


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
/* =              SECURITY
/* =================================================================*/
remove_action('wp_head', 'wp_generator');

/* =================================================================*/
/* =             PHP DEBUG   
/* =================================================================*/

// add_action( 'wp', 'display_trace');
function display_trace() {
	if ( class_exists( 'PHP_Debug' ) ) {
		$debug = new PHP_Debug();
		$debug->trace('WP PHP Debug plugin activated');
	}
}

function php_log($msg, $var=false, $type='DEBUG', $color='blue') {
	if ( class_exists( 'PHP_Debug' ) ) {
		PHP_Debug::log($msg, $var=false, $type='DEBUG', $color='blue');
	}
}

// add_shortcode('custom-functions-debug', 'foodiepro_debug_shortcode');
function foodiepro_debug_shortcode($args) {
	$args = shortcode_atts( array(
        'class' => 'CustomSiteMails',
        'function' => 'published_post_notification',
        'paramtype' => 'wp_post',
        'paramval' => '7504'
    ), $args );

    $class=$args['class'];
    $function=$args['function'];

    switch ( $args['paramtype'] ) {
		case 'wp_post' :
			$param1 = get_post( $args['paramval'] );
			break;
	}

    $instance = new $class('debug');
    $html = $instance->$function( $param1 );

}


/* =             SCRIPTS AND STYLES DEBUG 
/* =================================================================*/

// add_action( 'wp_footer', 'foodiepro_record_scripts_styles',PHP_INT_MAX );
function foodiepro_record_scripts_styles() {
	if( !is_admin() && is_user_logged_in() && current_user_can( 'manage_options' )) {
		// Print Scripts
		global $wp_scripts;
		echo '<table align="center" style="color:#777;font-family:sans-serif;font-size:14px;width:100%;margin:20px">';
		echo '<th colspan="2" style="margin: 0px 3%; border: 1px solid #eee; padding: 10px;background-color: #ffffff;">Scripts</th>';
		foreach( $wp_scripts->queue as $handle ) {
			echo '<tr style="margin: 0px 3%; border: 1px solid #eee; padding: 10px;background-color: #ffffff;">';
			echo '<td style="padding:5px 10px">' . $handle . '</td>';
			echo '<td>' . $wp_scripts->registered[$handle]->src . '</td>';
			echo '</tr>';
			echo '</div>';
		}
		echo '</table>';
		
        // Print Styles
        global $wp_styles;
		echo '<table align="center" style="color:#777;font-family:sans-serif;font-size:14px;width:100%;margin:20px">';
		echo '<th colspan="2" style="margin: 0px 3%; border: 1px solid #eee; padding: 10px;background-color: #ffffff;">Styles</th>';
		foreach( $wp_styles->queue as $handle ) {
			echo '<tr style="margin: 0px 3%; border: 1px solid #eee; padding: 10px;background-color: #ffffff;">';
			echo '<td style="padding:5px 10px">' . $handle . '</td>';
			echo '<td>' . $wp_styles->registered[$handle]->src . '</td>';
			echo '</tr>';
			echo '</div>';			
        }
    }
}



/* =================================================================*/
/* =              CUSTOM QUERIES     
/* =================================================================*/

add_filter('terms_clauses', 'add_terms_clauses', 10, 3 );
function add_terms_clauses($clauses, $taxonomy, $args) {
  global $wpdb;
  if (isset($args['tags_post_type'])) {
    $post_types = $args['tags_post_type'];
    // allow for arrays
    if ( is_array($args['tags_post_type']) ) {
      $post_types = implode("','", $args['tags_post_type']);
    }
    $clauses['join'] .= " INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id";
    $clauses['where'] .= " AND p.post_type IN ('". esc_sql( $post_types ). "') GROUP BY t.term_id";
  }
  return $clauses;
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

// Capitalize SEO title
add_filter( 'wpseo_title', 'wpseo_uppercase_title' );
function wpseo_uppercase_title( $title ) {
	return ucfirst($title);
}

// Populate SEO meta if empty
add_filter('wpseo_metadesc', 'populate_metadesc');
function populate_metadesc( $text ) {
	if (empty($text)) {
		if (is_single()) {
			$text = get_the_excerpt( get_post() );
		} 
	}
	return $text;
}


// Add pinterest meta
// add_action ('genesis_meta','add_pinterest_meta'); /* Already done in YOAST SEO */
 
function add_pinterest_meta() { 
	echo '<meta name="p:domain_verify" content="c4a191084b3f5ef29b9df4a1a9f05aab"/>'; 
}

/* =================================================================*/
/* =              LAYOUT      
/* =================================================================*/

// Adds custom inline Javascript
// to solve screen header width issue on chrome mobile displays 
// add_action('wp_head','adjust_header_width');
function adjust_header_width(){
?>
<script>
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

// Remove the post meta display from footer
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );



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

/* Search Widget
-------------------------------------------------------------------*/
add_filter( 'genesis_search_text', 'custom_search_text' );
function custom_search_text( $text ) {
    $text=__( 'Recipe, Ingredient, Keyword, Author...','foodiepro' );
    return $text;
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


/* =================================================================*/
/* =      FOOTER           
/* =================================================================*/


//* Change the credits text
add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');
function sp_footer_creds_filter( $credits ) {

	ob_start();
	?>

	[footer_copyright before="<?= __('All rights reserved','foodiepro'); ?>" first="2015"] &middot; <a href="\">Goutu.org</a> &middot; [permalink slug="contact"]<?= __('Contact us', 'foodiepro') ?>[/permalink] &middot; [permalink slug="mentions-legales"]<?= __('Legal notice', 'foodiepro') ?>[/permalink] &middot; [footer_loginout]

	<?php

	$credits = ob_get_contents();
	ob_clean();

	return $credits;
}
