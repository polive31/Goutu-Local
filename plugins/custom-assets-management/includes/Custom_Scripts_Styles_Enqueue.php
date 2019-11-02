<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomScriptsStylesEnqueue {

	// Scripts to be loaded asynchronously
	const DEFER_JS = array(
		'skip-links',
		'foodie-pro-general',
		'grecaptcha-invisible',
		'newscript',
	);

	// Stylesheets to be loaded asynchronously
	const DEFER_CSS = array(
		'custom-gdpr',
		'dashicons',
		'bp-mentions-css',
	);

	// Stylesheets to be preloaded
	const PRELOAD_CSS = array(
		'google-fonts',
		'child-theme-fonts',
	);

	/* Stylesheets to be replaced.
	IMPORTANT there must also be an entry for them in $css_if, otherwise they will not be considered */
	private $css_replace = array(
		'name-directory-style' 	=> array(
			'file' 		=> '/assets/css/name_directory.css',
		),
		/* Force Peepso custom stylesheet update by using CHILD_THEME_VERSION */
		// 'peepso-custom' 	=> array(
			// 	'file' 		=> '/peepso/custom.css',
			// ),
		'peepso-jquery-ui' 		=> array(
			'file'		=> '/assets/css/datepicker.css',
		),
	);

	// Stylesheets to be loaded conditionnally
	private $css_if = array(
		// 'custom-star-ratings' 				=> array('page' => 'blog-page'),
		// 'peepso'							=> array('page' => 'social'),
		'peepso-custom'						=> array('true' => ''),
		'news-style'						=> array('page' => 'home'),
		'newsletter'						=> array('page' => 'home'),
		'peepso-appearance-avatars-circle'	=> array('page' => 'social home'),
		'peepso-blogposts-dynamic'			=> array('page' => 'social home'),
		'peepso-fileupload'					=> array('page' => 'social home'),
		'yarppRelatedCss' 					=> array('singular' => 'post recipe'),
		'custom-lightbox'					=> array('singular' => 'post recipe'),
		'name-directory-style' 				=> array('shortcode' => 'namedirectory'),
		'yarppWidgetCss' 					=> array('false' => ''),
		'megamenu-fontawesome' 				=> array('false' => ''),
		'megamenu-google-fonts' 			=> array('false' => ''),
		'megamenu-genericons' 				=> array('false' => ''),
		'popup-maker-site' 					=> array('false' => ''),
		'wpba_front_end_styles' 			=> array('false' => ''),
		'frontend-uploader' 				=> array('false' => ''),
		'peepso-jquery-ui'					=> array('page' => 'social'),
		'peepso-datepicker'					=> array('false' => ''),
		'peepso-markdown'					=> array('false' => ''),
		'lazysizes-fadein-style'			=> array('false' => ''),
		'lazysizes-spinner-style'			=> array('false' => ''),
		'wp-block-library'					=> array('false' => ''),
	);


	// Scripts to be loaded conditionnally
	private $js_if = array(
		'newscript'							=> array('page' => 'home'),
		'newsletter-subscription'			=> array('page' => 'home'),
		'peepso-resize'						=> array('page' => 'social'),
		'peepsolocation-js'					=> array('page' => 'social'),
		'peepso-time'						=> array('page' => 'social'),
		'peepso-groups'						=> array('page' => 'social'),
		'peepso-groups-group'				=> array('page' => 'social'),
		'peepso-groups-group'				=> array('page' => 'social'),
		'peepso-blogposts'					=> array('page' => 'social'),
		'wp-embed'							=> array('page' => 'social home'),
		'peepsovideos'						=> array('page' => 'social home'),
		'peepso-photos'						=> array('page' => 'social home'),
		'peepso-moods'						=> array('page' => 'social home'),
		'peepso-markdown'					=> array('false' => ''),
		'peepso-modal-comments'				=> array('false' => ''),
		'peepso-friends-shortcode'			=> array('false' => ''),
	);

	// Plugin path & url properties
	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;


	public function __construct() {
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		add_action( 'wp_enqueue_scripts', 		array($this, 'enqueue_if'), PHP_INT_MAX);
		add_action( 'wp_print_footer_scripts',	array($this, 'enqueue_if'), 0);

		add_filter( 'script_loader_tag', 	array($this, 'async_load_js'), PHP_INT_MAX, 3 );
		add_filter( 'style_loader_tag', 	array($this, 'async_load_css'), PHP_INT_MAX, 4 );
		add_filter( 'style_loader_tag', 	array($this, 'preload_css'), PHP_INT_MAX, 4 );

		// add_action( 'wp_print_styles', 		array($this, 'megamenu_dequeue_google_fonts'), 100 );
		// add_action('init', 'load_jquery_from_google');

		add_filter( 'stylesheet_uri', 		array($this, 'enqueue_minified_theme_stylesheet'), 10, 1 );
	}


	/*  LOAD CONDITIONALLY
	/* ----------------------------------------------------------------*/
	public function enqueue_if() {
		$temp = $this->css_if;
		foreach ($this->css_if as $style => $conditions ) {
			if (!$this->current_page_matches( $conditions ) ) {
				remove_style($style);
			}
			else {
				if ( in_array($style, array_keys( $this->css_replace) ) ) {
					remove_style($style);
					$args = $this->css_replace[$style];
					$args['handle'] = $style;
					custom_enqueue_style( $args );
				}
				unset( $temp[$style] ); // When loop is run next time in the footer, only styles for which condition is true are present
			}
		}
		$this->css_if = $temp;

		$temp = $this->js_if;
		foreach ($this->js_if as $script => $conditions ) {
			if (!$this->current_page_matches( $conditions ) ) {
				remove_script($script);
			}
			else
				unset( $temp[$script] );
		}
		$this->js_if = $temp;
	}

	public function current_page_matches( $conditions ) {
		$met = true;
		foreach ($conditions as $type => $value) {
			$thismet = true;
			switch ($type) {
				case 'true' :
				case 'always' :
					$thismet = true;
					break;
				case 'false' :
				case 'never' :
					$thismet = false;
					break;
				case 'page' :
					$thismet = $this->is_page_of_type( explode(' ', $value) );
					break;
				case 'shortcode' :
					$content='';
					if ( is_singular() ) {
						$post = get_post();
						$content = $post->post_content;
					}
					elseif ( is_archive() ) {
						$term_id = get_queried_object_id();
						$content = get_term_meta( $term_id, 'intro_text', true );
					}
					$thismet = empty($content)?false:has_shortcode( $content, $value);
					break;
				case 'single' :
					$thismet = is_single();
					break;
				case 'singular' :
					$thismet = is_singular( explode(' ', $value) );
					break;
			}
			$met = $met && $thismet;
		}
		return $met;
	}

	public function is_page_of_type( $types ) {
		$met = false;
		foreach ($types as $type) {
			$thismet = false;
			switch ($type) {
				case 'home' :
					$thismet = is_front_page();
					break;
				case 'social' :
					$template = get_page_template();
					$thismet = strpos($template, 'social') != false;
					break;
				case 'blog-page' :
					$template = get_page_template();
					$thismet = strpos($template, 'social') == false;
					break;
			}
			$met = $met||$thismet;
		}
		return $met;
	}


	/*  ASYNC STYLE & SCRIPTS LOADING
	/* ----------------------------------------------------------------*/

	public function async_load_js( $html, $handle, $src ) {
		if ( is_admin() ) return $html;
		if ( in_array($handle, self::DEFER_JS ) ) {
		  $html='<script src="' . $src . '" async type="text/javascript"></script>' . "\n";
		}
		return $html;
	}


	public function async_load_css( $html, $handle, $href, $media ) {
		if ( is_admin() ) return $html;
		if ( in_array($handle, self::DEFER_CSS ) ) {
			$html = '<link rel="stylesheet" href="' . $href . '" media="async" onload="if(media!=\'all\')media=\'all\'"><noscript><link rel="stylesheet" href="css.css"></noscript>' . "\n";
		}
		return $html;
	}

	public function preload_css( $html, $handle, $href, $media ) {
		if ( is_admin() ) return $html;
		if ( in_array($handle, self::PRELOAD_CSS ) ) {
			$search = "/rel=\"(.*?)\"/i";
			$replace = "rel='preload'";

			$html = preg_replace($search, $replace, $html);
		}
		return $html;
	}


	/*  Making jQuery Google API
	--------------------------------------------------------*/
	public function load_jquery_from_google() {
		if (!is_admin()) {
			// comment out the next two lines to load the local copy of jQuery
			wp_deregister_script('jquery');
			wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js', false, '1.8.1');
			wp_enqueue_script('jquery');
		}
	}

	// Prevent Max Mega Menu to load all google fonts
	public function megamenu_dequeue_google_fonts() {
	   wp_dequeue_style( 'megamenu-google-fonts' );
	}


	/* Gestion des feuilles de style minifiées */
	public function enqueue_minified_theme_stylesheet( $default_stylesheet_uri ) {
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

}

new CustomScriptsStylesEnqueue();
