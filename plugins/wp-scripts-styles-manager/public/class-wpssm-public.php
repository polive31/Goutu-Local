<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Public {

	/* Attributes */
	private $Mods;

	/* Methods */
	public function __construct() {
		//PHP_Debug::trace('*** In WPSSM_Public __construct ***' );
	}

	public function init_public_cb() {
		if ( is_admin() ) return;
		//PHP_Debug::trace('*** In WPSSM_Public init_plugin_cb ***' );
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-options.php' ;
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-options-mods.php' ;
		$this->Mods = new WPSSM_Options_Mods;
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'wpssm_loadjs', plugins_url( '../public/js/loadjs.min.js', __FILE__ ) , false, $this->version );
	}

	public function add_async_tag_cb( $tag, $handle, $src ) {
		if ( is_admin() ) return;
		//PHP_Debug::trace('in script_loader_trag => add_async_tag_cb' );
		if ( $this->Mods->is_async( $handle ) ) {
			//PHP_Debug::trace('in add_async_tag_cb : async found for ' . $handle );
	    $tag='<script src="' . $src . '" async type="text/javascript"></script>' . "\n";
		}
		return $tag;
	}

	public function apply_scripts_mods_cb() {
		if ( is_admin() ) return;
		//PHP_Debug::trace('In apply_scripts_mods_cb');
		global $wp_scripts;
		$scripts = $wp_scripts->registered;
		//PHP_Debug::trace('In apply_scripts_mods_cb : registered scripts ',$scripts);
		//PHP_Debug::trace('In apply_scripts_mods_cb : mods ', $this->opt_mods['scripts']);

		foreach ( $this->Mods->get( 'scripts', 'disabled' ) as $handle) {
			// continue in case a script was recorded but disappeared in between - plugin uninstalled for instance
			if (!isset($scripts[$handle])) continue;
			wp_deregister_script( $handle );
		}
		foreach ( $this->Mods->get( 'scripts', 'footer' ) as $handle) {
				// continue in case a script was recorded but disappeared in between - plugin uninstalled for instance
			if (!isset($scripts[$handle])) continue;
//				//PHP_Debug::trace('In footer enqueue loop, src for handle ' . $handle, $scripts[$handle]->src);
//				//PHP_Debug::trace('In footer enqueue loop, deps for handle ' . $handle, $scripts[$handle]->deps);
//				//PHP_Debug::trace('In footer enqueue loop, ver for handle ' . $handle, $scripts[$handle]->ver);

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
		if ( is_admin() ) return;
		//PHP_Debug::trace('In apply_styles_mods_cb');
		//PHP_Debug::trace('In apply_styles_mods_cb : mods ',$this->opt_mods['styles']);
		global $wp_styles;
		$styles = $wp_styles->registered;
		foreach ( $this->Mods->get( 'styles', 'disabled' ) as $handle) {
				if (!isset($styles[$handle])) continue;
				wp_deregister_styles( $handle );
		}
		foreach ( $this->Mods->get( 'styles', 'footer' ) as $handle) {
				if (!isset($styles[$handle])) continue;
				wp_dequeue_style( $handle );
		}
	}

	function enqueue_footer_styles_cb() {
		if ( is_admin() ) return;
		//PHP_Debug::trace('In enqueue_footer_styles_cb');
		global $wp_styles;
		$styles = $wp_styles->registered;
		//PHP_Debug::trace('In enqueue_footer_styles_cb : styles ',$styles);
		foreach ( $this->Mods->get( 'styles', 'footer' ) as $handle) {
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
//			//PHP_Debug::trace(array('Not in POST OR RECIPE'));
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
