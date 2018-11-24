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

		add_action('wp_enqueue_scripts', array($this, 'enqueue_image_lightbox_js'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_image_lightbox_css'));

	}

    public function enqueue_image_lightbox_js() {
        if (! is_single() ) return;

   		$uri = self::$PLUGIN_URI . '/vendor/';
  		$path = self::$PLUGIN_PATH . '/vendor/';
		custom_enqueue_script( 'image-lightbox-plugin', $uri, $path, 'imagelightbox.js', array( 'jquery' ), CHILD_THEME_VERSION, true );

   		$uri = self::$PLUGIN_URI . '/assets/js/';
  		$path = self::$PLUGIN_PATH . '/assets/js/';  
		custom_enqueue_script( 'custom-lightbox', $uri, $path, 'lightbox.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
    	wp_enqueue_script( 'jquery-touch-punch', true );
    }

	public function enqueue_image_lightbox_css() {
        if (! is_single() ) return;
  		$uri = self::$PLUGIN_URI . '/assets/css/';
  		$path = self::$PLUGIN_PATH . '/assets/css/';
		custom_enqueue_style( 'custom-lightbox', $uri, $path, 'lightbox.css', array(), CHILD_THEME_VERSION );	
	}    

}
