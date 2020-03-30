<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CASM_Assets {

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
	const CSS_REPLACE = array(
		'name-directory-style' 	=> array(
			'file' 		=> '/assets/css/name_directory.css',
		),
		'megamenu' 		=> array(
			'file' 		=> '/assets/css/max-mega-menu.css',
		),
		/* Force Peepso custom stylesheet update by using CHILD_THEME_VERSION */
		'peepso-custom' 	=> array(
			'file' 		=> '/assets/css/peepso.css',
		),
		'peepso-jquery-ui' 		=> array(
			'file'		=> '/assets/css/datepicker.css',
		),
	);

	// Stylesheets to be loaded conditionnally
	const CSS_IF = array(
		// 'custom-star-ratings' 				=> array('page' => 'blog-page'),
		'foodiepro-color-theme'				=> array('mobile' => false),
		'news-style'						=> array('page' => 'home'),
		'newsletter'						=> array('page' => 'home'),
		'yarppRelatedCss' 					=> array('singular' => 'post recipe'),
		'custom-lightbox'					=> array('singular' => 'post recipe'),
		'name-directory-style' 				=> array('shortcode' => 'namedirectory'),
		'yarppWidgetCss' 					=> array('false' => ''),
		'megamenu' 							=> array('true' => ''),
		'megamenu-fontawesome' 				=> array('false' => ''),
		'megamenu-google-fonts' 			=> array('false' => ''),
		'megamenu-genericons' 				=> array('false' => ''),
		'popup-maker-site' 					=> array('false' => ''),
		'wpba_front_end_styles' 			=> array('false' => ''),
		'frontend-uploader' 				=> array('false' => ''),
		'wp-block-library'					=> array('false' => ''),
		// Lazysizes
		'lazysizes-fadein-style'			=> array('false' => ''),
		'lazysizes-spinner-style'			=> array('false' => ''),
		//Peepso
		'peepso-custom'						=> array('true' => ''),
		'peepso*'							=> array('page' => 'social'),
		// Fonts & icons
		// 'dashicons'							=> array('admin' => true),
	);


	// Scripts to be loaded conditionnally
	const JS_IF = array(
		'newscript'								=> array('page' => 'home'),
		'newsletter-subscription'				=> array('page' => 'home'),
		'wp-embed'								=> array('page' => 'social home'),
		//Megamenu
		'megamenu'								=> array('mobile' => false),
		'megamenu-pro'							=> array('mobile' => false),
		'hoverIntent'							=> array('mobile' => false),
		'hoverintent-js'						=> array('mobile' => false),
		// jQuery
		// 'jquery-ui-datepicker'				=> CRITICAL FOR PEEPSO NOTIFICATIONS & MENUS,
		//Peepso

		// PEEPSO CRITICAL SCRIPTS
		'peepso-friends'						=> array('logged-in' => true),
		'msgso'									=> array('logged-in' => true),
			'peepso-activity'					=> array('logged-in' => true),
				'peepso-activitystream'			=> array('logged-in' => true),
				'peepso-comment'				=> array('logged-in' => true),
		'peepso-bundle'							=> array('logged-in' => true),
			'peepso'							=> array('logged-in' => true),
			'peepso-window'						=> array('logged-in' => true),
			'peepso-elements'					=> array('logged-in' => true),
			'peepso-modules'					=> array('logged-in' => true),
			'peepso-notification'				=> array('logged-in' => true),
			'peepso-sections'					=> array('logged-in' => true),
			'peepso-datepicker'					=> array('logged-in' => true),
			'peepso-avatar'						=> array('logged-in' => true),
				'peepso-fileupload'				=> array('logged-in' => true),
			'peepso-avatar-dialog'				=> array('logged-in' => true),
				'peepso-crop'					=> array('logged-in' => true),
					'peepso-hammer'				=> array('logged-in' => true),

		// OTHER PEEPSO SCRIPTS
		'peepso*'								=> array('logged-in' => true, 'page' => 'social'),

		// 'peepso-member'							=> array('page' => 'social'),
		// 'peepsotags'							=> array('page' => 'social'),
		// 'peepsoreactions'						=> array('page' => 'social'),
		// 'peepso-hashtags'						=> array('page' => 'social'),
		// 'peepso-resize'							=> array('page' => 'social'),
		// 'peepsolocation-js'						=> array('page' => 'social'),
		// 'peepso-time'							=> array('page' => 'social'),
		// 'peepso-groups'							=> array('page' => 'social'),
		// 'peepso-groups-group'					=> array('page' => 'social'),
		// 'peepso-groups-group'					=> array('page' => 'social'),
		// 'peepso-page-autoload'					=> array('page' => 'social'),
		// 'peepso-blogposts'						=> array('page' => 'social'),
		// 'peepsovideos'							=> array('page' => 'social'),
		// 'peepso-photos'							=> array('page' => 'social'),
		// 'peepso-moods'							=> array('page' => 'social'),
		// 'peepso-resize'							=> array('page' => 'social'),
		// 'peepso-profile'						=> array('page' => 'social'),

		'peepso-markdown'						=> array('false' => ''),
		'peepso-modal-comments'					=> array('false' => ''),
		'peepso-friends-shortcode'				=> array('false' => ''),
		// 'responsive-menu-pro-jquery-touchswipe'	=> array('mobile' => true), // Smart Slider lost
		'responsive-menu-pro-noscroll'			=> array('mobile' => true), //

		// MASONRY
		'masonry'								=> array('mobile' => false),
		'jquery-masonry'						=> array('mobile' => false),
		'cnh-masonry'							=> array('mobile' => false),
	);

	// Plugin path & url properties
	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

	// Those properties will change
	private static $css_if;
	private static $js_if;


	public function __construct() {
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		self::$css_if = self::CSS_IF;
		self::$js_if = self::JS_IF;
	}


	/* GETTERS
	-------------------------------------------------------*/
	public function plugin_path() {
		return self::$PLUGIN_PATH;
	}

	public function plugin_uri() {
		return self::$PLUGIN_URI;
	}

	public static function css_if() {
		return self::$css_if;
	}

	public static function css_if_remove( $style)
	{
		if (isset(self::$css_if[$style])) {
			unset(self::$css_if[$style]);
			return true;
		}
		return false;
	}

	public static function js_if()
	{
		return self::$js_if;
	}

	public static function js_if_remove($script)
	{
		if (isset(self::$js_if[$script])) {
			unset(self::$js_if[$script]);
			return true;
		}
		return false;
	}

	public static function is_deferred( $type, $handle ) {
		if ($type=='script')
			$deferred = in_array($handle, self::DEFER_JS);
		elseif ($type=='style')
			$deferred = in_array($handle, self::DEFER_CSS);
		else
			$deferred=false;
		return $deferred;
	}

	public static function is_preloaded( $style )
	{
		$preloaded = in_array($style, self::PRELOAD_CSS);
		return $preloaded;
	}

	public static function is_replaced($style)
	{
		$replaced = in_array($style, array_keys(self::CSS_REPLACE));
		return $replaced;
	}

	public static function get_replacement($style) {
		$args = self::CSS_REPLACE[$style];
		$args['handle'] = $style;
		return $args;
	}

}
