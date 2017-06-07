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

  /* Class arguments */
	private $sizes;

	public function __construct( $args ) {
		PHP_Debug::trace('*** __construct ***' );		
  	$this->hydrate_args( $args );	
		$opt_proto = array( 
									'pages'=>array(), 
									'scripts'=>array(), 
									'styles'=>array());	
		parent::__construct( self::OPT_KEY, $opt_proto );
		PHP_Debug::trace('In WPSSM_Options_Assets __construct() $this->get() ', $this->get() );
	}
		
	
/* GETTER FUNCTIONS	
--------------------------------------------------------------------------*/	
	
	/* 	Retrieves the most up-to-date value of a field within an asset
			Whether the original value or the one after modification          
	---------------------------------------------------------------------*/
	public function get_field( $type, $handle, $field) {
		PHP_Debug::trace('In WPSSM_Options_Assets get_value() ' . $type . ' ' . $handle . ' ' . $field);
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
		PHP_Debug::trace('In WPSSM_Options_Assets get() after ', $get);
		return $value;
	}
	
	public function get_mod( $type, $handle, $field) {
		$value = false;
		$asset = $this->get( $type, $handle );
		if ( $asset != false ) {
			if ( isset( $asset[$field]['mods'] )) {
				$value = $asset[$field]['mods'];
			}
		}
		return $value;
	}	

	public function is_mod( $type, $handle, $field ) {
		$asset = parent::get( $type, $handle );
		return ( isset( $asset[ 'mods' ][ $field ] ) );
	}		
	

/* SETTING FUNCTIONS
-----------------------------------------------------------*/

/* Asset 
-----------------------------------------------------------*/

	public function store_page( $value , $handle ) {
		$this->set( $value, 'pages', $handle);
	}

	public function store( $type, $handle, $obj, $location ) {
		$path = strtok($obj->src, '?'); // remove any query parameters
		if ( strpos( $path, 'wp-' ) != false) {
			$path = wp_make_link_relative( $path );
			$uri = $_SERVER['DOCUMENT_ROOT'] . $path;
			$size = filesize( $uri );
			$version = $obj->ver;
		}
		else {
			$path = $obj->src;
			$version = $obj->ver;
			$size = 0;
		}
		// Update current asset properties
		$args = array(
			'handle' => $handle,
			'enqueue_index' => $index,
			'filename' => $path,
			'location' => $location,
			'dependencies' => $obj->deps,
			'dependents' => array(),
			'minify' => (strpos( $obj->src, '.min.' ) != false )?'yes':'no',
			'size' => $size,
			'version' => $version,
		);						
		parent::set($args, $type, $handle);
		$this->update_priority( $type, $handle);
		$this->update_dependants( $type, $handle);
	}

	public function update_from_post( $type, $handle, $field ) {
		$is_mod=false;
		$val='';
		$input = $this->get_input_name($type, $handle, $field);
		if ( ( isset($_POST[ $input ] ) ) && ( $_POST[ $input ] != $this->get($type,$handle,$field) ) ) {
			PHP_Debug::trace( 'Asset field modified (mods) !' , $this->get($type,$handle) );
			//PHP_Debug::trace( 'input name', $input );
			//PHP_Debug::trace( 'POST content for this field',$_POST[ $input ] );
			$val = esc_html($_POST[ $input ]);
			$this->set_mod_field($type,$handle,$field,$val);
			$is_mod=true;
		}
		else {
			$this->unset_mod( $type, $handle, $field );
			PHP_Debug::trace( 'Mod Field removed !' , $this->get($type,$handle) );
		}
		return array('modified' => $is_mod, 'value' =>$val);
	}
	
	
/* Asset field
-----------------------------------------------------------*/
	public function set_priority( $type, $handle, $value ) {
		$this->set( $value, $type, $handle, 'priority' );
	}
	
	public function add_field_value( $type, $handle, $field, $value ) {
		$this->add( $value, $type, $handle, $field );
	}
	
/* Asset Mod
-----------------------------------------------------------*/
	public function set_mod_field( $type, $handle, $field, $value ) {
		$this->set( $value, $type, $handle, 'mods', $field );
	}

	public function unset_mod( $type, $handle=false, $field=false ) {
		if ( $handle == false ) {
			foreach ( $this->get($type) as $handle=>$asset ) {
				$this->unset_mod( $type, $handle); 
			}
		}	
		elseif ( $field == false )
			if ( isset( $this->opt_enqueued_assets[$type][$handle]['mods'] ) )
				unset($this->opt_enqueued_assets[$type][$handle]['mods']); 
		else
			if ( isset( $this->opt_enqueued_assets[$type][$handle]['mods'][$field] ) )
				unset($this->opt_enqueued_assets[$type][$handle]['mods'][$field]); 
		$this->update_priority( $type, $handle ); 
	}		
	

/* ASSET FIELDS UPDATE FUNCTIONS
-----------------------------------------------------------*/	

	public function update_priority( $type, $handle ) {
		PHP_Debug::trace1('update_priority $this->sizes',$this->sizes);
		$location = $this->get_field( $type, $handle, 'location');
		if ( $location != 'disabled' ) {
			$minify = $this->get_field( $type, $handle, 'minify');
			$size = $this->get_field( $type, $handle, 'size');
			$score = ( $location == 'header' )?1000:0;
			PHP_Debug::trace1('base after location',$score);
			$score += ( $size >= $this->sizes['large'] )?500:0; 	
			$score += ( ($minify == 'no') && ( $size != 0 ))?200:0;
			PHP_Debug::trace1('base after minify',$score);
			$score += ( $size <= $this->sizes['small'] )?100:0; 	
			PHP_Debug::trace1('base after size',$score);
			if ( $size >= $this->sizes['large'] ) 
				$normalizer = $this->sizes['max'];
			elseif ( $size <= $this->sizes['small'] )
				$normalizer = $this->sizes['small'];
			else 
				$normalizer = $this->sizes['large'];
			PHP_Debug::trace1('normalizer', $normalizer);
			$score += $size/$normalizer*100; 	
			PHP_Debug::trace1('score',$score);
		}
		else 
			$score = 0;
		$this->set_priority( $type, $handle, $score);
	}		
	
	// Update all assets 'dependants' property, based on this asset's dependencies
	public function update_dependants( $type, $handle ) {
		$dependencies = $this->get_field( $type, $handle, 'dependencies');
		foreach ($dependencies as $dep_handle) {
			//PHP_Debug::trace(array('dependencies loop : '=>$dep_handle));
			$this->add( $handle, $type, $dep_handle, 'dependents' );
		}	
	}
	


}

