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
	add_image_size( 'flexslider', 680, 400, true );
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

	/** Move Primary Nav Menu Above Header */
	//remove_action( 'genesis_after_header', 'genesis_do_nav' );
	//add_action( 'genesis_before_header', 'genesis_do_nav' );

	/** Add primary Nav Menu to Header Right */
	//add_action( 'genesis_header_right', 'genesis_do_nav' );

	/* Disables Genesis responsive menu toggle */
	remove_action( 'wp_enqueue_scripts', 'genesis_sample_enqueue_menu_scripts_styles' );

	//* Add support for custom background.
	add_theme_support( 'custom-background' );

	//* Unregister header right sidebar.
	//unregister_sidebar( 'header-right' );

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


//add_action( 'wp_enqueue_scripts', 'foodie_pro_enqueue_js' );
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
/* =              CUSTOM LOGIN                                     =*/
/* =================================================================*/

/* Sets login page color theme */
function my_custom_login() {
	if ( CHILD_COLOR_THEME=='autumn')
		echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-styles-autumn.css" />';
	else 
		echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-styles-white.css" />';
}
add_action('login_head', 'my_custom_login');

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

/**
 * Add private/draft/future/pending pages to parent dropdown.
 */
 
add_filter( 'page_attributes_dropdown_pages_args', 'wps_dropdown_pages_args_add_parents' );
add_filter( 'quick_edit_dropdown_pages_args', 'wps_dropdown_pages_args_add_parents' );

function wps_dropdown_pages_args_add_parents( $dropdown_args, $post = NULL ) {
    $dropdown_args['post_status'] = array( 'publish', 'draft', 'pending', 'future', 'private', );
    return $dropdown_args;
}

/* =================================================================*/
/* =              STYLING     
/* =================================================================*/

/* adds new body class for home page
add_filter('body_class', 'add_homepage_class');
function add_homepage_class($classes){
	global $post;

	if(is_home()) {
		$classes[] = 'homepagetoto';
	}

return $classes;*/

/* adds new body class for post category
add_filter('body_class', 'add_post_category');
function add_post_category($classes){
	global $post;

	if(is_single()) {
		$category = get_the_category($post->ID);
		$slug = $category[0]->slug;
		$classes[] = 'post-category-' . $slug;
		}
return $classes; */




add_action('admin_head', 'custom_admin_css');

function custom_admin_css() {
  echo '<style>
    #ozhmenu img.wp-menu-image {
    	width: 25px;
    } 
  </style>';
}


/* Chargement des feuilles de style custom et polices */
add_action( 'wp_enqueue_scripts', 'custom_load_custom_style_sheet' );
function custom_load_custom_style_sheet() {
	if ( CHILD_COLOR_THEME=='autumn')
		wp_enqueue_style( 'color-theme-autumn', get_stylesheet_directory_uri() . '/custom_css/color-theme-autumn.css', array(), PARENT_THEME_VERSION );
	else 
		wp_enqueue_style( 'color-theme-white', get_stylesheet_directory_uri() . '/custom_css/color-theme-white.css', array(), PARENT_THEME_VERSION );		
	wp_enqueue_style( 'custom-recipe', get_stylesheet_directory_uri() . '/custom_css/recipe.css', array(), PARENT_THEME_VERSION );
	wp_enqueue_style( 'google-font-ruge', '//fonts.googleapis.com/css?family=Ruge+Boogie:400', array(), CHILD_THEME_VERSION );
	//wp_enqueue_style( 'google-font-crafty-girls', '//fonts.googleapis.com/css?family=Crafty+Girls', array(), CHILD_THEME_VERSION );
	//wp_enqueue_style( 'google-font-sacramento', '//fonts.googleapis.com/css?family=Sacramento', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-font-delius-swash-caps', '//fonts.googleapis.com/css?family=Delius+Swash+Caps', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-font-delius', '//fonts.googleapis.com/css?family=Delius', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-font-lobstertwo', '//fonts.googleapis.com/css?family=Lobster+Two', array(), CHILD_THEME_VERSION );
}

/* Suppression de la feuille de style de la gallerie Wordpress */
add_filter( 'use_default_gallery_style', '__return_false' );

/* Suppression de la feuille de style YARPP */
function yarpp_dequeue_footer_styles() {
  wp_dequeue_style('yarppRelatedCss');
  wp_dequeue_style('yarpp-thumbnails-yarpp-thumbnail');
}
add_action('get_footer','yarpp_dequeue_footer_styles');

/* =================================================================*/
/* =              LAYOUT      
/* =================================================================*/

//remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
//add_action( 'genesis_before_content', 'genesis_do_breadcrumbs' );

// Move title (breaks avatar appending in "Avatars" section)
//remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
//add_action( 'genesis_before_loop', 'genesis_do_post_title', 10 );

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


/* Batch update user_ratings_ratings custom field */
//function batch_update_meta() {
//	if (is_page(9645)) {
//		
//		echo '<div class="clearfix">';
//		echo "Batch Update Meta script started..." . "\n";
//		
//    $post_type = 'recipe';
//    $key = 'recipe_user_ratings_rating';
//    $value = 0;
//    
//    $post_type_object = get_post_type_object($post_type);
//    $label = $post_type_object->label;
//    echo  "Updating all " . $label . "\n";
//    $posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
//
//    foreach ($posts as $post) {
//      $meta_value = get_post_meta($post->ID, $key, True);
//      if (empty($meta_value)){
//      //$meta_value2 = media_process($meta_value1, $post->ID); //Returns a string after it finishes process.
//      update_post_meta($post->ID, $key, $value);
//      echo $post->post_title." UPDATED" . "\n"; //Prints updated after ran.
//      }
//    }
//    echo '/div';
//  }
//}
//add_action( 'genesis_after_content', 'batch_update_meta' );


/* Add ratings default value on recipe save */ 
function wpurp_add_default_rating( $id, $post ) {
 if ( $post->post_type == 'recipe' && !wp_is_post_revision($post->ID) )
    update_post_meta($post->ID, 'recipe_user_ratings_rating', '0');
}
add_action( 'save_post', 'wpurp_add_default_rating', 10, 2 );

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


// User rating calculation from votes
function output_recipe_rating( $recipe_id ) {
  $user_ratings = get_post_meta( $recipe_id, 'recipe_user_ratings' );

  $votes = count( $user_ratings );
  $total = 0;
  $rating = 0;
  $stars = 0;
  $half_star = false;

  foreach( $user_ratings as $user_rating )
  	{$total += $user_rating['rating'];}

  if( $votes !== 0 ) {
      $rating = $total / $votes; // TODO Just an average for now, implement some more functions later
      $stars = floor( $rating );
      if( $rating - $stars >= 0.5 ) {
          $half_star = true;}
      $rating = round( $rating, 2 );
  }
/*
  // Save numeric value of rating to allow sort by
  if( $rating != get_post_meta( $recipe_id, 'recipe_user_ratings_rating', true ) ) {
      update_post_meta( $recipe_id, 'recipe_user_ratings_rating', $rating );
  }*/

  return array(
      'votes' => $votes,
      'rating' => $rating,
      'stars' => $stars,
      'half_star' => $half_star,
  );
}

/* Modify WP Recent Posts extended output, depending on the css ID field value */
function wprpe_add_post_info($args) {
		$disp_avatar = substr($args['cssID'],0,1);
		$disp_rating = substr($args['cssID'],1,1);
		if ( $disp_avatar == '1') {
			$output = '<a class="auth-avatar" href="' . bp_core_get_user_domain( get_the_author_meta( 'ID' )) . '" title="' . bp_core_get_username(get_the_author_meta( 'ID' )) . '">';
			$output .= get_avatar( get_the_author_meta( 'ID' ), '45');
			$output .= '</a>';
		}
		if ( $disp_rating == '1') {
			$rating = output_recipe_rating( get_the_ID());
			$output .= '<div class="rating" id="stars-' . $rating['stars'] . '"></div>';
		}
		//$output = print_r($args, true);
	return $output;
}
add_filter('rpwe_after_thumbnail', 'wprpe_add_post_info', 10, 1);


/* Modify WP Recent Posts ordering, depending on the orderby field value */
function wprpe_orderby_rating( $args ) {
		if ( $args['orderby'] == 'meta_value_num')
    	$args['meta_key'] = 'recipe_user_ratings_rating';
    return $args;
}
add_filter( 'rpwe_default_query_arguments', 'wprpe_orderby_rating' );

//
///* Display current member posts and more link  */
//function wprpe_author_more_link( $args ) {
//		//print_r($args);
//		//if ( $args['author']=='bp_member' ) {
//    	$args['after'] = '
//    		<p class="more-from-category"> 
//				<a href="/author/' . do_shortcode( '[bp_displayed]' ) . '">Toutes les recettes de' . do_shortcode( '[bp_displayed type="name"]' ) . '→</a>
//				</p><br>
//    	';
//		//}
//    return $args;
//}
////add_filter( 'rpwe_default_args', 'wprpe_author_more_link' );


function wprpe_query_displayed_user_posts( $args ) {
		if ( $args['author']=='bp_member' )  {
    	$args['author'] = bp_displayed_user_id();
		}
    return $args;
}
add_filter( 'rpwe_default_query_arguments', 'wprpe_query_displayed_user_posts' );


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

//Removes Title and Description on Search Page
//remove_action( 'genesis_before_loop', 'genesis_do_search_title' );
//add_action( 'genesis_before_loop', 'genesis_do_search_title' );


/* Display customized title and description before the widget area
 ------------------------------------------------------------*/

function custom_archive_headline() {
	
	if ( is_archive() ) {
			
		$vocals = array('a','e','i','o','u');
		
		echo '<div class="archive-description taxonomy-archive-description taxonomy-description">';
	  echo '<h1 class="archive-title">';
		
	  if ( is_author() ) {
	  	$name = get_queried_object()->user_login;
	  	$first = strtolower( $name[0] );
	  	
			if ( in_array($first, $vocals) )
			  echo _x('All recipes from ','vowel','foodiepro') . $name;
			else 
			  echo _x('All recipes from ','consonant','foodiepro') . $name;
	  }
		
		elseif ( is_tax() ) {
			$first = get_queried_object()->slug[0];
		  $headline = get_term_meta( get_queried_object()->term_id, 'headline', true );

	    if( is_tax('ingredient') ) {
		    	if ( !empty($headline) )
					  echo $headline;
					else {
			    	if ( in_array($first, $vocals) )
			        echo single_term_title(_x('All recipes containing ','vowel','foodiepro'), false);
			      else 
			        echo single_term_title(_x('All recipes containing ','consonant','foodiepro'), false);				
					}
			}

			elseif( is_tax('cuisine') ) {
					if ( !empty($headline) )
						echo $headline;
				  else {
						if ( in_array($first, $vocals) )
			        echo single_term_title(_x('All recipes from ','vowel','foodiepro'), false);
			      else 
			        echo single_term_title(_x('All recipes from ','consonant','foodiepro'), false);			  	
				  }
			}
								
			else {
				if ( !empty($headline) )
					echo $headline;		
				else echo single_term_title('', false);
			}
			
		}
		
		else echo single_term_title('', false);
		
		echo '</h1>';
		echo '</div>';

	}
	
}
add_action( 'genesis_before_content', 'custom_archive_headline' );


/* Display customized title and description before the widget area
 ------------------------------------------------------------*/

function custom_search_terms() {
	if ( is_search() ) {
		echo '<div class="search-description archive-description">';
	  echo '<h1 class="archive-title">';
		echo single_term_title('', false);
		echo '</h1>';
		echo '</div>';
	}
}
add_action( 'genesis_before_content', 'custom_search_terms' );


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
  	$url = $_SERVER["REQUEST_URI"];
		$WPURP_search = strpos($url, 'wpurp-search');
		if ($WPURP_search==false)
  		genesis_widget_area( 'archives-top', array(
        'before' => '<div class="archives-top widget-area">',
        'after'  => '</div>',
  		));
  }     
}
//*add_action( 'genesis_before_loop', 'add_archive_widgeted_area', 999 );
add_action( 'genesis_before_loop', 'add_archive_widgeted_area');


//* Customize the entry meta in the entry header (requires HTML5 theme support)
/*function archive_meta_filter($post_info) {
	$post_info = '[post_date] by [post_author_posts_link] [post_comments]';
	return $post_info;
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
/* =                      POSTS
/* =================================================================*/

/* Remove mention from private & protected titles */
function title_format($content) {
	return '%s';
}
add_filter('private_title_format', 'title_format');
add_filter('protected_title_format', 'title_format');

//* Add post navigation 
add_action( 'genesis_after_entry_content', 'add_prev_next_post_nav', 1 );

function add_prev_next_post_nav() {
	if ( !is_singular( 'post' ) ) //add your CPT name to the array
		return;
	echo '<h3>' . __('More posts in this category','foodiepro') . '</h3>';
	genesis_markup( array(
		'html5'   => '<div %s>',
		'xhtml'   => '<div class="navigation">',
		'context' => 'adjacent-entry-pagination',
	) );
		echo '<div class="post-nav prev-post alignleft">';
			//previous_post_link();
			$prevPost = get_previous_post();
			$prevLnk = get_the_post_thumbnail( $prevPost->ID, 'mini-thumbnail',  array( 'class' => 'alignleft' )  );
			previous_post_link( '%link',  $prevLnk . '<div class="post-nav-title alignleft">← %title</div>', true );
		echo '</div>';
		echo '<div class="post-nav next-post alignright">';
			//next_post_link();
			$nextPost = get_next_post();
			$nextLnk = get_the_post_thumbnail( $nextPost->ID, 'mini-thumbnail',  array( 'class' => 'alignright' )  );
			//$nextLnk = $nextLnk . $nextPost->post_title;
			next_post_link( '%link', $nextLnk . '<div class="post-nav-title alignright">%title →</div>', true );
		echo '</div>';
	echo '</div>';
}


//* Add readmore links
add_filter( 'excerpt_more', 'foodie_pro_read_more_link' );
add_filter( 'get_the_content_more_link', 'foodie_pro_read_more_link' );
add_filter( 'the_content_more_link', 'foodie_pro_read_more_link' );


//* Add social share icons
function add_share_icons() {
	if ( is_singular( 'post' ) )
		echo do_shortcode('[mashshare]');
}
add_action( 'genesis_entry_footer', 'add_share_icons' , 10 ); /* Original genesis_after_entry_content */


/**
 * Modify the Genesis read more link */
function foodie_pro_read_more_link() {
	return '...</p><p><a class="more-link" href="' . get_permalink() . '">' . __( 'Read More', 'foodiepro' ) . ' &raquo;</a></p>';
}

//* Modify comments title text in comments
add_filter( 'genesis_title_comments', 'sp_genesis_title_comments' );
function sp_genesis_title_comments() {
	$title = '<h3>Commentaires</h3>';
	return $title;
}

//* Use specific foodiepro comments form
add_filter( 'genesis_comment_form_args', 'foodie_pro_comment_form_args' );


//* Remove the post meta function
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );


/* =================================================================*/
/* =          AVATARS
/* =================================================================*/


//* Prevents Gravatar to be fetched from internet
add_filter('bp_core_fetch_avatar_no_grav', '__return_true');


//* Add gravatar or picture before entry title
function bg_entry_image() {
	if ( is_singular( 'recipe' ) | is_singular( 'post' ) ) /*&& ( function_exists('bp_is_active') ) */{ /* Post or Custom Post */
		$id = get_the_author_meta( 'ID' );
		$pseudo = bp_core_get_username( $id );
		$url = bp_core_get_user_domain( $id );
		$args = array( 
	    'item_id' => $id, 
	    'type' => 'thumb',
	    'title' => $pseudo 
		); 
		echo '<div class="entry-avatar">';
		echo '<a href="' . $url . '">';
		echo bp_core_fetch_avatar( $args );
		echo '</a>';
		echo '</div>';
	}

	elseif ( is_page() ) {
		$key_val = get_post_meta( get_the_ID(), 'entry_header_image', true );
		if ( ! empty( $key_val ) ) {
			echo '<div class="entry-header-image">';
			echo '<img src="' . site_url( NULL, 'https' ) . '/wp-content/themes/foodiepro-2.1.8/images/' . $key_val . '">';	
			echo '</div>';	
		}
	}
}

add_action( 'genesis_entry_header', 'bg_entry_image', 7 );


/* =================================================================*/
/* =                      FOOTER
/* =================================================================*/


//* Change the credits text
function sp_footer_creds_filter( $creds ) {
	$creds = '[footer_copyright before="' . __('All rights reserved','foodiepro') . ' " first="2015"] &middot; <a href="http://goutu.org">Goutu.org</a> &middot; <a href="http://goutu.org/contact">' . __('Contact us', 'foodiepro') . '</a> &middot; ' . __('Legal notice', 'foodiepro') . ' &middot; ' . __('Goûtu charter','foodiepro') . ' &middot; ' . __('Personal data','foodiepro') . ' &middot; ' . __('Terms of use','foodiepro') . ' &middot; [footer_loginout]';
	return $creds;
}

add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');