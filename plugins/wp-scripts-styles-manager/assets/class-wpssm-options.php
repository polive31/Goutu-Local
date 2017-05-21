<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class WPSSM_Options {

	/* Class local attributes */
		private $asset;
		private $option;
		private $prototype;

	public function __construct( $option, $prototype ) {
		$this->asset = $this->get_opt( $option, $prototype );
		$this->option = $option;
		$this->prototype = $prototype;
	}

	/* OPTIONS MANAGEMENT */
		
	// Hydrates the asset attribute with content of the WP option $id
	// Only populate the initial $asset prototype with $id option existing values 
	public function get_opt( $id, $asset ) {
		$get_option = get_option( $id );
		//WPSSM_Debug::log('In WPSSM_Options $get_option', $get_option);
		if ( $get_option!=false ) {
			//WPSSM_Debug::log('In WPSSM hydrate_opt get_option', $get_option);
			if ( is_array($get_option) ) {
				foreach ($get_option as $key=>$value) {
					//WPSSM_Debug::log('In WPSSM hydrate_opt array loop key = ' . $key . ' value ', $value );
					if ( is_array($value) )
						foreach ($value as $key1=>$value1) {$asset[$key][$key1]=$value1;}
					else 
						$asset[$key]=$value;
				}
			}
			else
				$asset = $get_option;
		}
		//WPSSM_Debug::log('In WPSSM_Options get_opt option=' . $id, $asset);
		return $asset;
	}
	
	
	public function update_opt() {
		update_option( $this->option, $this->asset );
	}	
		
		
	/* ASSETS MANAGEMENT */	
	public function delete() {
		$this->asset = $this->prototype;
		$this->update_opt();
	}
	
	public function get( $field=false, $subfield=false ) {
		//WPSSM_Debug::log( ' In WPSSM_Assets get() ' );
		$get = false;
		if ($field == false) {
			if ( isset( $this->asset ) ) $get=$this->asset;			
		}
		elseif ($subfield==false) {
			if ( isset( $this->asset[$field] ) ) $get=$this->asset[$field];
		}
		elseif ( isset( $this->asset[$field][$subfield] ) ) {
			$get=$this->asset[$field][$subfield]; 
		}
		//WPSSM_Debug::log( ' In WPSSM_Assets get() ' . $field . ' ' . $subfield, $get);
		return $get;
	}
	
	public function set( $value, $field=false, $subfield=false ) {
		WPSSM_Debug::log( '*** In ' . get_class($this) . ' set() ***' );
		$set = false;
		if ( $field == false) {
			$this->asset = $value;
			$set = true;			
		}
		elseif ($subfield==false) {
			$this->asset[$field] = $value;
			$set = true;			
		}
		elseif ( isset( $this->asset[$field][$subfield] ) ) {
			$this->asset[$field][$subfield] = $value; 
			$set = true;			
		}
		WPSSM_Debug::log( ' Set result $this->asset ', $this->asset);
		return $set;
	}	
	
	public function add( $value, $field, $subfield=false ) {
		if ($subfield == false) 
			$this->asset[$field][] = $value;
		else
			$this->asset[$field][$subfield][] = $value;
	}
	

}

