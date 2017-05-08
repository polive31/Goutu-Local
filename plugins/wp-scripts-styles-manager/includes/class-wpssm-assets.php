<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Assets {

	/* Assets attributes */
	private $opt_enqueued_assets = array( 
									'pages'=>array(), 
									'scripts'=>array(), 
									'styles'=>array());	

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
						
						
	public function __construct() {
		//WPSSM_Debug::log('In WPSSM_Assets __construct()');
		$this->assets_hydrate();
	}
	
	protected function assets_hydrate() {
		//WPSSM_Debug::log('In WPSSM_Assets assets_hydrate() ');
		$this->hydrate_opt( $this->opt_enqueued_assets, 'wpssm_enqueued_assets');
		$this->hydrate_opt( $this->opt_mods, 'wpssm_mods');
		//WPSSM_Debug::log('In WPSSM_Assets hydrate() opt_enqueued_assets ', $this->opt_enqueued_assets);
		//WPSSM_Debug::log('In WPSSM_Assets hydrate() opt_mods ', $this->opt_mods);
	}
	
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

	
/* GETTER FUNCTIONS	
--------------------------------------------------------------------------*/	
  public function get_assets( $type ) {
		//WPSSM_Debug::log('In WPSSM_Assets get_assets() type=' . $type . ' - opt_enqueued_assets ', $this->opt_enqueued_assets[$type] );
		if ( !isset( $this->opt_enqueued_assets[$type] ) ) return '';
		return $this->opt_enqueued_assets[$type];
  }
  
  public function get_asset( $type, $handle ) {
		//WPSSM_Debug::log('In WPSSM_Assets get_asset() type=' . $type . ' - opt_enqueued_assets ', $this->opt_enqueued_assets[$type][$handle] );
		if ( !isset( $this->opt_enqueued_assets[$type][$handle] ) ) return '' ;
		return $this->opt_enqueued_assets[$type][$handle];
  }
	
	public function get_field_name( $type, $handle, $field ) {
		return  $type . '_' . $handle . '_' . $field;
	}
	
	public function get_field_value( $type, $handle, $field ) {
		//WPSSM_Debug::log('In Field Value for ' . $field);
		//WPSSM_Debug::log(array('Asset : ' => $asset));
		if ( !isset( $this->opt_enqueued_assets[$type][$handle] ) ) return false ;
		$asset = $this->opt_enqueued_assets[$type][$handle];
		if ( isset( $asset['mods'][ $field ] ) ) 
			return $asset['mods'][ $field ];
		elseif ( isset( $asset[ $field ] )) 
			return $asset[ $field ];
		else
			return false;
	}
	
	public function is_modified( $asset, $field ) {
		if ( isset( $asset['mods'][ $field ] ) ) return 'modified';
	}


/* SETTING FUNCTIONS
-----------------------------------------------------------*/
	public function set_field_value( $type, $handle, $field, $value ) {
		$this->opt_enqueued_assets[$type][$handle][$field]=$value;
	}
	
	public function mod_field_value( $type, $handle, $field, $value ) {
		$this->opt_enqueued_assets[$type][$handle]['mods'][$field]=$value;
	}
	
	

/* RECORDING FUNCTIONS
-----------------------------------------------------------*/

	public function record( $in_footer ) {
		WPSSM_Debug::log('In record enqueued assets');
		global $wp_scripts;
		global $wp_styles;

		/* Select data source depending whether in header or footer */
		if ($in_footer) {
			//WPSSM_Debug::log('FOOTER record');
			//WPSSM_Debug::log(array( '$header_scripts' => $this->header_scripts ));
			$scripts=array_diff( $wp_scripts->done, $this->header_scripts );
			$styles=array_diff( $wp_styles->done, $this->header_styles );
			//WPSSM_Debug::log(array('$source'=>$source));
		}
		else {
			$this->opt_enqueued_assets['pages'][get_permalink()] = array(get_permalink(), current_time( 'mysql' ));
			$scripts=$wp_scripts->done;
			$styles=$wp_styles->done;
			$this->header_scripts = $scripts;
			$this->header_styles = $styles;
			//WPSSM_Debug::log('HEADER record');
			//WPSSM_Debug::log(array('$source'=>$source));
		}
	  //WPSSM_Debug::log(array('assets before update' => $this->opt_enqueued_assets));
				
		$assets = array(
			'scripts'=>array(
					'handles'=>$scripts,
					'registered'=> $wp_scripts->registered),
			'styles'=>array(
					'handles'=>$styles,
					'registered'=> $wp_styles->registered),
			);
				
		WPSSM_Debug::log( array( '$assets' => $assets ) );		
			
		foreach( $assets as $type=>$asset ) {
			WPSSM_Debug::log( $type . ' recording');		
					
			foreach( $asset['handles'] as $index => $handle ) {
				$obj = $asset['registered'][$handle];
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
				$this->opt_enqueued_assets[$type][$handle] = array(
					'handle' => $handle,
					'enqueue_index' => $index,
					'filename' => $path,
					'location' => $in_footer?'footer':'header',
					'dependencies' => $obj->deps,
					'dependents' => array(),
					'minify' => (strpos( $obj->src, '.min.' ) != false )?'yes':'no',
					'size' => $size,
					'version' => $version,
				);
				// Update current asset priority
				$priority = $this->assets->update_priority( $type, $handle );
				// Update all dependancies assets properties
				foreach ($obj->deps as $dep_handle) {
					//WPSSM_Debug::log(array('dependencies loop : '=>$dep_handle));
					$this->opt_enqueued_assets[$type][$dep_handle]['dependents'][]=$handle;
				}
			}
		}
	  WPSSM_Debug::log(array('assets after update' => $this->opt_enqueued_assets));
	  if ( $in_footer )	hydrate_option( 'wpssm_enqueued_assets', $this->opt_enqueued_assets, true );
	}
		

}

