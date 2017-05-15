<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Options_General extends WPSSM_Options {
	
	const OPT_KEY = 'wpssm_mods';
	
	/* Args attributes */
	private $plugin_version;
	
	public function __construct( $args ) {
		
		foreach ($args as $key=>$value) {$this->$key = $value;}		
		$opt_proto = array(
									'record'=>'off', 
									'optimize'=>'off', 
									'javasync'=>'off', 
									'wpssm_version'=>$this->plugin_version);	
						
		parent::__construct( self::OPT_KEY, $opt_proto );
		WPSSM_Debug::log('In WPSSM_Assets __construct() $this->get()', $this->get() );
		WPSSM_Debug::log('In WPSSM_Assets __construct() $this->get("record")', $this->get('record') );
	}


}

