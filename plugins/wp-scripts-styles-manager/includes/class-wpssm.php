<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	trait Utilities {
		function hydrate_args( $args ) {
			//WP_Debug::log( 'In hydrate_args before ', $args);
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
		
	}
	
class WPSSM {
	
	/* Plugin attributes */
	const PLUGIN_NAME = 'wpssm';
	const PLUGIN_VERSION = '1.1.0';
	const PLUGIN_SUBMENU = 'tools.php'; // 'options-general.php'
	
	/* Debug */
	const PLUGIN_DBG = on;
	
	/* Post update */
	const FORM_ACTION = 'wpssm_update_settings';
	const NONCE = 'wp8756';	
		
	/* Plugin load atttributes */
	protected $loader;
	protected $user_notification; 
						
	/* Plugin settings */
	protected $settings;
	private $record;
	private $optimize;
	private $javasync;
	private $args;
	
	public function __construct() {
		$this->load_dependencies();
		//$this->define_debug_hooks();
		$this->init_plugin();
		$this->define_admin_hooks();
		$this->define_admin_post_hooks();
		if ( ($this->record=='off') && ($this->optimize=='on') ) 	$this->define_public_hooks();
		if ( $this->record == 'on' ) 															$this->define_record_hooks();
	}

	
	private function load_dependencies() {
		/* The class responsible for orchestrating the actions and filters of the core plugin */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpssm-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'debug/class-dbg.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'assets/class-wpssm-options.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'assets/class-wpssm-options-general.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpssm-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/class-wpssm-admin-post.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/class-wpssm-admin-record.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpssm-public.php';
		$this->loader = new WPSSM_Loader();
	}
	
	private function init_plugin() {
		//WPSSM_Debug::log('In WPSSM get_plugin_settings');
		$this->settings = new WPSSM_Options_General( array('plugin_version'=>self::PLUGIN_VERSION) );
		$this->record = 	$this->settings->get('record');
		$this->optimize = $this->settings->get('optimize');
		$this->javasync = $this->settings->get('javasync');	
		$this->args = array(	'plugin_name' => self::PLUGIN_NAME,
													'submenu' 		=> self::PLUGIN_SUBMENU,
													'form_action' => self::FORM_ACTION,
													'record' 			=> $this->record,
													'optimize' 		=> $this->optimize,
													'javasync' 		=> $this->javasync,
													'nonce' 			=> self::NONCE,
													'sizes' 			=> $this->sizes );	
		//WPSSM_Debug::log('$this->settings->get()', $this->settings->get() );
	}
	
	
	private function define_debug_hooks() {
		if ( !self::PLUGIN_DBG ) return;
		WPSSM_Debug::log('In define_debug_hooks');
		//$plugin_debug = new WPSSM_Debug();
		//$this->loader->add_action( 'wp',	$plugin_debug, 'init_dbg' 																);
		//set_error_handler( 'WPSSM_Debug::error' );	
	}
	
	private function define_admin_post_hooks() {
		WPSSM_Debug::log('In define_admin_post_hooks');														
		$plugin_post = new WPSSM_Admin_Post(	$this->args );
		$this->loader->add_action( 'admin_menu', 												$plugin_post, 'init_post_cb' 															);
		$this->loader->add_action( 'admin_post_' . self::FORM_ACTION, 	$plugin_post, 'update_settings_cb' 											);
	}
	
	private function define_admin_hooks() {
		WPSSM_Debug::log('In define_admin_hooks');														
		$plugin_admin = new WPSSM_Admin( $this->args );
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'init_admin_cb' 															);
		//$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'init_settings' 														);
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'add_plugin_menu_option_cb' 								);
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_scripts' 													);
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_styles' 													);
	}
	

	private function define_public_hooks() {
		WPSSM_Debug::log('In define_public_hooks'); 
		$plugin_public = new WPSSM_Public();
		$this->loader->add_action( 'wp', 															$plugin_public, 'init_public_cb' 																);
		$this->loader->add_action( 'wp_enqueue_scripts',							$plugin_public, 'apply_scripts_mods_cb', 	PHP_INT_MAX 		);
		$this->loader->add_action( 'wp_enqueue_scripts', 							$plugin_public, 'apply_styles_mods_cb', 	PHP_INT_MAX 		);
		$this->loader->add_action( 'get_footer', 											$plugin_public, 'enqueue_footer_styles_cb' 								);
		$this->loader->add_action( 'script_loader_tag', 							$plugin_public, 'add_async_tag_cb', 			PHP_INT_MAX, 3 	);
		if ( ( $this->javasync=='on') ) {	
    	$this->loader->add_action( 'wp_enqueue_scripts', 						$plugin_public, 'enqueue_scripts' , 1 										);
  	}
	}			
		
	public function define_record_hooks() {
		WPSSM_Debug::log('In define_record_hooks'); 
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




