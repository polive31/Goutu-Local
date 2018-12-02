<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomScriptsStylesEnqueue {

	const DEFER_JS = array(
			'masterslider-core',
			'bp-confirm',
			'skip-links',
			'foodie-pro-general',
			// 'one-signal'
		);

	const DEFER_CSS = array(
			'custom-gdpr',
			'dashicons',
			'bp-admin-bar',
			'bp-mentions-css',
			'custom-lightbox',
		);

	const CSS_IF = array(
			'name-directory-style' 				=> array('shortcode' => 'namedirectory'),
			'bp-child-css' 						=> array('blog-page' => ''),
			'yarppWidgetCss' 					=> array('false' => ''),
			'yarpp-thumbnails-yarpp-thumbnail' 	=> array('false' => ''),
			'megamenu-fontawesome' 				=> array('false' => ''),
			'popup-maker-site' 					=> array('false' => ''),
			'bppp-style' 						=> array('false' => ''),
		);

	const JS_IF = array();

	const CSS_REPLACE = array(
			'name-directory-style' 	=> 'name_directory.css',
		);


	private $css_if=self::CSS_IF;
	private $js_if=self::JS_IF;


	public function __construct() {

		add_action( 'wp_enqueue_scripts', 	array($this, 'foodie_pro_enqueue_js' ));
		add_action( 'wp_enqueue_scripts', 	array($this, 'foodie_pro_enqueue_stylesheets' ));

		add_action( 'wp_enqueue_scripts', 	array($this, 'enqueue_if'));
		add_action( 'get_footer',			array($this, 'enqueue_if'));

		add_filter( 'script_loader_tag', 	array($this, 'async_load_js', PHP_INT_MAX, 3 ));
		add_filter( 'style_loader_tag', 	array($this, 'async_load_css', PHP_INT_MAX, 4 ));

		add_filter( 'foodie_pro_disable_google_fonts', array($this, '__return_true' ));

		add_action( 'wp_print_styles', 		array($this, 'megamenu_dequeue_google_fonts', 100 ));
		//add_action('init', 'load_jquery_from_google');   */
		add_filter( 'stylesheet_uri', 		array($this, 'enqueue_minified_theme_stylesheet', 10, 1 ));

	}

	/* =================================================================*/
	/* =              CUSTOM SCRIPTS ENQUEUE
	/* =================================================================*/

	/**
	 * Load all required JavaScript for the Foodie theme.
	 *
	 * @since   1.0.1
	 * @return  void
	 */
	public function foodie_pro_enqueue_js() {
		$js_uri = CHILD_THEME_URL . '/assets/js/';
		$js_path = CHILD_THEME_PATH . '/assets/js/';
		// Add general purpose scripts.
		custom_enqueue_script( 'foodie-pro-general', $js_uri, $js_path, 'general.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
		custom_enqueue_script( 'custom-js-helpers', $js_uri, $js_path, 'custom_helpers.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
		// custom_enqueue_script( 'one-signal', $js_uri, $js_path, 'one_signal.js', array(), CHILD_THEME_VERSION, true);
		// .webp detection
		custom_enqueue_script( 'modernizr', $js_uri, $js_path, 'modernizr-custom.js', array(), CHILD_THEME_VERSION );
	}


	/*  LOAD CONDITIONNALY 
	/* ----------------------------------------------------------------*/

	public function enqueue_if() {
		$temp = self::$css_if;
		foreach ($css_if as $style => $conditions ) {
			if (!$this->is_met( $conditions ) ) {
				remove_style($style);
				unset( $temp[$style] );
			}
		}
		self::$css_if = $temp;

		$temp = self::$js_if;
		foreach (self::$js_if as $script => $conditions ) {
			if (!$this->is_met( $conditions ) ) {
				remove_script($script);
				unset( $temp[$script] );
			} 	
		}
		self::$js_if = $temp;

	}

	public function is_met( $conditions ) {
		$met = true;
		foreach ($conditions as $type => $value) {
			switch ($type) {
				case 'false' :
					$met = false;
					break;	
				case 'shortcode' :
					$met = $met && has_shortcode( get_the_content(), $value);
					break;
				case 'single' :
					$met = $met && is_single();
					break;
				case 'singular' :
					$met = $met && is_singular( explode(',', $value) );
					break;
				case 'blog-page' :
					if (!function_exists( 'bp_is_blog_page')) break;
					$met = $met && bp_is_blog_page();
					break;
			}
		}
		return $met;
	}

	/*  ASYNC STYLE & SCRIPTS LOADING
	/* ----------------------------------------------------------------*/

	public function async_load_js( $html, $handle, $src ) { 
		if ( is_admin() ) return $html;
		if ( in_array($handle, DEFER_JS ) ) {
		  $html='<script src="' . $src . '" async type="text/javascript"></script>' . "\n";
		}
		return $html;
	} 


	public function async_load_css( $html, $handle, $href, $media ) { 
		if ( is_admin() ) return $html;
		if ( in_array($handle, DEFER_CSS ) ) {
			$html = '<link rel="stylesheet" href="' . $href . '" media="async" onload="if(media!=\'all\')media=\'all\'"><noscript><link rel="stylesheet" href="css.css"></noscript>' . "\n";
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


	/* =================================================================*/
	/* =              CUSTOM STYLES ENQUEUE     
	/* =================================================================*/

	//* Load Custom Styles & Fonts


	public function foodie_pro_enqueue_stylesheets() {
		
		/* Google Fonts
		--------------------------------------------------- */
		wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Amatic+SC:400,700|Oswald|Vollkorn:300,400', array(), CHILD_THEME_VERSION );
		//wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Amatic+SC:400,700|Oswald|Lato:300,400', array(), CHILD_THEME_VERSION );
		
		/* Font Awesome
		--------------------------------------------------- */
		wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
		wp_enqueue_style('font-awesome', '//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
		//wp_enqueue_style( 'font-awesome', CHILD_THEME_URL . '/assets/fonts/font-awesome/css/font-awesome.min.css', array(), CHILD_THEME_VERSION );

		/* Material Icons
		--------------------------------------------------- */
		// wp_enqueue_style('material-icons', '//fonts.googleapis.com/icon?family=Material+Icons'); 


		/* Theme stylesheet with varying name & version, forces cache busting at browser level
		--------------------------------------------------- */
		$css_url = CHILD_THEME_URL . '/assets/css/';
		$css_path = CHILD_THEME_PATH . '/assets/css/';
		$color_theme_handler = 'color-theme-' . CHILD_COLOR_THEME;
		custom_enqueue_style( $color_theme_handler , $css_url, $css_path, $color_theme_handler . '.css', array(), CHILD_COLOR_THEME . CHILD_COLOR_THEME_VERSION );

		
		/* Customized GDPR stylesheet 
		--------------------------------------------------- */
		custom_enqueue_style( 'custom-gdpr' , $css_url, $css_path, 'custom-gdpr-public.css', array(), CHILD_THEME_VERSION );

		/* Print stylesheet
		--------------------------------------------------- */
		custom_enqueue_style( 'custom-print-style' , $css_url, $css_path, 'print.css', array(), CHILD_THEME_VERSION, 'print' );	

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
