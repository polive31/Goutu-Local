<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CASM_Assets {

	// Scripts to be loaded asynchronously
	const DEFER_JS = array(
		'skip-links',
		// 'foodie-pro-general',
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
		'foodiepro-fonts',
	);


	// Stylesheets to be loaded conditionnally & replaced if needed
	const CSS_IF = array(
		// 'custom-star-ratings' 				=> array('page' => 'blog-page'),
		'responsive-menu-pro'				=> array(	'mobile' => true,
														'replace' => array(
															'file' 		=> '/assets/css/responsive-menu.css',
														)
													),
		'pwp-offline-style'					=> array( 'mobile'	=> true,
														'replace' => array(
															'file' 		=> '/assets/css/offline.css',
														)
													),
		'news-style'						=> array('page' => 'home'),
		'newsletter'						=> array('page' => 'home'),
		'yarppRelatedCss' 					=> array('singular' => 'post recipe'),
		'custom-lightbox'					=> array('singular' => 'post recipe'),
		'name-directory-style' 				=> array(	'shortcode' => 'namedirectory',
														'replace' => array(
															'file' 		=> '/assets/css/name_directory.css',
														)),
		'yarppWidgetCss' 					=> array('false' => ''),
		'megamenu' 							=> array(	'mobile' => false,
														'replace' => array(
															'file' 		=> '/assets/css/max-mega-menu.css',
														)),
		'megamenu-fontawesome' 				=> array('false' => ''),
		'megamenu-google-fonts' 			=> array('false' => ''),
		'megamenu-genericons' 				=> array('false' => ''),
		'popup-maker-site' 					=> array('false' => ''),
		'wpba_front_end_styles' 			=> array('false' => ''),
		'frontend-uploader' 				=> array('false' => ''),
		'wp-block-library'					=> array('false' => ''),

		//Peepso
		'peepso'							=> array('page' => 'social'),
		'peepso-custom'						=> array('true' => '',
													'replace' => array(
															'file' 		=> '/assets/css/peepso.css',
													)),
		'peepso-datepicker'					=> array('page' => 'social',
													'replace' => array(
														'file' 		=> '/assets/css/datepicker.css',
													)),
		'peepso*'							=> array('page' => 'social'),
		'msgso*'							=> array('page' => 'social'),
		// Fonts & icons
		'dashicons'							=> array('admin' => true),
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

		// PEEPSO RECAPTCHA & DEPENDENCIES
		'peepso-recaptcha'						=> array('or'=>array('logged-in' => true, 'page' => 'contact')),
			'peepso'							=> array('or'=>array('logged-in' => true, 'page' => 'contact')),
				'peepso-elements'				=> array('or'=>array('logged-in' => true, 'page' => 'contact')),
				'peepso-sections'				=> array('or'=>array('logged-in' => true, 'page' => 'contact')),
				'peepso-modules'				=> array('or'=>array('logged-in' => true, 'page' => 'contact')),

		// PEEPSO WIDGETS SCRIPTS
		'peepso-friends'						=> array('logged-in' => true),
		'msgso'									=> array('logged-in' => true),
			'peepso-activity'					=> array('logged-in' => true),
				'peepso-activitystream'			=> array('logged-in' => true),
				'peepso-comment'				=> array('logged-in' => true),
		'peepso-bundle'							=> array('logged-in' => true),
			'peepso-window'						=> array('logged-in' => true),
			'peepso-notification'				=> array('logged-in' => true),
			'peepso-datepicker'					=> array('logged-in' => true),
			'peepso-avatar'						=> array('logged-in' => true),
			'peepso-fileupload'					=> array('logged-in' => true),
			'peepso-avatar-dialog'				=> array('logged-in' => true),
			'peepso-crop'						=> array('logged-in' => true),
			'peepso-hammer'						=> array('logged-in' => true),

		// OTHER PEEPSO SCRIPTS
		'peepso*'								=> array('logged-in' => true, 'page' => 'social'),
		'peepso-markdown'						=> array('false' => ''),
		'peepso-modal-comments'					=> array('false' => ''),
		'peepso-friends-shortcode'				=> array('false' => ''),

		// RESPONSIVE MENU PRO
		// 'responsive-menu-pro-jquery-touchswipe'	=> array('mobile' => true), // Menu button doesn't work
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


}
