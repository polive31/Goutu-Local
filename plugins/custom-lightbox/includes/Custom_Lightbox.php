<?php

/* CustomLightbox class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomLightbox {

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

	public function __construct() {
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		add_action('wp_enqueue_scripts', array($this, 'enqueue_image_lightbox_scripts_styles'));
		// add_action('wp_enqueue_scripts', array($this, 'enqueue_image_lightbox_css'));
	}

    public function enqueue_image_lightbox_scripts_styles() {
		if (! is_single() ) return;

		$args=array(
			'uri'		=> self::$PLUGIN_URI,
			'dir'		=> self::$PLUGIN_PATH,
			'deps'		=> array( 'jquery' ),
			'version'	=> CHILD_THEME_VERSION,
			'footer'	=> true,
		);

		// Enqueue scripts
		$args['handle']= 'image-lightbox-plugin';
		$args['file']= '/vendor/imagelightbox.js';
		foodiepro_enqueue_script( $args );

		$args['handle']= 'custom-lightbox';
		$args['file']= '/assets/js/lightbox.js';
		foodiepro_enqueue_script( $args );
		// foodiepro_enqueue_script( 'custom-lightbox', '', self::$PLUGIN_URI, self::$PLUGIN_PATH, array( 'jquery' ), CHILD_THEME_VERSION, true );

		wp_enqueue_script( 'jquery-touch-punch', true );

		// Enqueue styles
		$args['handle']= 'custom-lightbox';
		$args['file']= '/assets/css/lightbox.css';
		$args['deps']= array();
		foodiepro_enqueue_style( $args );
		// foodiepro_enqueue_style( 'custom-lightbox', '/assets/css/lightbox.css', self::$PLUGIN_URI, self::$PLUGIN_PATH, array(), CHILD_THEME_VERSION );
	}

}
