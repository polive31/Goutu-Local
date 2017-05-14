<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Assets_Mods {
	
	use DB_Opt;

	/* Options attributes */	
	private $opt_mods = array(
						'scripts'=>array(
									'footer'=> array(),
									'async'=>array(),
									'group'=>array(),
									'disabled'=>array(),
									'minify'=>array(),
									), 
						'styles'=>array(
									'footer'=> array(),
									'async'=>array(),
									'group'=>array(),
									'disabled'=>array(),
									'minify'=>array(),
									), 						
						);
					
						
	public function __construct() {
		//$this->assets_hydrate();
		$this->hydrate_opt( $this->opt_mods, 'wpssm_mods');
		WPSSM_Debug::log('In WPSSM_Assets __construct() $this->opt_mods ', $this->opt_mods );
	}

	public function is_mod( $handle, $field ) {
		if ( isset( $this->opt_mods[ $handle ][ $field ] ) ) return 'modified';
		return '';
	}
	
	public function is_async( $handle ) {
		if ( ! isset( $this->opt_mods['scripts']['async'] )) return false;
		return in_array( $handle, $this->opt_mods['scripts']['async'] );
	}
	
	public function is_footer( $type, $handle ) {
		if ( ! isset( $this->opt_mods[ $type ]['footer'] )) return false;
		return in_array( $handle, $this->opt_mods[$type]['footer'] );
	}

	public function is_disabled( $type, $handle ) {
		if ( ! isset( $this->opt_mods[ $type ]['disabled'] )) return false;
		return in_array( $handle, $this->opt_mods[$type]['disabled'] );
	}

	public function get( $type, $field ) {
		WPSSM_Debug::log( ' In WPSSM_Assets get() ');
		//if ( ! isset( $this->opt_mods[ $type ]['footer'] ) return false;
		if ( !isset( $this->opt_mods[$type][$field] ) ) {
			//WPSSM_Debug::log( ' In WPSSM_Assets get() unset element ' . $type . ' ' . $field);
			return array();
		}
		//WPSSM_Debug::log( ' In WPSSM_Assets get() found content for ' . $type . ' ' . $field, $this->opt_mods[$type][$field]);
		return $this->opt_mods[$type][$field];
	}

}

