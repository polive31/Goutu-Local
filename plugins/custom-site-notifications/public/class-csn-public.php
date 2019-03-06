<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSN_Public {

	public $plugin_path;
	public $plugin_url;

	public function __construct( $plugin_path, $plugin_url ) {	
		$this->plugin_path = $plugin_path;
		$this->plugin_url = $plugin_url;
	}

    public function popups_styles_register() {
			custom_register_style(
				'custom-site-popups', 
				'/assets/css/custom_site_popups.css',
				$this->plugin_url, 
				$this->plugin_path
			);
    }



}