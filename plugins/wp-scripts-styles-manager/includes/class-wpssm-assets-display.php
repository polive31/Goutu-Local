<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Assets_Display extends WPSSM_Assets {

	/* Assets attributes */
	private $displayed=array();			
	
	/* Filter & sort array functions arguments */
	private $type;
	private $groupby;
	private $display_fields = array( 'handle', 'filename', 'version', 'dependencies', 'dependents', 'location', 'minify', 'size', 'group', 'priority');
	private $filter_args = array( 'location' => 'header' );			
	private $sort_args = array( 'field' => 'priority', 'order' => SORT_DESC, 'type' => SORT_NUMERIC);
						
	public function __construct( $args ) {
		//WPSSM_Debug::log('In WPSSM_Assets_Display before __construct() ');
		parent::__construct();
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



}

