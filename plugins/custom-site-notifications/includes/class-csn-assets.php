<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CSN_Assets {

	public static function plugin_url() {
		return plugin_dir_url( dirname( __FILE__ ) );
	}
	
	public static function plugin_path() {
		return plugin_dir_path( dirname( __FILE__ ) );
	}


}