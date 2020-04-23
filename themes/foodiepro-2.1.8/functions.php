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
if (!defined('ABSPATH')) {
	exit;
}

define('CHILD_THEME_NAME', 'Foodie Pro Theme');
define('CHILD_THEME_DEVELOPER', 'Shay Bocks/Pascal Olive');
define('CHILD_THEME_OPTIONS', get_option('foodiepro'));
define('CHILD_THEME_VERSION', ((bool)CHILD_THEME_OPTIONS['reload'])?time():'2.4.02');
define('CHILD_THEME_URL', get_stylesheet_directory_uri());
define('CHILD_THEME_PATH', get_stylesheet_directory());
define('DEFAULT_CHILD_COLOR_THEME', 'spring');
define('DEFAULT_LOGIN_COLOR_THEME', 'spring');
define('CHILD_COLOR_THEME', foodiepro_get_color_theme());
define('LOGIN_COLOR_THEME', foodiepro_get_login_color_theme());

function foodiepro_get_showlog()
{
	if (CHILD_THEME_OPTIONS) {
		$showlog = !empty(CHILD_THEME_OPTIONS['show-console-logs']);
	} else {
		$showlog = false;
	}
	return $showlog;
}

function foodiepro_get_color_theme() {
	if (CHILD_THEME_OPTIONS ) {
		$color=!empty(CHILD_THEME_OPTIONS['color'])? CHILD_THEME_OPTIONS['color']: DEFAULT_CHILD_COLOR_THEME;
	}
	else {
		$color=DEFAULT_CHILD_COLOR_THEME;
	}
	return $color;
}

function foodiepro_get_login_color_theme()
{
	if (CHILD_THEME_OPTIONS) {
		$login_color = !empty(CHILD_THEME_OPTIONS['login-color']) ? CHILD_THEME_OPTIONS['login-color'] : DEFAULT_LOGIN_COLOR_THEME;
	} else {
		$login_color = DEFAULT_LOGIN_COLOR_THEME;
	}
	return $login_color;
}

define('PLUGINS_URL', plugins_url());


/* =================================================================*/
/* =              FOODIEPRO CHILD THEME SETUP
/* =================================================================*/

add_action('after_setup_theme', 'foodie_pro_load_textdomain');
/**
 * Loads the child theme textdomain.
 *
 * @since  2.1.0
 * @return void
 */
function foodie_pro_load_textdomain()
{
	load_child_theme_textdomain(
		'foodiepro',
		trailingslashit(get_stylesheet_directory()) . 'languages'
	);
}

add_action('genesis_setup', 'foodie_pro_theme_setup', 15);

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
function foodie_pro_theme_setup()
{
	//* Add viewport meta tag for mobile browsers.
	add_theme_support('genesis-responsive-viewport');

	//* Add HTML5 markup structure.
	add_theme_support('html5');

	//*	Set content width.
	$content_width = apply_filters('content_width', 610, 610, 980);

	//* Add new featured image sizes.
	add_image_size('horizontal-thumbnail', 680, 450, false);
	add_image_size('vertical-thumbnail', 680, 900, false);
	add_image_size('recipe-thumbnail', 750, 600, false);
	add_image_size('medium-thumbnail', 450, 450, false);
	add_image_size('square-thumbnail', 320, 320, false);
	add_image_size('small-thumbnail', 100, 100, false);
	add_image_size('mini-thumbnail', 75, 75, false);
	// Standard WP sizes are :
	// 'thumbnail' => 200x200

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
	remove_action('wp_enqueue_scripts', 'genesis_sample_enqueue_menu_scripts_styles');

	//* Add support for custom background.
	add_theme_support('custom-background');

	/** Reposition header outside container */
	remove_action('genesis_header', 'genesis_header_markup_open', 5);
	remove_action('genesis_header', 'genesis_do_header');
	remove_action('genesis_header', 'genesis_header_markup_close', 15);

	add_action('genesis_before', 'custom_header_markup_open', 5);
	add_action('genesis_before', 'genesis_do_header');
	add_action('genesis_before', 'custom_header_markup_close', 15);


	//New Header functions
	function custom_header_markup_open()
	{
		genesis_markup(array(
			'html5'   => '<header %s>',
			'context' => 'site-header',
		));
		// Added in content
		echo '<div class="header-inner">';
		genesis_structural_wrap('header');
	}
	function custom_header_markup_close()
	{
		genesis_structural_wrap('header', 'close'); // widgets area
		do_action('before_header_close');
		echo '</div>'; // header-inner
		genesis_markup(array(
			'close'   => '</header>',
			'context' => 'site-header',
		)); // <header> tag
	}


	//* Add support for custom header.
	add_theme_support(
		'genesis-custom-header',
		array(
			'width'  => 1400, /*P.O. Original 800 */
			'height' => 260, /*P.O. Original 340 */
			'header_callback' => 'goutu_custom_header_style',
		)
	);

	/* Original file : genesis/lib/structure/header.php */
	function goutu_custom_header_style()
	{
		$output = '';

		$header_image = get_header_image();
		$text_color   = get_header_textcolor();

		// If no options set, don't waste the output. Do nothing.
		if (empty($header_image) && !display_header_text() && $text_color === get_theme_support('custom-header', 'default-text-color'))
			return;

		$header_selector = get_theme_support('custom-header', 'header-selector');
		$title_selector  = genesis_html5() ? '.custom-header .site-title'       : '.custom-header #title';
		$desc_selector   = genesis_html5() ? '.custom-header .site-description' : '.custom-header #description';

		// Header selector fallback.
		if (!$header_selector)
			$header_selector = genesis_html5() ? '.custom-header .site-header' : '.custom-header #header';

		// Header image CSS, if exists.
		//if ( $header_image )
		if (is_front_page() && $header_image)
			$output .= sprintf('%s { background: url(%s) no-repeat !important; }', $header_selector, esc_url($header_image));

		// Header text color CSS, if showing text.
		if (display_header_text() && $text_color !== get_theme_support('custom-header', 'default-text-color'))
			$output .= sprintf('%2$s a, %2$s a:hover, %3$s { color: #%1$s !important; }', esc_html($text_color), esc_html($title_selector), esc_html($desc_selector));

		if ($output)
			printf('<style type="text/css">%s</style>' . "\n", $output);
	}

	//* Add support for 4-column footer widgets.
	add_theme_support('genesis-footer-widgets', 4);
}

/* =================================================================*/
/* =        LOAD OF FOODIE INCLUDES                                =*/
/* =================================================================*/

add_action('genesis_setup', 'foodie_pro_includes', 20);
/**
 * Load additional functions and helpers.
 *
 * DO NOT MODIFY ANYTHING IN THIS FUNCTION.
 *
 * @since   2.0.0
 * @return  void
 */
function foodie_pro_includes()
{
	$includes_dir = trailingslashit(get_stylesheet_directory()) . 'includes/';

	// Load the customizer library.
	// require_once $includes_dir . 'vendor/customizer-library/customizer-library.php';

	// Load all customizer files.
	// require_once $includes_dir . 'customizer/customizer-display.php';
	// require_once $includes_dir . 'customizer/customizer-settings.php';

	// Load everything in the includes root directory.
	require_once $includes_dir . 'helper-functions.php';
	require_once $includes_dir . 'compatability.php';
	require_once $includes_dir . 'simple-grid.php';
	require_once $includes_dir . 'widgeted-areas.php';
	require_once $includes_dir . 'widgets.php';

	// P.O. Load the custom helpers
	require_once trailingslashit(CHILD_THEME_PATH) . 'custom-helpers.php';
	require_once trailingslashit(CHILD_THEME_PATH) . 'custom-shortcodes.php';
	require_once trailingslashit(CHILD_THEME_PATH) . '/login/custom-login.php';

	// End here if we're not in the admin panel.
	if (is_admin()) {
		// Load everything in the admin root directory.
		require_once $includes_dir . 'admin/functions.php';
	}
}

/* =================================================================*/
/* =                  SCRIPTS & STYLES ENQUEUE
/* =================================================================*/
add_action('wp_enqueue_scripts', 	'enqueue_high_priority_assets', 10);
add_action('wp_enqueue_scripts', 	'enqueue_low_priority_assets', 20);
// add_action( 'wp_head', 				'typekit_inline');
// Remove Google fonts loading
// add_filter( 'foodie_pro_disable_google_fonts', '__return_true' );

function enqueue_high_priority_assets()
{
	/* Scripts enqueue
		--------------------------------------------------- */
	// .webp detection
	foodiepro_enqueue_script('custom-modernizr', '/assets/js/modernizr-custom.js', CHILD_THEME_URL, CHILD_THEME_PATH, array(), CHILD_THEME_VERSION);
	// Add general purpose scripts.
	foodiepro_enqueue_script('foodie-pro-general', '/assets/js/general.js', CHILD_THEME_URL, CHILD_THEME_PATH, array('jquery'), CHILD_THEME_VERSION, true);
	foodiepro_enqueue_script('custom-js-helpers', '/assets/js/custom_helpers.js', CHILD_THEME_URL, CHILD_THEME_PATH, array('jquery'), CHILD_THEME_VERSION, true);
	$showlog = foodiepro_get_showlog();
	wp_localize_script('custom-js-helpers', 'foodiepro_options', array('showlogs'=> $showlog));
	// foodiepro_enqueue_script( 'one-signal', $js_uri, $js_path, 'one_signal.js', array(), CHILD_THEME_VERSION, true);

	/* Styles enqueue
		--------------------------------------------------- */
	wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Cabin|Amatic+SC:400,700|Oswald|Vollkorn:300,400', array(), CHILD_THEME_VERSION);
	// wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Amatic+SC:400,700|Oswald|Vollkorn:300,400', array(), CHILD_THEME_VERSION );
	// wp_enqueue_script( 'typekit', '//use.typekit.net/hen2swu.js', array(), '1.0.0' );
	foodiepro_enqueue_style('child-theme-fonts', '/assets/css/fonts.css', CHILD_THEME_URL, CHILD_THEME_PATH, array('foodie-pro-theme'), CHILD_THEME_VERSION);
}

function typekit_inline()
{
	if (wp_script_is('typekit', 'enqueued')) {
		echo '<script type="text/javascript">try{Typekit.load();}catch(e){}</script>';
	}
}

function enqueue_low_priority_assets()
{

	// wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
	// wp_enqueue_script('font-awesome-5', 'https://kit.fontawesome.com/e61ef10a3b.js');

	/* Theme stylesheet with varying name & version, forces cache busting at browser level
		--------------------------------------------------- */
	$color_theme_handler = 'color-theme-' . CHILD_COLOR_THEME;
	foodiepro_enqueue_style('foodiepro-color-theme', '/assets/css/color/' . $color_theme_handler . '.css', CHILD_THEME_URL, CHILD_THEME_PATH,  array(), CHILD_COLOR_THEME . CHILD_THEME_VERSION);

}

/* =================================================================*/
/* =                       LOAD GENESIS
/* =================================================================*/

//Child Theme Language override
// define('GENESIS_LANGUAGES_DIR', STYLESHEETPATH . '/languages/genesis');
// define('GENESIS_LANGUAGES_URL', STYLESHEETPATH . '/languages/genesis');

/**
 * Load Genesis
 *
 * This is technically not needed.
 * However, to make functions.php snippets work, it is necessary.
 */
require_once(get_template_directory() . '/lib/init.php');

/**
 * Add the theme name class to the body element.
 *
 * @since  1.0.0
 *
 * @param  string $classes
 * @return string Modified body classes.
 */
add_filter('body_class', 'foodie_pro_add_body_class');
function foodie_pro_add_body_class($classes)
{
	$classes[] = 'foodie-pro';
	$classes[] = 'no-js';
	$classes[] = 'color-theme-' . CHILD_COLOR_THEME;
	if ( is_single() ) {
		$classes[] = 'status-' . get_post_status();
	}
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
add_action('genesis_meta', 'custom_favicon_links');

// function custom_favicon_links_bak()
// {
// 	$path = CHILD_THEME_URL . '/images/favicon';
// 	echo sprintf('<link rel="apple-touch-icon" sizes="57x57" href="%s/apple-icon-57x57.png">', $path);
// 	echo sprintf('<link rel="apple-touch-icon" sizes="60x60" href="%s/apple-icon-60x60.png">', $path);
// 	echo sprintf('<link rel="apple-touch-icon" sizes="72x72" href="%s/apple-icon-72x72.png">', $path);
// 	echo sprintf('<link rel="apple-touch-icon" sizes="76x76" href="%s/apple-icon-76x76.png">', $path);
// 	echo sprintf('<link rel="apple-touch-icon" sizes="114x114" href="%s/apple-icon-114x114.png">', $path);
// 	echo sprintf('<link rel="apple-touch-icon" sizes="120x120" href="%s/apple-icon-120x120.png">', $path);
// 	echo sprintf('<link rel="apple-touch-icon" sizes="144x144" href="%s/apple-icon-144x144.png">', $path);
// 	echo sprintf('<link rel="apple-touch-icon" sizes="152x152" href="%s/apple-icon-152x152.png">', $path);
// 	echo sprintf('<link rel="apple-touch-icon" sizes="180x180" href="%s/apple-icon-180x180.png">', $path);
// 	echo sprintf('<link rel="icon" type="image/png" sizes="192x192" href="%s/android-icon-192x192.png">', $path);
// 	echo sprintf('<link rel="icon" type="image/png" sizes="32x32" href="%s/favicon-32x32.png">', $path);
// 	echo sprintf('<link rel="icon" type="image/png" sizes="96x96" href="%s/favicon-96x96.png">', $path);
// 	echo sprintf('<link rel="icon" type="image/png" sizes="16x16" href="%s/favicon-16x16.png">', $path);
// 	echo sprintf('<link rel="manifest" href="%s/manifest.json">', $path);
// 	echo sprintf('<meta name="msapplication-TileColor" content="#ffffff">', $path);
// 	echo sprintf('<meta name="msapplication-TileImage" content="%s/ms-icon-144x144.png">', $path);
// 	echo sprintf('<meta name="theme-color" content="#ffffff">', $path);
// }

function custom_favicon_links() {
	$path = CHILD_THEME_URL . '/images/favicon';
	echo sprintf('<link rel="apple-touch-icon" sizes="180x180" href="%s/apple-touch-icon.png?v=2">', $path);
	echo sprintf('<link rel="icon" type="image/png" sizes="32x32" href="%s/favicon-32x32.png?v=2">', $path);
	echo sprintf('<link rel="icon" type="image/png" sizes="16x16" href="%s/favicon-16x16.png?v=2">', $path);
	echo sprintf('<link rel="manifest" href="%s/site.webmanifest?v=2">', $path);
	echo sprintf('<link rel="mask-icon" href="%s/safari-pinned-tab.svg?v=2" color="#5bbad5">', $path);
	echo sprintf('<link rel="shortcut icon" href="%s/favicon.ico?v=2">', $path);
	echo sprintf('<meta name="msapplication-TileColor" content="#ffc40d">', $path);
	echo sprintf('<meta name="msapplication-config" content="%s/browserconfig.xml">', $path);
	echo sprintf('<meta name="theme-color" content="#ffffff">', $path);
}

/* =================================================================*/
/* =              REWRITE RULES
/* =================================================================*/

/* Customize author base
IMPORTANT : for this setting to take effect, save WP permalinks page
in order to flush the rules
-----------------------------------------------------------------------*/
add_action('init', 'foodiepro_custom_author_base');
function foodiepro_custom_author_base()
{
	global $wp_rewrite;
	// IMPORTANT : don't try to translate as we are in functions.php, or declare it within the paths
	$author_slug = foodiepro_get_author_base(); // the new slug name
	$wp_rewrite->author_base = $author_slug;
}

/* =================================================================*/
/* =              COMMENTS
/* =================================================================*/

/* Allows logged-in users to submit comments without manual moderation */
add_filter('pre_comment_approved', 'foodiepro_approve_loggedin_users');
function foodiepro_approve_loggedin_users($approved)
{
	return is_user_logged_in() ? 1 : $approved;
}


/* =================================================================*/
/* =              SECURITY
/* =================================================================*/

/* Add nicename column to users table */
add_filter('manage_users_columns', 'add_user_nicename_column');
function add_user_nicename_column($columns)
{
	$columns['user_nicename'] = 'User Nicename';
	return $columns;
}

add_action('manage_users_custom_column',  'show_user_nicename_column_content', 10, 3);
function show_user_nicename_column_content($value, $column_name, $user_id)
{
	$user = get_userdata($user_id);
	if ('user_nicename' == $column_name)
		$value = $user->user_nicename;
	return $value;
}


// Remove meta generator to hide WP version, commented out since already covered in WP security
// remove_action('wp_head', 'wp_generator');


add_filter('pre_user_login', 'foodiepro_custom_user_login_id');
function foodiepro_custom_user_login_id($sanitized_user_login)
{
	/* Check if new user creation or user update */
	$update = get_user_by('login', $sanitized_user_login) ? true : false;
	if ($update) return $sanitized_user_login;

	do {
		$ulogin = generateRandomString(10);
		$check = username_exists($ulogin);
	} while ($check);

	return $ulogin;
}

function generateRandomString($length = 10)
{
	$letters = 'abcdefghijklmnopqrstuvwxyz';
	$digits = '0123456789';
	$characters = $letters . $digits;
	$lettersLength = strlen($letters);
	$charactersLength = strlen($characters);

	$randomString = $letters[rand(0, $lettersLength - 1)];
	for ($i = 1; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

/**
 * Set the display name and nickname values to user nicename
 * @param int $user_id The user ID
 */

add_action('user_register', 'set_default_display_name');
function set_default_display_name($user_id)
{
	$user = get_userdata($user_id);
	$name = $user->user_nicename;
	$args = array(
		'ID'           => $user_id,
		'display_name' => $name,
		'nickname'     => $name
	);
	wp_update_user($args);
}


/* =================================================================*/
/* =             CUSTOM WP_DIE HANDLER
/* =================================================================*/
function themed_wp_die_handler($message, $title = '', $args = array())
{
	$defaults = array('response' => 500);
	$r = wp_parse_args($args, $defaults);
	if (function_exists('is_wp_error') && is_wp_error($message)) {
		$errors = $message->get_error_messages();
		switch (count($errors)) {
			case 0:
				$message = '';
				break;
			case 1:
				$message = $errors[0];
				break;
			default:
				$message = "<ul>\n\t\t<li>" . join("</li>\n\t\t<li>", $errors) . "</li>\n\t</ul>";
				break;
		}
	} else {
		$message = strip_tags($message);
	}
	require_once get_stylesheet_directory() . '/wp-die.php';
	die();
}


/* =================================================================*/
/* =              MAIL
/* =================================================================*/

/**
 * This function will connect wp_mail to your authenticated
 * SMTP server. This improves reliability of wp_mail, and
 * avoids many potential problems.
 *
 * For instructions on the use of this script, see:
 * https://www.butlerblog.com/2013/12/12/easy-smtp-email-wordpress-wp_mail/
 *
 * Values are constants set in wp-config.php
 */
// add_action( 'phpmailer_init', 'send_smtp_email' );
// function send_smtp_email($phpmailer)
// {
// 	$phpmailer->isSMTP();
// 	$phpmailer->Host       = SMTP_HOST;
// 	$phpmailer->SMTPAuth   = SMTP_AUTH;
// 	$phpmailer->Port       = SMTP_PORT;
// 	$phpmailer->Username   = SMTP_USER;
// 	$phpmailer->Password   = SMTP_PASS;
// 	$phpmailer->SMTPSecure = SMTP_SECURE;
// 	$phpmailer->From       = SMTP_FROM;
// 	$phpmailer->FromName   = SMTP_NAME;
// }

/* =================================================================*/
/* =              REMOVE ADMIN FROM USER SEARCH RESULTS
/* =================================================================*/
add_action('pre_get_users', 'foodiepro_hide_admin');
function foodiepro_hide_admin($query) {
	if ( isset($query->query_vars) && !is_admin() ) {
		$role_not_in = $query->query_vars['role__not_in'];
		if (!in_array('admin', $role_not_in)) {
			array_push($query->query_vars['role__not_in'], 'administrator');
		}
	};
}

/* =================================================================*/
/* =              CUSTOM QUERIES
/* =================================================================*/

add_filter('terms_clauses', 'add_terms_clauses', 10, 3);
function add_terms_clauses($clauses, $taxonomy, $args)
{
	global $wpdb;
	if (isset($args['tags_post_type'])) {
		$post_types = $args['tags_post_type'];
		// allow for arrays
		if (is_array($args['tags_post_type'])) {
			$post_types = implode("','", $args['tags_post_type']);
		}
		$clauses['join'] .= " INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id";
		$clauses['where'] .= " AND p.post_type IN ('" . esc_sql($post_types) . "') GROUP BY t.term_id";
	}
	return $clauses;
}


/* =================================================================*/
/* =              SEO
/* =================================================================*/

/* Exclude Multiple Taxonomies From Yoast SEO Sitemap */
add_filter('wpseo_sitemap_exclude_taxonomy', 'sitemap_exclude_taxonomy', 10, 2);
function sitemap_exclude_taxonomy($value, $taxonomy)
{
	$taxonomy_to_exclude = array('slider');
	if (in_array($taxonomy, $taxonomy_to_exclude)) return true;
}

// Capitalize SEO title
add_filter('wpseo_title', 'wpseo_uppercase_title');
function wpseo_uppercase_title($title)
{
	return ucfirst($title);
}

// Populate SEO meta if empty
// add_filter('wpseo_metadesc', 'foodiepro_populate_metadesc', 100, 1);
function foodiepro_populate_metadesc($text)
{
	if (empty($text)) {
		if (is_single()) {
			$text = get_the_excerpt();
		}
	}
	return $text;
}


// Add pinterest meta
// add_action ('genesis_meta','add_pinterest_meta'); /* Already done in YOAST SEO */

function add_pinterest_meta()
{
	echo '<meta name="p:domain_verify" content="c4a191084b3f5ef29b9df4a1a9f05aab"/>';
}


/* =================================================================*/
/* =              FORMATTING
/* =================================================================*/
// remove_filter('the_content', 'wpautop');
// add_filter('the_content', 'wpautop', 12); // 11 is the priority of do_shortcode
// add_filter('the_content', 'shortcode_unautop', 15);

/* =================================================================*/
/* =              LAYOUT
/* =================================================================*/

/* Additional layout with only secondary sidebar */

add_action('init', 'foodiepro_alt_sidebar_layout');
function foodiepro_alt_sidebar_layout()
{
	genesis_register_layout('content-alt-sidebar', array(
		'label' => __('Content/Alt Sidebar', 'genesis'),
		'img' => CHILD_THEME_URL . '/images/admin/gle_c-salt.gif'
	));
}

remove_action('genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt');
add_action('genesis_after_content', 'genesis_get_sidebar_alt');

add_action('genesis_before_content', 'apply_content_alt_sidebar_layout');
function apply_content_alt_sidebar_layout()
{
	$site_layout = genesis_site_layout();
	if ('content-alt-sidebar' == $site_layout) {
		// Remove the Primary Sidebar from the Primary Sidebar area.
		remove_action('genesis_sidebar', 'genesis_do_sidebar');

		// Remove the Secondary Sidebar from the Secondary Sidebar area.
		add_action('genesis_sidebar_alt', 'genesis_do_sidebar_alt');

		// // Place the Secondary Sidebar into the Primary Sidebar area.
		// add_action( 'genesis_sidebar', 'genesis_do_sidebar_alt' );

		// // Place the Primary Sidebar into the Secondary Sidebar area.
		// add_action( 'genesis_sidebar_alt', 'genesis_do_sidebar' );
	}
}


/* Adds custom inline Javascript
	to solve screen header width issue on chrome mobile displays */
// add_action('wp_head','custom_inline_js');
function custom_inline_js()
{
	?>
	<script>
	</script>
	<?php
}

	//* Reposition the primary navigation menu within header
	remove_action('genesis_after_header', 'genesis_do_subnav');
	add_action('before_header_close', 'genesis_do_subnav');

	//* Reposition the primary navigation menu within header
	remove_action('genesis_after_header', 'genesis_do_nav');
	//add_action( 'genesis_header', 'genesis_do_nav');

	// Move pagination on all archive pages
	remove_action('genesis_after_endwhile', 'genesis_posts_nav');
	add_action('genesis_after_content', 'genesis_posts_nav');

	// Move footer widget area (avoid "out of content" issue on buddypress pages)
	remove_action('genesis_before_footer', 'genesis_footer_widget_areas');
	add_action('genesis_after_content_sidebar_wrap', 'genesis_footer_widget_areas', 999);

	// Remove the post meta display from footer
	remove_action('genesis_entry_footer', 'genesis_entry_footer_markup_open', 5);
	remove_action('genesis_entry_footer', 'genesis_post_meta');
	remove_action('genesis_entry_footer', 'genesis_entry_footer_markup_close', 15);


	/* Hook widget areas
-----------------------------------------------------------------------------*/

	add_shortcode('widget-area', 'add_widget_area');
	function add_widget_area($a)
	{
		$a = shortcode_atts(array(
			'hook' => '',
			'id' => '',
			'class' => '',
		), $a);

		if (empty($a['id'])) return '';

		ob_start();

		genesis_widget_area($a['id'], array(
			'before' => '<div class="' . $a['id'] . ' ' . $a['class'] . '">',
			'after'  => '</div>',
		));

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	add_action('genesis_after_content', 'add_after_content_area');
	function add_after_content_area()
	{
		$template = get_page_template();
		if (strpos($template, 'social')) return;
		genesis_widget_area('after-content', array(
			'before' => '<div class="bottom after-content widget-area">',
			'after'  => '</div>',
		));
	}


	/* Nav Menus Customization :
 - Main nav gets a widgeted area "main-nav" on its last item
 - Mobile nav gets an additional item containing a shortcode : [mobile-nav-bottom] => can be used to display a login/logout link for instance
 */

	add_filter('wp_nav_menu_items', 'custom_nav_menu_items', 10, 2);

	function custom_nav_menu_items($html, $args)
	{
		if (isset($args->name) && is_string($args->name))
			$name = $args->name;
		elseif (is_string($args->menu))
			$name = $args->menu;
		elseif (is_string($args->menu->name))
			$name = $args->menu->name;
		else return $html;

		if ($name == 'main_nav_fr') {
			ob_start();
			genesis_widget_area('main-nav', array(
				'before' => '<li class="mega-menu-item main-nav-widget-area">',
				'after'  => '</li>',
			));
			$html .= ob_get_contents();
			ob_end_clean();
		} elseif ($name == 'mobile_nav_fr') {
			$html .= '<li class="menu-item menu-item-type-custom menu-item-object-custom responsive-menu-pro-item responsive-menu-pro-desktop-menu-col-auto">';
			if (is_user_logged_in()) {
				$html .= '<a href="' . wp_logout_url() . '" class="responsive-menu-pro-item-link">';
				$html .= '<i class="fa ps-icon-off" aria-hidden="true"></i>';
				$html .= __('Log Out', 'foodiepro');
			} else {
				$html .= '<a href="' . wp_login_url() . '" class="responsive-menu-pro-item-link">';
				$html .= '<i class="fa fa-user" aria-hidden="true"></i>';
				$html .= __('Log In', 'foodiepro');
			}
			$html .= '</a>';
			$html .= '</li>';
		}

		return $html;
	}


	/* =================================================================*/
	/* =              EMBEDDED POSTS
/* =================================================================*/


	add_filter('embed_thumbnail_image_size', 'foodiepro_embed_thumbnail_size');
	add_filter('embed_thumbnail_image_shape', 'foodiepro_embed_thumbnail_shape');

	function foodiepro_embed_thumbnail_size()
	{
		return 'square-thumbnail';
	}

	function foodiepro_embed_thumbnail_shape()
	{
		return 'square';
	}


/* =================================================================*/
/* =              WIDGETS / PLUGINS
/* =================================================================*/

// Force textdomain for wordfence
// add_filter('load_textdomain_mofile', 'foodiepro_override_mofile_path', 10, 2);
function foodiepro_override_mofile_path($mofile, $domain)
{
	if ('wordfence' == $domain) {
		$mofile = WP_LANG_DIR . '/plugins/' . basename($mofile);
	}
	return $mofile;
}


	//* Adds Mailchimp newsletter subscribe form
	//add_action('wp_head','mailchimp_subscribe_form');
	function mailchimp_subscribe_form()
	{
		?>
		<script type="text/javascript" src="//downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script>
		<script type="text/javascript">
			require(["mojo/signup-forms/Loader"], function(L) {
				L.start({
					"baseUrl": "mc.us17.list-manage.com",
					"uuid": "86ca729ff9d0eb5dc6a0d0ff1",
					"lid": "f2167601d1"
				})
			})
		</script>
	<?php
	}

	// Allow Text widgets to execute shortcodes
	add_filter('widget_text', 'shortcode_unautop');
	add_filter('widget_text', 'do_shortcode');

	// Enable PHP in widgets
	add_filter('widget_text', 'execute_php', 100);
	function execute_php($html)
	{
		if (strpos($html, "<" . "?php") !== false) {
			ob_start();
			eval("?" . ">" . $html);
			$html = ob_get_contents();
			ob_end_clean();
		}
		return $html;
	}

	/* Search Widget
-------------------------------------------------------------------*/
	add_filter('genesis_search_text', 'custom_search_text');
	function custom_search_text($text)
	{
		$text = __('Recipe, Ingredient, Keyword, Author...', 'foodiepro');
		return $text;
	}

	/* WP Fastest Cache
-------------------------------------------------------------------*/
	// add_action('csn_after_post_like', 'foodiepro_clear_cache', 15, 2);
	// function foodiepro_clear_cache($user_id, $post_id)
	// {
	// 	// Flushes the cache in the case of WP Faster Cache installed
	// 	if (function_exists('wpfc_clear_post_cache_by_id')) {
	// 		wpfc_clear_post_cache_by_id($post_id);
	// 	}
	// }

	/* =================================================================*/
	/* =               PAGES
/* =================================================================*/

	//* Add icon before page title
	add_action('genesis_entry_header', 'add_page_icon', 7);
	function add_page_icon()
	{
		if (is_page()) {
			$icon_url = trailingslashit(CHILD_THEME_URL) . 'images/page-icons/';
			$icon_path = trailingslashit(CHILD_THEME_PATH) . 'images/page-icons/';
			$key_val = get_post_meta(get_the_ID(), 'entry_header_image', true);
			if (!empty($key_val)) {
				$ext = substr(strrchr($key_val, "."), 1);
				$filename = substr($key_val, 0, (strrpos($key_val, ".")));
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
	add_filter('genesis_pre_get_option_footer_text', 'sp_footer_creds_filter');
	function sp_footer_creds_filter($credits)
	{

		ob_start();
		?>

		[footer_copyright before="<?= __('All rights reserved', 'foodiepro'); ?>" first="2015"] &middot; <a href="\">Goutu.org</a> &middot; [permalink slug="contact"]<?= __('Contact us', 'foodiepro') ?>[/permalink] &middot; [permalink slug="mentions-legales"]<?= __('Legal notice', 'foodiepro') ?>[/permalink] &middot; [footer_loginout]

		<a target="_blank" href="https://seal.beyondsecurity.com/vulnerability-scanner-verification/goutu.org">
			<img src="https://seal.beyondsecurity.com/verification-images/goutu.org/vulnerability-scanner-8.gif" alt="Vulnerability Scanner" border="0" />
		</a>

	<?php

		$credits = ob_get_contents();
		ob_clean();

		return $credits;
	}
