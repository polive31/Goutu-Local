<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Asset structure array(
											'handle' 
											'enqueue_index' 
											'filename' 
											'location' 
											'minify' 
											'dependents' 
											'dependencies' 
											'size' 
											'version' 
											'priority' )
-----------------------------------------------------------*/
class WPSSM_Options_Assets extends WPSSM_Options {	

	use Utilities;	

	const OPT_KEY = 'wpssm_enqueued_assets';

  /* Recording attributes */
	protected $header_scripts;
	protected $header_styles;

	public function __construct( $args ) {
		WPSSM_Debug::log('*** In WPSSM_Options_Assets __construct ***' );		
  	$this->hydrate_args( $args );	
		$opt_proto = array( 
									'pages'=>array(), 
									'scripts'=>array(), 
									'styles'=>array());	
		parent::__construct( self::OPT_KEY, $opt_proto );
		WPSSM_Debug::log('In WPSSM_Options_Assets __construct() $this->get() ', $this->get() );
	}
	
	
/* GETTER FUNCTIONS	
--------------------------------------------------------------------------*/	

	
	/* 	Retrieves the most up-to-date value of a field within an asset
			Whether the original value or the one after modification          
	---------------------------------------------------------------------*/
	public function get_field( $type, $handle, $field) {
		WPSSM_Debug::log('In WPSSM_Options_Assets get_value() ' . $type . ' ' . $handle . ' ' . $field);
		$value = false;
		$asset = $this->get( $type, $handle );
//		if ( $get == false ) 
//			$get=array();
		if ( $asset != false ) {
			if ( isset( $asset[$field]['mods'] )) 
				$value = $asset[$field]['mods'];
			elseif ( isset( $asset[$field] )) 
				$value = $asset[$field];
		}
		WPSSM_Debug::log('In WPSSM_Options_Assets get() after ', $get);
		return $value;
	}


//	public function get_field_value( $type, $handle, $field ) {
//		//WPSSM_Debug::log('In Field Value for ' . $field);
//		//WPSSM_Debug::log(array('Asset : ' => $asset));
//		$value = false;
//		if ( !isset( $this->opt_enqueued_assets[$type][$handle] ) ) return false ;
//		$asset = $this->opt_enqueued_assets[$type][$handle];
//		
//		if ( isset( $asset['mods'][ $field ] ) ) return $asset['mods'][ $field ];
//		elseif ( isset( $asset[ $field ] )) 
//			return $asset[ $field ];
//		else
//			return false;
//	}
	
	public function is_mod( $type, $handle, $field ) {
		$asset = parent::get( $type, $handle );
		return ( isset( $asset[ 'mods' ][ $field ] ) );
	}		
	

/* SETTING FUNCTIONS
-----------------------------------------------------------*/

	public function add_asset( $value, $type, $handle ) {
		parent::set($value, $type, $handle);
		$this->update_priority( $type, $handle);
		$this->update_dependants( $type, $handle);
	}

	public function set_field( $type, $handle, $field, $value ) {
		$this->opt_enqueued_assets[$type][$handle][$field]=$value;
	}
	
	public function add_field_value( $type, $handle, $field, $value ) {
		$this->opt_enqueued_assets[$type][$handle][$field][]=$value;
	}
	
	public function set_mod( $type, $handle, $field, $value ) {
		$this->opt_enqueued_assets[$type][$handle]['mods'][$field]=$value;
	}
	
	public function remove_mod_field( $type, $handle, $field ) {
		if ( !isset($this->opt_enqueued_assets[$type][$handle]['mods'][$field]) ) return false; 
		unset($this->opt_enqueued_assets[$type][$handle]['mods'][$field]); 
		$this->assets->update_priority( $type, $handle ); 
	}		
	
	public function reset_asset( $type, $handle ) {
		unset($this->opt_enqueued_assets[$type][$handle]['mods']); 
		$this->assets->update_priority( $type, $handle ); 	
	}

	public function reset( $type ) {
		foreach ( $this->opt_enqueued_assets[$type] as $handle=>$asset ) {
			unset( $this->opt_enqueued_assets[$type][$handle]['mods'] ); 
			$this->update_priority( $type, $handle ); 
		}
	}	
	

/* ASSET FIELDS UPDATE FUNCTIONS
-----------------------------------------------------------*/	

	public function update_priority( $type, $handle ) {
		$location = $this->get_field_value( $type, $handle, 'location');
		
		if ( $location != 'disabled' ) {
			$minify = $this->get_field_value( $type, $handle, 'minify');
			$size = $this->get_field_value( $type, $handle, 'size');
			$score = ( $location == 'header' )?1000:0;
			//WPSSM_Debug::log(array('base after location'=>$score));
			$score += ( $size >= self::SIZE_LARGE )?500:0; 	
			$score += ( ($minify == 'no') && ( $size != 0 ))?200:0;
			//WPSSM_Debug::log(array('base after minify'=>$score));
			$score += ( $size <= self::SIZE_SMALL )?100:0; 	
			//WPSSM_Debug::log(array('base after size'=>$score));
			if ( $size >= self::SIZE_LARGE ) 
				$normalizer = self::SIZE_MAX;
			elseif ( $size <= self::SIZE_SMALL )
				$normalizer = self::SIZE_SMALL;
			else 
				$normalizer = self::SIZE_LARGE;
			//WPSSM_Debug::log(array('normalizer'=>$normalizer));
			$score += $size/$normalizer*100; 	
			//WPSSM_Debug::log(array('score'=>$score));
		}
		else 
			$score = 0;
		$this->set_field_value( $type, $handle, 'priority', $score);
	}		
	
	// Update all assets 'dependants' property, based on this asset's dependencies
	public function update_dependants( $type, $handle ) {
		$dependencies = $this->get_field( $type, $handle, 'dependencies');
		foreach ($dependencies as $dep_handle) {
			//WPSSM_Debug::log(array('dependencies loop : '=>$dep_handle));
			$this->add_field_value( $type, $dep_handle, 'dependents', $handle );
		}	
	}
	


}

