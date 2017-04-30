<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Optimize extends WPSSM_Settings {
	
	public $stylemods;
	public $scriptmods;
	public $async;
	
	public function __construct() {
		parent::__construct();
	
		$this->hydrate_optimize();
		//echo '<pre> <p>opt_general_settings in construct : </p><p>' . var_dump($this->opt_general_settings['record']) . '</p></pre>';
		if ( ($this->opt_general_settings['loadjs']=='on') ) {	
			// load assets for this page
	    add_action( 'wp_enqueue_scripts', array($this,'load_frontend_assets_cb') );
	  }
		if ( ($this->opt_general_settings['record']=='off') && ($this->opt_general_settings['optimize']=='on') ) {	
			add_action( 'wp_enqueue_scripts', array($this, 'apply_scripts_mods_cb'), PHP_INT_MAX );
			//add_action( 'wp_enqueue_scripts', array($this, 'debug_scripts_enqueue_cb'), PHP_INT_MAX );
			add_action( 'wp_enqueue_scripts', array($this, 'apply_styles_mods_cb'), PHP_INT_MAX );
			add_action( 'get_footer', array($this, 'enqueue_footer_styles_cb') );
			add_filter( 'script_loader_tag', 'add_async_tag_cb', 10, 3 );
		}
	}		
	
	public function hydrate_optimize() {
		$scripts = $this->enqueued_assets['scripts'];
		DBG::log('In WPSSM_Optimize hydrate : scripts', $scripts);
		$this->scriptmods = array_column($this->enqueued_assets['scripts'], 'mods', 'handle');
		$this->stylemods = array_column($this->enqueued_assets['styles'], 'mods', 'handle');
		
		//$this->async = array_column($this->scriptmods['location'], 'handle');		
		//DBG::log('in hydrate : $this->async after column', $this->async);
		//$this->async = array_filter($this->async, array($this, 'filter_async') );	
		//DBG::log('in hydrate : $this->async after filter', $this->async);
	}
	
	public function filter_async($location) {
		return ($location=='async');
	}
	
	public function debug_scripts_enqueue_cb() {
		DBG::log('In debug_styles_enqueue_cb');
		wp_deregister_script( 'jquery-core' );
		wp_register_script( 'jquery-core', '/wp-includes/js/jquery/jquery.js', array(), false, true);
		wp_enqueue_script('jquery-core');
	}
	
	public function load_frontend_assets_cb() {
		wp_enqueue_script( 'loadjs_js', plugins_url( '../assets/js/loadjs.min.js', __FILE__ ) , false, self::WPSSM_VERSION );
	}
	
	function add_async_tag_cb( $tag, $handle, $src ) { 
		if ( in_array( $handle, $this->async ) ) {
		    return '<script src="' . $src . '" async type="text/javascript"></script>' . "\n";
		}
		DBG::log('in add_async_tag_cb : $tag = ', $tag);
		return $tag;
	} 
	

	public function apply_scripts_mods_cb() {
		//DBG::log('In apply_scripts_mods_cb');
		$scripts = $this->opt_enqueued_assets['scripts'];
		DBG::log(array('In apply_scripts_mods_cb : scripts '=>$scripts));
		DBG::log(array('In apply_scripts_mods_cb : mods '=>$this->scriptmods));
		
		foreach ($this->scriptmods as $handle => $mod) {
			if ( isset( $mod['location'] ) ) {
				$location = $mod['location'];
				wp_deregister_script( $handle );
				if ($location != 'disabled' ) {
					wp_register_script( $handle, 
						$scripts[$handle]['filename'],
						$scripts[$handle]['dependencies'],
						$scripts[$handle]['version'],
						($location=='footer')?true:false );				
					wp_enqueue_script( $handle );
				}
			}
		}
	}
	
	public function apply_styles_mods_cb() {
		DBG::log('In apply_styles_mods_cb');
		$styles = $this->opt_enqueued_assets['styles'];
		DBG::log('In apply_styles_mods_cb : styles ',$styles);
		DBG::log('In apply_styles_mods_cb : mods ',$this->stylemods);
		
		foreach ($this->stylemods as $handle => $mod) {
			if ( isset( $mod['location'] ) ) {
				if ( $mod['location']=='footer') {
					wp_dequeue_style( $handle );
				}
				elseif ($mod['location']=='disabled') {
					wp_deregister_style( $handle );
				}
			}
		}	
	
	}
	
	function enqueue_footer_styles_cb() {
		$styles = $this->opt_enqueued_assets['styles'];
		foreach ($this->stylemods as $handle => $mod) {
			if ( isset( $mod['location'] ) && ( $mod['location']=='footer') ) {
    		wp_enqueue_style( $handle, 
													$styles[$handle]['filename'],
													$styles[$handle]['dependencies'],
													$styles[$handle]['version']
													);	
			}
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

