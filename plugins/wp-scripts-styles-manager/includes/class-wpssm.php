<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM {
	
	/* Plugin attributes */
	const PLUGIN_NAME = 'wpssm';
	const PLUGIN_VERSION = '1.1.0';
	const PLUGIN_SUBMENU = 'tools.php'; // 'options-general.php'
	
	/* Post update */
	const FORM_ACTION = 'wpssm_update_settings';
	const NONCE = 'wp8756';	
		
	/* Plugin load atttributes */
	protected $loader;
	protected $user_notification; 

	/* WPSSM-specific atttributes */
	protected static $opt_general_settings = array(
									'record'=>'off', 
									'optimize'=>'off', 
									'javasync'=>'off', 
									'wpssm_version'=>self::PLUGIN_VERSION);
									
	protected static $opt_enqueued_assets = array( 
									'pages'=>array(), 
									'scripts'=>array(), 
									'styles'=>array());
									
	protected static $opt_mods = array(
						'scripts'=>array(
									'footer'=> array(),
									'async'=>array(),
									'group'=>array(),
									'minify'=>array(),
									), 
						'styles'=>array(
									'footer'=> array(),
									'async'=>array(),
									'group'=>array(),
									'minify'=>array(),
									), 						
						);
							
						
	public function __construct() {
		$this->plugin_name = self::PLUGIN_NAME;
		$this->plugin_version = self::PLUGIN_VERSION;
		$this->plugin_submenu = self::PLUGIN_SUBMENU;
		
		$this->load_dependencies();
		$this->define_debug_hooks();
		$this->hydrate();
		$this->define_admin_hooks();
		add_action( 'wp', array($this, 'define_public_hooks') );
	}

	
	private function load_dependencies() {
		/* The class responsible for orchestrating the actions and filters of the core plugin */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpssm-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'debug/class-dbg.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpssm-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpssm-public.php';
		$this->loader = new WPSSM_Loader();
	}
	
	private function hydrate() {
		$this->update_opt( self::$opt_general_settings, 'wpssm_general_settings');
	}
	
	private function define_debug_hooks() {
		WPSSM_Debug::log('In define_debug_hooks');
		$plugin_debug = new WPSSM_Debug();
	}
	
	
	private function define_admin_hooks() {
		WPSSM_Debug::log('In define_admin_hooks');														
		$plugin_admin = new WPSSM_Admin();
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'admin_init_cb' 														);
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'add_plugin_menu_option_cb' 								);
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_scripts' 													);
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_styles' 													);
		$this->loader->add_action( 'admin_post_' . self::FORM_ACTION, 	$plugin_admin, 'update_settings_cb' 											);
	}
	

	private function define_public_hooks() {
		WPSSM_Debug::log('In define_public_hooks');
		if (is_admin()) return;
		$plugin_public = new WPSSM_Public();
		
		// manage frontend pages recording 
		if ( self::$opt_general_settings['record'] == 'on' ) {
			$this->loader->add_action( 'wp_head', 												$plugin_public, 'record_header_assets_cb' 								);
			$this->loader->add_action( 'wp_print_footer_scripts', 				$plugin_public, 'record_footer_assets_cb' 								);
		}	

		if ( (self::$opt_general_settings['record']=='off') && (self::$opt_general_settings['optimize']=='on') ) {	
			$this->loader->add_action( 'wp_enqueue_scripts',							$plugin_public, 'apply_scripts_mods_cb', 	PHP_INT_MAX 		);
			$this->loader->add_action( 'wp_enqueue_scripts', 							$plugin_public, 'apply_styles_mods_cb', 	PHP_INT_MAX 		);
			$this->loader->add_action( 'get_footer', 											$plugin_public, 'enqueue_footer_styles_cb' 								);
			$this->loader->add_action( 'script_loader_tag', 							$plugin_public, 'add_async_tag_cb', 			PHP_INT_MAX, 3 	);

			if ( (self::$opt_general_settings['javasync']=='on') ) {	
	    	$this->loader->add_action( 'wp_enqueue_scripts', 						$plugin_public, 'enqueue_scripts' , 1 										);
	  	}

		}
	}			
		
		
	public function run() {
		$this->loader->run();
	}
	
	
	public function get_loader() {
		return $this->loader;
	}


	protected function update_opt( &$attribute, $option ) {
		$get_option = get_option( $option );
		if ( $get_option!=false ) {
			if ( is_array($get_option) )
				foreach ($get_option as $key=>$value) {$attribute[$key]=$value;}
			else
				$attribute = $get_option;
		}
		WPSSM_Debug::log('In WPSSM update_opt ', $attribute);
	}
	

}
