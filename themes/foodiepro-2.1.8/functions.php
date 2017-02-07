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
define( 'CHILD_THEME_URL', 'http://shaybocks.com/foodie-pro/' );
define( 'CHILD_THEME_DEVELOPER', 'Shay Bocks' );
define( 'CHILD_COLOR_THEME', 'autumn' ); /* Other values white */

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
	require_once $includes_dir . 'vendor/class-tgm-plugin-activation.php';

	// Load everything in the admin root directory.
	require_once $includes_dir . 'admin/functions.php';
	

}


/* =================================================================*/
/* =                       LOAD GENESIS                                                                         =*/
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
	$js_uri = get_stylesheet_directory_uri() . '/assets/js/';
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
/* =              CUSTOM SCRIPTS ENQUEUE
/* =================================================================*/

/* Enqueue default WP jQuery in the footer rather than the header 
--------------------------------------------------------------------*/
function move_jquery_into_footer( $wp_scripts ) {
    if( is_admin()) return;
    $wp_scripts->add_data( 'jquery', 'group', 1 );
    $wp_scripts->add_data( 'jquery-core', 'group', 1 );
    $wp_scripts->add_data( 'jquery-migrate', 'group', 1 );
}
//add_action( 'wp_default_scripts', 'move_jquery_into_footer' );


function enqueue_wpurp_js($js_enqueue) {
		if ( is_singular('post') ) return '';
		elseif ( !is_singular('recipe') || ( is_singular('recipe') && is_admin() ) ) return $js_enqueue;
	
    $js_enqueue=array(
            array(
                'name' => 'fraction',
                'url' => WPUltimateRecipe::get()->coreUrl . '/vendor/fraction-js/index.js',
                'public' => true,
                'admin' => true,
            ),
            /*array(
                'url' => WPUltimateRecipe::get()->coreUrl . '/vendor/jquery.tools.min.js',
                'public' => true,
                'deps' => array(
                    'jquery',
                ),
            ),*/
            array(
                'name' => 'print_button',
                'url' => WPUltimateRecipe::get()->coreUrl . '/js/print_button.js',
                'public' => true,
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_print',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_print' ),
                    'custom_print_css_url' => get_stylesheet_directory_uri() . '/assets/css/custom-recipe-print.css',
                    'coreUrl' => WPUltimateRecipe::get()->coreUrl,
                    'premiumUrl' => WPUltimateRecipe::is_premium_active() ? WPUltimateRecipePremium::get()->premiumUrl : false,
                    'title' => __('Print this Recipe','foodiepro'),
                    'permalinks' => get_option('permalink_structure'),
                ),
            ),
    	      array(
                'url' => WPUltimateRecipe::get()->coreUrl . '/js/adjustable_servings.js',
                'public' => true,
                'deps' => array(
                    'jquery',
                    'fraction',
                		'print_button',
                ),
                'data' => array(
                    'name' => 'wpurp_servings',
                    'precision' => 1,
                    'decimal_character' => ',',
                ),
            ),
						/*array(
                'url' => WPUltimateRecipePremium::get()->premiumUrl . '/addons/favorite-recipes/js/favorite-recipes.js',
               	'premium' => true,
                'public' => true,
                'setting' => array( 'favorite_recipes_enabled', '1' ),
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_favorite_recipe',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_favorite_recipe' ),
                )
            ),
						array(
                'url' => WPUltimateRecipePremium::get()->premiumUrl . '/js/add-to-shopping-list.js',
                'premium' => true,
                'public' => true,
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_add_to_shopping_list',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_add_to_shopping_list' ),
                )
            ),*/	  
						array(
                'url' => get_stylesheet_directory_uri() . '/assets/js/custom_favorite_recipe.js',
               	'premium' => true,
                'public' => true,
                'setting' => array( 'favorite_recipes_enabled', '1' ),
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_favorite_recipe',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_favorite_recipe' ),
                )
            ),	  
            array(
                'url' => get_stylesheet_directory_uri() . '/assets/js/custom_shopping_list.js',
                'premium' => true,
                'public' => true,
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_add_to_shopping_list',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_add_to_shopping_list' ),
                )
            ),
    );	
	  
//	print "<pre>";
//	print_r($js_enqueue);
//	print "</pre>";
	return $js_enqueue;
}
add_filter ( 'wpurp_assets_js', 'enqueue_wpurp_js', 15, 1 );


//remove_action ( 'wp_enqueue_scripts', 'WPURP_Assets::enqueue');
//wp_deregister_script('wpurp_script_minified');
//wp_enqueue_script( 'wpurp_custom_script', get_stylesheet_directory_uri() . '/assets/js/wpurp_custom.js', array('jquery'), WPURP_VERSION, true );

/* =================================================================*/
/* =              CUSTOM LOGIN                                     =*/
/* =================================================================*/

/* Sets login page color theme */
function my_custom_login() {
	if ( CHILD_COLOR_THEME=='autumn')
		echo '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/login/custom-login-styles-autumn.css" />';
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
function block_new_users ($user) {
		$role=$user->roles[0];
    if ( $role=='pending' )
    	return new WP_Error( 'user_not_approved', __( '<strong>ERROR</strong>: User pending ', 'foodiepro') . '<a href="' . get_page_link(10066) . '"> ' . __('approval', 'foodiepro') . ' </a>');
		else
			return $user;
}
add_filter('wp_authenticate_user', 'block_new_users',10,1);


/* =================================================================*/
/* =              ADMIN
/* =================================================================*/


/* =================================================================*/
/* =              STYLING     
/* =================================================================*/

/* Chargement des feuilles de style custom et polices */
function custom_load_custom_style_sheet() {
	if ( CHILD_COLOR_THEME=='autumn')
		wp_enqueue_style( 'color-theme-autumn', get_stylesheet_directory_uri() . '/assets/css/color-theme-autumn.css', array(), CHILD_THEME_VERSION );
	else 
		wp_enqueue_style( 'color-theme-white', get_stylesheet_directory_uri() . '/assets/css/color-theme-white.css', array(), CHILD_THEME_VERSION );		
	//wp_enqueue_style( 'font-awesome', get_stylesheet_directory_uri() . '/assets/fonts/font-awesome/css/font-awesome.min.css', array(), CHILD_THEME_VERSION );
	//wp_enqueue_style( 'google-font-ruge', '//fonts.googleapis.com/css?family=Ruge+Boogie:400', array(), CHILD_THEME_VERSION );
	//wp_enqueue_style( 'google-font-crafty-girls', '//fonts.googleapis.com/css?family=Crafty+Girls', array(), CHILD_THEME_VERSION );
	//wp_enqueue_style( 'google-font-sacramento', '//fonts.googleapis.com/css?family=Sacramento', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-font-delius-swash-caps', '//fonts.googleapis.com/css?family=Delius+Swash+Caps', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-font-delius', '//fonts.googleapis.com/css?family=Delius', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-font-lobstertwo', '//fonts.googleapis.com/css?family=Lobster+Two', array(), CHILD_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'custom_load_custom_style_sheet' );


/* Chargement des feuilles de style WPURP */
function enqueue_wpurp_css($js_enqueue) {
	if ( is_singular('recipe') ) {
	  $js_enqueue=array(
						array(
	              'url' => WPUltimateRecipe::get()->coreUrl . '/css/admin.css',
	              'admin' => true,
	          ),
						array(
	              'url' => get_stylesheet_directory_uri() . '/assets/css/custom-recipe.css',
	              'public' => true,
	          ),
		);
	}
	elseif ( is_page( 8428 ) ) {
	//elseif ( is_singular('menu') ) {
	  $js_enqueue=array(
						array(
	              'url' => get_stylesheet_directory_uri() . '/assets/css/custom-menu.css',
	              'public' => true,
	          ),
		);		
	}
	return $js_enqueue;
}
add_filter ( 'wpurp_assets_css', 'enqueue_wpurp_css', 15, 1 );

/* Suppression de la feuille de style de la gallerie Wordpress */
add_filter( 'use_default_gallery_style', '__return_false' );

/* Suppression de la feuille de style YARPP */
function yarpp_dequeue_footer_styles() {
  wp_dequeue_style('yarppRelatedCss');
  wp_dequeue_style('yarpp-thumbnails-yarpp-thumbnail');
}
//add_action('get_footer','yarpp_dequeue_footer_styles');

/* =================================================================*/
/* =              LAYOUT      
/* =================================================================*/

// Move pagination on all archive pages
remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action( 'genesis_after_content', 'genesis_posts_nav' );


/* =================================================================*/
/* =                 RECIPES
/* =================================================================*/

/*add_filter( 'wpurp_user_submissions_current_user_edit_item', 'add_empty_msg')*/



/* DEBUG Add all meta info before recipe */
function display_all_meta() {
	if ( is_singular( 'recipe' )) {
		echo '<div class="post_meta">';
		echo print_r(get_post_meta( get_the_ID() ), true );
		//$nutritional_info = get_post_meta( get_the_ID(), 'recipe_nutritional', True);
		//echo print_r( $nutritional_info , true );
		echo '</div>';
	}
}
//add_action( 'genesis_entry_header', 'display_all_meta', 10 );


/* Custom recipe template */
require_once( 'custom-recipe-template.php'); 
//require_once( 'custom-recipe-print-template.php'); 


/* Custom menu template */
function wpurp_custom_menu_template( $form, $menu ) {
	return '';
}
add_filter( 'wpurp_user_menus_form', 'wpurp_custom_menu_template', 10, 2 );

//require_once( 'custom-menu-template.php'); 

/* =================================================================*/
/* =                      WIDGETS
/* =================================================================*/

// Allow Text widgets to execute shortcodes
add_filter( 'widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

// Enable PHP in widgets
function execute_php($html){
     if(strpos($html,"<"."?php")!==false){
          ob_start();
          eval("?".">".$html);
          $html=ob_get_contents();
          ob_end_clean();
     }
     return $html;
}
add_filter('widget_text','execute_php',100);


/* Modify WP Recent Posts ordering, depending on the orderby field value */
function wprpe_orderby_rating( $args ) {
		if ( $args['orderby'] == 'meta_value_num')
    	$args['meta_key'] = 'recipe_user_ratings_rating';
    return $args;
}
add_filter( 'rpwe_default_query_arguments', 'wprpe_orderby_rating' );


/*function add_recipe_rating($args) {
	$rating = output_recipe_rating( get_the_ID());
	$output = 'Enter your text here';
	return $output;
}
add_filter('rpwe_excerpt', 'add_recipe_rating', 10, 1);*/

/* =================================================================*/
/* =                      ARCHIVES
/* =================================================================*/

// Apply Full Width Content layout to Archives.
function set_full_layout() {
//	if ( ! ( is_archive() ) ) {
//	if ( !( is_tax()) ) {
	if ( !( is_search()) ) {
		return;
	}
	// Force full width content
	add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
}
//add_action( 'get_header', 'set_full_layout' );

//Removes Title and Description on CPT Archive
remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
//Removes Title and Description on Blog Archive
remove_action( 'genesis_before_loop', 'genesis_do_posts_page_heading' );
//Removes Title and Description on Date Archive
remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
//Removes Title and Description on Archive, Taxonomy, Category, Tag
remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
//Removes Title and Description on Author Archive
remove_action( 'genesis_before_loop', 'genesis_do_author_box_archive', 15 );
//Removes Title and Description on Author Archive
remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
//Removes Title and Description on Blog Template Page
remove_action( 'genesis_before_loop', 'genesis_do_blog_template_heading' );


/* Display customized title and description before the widget area
 ------------------------------------------------------------*/
function custom_archive_headline() {
	
	if ( is_archive() ) {
			
		$vocals = array('a','e','i','o','u');
		
	  $head_style=array('begin'=>'<h1 class="archive-title">','end'=>'</h1>');
		$intro_style=array('begin'=>'<div class="archive-description taxonomy-archive-description taxonomy-description">','end'=>'</div>');
		
	  if ( is_author() ) {
	  	$name = get_queried_object()->user_login;
	  	$first = strtolower( $name[0] );
	  	
			if ( in_array($first, $vocals) )
			  echo $head_style['begin'] . _x('All recipes from ','vowel','foodiepro') . $name . $head_style['end'];
			else 
			  echo $head_style['begin'] . _x('All recipes from ','consonant','foodiepro') . $name . $head_style['end'];
	  }
		
		elseif ( is_tax() ) {
			$first = get_queried_object()->slug[0];
			$term_id = get_queried_object()->term_id;
		  $headline = get_term_meta( $term_id, 'headline', true );
		  $intro_text = get_term_meta( $term_id, 'intro_text', true );

	    if( is_tax('ingredient') ) {
		    	if ( !empty($headline) )
					  echo $head_style['begin'] . $headline . $head_style['end'];
					else {
			    	if ( in_array($first, $vocals) )
			        echo $head_style['begin'] . single_term_title(_x('All recipes containing ','vowel','foodiepro'), false) . $head_style['end'];
			      else 
			        echo $head_style['begin'] . single_term_title(_x('All recipes containing ','consonant','foodiepro'), false) . $head_style['end'];				
					}
			}

			elseif( is_tax('cuisine') ) {
					if ( !empty($headline) )
						echo $head_style['begin'] . $headline;
				  else {
						if ( in_array($first, $vocals) )
			        echo $head_style['begin'] . single_term_title(_x('All recipes from ','vowel','foodiepro'), false) . $head_style['end'];
			      else 
			        echo $head_style['begin'] . single_term_title(_x('All recipes from ','consonant','foodiepro'), false) . $head_style['end'];			  	
				  }
			}
								
			elseif( is_tax('difficult') ) {
					if ( !empty($headline) )
						echo $head_style['begin'] . $headline . $head_style['end'];
					if ( !empty($intro_text) )
						echo $intro_style['begin'] . $intro_text . $intro_style['end'];
			}

			else {
				if ( !empty($headline) )
					echo $head_style['begin'] . $headline . $head_style['end'];		
				else 
					echo $head_style['begin'] . single_term_title('', false) . $head_style['end'];
			}
			
		}
		
		else 
			echo $head_style['begin'] . single_term_title('', false) . $head_style['end'];

	}
	
}
add_action( 'genesis_before_content', 'custom_archive_headline' );

add_filter( 'genesis_term_intro_text_output', 'wpautop' );

/* Display customized title and description before the widget area
 ------------------------------------------------------------*/

function custom_search_title_text() {	
	$url = $_SERVER["REQUEST_URI"];
	$WPURP_search = strpos($url, 'wpurp-search');

	if ($WPURP_search!==false)
		$html = __('Detailed Search Results', 'foodiepro');
	else 
		$html = __('Search Results for:', 'genesis');

  return $html;
}
add_filter( 'genesis_search_title_text', 'custom_search_title_text' );


//* Hook category widget areas before post content and after archive title
function add_archive_widgeted_area() {
  if ( is_archive() || is_search() ) {
  		genesis_widget_area( 'archives-top', array(
        'before' => '<div class="archives-top widget-area">',
        'after'  => '</div>',
  		));
  }     
}
add_action( 'genesis_before_loop', 'add_archive_widgeted_area');


//* Customize the entry meta in the entry header (requires HTML5 theme support)
/*function archive_meta_filter($post_info) {
	$post_info = '[post_date] by [post_author_posts_link] [post_comments]';
	return $post_
}*/
//add_filter( 'genesis_post_info', 'archive_meta_filter' );


//* Customize the post title in the archive pages
function archive_title($title) {
	if ( is_tax() || is_search() ) :
//	if ( is_post_type_archive( 'recipe' ) ) :
		//$rating = output_recipe_rating( get_the_ID());
		//$title .= '<div class="rating" id="stars-' . $rating['stars'] . '"></div>';
		$saved_rating = get_post_meta( get_the_ID(), 'recipe_user_ratings_rating' );
		if (empty($saved_rating)) $saved_rating=array(0 =>'0');
		$title .= '<div class="rating" id="stars-' . $saved_rating[0] . '"></div>';
		//$title .= '<div>' . print_r($saved_rating, true) . '</div>';
	endif;

	if ( is_tax('cuisine', array('france', 'europe', 'asie', 'afrique', 'amerique-nord', 'amerique-sud') ) ) :
		$origin = wp_get_post_terms( get_the_ID(), 'cuisine', array("fields" => "names"));
		$title .= '<div class="origin">' . $origin[0] . '</div>';
		//$title .= get_query_var( 'taxonomy' );  => ex : "cuisine"
		//$title .= get_query_var( 'term' ); => ex : "europe"
	endif;

	//$title .= '<div>' . print_r( get_user_meta( get_the_author_meta( 'ID' ) ), true) . '</div>';
	//$title .= '<div>' . print_r( get_post_meta( get_the_ID() ), true) . '</div>';

	return $title;
}
add_filter( 'genesis_post_title_output', 'archive_title', 15 );


//* Change the archive post orderby and sort order from slug
function archive_change_sort_order($query){
    // Select any archive. For custom post type use: is_post_type_archive( $post_type )
    //if (is_archive() || is_search() ): => ne pas utiliser car résultats de recherche non releants
    if (is_archive() ):
       $orderby= get_query_var('orderby','title');
       if ($orderby=='rating'):
       	$orderby = 'meta_value_num';
       	$meta_key = 'recipe_user_ratings_rating';
       	$order = 'DESC';
       else:
       	$meta_key='';
       	$order=get_query_var('order','ASC');
       endif;

       $query->set( 'orderby', $orderby );
       $query->set( 'meta_key', $meta_key );
       $query->set( 'order', $order );
       //$query->set( 'orderby', array( 'meta_value_num' => 'DESC', 'title' => 'ASC' ) );
       //$query->set( 'meta_key', 'recipe_user_ratings_rating' );
       //$query->set( 'meta_type', 'NUMERIC' );
    endif;
};
add_action( 'pre_get_posts', 'archive_change_sort_order');
//add_action( 'pre_get_posts', 'archive_sort_by_rating');


/* =================================================================*/
/* =                      DEBUG
/* =================================================================*/

add_action( 'genesis_before_content', 'display_debug_info' );
function display_debug_info() {
	if (is_single()) {

		$post_id = get_the_id();
		//echo '<pre>';
		//PC:debug( 'In foodiepro functions.php' );
		
//		$output = get_post_meta( $post_id , '' , false);
//		echo 'All post meta : ';
//		print_r($output);
//		//PC:debug(array('get_post_meta( $post_id ) : '=> $output) );
		
		$output = get_post_meta( $post_id, 'user_ratings' );
		//PC:debug(array('user_ratings : '=> $output) );

		$output = get_post_meta( $post_id, 'user_rating_stats' );
		//PC:debug(array('user_rating_stats : '=> $output) );
		
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


/* =================================================================*/
/* =          COMMENTS
/* =================================================================*/

// Remove the genesis_default_list_comments function
remove_action( 'genesis_list_comments', 'genesis_default_list_comments' );

/* Remove comment form from recipes 
-------------------------------------------------------*/
function remove_recipe_comments_form() {
	if ( is_singular( 'recipe' ) ) {
		remove_action( 'genesis_comment_form', 'genesis_do_comment_form' );
	}
}
add_action( 'genesis_comment_form', 'remove_recipe_comments_form', 0 );



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
	return $creds;
}

add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');