<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_FrontEnd extends WPSSM_Admin {
	
	public $mods=array(
		'scripts'=>array(
					'async'=>array(),
					'footer'=>array(),
					'disabled'=>array(),
					'minify'=>array(),
					),
		'styles'=>array(
					'async'=>array(),
					'disabled'=>array(),
					'minify'=>array(),
					),
	);
	
	public function __construct() {
		parent::__construct();
		add_action( 'wp', array($this, 'setup_frontend_hooks') );
	}		
	
	public function setup_frontend_hooks() {
		if (is_admin()) return;
		DBG::log('In add_frontend_mods');

		if ( ($this->opt_general_settings['record']=='off') && ($this->opt_general_settings['optimize']=='on') ) {	
			$this->hydrate_optimize();
			add_action( 'wp_enqueue_scripts', array($this, 'apply_scripts_mods_cb'), PHP_INT_MAX );
			add_action( 'wp_enqueue_scripts', array($this, 'apply_scripts_mods_cb'), PHP_INT_MAX );
			//add_action( 'wp_enqueue_scripts', array($this, 'debug_scripts_enqueue_cb'), PHP_INT_MAX );
			add_action( 'wp_enqueue_scripts', array($this, 'apply_styles_mods_cb'), PHP_INT_MAX );
			add_action( 'get_footer', array($this, 'enqueue_footer_styles_cb') );
			add_filter( 'script_loader_tag', array($this, 'add_async_tag_cb'), 10, 3 );
			if ( ($this->opt_general_settings['javasync']=='on') ) {	
	    	add_action( 'wp_enqueue_scripts', array($this,'load_frontend_assets_cb') );
	  	}
		}
	}
	
	public function hydrate_optimize() {
		DBG::log('In hydrate optimize - $this->mods',$this->mods);
	}
	
	public function debug_scripts_enqueue_cb() {
		DBG::log('In debug_styles_enqueue_cb');
		wp_deregister_script( 'jquery-core' );
		wp_register_script( 'jquery-core', '/wp-includes/js/jquery/jquery.js', array(), false, true);
		wp_enqueue_script('jquery-core');
	}
	
	public function load_frontend_assets_cb() {
		wp_enqueue_script( 'wpssm_loadjs', plugins_url( '../assets/js/loadjs.min.js', __FILE__ ) , false, self::WPSSM_VERSION );
	}
	
	public function add_async_tag_cb( $tag, $handle, $src ) { 
		if ( !is_admin() && in_array( $handle, $this->mods['scripts']['async'] ) ) {
		    $tag='<script src="' . $src . '" async type="text/javascript"></script>' . "\n";
				DBG::log('in add_async_tag_cb : ASYNC added ! ');
		}
		return $tag;
	} 
	
	public function apply_scripts_mods_cb() {
		DBG::log('In apply_scripts_mods_cb');
		global $wp_scripts;
		$scripts = $wp_scripts->registered;
		DBG::log('In apply_scripts_mods_cb : registered scripts ',$scripts);
		DBG::log('In apply_scripts_mods_cb : mods ', $this->mods['scripts']);
		
		if (isset($this->mods['scripts']['disabled'])) {
			foreach ($this->mods['scripts']['disabled'] as $handle) {
				// continue in case a script was recorded but disappeared in between - plugin uninstalled for instance
				if (!isset($scripts[$handle])) continue;
				wp_deregister_script( $handle );
			}	
		}
		if (isset($this->mods['scripts']['footer'])) {
			foreach ($this->mods['scripts']['footer'] as $handle) {
				// continue in case a script was recorded but disappeared in between - plugin uninstalled for instance
				if (!isset($scripts[$handle])) continue;
				DBG::log('In footer enqueue loop, src for handle ' . $handle, $scripts[$handle]->src);
				DBG::log('In footer enqueue loop, deps for handle ' . $handle, $scripts[$handle]->deps);
				DBG::log('In footer enqueue loop, ver for handle ' . $handle, $scripts[$handle]->ver);
			
				wp_deregister_script( $handle );
				wp_register_script( $handle, 
				$scripts[$handle]->src,
				$scripts[$handle]->deps,
				$scripts[$handle]->ver,
				true);				
				wp_enqueue_script( $handle );
			}	
		}
	}	
		
	public function apply_styles_mods_cb() {
		DBG::log('In apply_styles_mods_cb');
		DBG::log('In apply_styles_mods_cb : mods ',$this->mods['styles']);
		if (isset($this->mods['styles']['disabled'])) {
			foreach ($this->mods['styles']['disabled'] as $handle) {
					wp_deregister_styles( $handle );
			}	
		}
		if (isset($this->mods['styles']['footer'])) {
			foreach ($this->mods['styles']['footer'] as $handle) {
					wp_dequeue_style( $handle );
			}	
		}
	}
	
	function enqueue_footer_styles_cb() {
		DBG::log('In enqueue_footer_styles_cb');
		global $wp_styles;
		$styles = $wp_styles->registered;
		DBG::log('In enqueue_footer_styles_cb : styles ',$styles);
		foreach ($this->mods['styles']['footer'] as $handle) {
  		wp_enqueue_style( $handle, 
												$styles[$handle]->src,
												$styles[$handle]->deps,
												$styles[$handle]->ver
												);	
		}
	}

	
	
	
	
	
		
//		if ( !is_front_page() ) {
//			wp_dequeue_script( 'easingslider' );
//		}
//		
//		if ( !is_single() ) {
//			//DBG::log(array('Not in POST OR RECIPE'));
//			wp_dequeue_script( 'galleria' );
//			wp_dequeue_script( 'galleria-fs' );
//			wp_dequeue_script( 'galleria-fs-theme' );
//		}
//		
//		wp_dequeue_script( 'cnss_js' );
//		//wp_enqueue_script( 'cnss_js', PLUGINS_URL . '/easy-social-icons/js/cnss.js' , true );
//
//
//		//wp_dequeue_script( 'jquery-ui-sortable' );
//		//wp_dequeue_script( 'bp-confirm' );
//		wp_deregister_script( 'bp-legacy-js' );
//		wp_register_script( 'bp-legacy-js', 
//			PLUGINS_URL . '/buddypress/bp-templates/bp-legacy/js/buddypress.min.js',
//			array(),
//			false,
//			true );
//		wp_enqueue_script( 'bp-legacy-js' );


}

