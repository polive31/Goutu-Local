<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Assets_Display extends WPSSM_Assets {

	/* Assets attributes */
	private $displayed;			
	
	/* Filter & sort array functions arguments */
	private $type;
	private $groupby;
	private $filter_args = array( 'location' => 'header' );			
	private $sort_args = array( 
														'field' => 'priority', 
														'order' => SORT_DESC, 
														'type' => SORT_NUMERIC);
	
						
	public function __construct( $type, $groupby) {
		WPSSM_Debug::log('In WPSSM_Assets_Display before __construct() ');
		parent::__construct();
		WPSSM_Debug::log('In WPSSM_Assets_Display after __construct() ');
		$this->type = $type;
		$this->groupby = $groupby;
		WPSSM_Debug::log('In WPSSM_Assets_Display __construct() $this->type: ', $this->type);
		WPSSM_Debug::log('In WPSSM_Assets_Display __construct() $this->groupby: ', $this->groupby);
		$this->displayed_hydrate();
	}
	
	public function displayed_hydrate() {
		WPSSM_Debug::log('In WPSSM_Assets_Display hydrate() $this->type: ', $this->type);
		if ($this->type=='general') {
			$this->displayed = $this->get('pages');
		}
		else {
			foreach ($this->get( $this->type ) as $handle=>$asset) {		
				WPSSM_Debug::log('In WPSSM_Assets_Display display_hydrate() loop on ' . $handle, $asset);
				//WPSSM_Debug::log('In WPSSM_Assets_Display display_hydrate() loop $this->get($this->type)', $this->get($this->type));
				$value = $this->get_field_value( $asset, $this->groupby);
				WPSSM_Debug::log('In WPSSM_Assets_Display display_hydrate() $value', $value);
				if (isset($this->displayed[$value])) $group=$this->displayed[$value];
				$group['assets'][$handle] = $asset;	
				if (isset($group['size'])) $group['size'] += (isset($asset['size']))?$asset['size']:0;	
				else $group['size']=0;
				if (isset($group['count'])) $group['count']++;	
				else $group['count']=0;	
				$this->displayed[$value]=$group;	
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

  public function get_displayed_assets( $field_value=false, $handle=false ) {
  	if ( $field_value==false ) {
			return $this->displayed;
  	}
  	else {
			if (! isset( $this->displayed[ $field_value ] ) ) return '';
			if ($handle==false) return $this->displayed[ $field_value ];
			elseif ( !isset( $this->displayed[ $field_value ]['assets'][ $handle ] )) return '';
			return $this->displayed[ $field_value ]['assets'][ $handle ];
  	}
  }

	public function sort( $field ) {
		$sort_field = $this->sort_args['field'];
		$sort_order = $this->sort_args['order'];
		$sort_type = $this->sort_args['type'];
		$assets = $this->get_displayed_assets( $field )['assets'];
		$list = array_column($assets, $sort_field, 'handle' );		
		//WPSSM_Debug::log( array( 'sorted list : '=>$list ));
		if ( $sort_order == SORT_ASC)
			asort($list, $sort_type );
		else 
			arsort($list, $sort_type );
//		foreach ($sort_column as $key => $value) {
//			echo '<p>' . $key . ' : ' . $value . '<p>';
//		}
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

	public function update_priority( $type, $handle ) {
		$asset = $this->opt_enqueued_assets[$type][$handle];
		$location = $this->get_field_value( $asset, 'location');
		
		if ( $location != 'disabled' ) {
			$minify = $this->get_field_value( $asset, 'minify');
			$size = $this->get_field_value( $asset, 'size');
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

		$this->opt_enqueued_assets[$type][$handle]['priority'] = $score;
	}		

}

