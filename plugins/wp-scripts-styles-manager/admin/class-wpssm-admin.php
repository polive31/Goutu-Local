<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Admin {

	const SIZE_SMALL = 1000;
	const SIZE_LARGE = 1000;
	const SIZE_MAX = 200000;

	protected $config_settings_pages; // Initialized in hydrate_settings
	
	protected $displayed_assets = array();
	
	protected $form_action = 'wpssm_update_settings';
	protected $nonce = 'wp8756';

	protected $header_scripts;
	protected $header_styles;
	protected $active_tab;
	
	protected $filter_args = array( 'location' => 'header' );
	protected $sort_args = array( 
														'field' => 'priority', 
														'order' => SORT_DESC, 
														'type' => SORT_NUMERIC);
														

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}														
														
	public function enqueue_styles() {
		DBG::log('In WPSSM_Admin enqueue styles');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpssm-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		DBG::log('In WPSSM_Admin enqueue scripts');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpssm-admin.js', array( 'jquery' ), $this->version, false );
	}




}

