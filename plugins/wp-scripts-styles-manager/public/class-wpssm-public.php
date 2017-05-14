<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Public extends WPSSM {
	/* Attributes */
	private $mods;

	/* Methods */
	public function __construct() {
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-assets.php' ;	
		$this->mods = new WPSSM_Assets_Mods;
	}		
	
	public function hydrate() {
		if ( is_admin() ) return;
		//$this->hydrate_opt( $this->opt_mods, 'wpssm_mods');
		WPSSM_Debug::log('In WPSSM_Public hydrate' );
	}
	
	public function init_recording() {
		/* In case recording is activated, instantiates relevant classes */
		$this->assets = new WPSSM_Assets();
	}
	
	public function enqueue_scripts() {
		wp_enqueue_script( 'wpssm_loadjs', plugins_url( '../public/js/loadjs.min.js', __FILE__ ) , false, $this->version );
	}
	
	public function add_async_tag_cb( $tag, $handle, $src ) { 
		if ( !is_admin() && $this->mods->is_async( $handle ) ) {
				WPSSM_Debug::log('in add_async_tag_cb : async found for ' . $handle );
		    $tag='<script src="' . $src . '" async type="text/javascript"></script>' . "\n";
		}
		return $tag;
	} 
	
	public function apply_scripts_mods_cb() {
		WPSSM_Debug::log('In apply_scripts_mods_cb');
		global $wp_scripts;
		$scripts = $wp_scripts->registered;
		//WPSSM_Debug::log('In apply_scripts_mods_cb : registered scripts ',$scripts);
		//WPSSM_Debug::log('In apply_scripts_mods_cb : mods ', $this->opt_mods['scripts']);
		
		foreach ( $this->mods->get( 'scripts', 'disabled' ) as $handle) {
			// continue in case a script was recorded but disappeared in between - plugin uninstalled for instance
			if (!isset($scripts[$handle])) continue;
			wp_deregister_script( $handle );
		}	
		foreach ( $this->mods->get( 'scripts', 'footer' ) as $handle) {
				// continue in case a script was recorded but disappeared in between - plugin uninstalled for instance
			if (!isset($scripts[$handle])) continue;
//				WPSSM_Debug::log('In footer enqueue loop, src for handle ' . $handle, $scripts[$handle]->src);
//				WPSSM_Debug::log('In footer enqueue loop, deps for handle ' . $handle, $scripts[$handle]->deps);
//				WPSSM_Debug::log('In footer enqueue loop, ver for handle ' . $handle, $scripts[$handle]->ver);
		
			wp_deregister_script( $handle );
			wp_register_script( $handle, 
			$scripts[$handle]->src,
			$scripts[$handle]->deps,
			$scripts[$handle]->ver,
			true);				
			wp_enqueue_script( $handle );
		}	
	}	
		
	public function apply_styles_mods_cb() {
		WPSSM_Debug::log('In apply_styles_mods_cb');
		//WPSSM_Debug::log('In apply_styles_mods_cb : mods ',$this->opt_mods['styles']);
		global $wp_styles;
		$styles = $wp_styles->registered;
		foreach ( $this->mods->get( 'styles', 'disabled' ) as $handle) {
				if (!isset($styles[$handle])) continue;
				wp_deregister_styles( $handle );
		}	
		foreach ( $this->mods->get( 'styles', 'footer' ) as $handle) {
				if (!isset($styles[$handle])) continue;
				wp_dequeue_style( $handle );
		}	
	}
	
	function enqueue_footer_styles_cb() {
		WPSSM_Debug::log('In enqueue_footer_styles_cb');
		global $wp_styles;
		$styles = $wp_styles->registered;
		WPSSM_Debug::log('In enqueue_footer_styles_cb : styles ',$styles);
		foreach ( $this->mods->get( 'styles', 'footer' ) as $handle) {
			if (!isset($styles[$handle])) continue;
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
//			//WPSSM_Debug::log(array('Not in POST OR RECIPE'));
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

