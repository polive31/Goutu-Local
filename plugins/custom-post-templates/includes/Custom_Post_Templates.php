<?php

/* CustomPostTemplates class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomPostTemplates {
	
	protected static $vowels = array('a','e','i','o','u');
	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	
	
	public function __construct() {	
		// IMPORTANT : use wp as a hook, otherwise the archive will not be set yet and errors will occur
		// add_action( 'wp', array($this,'hydrate'));		

		// add_filter( 'genesis_attr_content', 'add_columns_class_to_content' );
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		// add_action('wp_enqueue_scripts', array($this, 'enqueue_masonry_scripts'));

		add_action( 'genesis_before_entry_content', array($this,'add_post_toolbar'), 15);
	}

	public function add_post_toolbar() {
		if ( is_singular( 'post' ) ) {
			ob_start();
			require( self::$PLUGIN_PATH . 'templates/post_toolbar.php' );
			$toolbar = ob_get_contents();
        	ob_end_clean();
        	echo $toolbar;
		}
	} 





}
