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

		custom_enqueue_script( 'image-lightbox-plugin', '/vendor/imagelightbox.js', self::$PLUGIN_URI, self::$PLUGIN_PATH, array( 'jquery' ), CHILD_THEME_VERSION, true );

		custom_enqueue_script( 'custom-lightbox', '/assets/js/lightbox.js', self::$PLUGIN_URI, self::$PLUGIN_PATH, array( 'jquery' ), CHILD_THEME_VERSION, true );
    	wp_enqueue_script( 'jquery-touch-punch', true );

		custom_enqueue_style( 'custom-lightbox', self::$PLUGIN_URI, self::$PLUGIN_PATH, '/assets/css/lightbox.css', array(), CHILD_THEME_VERSION );	
	}    

}
