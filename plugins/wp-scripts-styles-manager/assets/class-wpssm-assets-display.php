<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Assets_Display extends WPSSM_Options_Assets {	

	use Utilities;	

	/* Assets attributes */
	private $displayed = array();			

	/* Class arguments */
	private $type;
	private $groupby;
	private $sizes;
	
	/* Filter & sort array functions arguments */
	private $display_fields = array( 'handle', 'filename', 'version', 'dependencies', 'dependents', 'location', 'minify', 'size', 'group', 'priority');
	private $filter_args = array( 'location' => 'header' );			
	private $sort_args = array( 'field' => 'priority', 'order' => SORT_DESC, 'type' => SORT_NUMERIC);
						
	public function __construct( $args ) {	
		WPSSM_Debug::log('*** In WPSSM_Options_Assets __construct ***' );				
		parent::__construct( $args );
  	$this->hydrate_args( $args );
		$this->displayed_hydrate();
	}
	
	public function displayed_hydrate() {
		WPSSM_Debug::log('In WPSSM_Assets_Display hydrate() $this->type: ', $this->type);
		if ($this->type!='general') {
			WPSSM_Debug::log('In WPSSM_Assets_Display hydrate() $this->get_assets for type ' . $this->type, $this->get( $this->type ));
			foreach ($this->get( $this->type ) as $handle=>$asset) {		
				$group_id = $this->get_value( $this->type, $handle, $this->groupby);
				//WPSSM_Debug::log('In WPSSM_Assets_Display display_hydrate() $group_id ', $group_id);
				if ( isset($this->displayed[$group_id]) ) 
					$group=$this->displayed[$group_id];
				else 
					$group = array();
				$group['assets'][$handle] = $this->get_modified_asset( $handle );	
				if (isset($group['size'])) $group['size'] += $this->get_value( $this->type, $handle, 'size');	
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

  public function get_displayed( $group_id, $handle=false ) {
		if ( !isset( $this->displayed[ $group_id ] ) ) return false;
		if ( $handle==false ) return $this->displayed[ $group_id ]['assets'];
		if ( !isset( $this->displayed[ $group_id ]['assets'][ $handle ] )) return array();
		return $this->displayed[ $group_id ]['assets'][ $handle ];
  }
  
  public function get_modified_asset( $handle ) {
  	/* Generate an asset with modified fields replacing original ones */
		$modasset = array();
		foreach ($this->display_fields as $field) {
			$value=$this->get_value( $this->type, $handle, $field);
			$modasset[$field]=$value;
		}
		//WPSSM_Debug::log( 'In WPSSM_Assets_Display get_modified_asset()', $modasset);
		return $modasset;
  }

	public function get_sort_list( $group_id ) {
		$sort_field = $this->sort_args['field'];
		$sort_order = $this->sort_args['order'];
		$sort_type = $this->sort_args['type'];
		$assets = $this->get_displayed( $group_id );
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


}

