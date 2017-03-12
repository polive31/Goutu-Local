<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomArchive {
	
	protected static $vowels = array('a','e','i','o','u');
	
	public function __construct() {

		add_action( 'pre_get_posts', array($this,'archive_change_sort_order'));		
	}
	
	/* Output debug information 
	--------------------------------------------------------------*/	
	protected function dbg( $msg, $var ) {
			if ( class_exists('PC') ) {
				PC::debug(array( $msg => $var ) );
			}
	}
		
		
	//* Change the archive post orderby and sort order from slug
	public function archive_change_sort_order($query) {
	  // Select any archive. For custom post type use: is_post_type_archive( $post_type )
	  //if (is_archive() || is_search() ): => ne pas utiliser car résultats de recherche non relevants
	  if (is_archive() ):
	     $orderby= get_query_var('orderby','title');
	     if ($orderby=='rating'):
	     	$orderby = 'meta_value_num';
	     	$meta_key = "user_rating_global";
	     	$order = 'DESC';
	     else:
	     	$meta_key='';
	     	$order=get_query_var('order','ASC');
	     endif;

	     $query->set( 'orderby', $orderby );
	     $query->set( 'meta_key', $meta_key );
	     $query->set( 'order', $order );
	  endif;
	}
	
	protected function initial_is_vowel($word) {
		$first = strtolower($word[0]);
		return in_array($first,self::$vowels);
	}
	
	protected function get_cuisine_caption($origin) {
		//PC::debug(array('origin'=>$origin));
		if ($this->initial_is_vowel($origin))
			$msg = _x('Cuisine from ','vowel','foodiepro') . $origin;
		else
			$msg = _x('Cuisine from ','consonant','foodiepro') . $origin;
		//$msg = sprintf(_x('Cuisine from %s',$context,'foodiepro'),$origin);
		return $msg;
	}
	



}
