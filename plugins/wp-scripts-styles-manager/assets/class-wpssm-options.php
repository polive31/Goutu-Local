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
		//PHP_Debug::log('In WPSSM_Options $get_option', $get_option);
		if ( $get_option!=false ) {
			//PHP_Debug::log('In WPSSM hydrate_opt get_option', $get_option);
			if ( is_array($get_option) ) {
				foreach ($get_option as $key=>$value) {
					//PHP_Debug::log('In WPSSM hydrate_opt array loop key = ' . $key . ' value ', $value );
					if ( is_array($value) )
						foreach ($value as $key1=>$value1) {$asset[$key][$key1]=$value1;}
					else 
						$asset[$key]=$value;
				}
			}
			else
				$asset = $get_option;
		}
		PHP_Debug::trace('In WPSSM_Options get_opt option=' . $id . ' $get_option = ', $get_option );
		PHP_Debug::trace('In WPSSM_Options get_opt option=' . $id . ' $asset = ', $asset );
		return $asset;
	}
	
	
	public function update_opt() {
		update_option( $this->option, $this->asset );
	}	
		
		
	/* ASSETS MANAGEMENT */	
	public function reset( $field=false ) {
		if ( $field == false) {
			$this->asset = $this->prototype;
		}
		else {
			$this->asset[$field] = $this->prototype[$field];			
		}
		$this->update_opt();
	}
	
	public function get( $field=false, $subfield=false ) {
		//PHP_Debug::log( ' In WPSSM_Assets get() ' );
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
		//PHP_Debug::log( ' In WPSSM_Assets get() ' . $field . ' ' . $subfield, $get);
		return $get;
	}
	
	public function set( $value, $field1=false, $field2=false, $field3=false, $field4=false ) {
		PHP_Debug::log( '*** In ' . get_class($this) . ' set() ***' );
		$set = false;
		if ( $field1==false) {
			$this->asset = $value;
			$set = true;			
		}
		elseif ($field2==false) {
			$this->asset[$field1] = $value;
			$set = true;			
		}
		elseif ($field3==false) {
			if ( isset( $this->asset[$field1] ) ) {
				$this->asset[$field1][$field2] = $value;
				$set = true;			
			}	
		}
		elseif ($field4==false) {
			if ( isset( $this->asset[$field1][$field2] ) ) {
				$this->asset[$field1][$field2][$field3] = $value;
				$set = true;			
			}	
		}		
		elseif ( isset( $this->asset[$field1][$field2][$field3] ) ) {
			$this->asset[$field1][$field2][$field3][$field4] = $value; 
			$set = true;			
		}		
		PHP_Debug::log( ' Set result $this->asset ', $this->asset);
		return $set;
	}	
	
	public function add( $value, $field1, $field2=false, $field3=false ) {
		if ($field2==false) 
			$this->asset[$field1][] = $value;
		elseif ($field3==false)
			$this->asset[$field1][$field2][] = $value;
		else	
			$this->asset[$field1][$field2][$field3][] = $value;
	}
	

}

