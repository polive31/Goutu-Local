<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Assets {

	/* Options attributes */	
	private $opt_mods = array(
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
						

	protected function hydrate_opt( &$attribute, $option ) {
		$get_option = get_option( $option );
		if ( $get_option!=false ) {
			if ( is_array($get_option) )
				foreach ($get_option as $key=>$value) {$attribute[$key]=$value;}
			else
				$attribute = $get_option;
		}
		WPSSM_Debug::log('In WPSSM hydrate_opt option=' . $option, $attribute);
	}
						
	public function __construct() {
		//WPSSM_Debug::log('In WPSSM_Assets __construct()');
		//$this->assets_hydrate();
		$this->hydrate_opt( $this->opt_mods, 'wpssm_mods');
	}
	


		

}

