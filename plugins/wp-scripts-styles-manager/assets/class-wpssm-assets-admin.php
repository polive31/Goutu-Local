<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Assets_Admin extends WPSSM_Assets {	

	/* Options attributes */	
	private $opt_enqueued_assets = array( 
									'pages'=>array(), 
									'scripts'=>array(), 
									'styles'=>array());	

	/* Assets attributes */
	private $displayed=array();			
	

  /* Recording attributes */
	protected $header_scripts;
	protected $header_styles;

	/* Filter & sort array functions arguments */
	private $type;
	private $groupby;
	private $display_fields = array( 'handle', 'filename', 'version', 'dependencies', 'dependents', 'location', 'minify', 'size', 'group', 'priority');
	private $filter_args = array( 'location' => 'header' );			
	private $sort_args = array( 'field' => 'priority', 'order' => SORT_DESC, 'type' => SORT_NUMERIC);
						
	public function __construct( $args ) {
		//WPSSM_Debug::log('In WPSSM_Assets_Display before __construct() ');
		parent::__construct();
		$this->hydrate_opt( $this->opt_enqueued_assets, 'wpssm_enqueued_assets');
		
		//WPSSM_Debug::log('In WPSSM_Assets_Display after __construct() ');
  	foreach ($args as $key=>$value) {
  		$this->$key = $value;
  	}
		//WPSSM_Debug::log('In WPSSM_Assets_Display __construct() $this->type: ', $this->type);
		//WPSSM_Debug::log('In WPSSM_Assets_Display __construct() $this->groupby: ', $this->groupby);
		$this->displayed_hydrate();
	}
	
	public function displayed_hydrate() {
		WPSSM_Debug::log('In WPSSM_Assets_Display hydrate() $this->type: ', $this->type);
		if ($this->type!='general') {
			//WPSSM_Debug::log('In WPSSM_Assets_Display hydrate() $this->get_assets for type ' . $this->type, $this->get_assets( $this->type ));
			foreach ($this->get_assets( $this->type ) as $handle=>$asset) {		
				$group_id = $this->get_field_value( $this->type, $handle, $this->groupby);
				//WPSSM_Debug::log('In WPSSM_Assets_Display display_hydrate() $group_id ', $group_id);
				if ( isset($this->displayed[$group_id]) ) 
					$group=$this->displayed[$group_id];
				else 
					$group = array();
				$group['assets'][$handle] = $this->get_modified_asset( $handle );	
				if (isset($group['size'])) $group['size'] += $this->get_field_value( $this->type, $handle, 'size');	
				else $group['size']=0;
				if (isset($group['count'])) $group['count']++;	
				else $group['count']=1;	
				$this->displayed[$group_id]=$group;	
				WPSSM_Debug::log('In WPSSM_Assets_Display display_hydrate() loop on ' . $handle . ', group_id=' . $group_id, $this->displayed);
			}
		}	
		WPSSM_Debug::log('In WPSSM_Assets_Display hydrate() $this->displayed: ', $this->displayed);
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
	
	public function add_mod( $type, $handle, $field, $value ) {
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

	public function reset_assets( $type ) {
		foreach ( $this->opt_enqueued_assets[$type] as $handle=>$asset ) {
			unset( $this->opt_enqueued_assets[$type][$handle]['mods'] ); 
			$this->update_priority( $type, $handle ); 
		}
	}	

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

	
	
/* WPSSM_ASSETS ADMIN FUNCTIONS	
	
/* GETTER FUNCTIONS	
--------------------------------------------------------------------------*/	

  public function get_display_attr( $attr ) {
		if ( !isset($this->$attr) ) return false;
		return $this->$attr;
	}
	
	public function get_group_stat( $group, $stat ) {
		if ( !isset( $this->displayed[$group][$stat] ) ) return false;
		return $this->displayed[$group][$stat];
	}

  public function get_displayed_assets( $group_id ) {
		if ( !isset( $this->displayed[ $group_id ] ) ) return array();
		return $this->displayed[ $group_id ]['assets'];
  }

  public function get_displayed_asset( $group_id, $handle ) {
		if ( !isset( $this->displayed[ $group_id ]['assets'][ $handle ] )) return array();
		return $this->displayed[ $group_id ]['assets'][ $handle ];
  }
  
  public function get_modified_asset( $handle ) {
  	/* Generate an asset with modified fields replacing original ones */
		$modasset = array();
		foreach ($this->display_fields as $field) {
			$value=$this->get_field_value( $this->type, $handle, $field);
			$modasset[$field]=$value;
		}
		//WPSSM_Debug::log( 'In WPSSM_Assets_Display get_modified_asset()', $modasset);
		return $modasset;
  }

	public function get_sort_list( $group_id ) {
		$sort_field = $this->sort_args['field'];
		$sort_order = $this->sort_args['order'];
		$sort_type = $this->sort_args['type'];
		$assets = $this->get_displayed_assets( $group_id );
		WPSSM_Debug::log( 'In WPSSM_Assets_Display get_sort_list() $assets for ' . $group_id, $assets);
		$list = array_column($assets, $sort_field, 'handle' );		
		WPSSM_Debug::log( 'In WPSSM_Assets_Display get_sort_list() $list before sorting', $list);
		if ( $sort_order == SORT_ASC)
			asort($list, $sort_type );
		else 
			arsort($list, $sort_type );
//		foreach ($sort_column as $key => $value) {
//			echo '<p>' . $key . ' : ' . $value . '<p>';
//		}
		WPSSM_Debug::log( 'In WPSSM_Assets_Display get_sort_list() $list after sorting', $list);
		return $list;
	}		
	
/* SETTER FUNCTIONS 
--------------------------------------------------------------------------*/	
//
//	public function filter($type, $filter_args) {
//		WPSSM_Debug::log('In WPSSM_Assets filter() : ' . $type );
//		if (! isset( $this->opt_enqueued_assets[$type] ) ) return false;
//		if (! is_array( $filter_args ) ) return false;
//		$this->filter_args = $filter_args;
//		WPSSM_Debug::log( 'In WPSSM_Assets filter_args() : ' . $this->filter_args );
//		$filtered_assets['assets'] = array_filter($this->opt_enqueued_assets[$type], array($this, 'filter_cb') );	
//		$filtered_assets['count']=count($filtered_assets['assets']);
//		$filtered_assets['size']=array_sum( array_column( $filtered_assets['assets'],'size'));
//		WPSSM_Debug::log('In WPSSM_Assets filter() return value ', $filtered_assets );
//		return $filtered_assets;
//	}
//
//	public function filter_cb( $asset ) {
//		$match=true;
//		foreach ($this->filter_args as $field=>$value) {
//			//WPSSM_Debug::log('In filter assets filter args loop', array($field=>$value));
//			$match=($this->get_field_value($asset,$field)==$value)?$match:false;
//		}
//		return $match;
//	}

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

