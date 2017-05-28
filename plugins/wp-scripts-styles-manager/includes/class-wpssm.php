<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	trait Utilities {
		
		function debug( $msg, $var ) {
			if ( class_exists( 'PHP_Debug' ) ) {
				PHP_Debug::log( $msg, $var );
			}
		}
		
		function hydrate_args( $args ) {
			//$this->debug( 'In ' . get_class() . ' : hydrate_args before ', $args);
			foreach ($args as $key=>$value) {
				if ( property_exists( $this,$key ) ) {
					$this->$key = $value;
					//WP_Debug::log( 'In hydrate_args loop ' . $key, $value);
				}
			}	
		}
		
		// Get active tab
		function get_tab() {
			return isset( $_GET[ 'tab' ] ) ? esc_html($_GET[ 'tab' ]) : 'general';
		}
		
		// Get form input name
		public function get_input_name( $type, $handle, $field ) {
			return  $type . '_' . $handle . '_' . $field;
		}	
		
	}
	
class WPSSM {
	
	/* Plugin attributes */
	const PLUGIN_NAME = 'wpssm';
	const PLUGIN_VERSION = '1.1.0';
	const PLUGIN_SUBMENU = 'tools.php'; // 'options-general.php'

	/* Default display attributes */
	const DEFAULT_GROUPBY = 'location';

	/* File size limits for priority calculation & notifications */
	const SMALL = 1000;
	const LARGE = 1000;
	const MAX = 200000;
	protected $sizes = array('small'=>self::SMALL, 'large'=>self::LARGE, 'max'=>self::MAX );	
	
	/* Post update */
	const FORM_ACTION = 'wpssm_update_settings';
	const NONCE = 'wp8756';	
		
	/* Plugin load atttributes */
	protected $loader;
	protected $user_notification; 
						
	/* Class local attributes */
	private $record;
	private $optimize;
	private $javasync;
	private $args;
	
	/* Class objects */
	protected $Settings;

	public function __construct() {
		$this->load_common_dependencies();
		$this->init_plugin();
		$this->define_admin_hooks();
		$this->define_admin_post_hooks();
		if ( ($this->record=='off') && ($this->optimize=='on') ) 	$this->define_public_hooks();
		if ( $this->record == 'on' ) 															$this->define_record_hooks();
	}

	private function load_common_dependencies() {
		/* The class responsible for orchestrating the actions and filters of the core plugin */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpssm-loader.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-options.php' ;			
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'assets/class-wpssm-options-general.php';
		$this->loader = new WPSSM_Loader();
	}
	
	private function init_plugin() {
		//PHP_Debug::log('In WPSSM get_plugin_Settings');
		$this->Settings = new WPSSM_Options_General( array('plugin_version'=>self::PLUGIN_VERSION) );
		$this->record = 	$this->Settings->get('record');
		$this->optimize = $this->Settings->get('optimize');
		$this->javasync = $this->Settings->get('javasync');	
		$this->args = array(	'plugin_name' 		=> self::PLUGIN_NAME,
													'plugin_submenu' 	=> self::PLUGIN_SUBMENU,
													'plugin_version' 	=> self::PLUGIN_VERSION,
													'form_action' 		=> self::FORM_ACTION,
													'record' 					=> $this->record,
													'optimize' 				=> $this->optimize,
													'javasync' 				=> $this->javasync,
													'nonce' 					=> self::NONCE,
													'groupby' 				=> self::DEFAULT_GROUPBY,
													'sizes' 					=> $this->sizes );	
		//PHP_Debug::log('$this->Settings->get()', $this->Settings->get() );
	}
	
	private function define_admin_post_hooks() {
		PHP_Debug::log('In define_admin_post_hooks');														
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-options-assets.php' ;			
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-options-mods.php' ;			
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/class-wpssm-admin-post.php';
		$plugin_post = new WPSSM_Admin_Post( $this->args );
		$this->loader->add_action( 'admin_post_' . self::FORM_ACTION, 	$plugin_post, 'update_settings_cb' 					);
		$this->loader->add_action( 'admin_head', 												$plugin_post, 'init_post_cb' 								);
	}
	
	private function define_admin_hooks() {
		PHP_Debug::log('In define_admin_hooks');														
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpssm-admin.php';
		$plugin_admin = new WPSSM_Admin( $this->args );
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'init_admin_cb' 							);
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'add_plugin_menu_option_cb' 	);
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_scripts_cb' 				);
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_styles_cb' 					);
	}

	private function define_public_hooks() {
		PHP_Debug::log('In define_public_hooks'); 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpssm-public.php';		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'assets/class-wpssm-options-mods.php';		
		$plugin_public = new WPSSM_Public();
		$this->loader->add_action( 'wp', 															$plugin_public, 'init_public_cb' 													);
		$this->loader->add_action( 'wp_enqueue_scripts',							$plugin_public, 'apply_scripts_mods_cb', 	PHP_INT_MAX 		);
		$this->loader->add_action( 'wp_enqueue_scripts', 							$plugin_public, 'apply_styles_mods_cb', 	PHP_INT_MAX 		);
		$this->loader->add_action( 'get_footer', 											$plugin_public, 'enqueue_footer_styles_cb' 								);
		$this->loader->add_filter( 'script_loader_tag', 							$plugin_public, 'add_async_tag_cb', 			PHP_INT_MAX, 3 	);
		if ( ( $this->javasync=='on') ) {	
    	$this->loader->add_action( 'wp_enqueue_scripts', 						$plugin_public, 'enqueue_scripts' , 1 										);
  	}
	}			
		
	public function define_record_hooks() {
		PHP_Debug::log('In define_record_hooks'); 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/class-wpssm-admin-record.php';		
		$plugin_record = new WPSSM_Admin_Record();
		$this->loader->add_action( 'wp', 															$plugin_record, 'init_recording' 													);
		$this->loader->add_action( 'wp_head', 												$plugin_record, 'record_header_assets_cb' 								);
		$this->loader->add_action( 'wp_print_footer_scripts', 				$plugin_record, 'record_footer_assets_cb' 								);
	}	
	
	/* SHARED FUNCTIONS 
	--------------------------------------------------*/
	public function run() {
		$this->loader->run();
	}
	
	public function get_loader() {
		return $this->loader;
	}
		

}




