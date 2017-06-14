<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Options_General extends WPSSM_Options {
		
	use Utilities;	
	
	const OPT_KEY = 'wpssm_general_settings';

	/* Class local attributes */
	private $opt_proto;
	
	/* Args attributes */
	private $plugin_version;
	
	public function __construct( $args ) {
		//PHP_Debug::trace('*** In WPSSM_Options_General __construct ***' );
		$this->hydrate_args( $args );	
		$this->opt_proto = array(
									'record'=>'off', 
									'optimize'=>'off', 
									'javasync'=>'off', 
									'wpssm_version'=>$this->plugin_version);																			
		parent::__construct( self::OPT_KEY, $this->opt_proto );
		//PHP_Debug::trace('In WPSSM_Assets __construct() $this->get()', $this->get() );
		//PHP_Debug::trace('In WPSSM_Assets __construct() $this->get(record)', $this->get('record') );
	}
	
	/* ASSET UPDATE 
	---------------------------------------*/

	public function update_from_post() {
		//PHP_Debug::trace('In WPSSM_Options_General update_from_post' );
		foreach ($this->opt_proto as $setting=>$value) {
			$name = 'general_' . $setting . '_checkbox';
			if ( isset($_POST[ $name ]) )
				$value = $_POST[ $name ];
			else 
				$value='off';
			//PHP_Debug::trace('In WPSSM_Options_General update_from_post loop ' . $setting . ' = ' . $value  );
			$this->set( $value, $setting );
		}			
	}

}

