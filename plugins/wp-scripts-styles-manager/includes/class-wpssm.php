<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM {
	
	const PLUGIN_SLUG = 'wpssm';
	const PLUGIN_VERSION = '1.1.0';
	const PLUGIN_SUBMENU = 'tools.php';
	
	/* Plugin load atttributes */
	protected $loader;
	protected $user_notification; 
	
	/* WPSSM-specific atttributes */
	public $opt_general_settings = array('record'=>'off', 'optimize'=>'off', 'javasync'=>'off', 'wpssm_version'=>self::WPSSM_VERSION);
	public $opt_enqueued_assets = array( 'pages'=>array(), 'scripts'=>array(), 'styles'=>array());
	public $opt_mods = array(
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
		$this->load_dependencies();
		$this->hydrate();
		$this->define_debug_hooks();
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
	
	
	private hydrate() {
		// Initialize all attributes common to admin & frontend
		$get_option = get_option( 'wpssm_general_settings' );
		if ($get_option!=false) {
			foreach ($get_option as $key=>$value) {$this->opt_general_settings[$key]=$value;}
		}
		DBG::log('In WPSSM_Admin hydrate common : $this->opt_general_settings', $this->opt_general_settings);

		// hydrate enqueued assets property with options content
		$get_option = get_option( 'wpssm_enqueued_assets' );
		if ($get_option!=false) {
			foreach ($get_option as $key=>$value) {$this->opt_enqueued_assets[$key]=$value;}
		}
		DBG::log('In WPSSM_Settings hydrate common $this->enqueud_assets: ', $this->opt_enqueued_assets);
	
		// hydrate mods table with options content
		$get_option = get_option('wpssm_mods');	
		if ($get_option!=false) {
			$this->mods = $get_option;	
		}
	}
	
	private function define_debug_hooks() {
		DBG::log('In define_debug_hooks');
		$plugin_admin = new DBG();
	}
	
	private function define_admin_hooks() {
		DBG::log('In define_admin_hooks');
		$plugin_admin = new WPSSM_Admin( PLUGIN_NAME, PLUGIN_VERSION );
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'add_plugin_menu_option_cb' );
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'admin_init_cb' );
		$this->loader->add_action( 'admin_post_' . $this->form_action, 	$plugin_admin, 'update_settings_cb' );
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_styles' );
	}

	private function define_public_hooks() {
		DBG::log('In define_public_hooks');
		if (is_admin()) return;
		$plugin_public = new WPSSM_Public( PLUGIN_NAME, PLUGIN_VERSION );

		if ( ($this->opt_general_settings['record']=='off') && ($this->opt_general_settings['optimize']=='on') ) {														);	
			$this->loader->add_action( 'wp_enqueue_scripts',							$plugin_public, 'apply_scripts_mods_cb', 	PHP_INT_MAX 		);
			$this->loader->add_action( 'wp_enqueue_scripts', 							$plugin_public, 'apply_styles_mods_cb', 	PHP_INT_MAX 		);
			$this->loader->add_action( 'get_footer', 											$plugin_public, 'enqueue_footer_styles_cb' 								);
			$this->loader->add_action( 'script_loader_tag', 							$plugin_public, 'add_async_tag_cb', 			PHP_INT_MAX, 3 	);

			if ( ($this->opt_general_settings['javasync']=='on') ) {	
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


}
