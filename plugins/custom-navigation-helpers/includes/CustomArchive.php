<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomArchive {
	
	protected static $vowels = array('a','e','i','o','u');
	protected $fcat;
	protected $fterm;
	protected $orderby;
	
	public function __construct() {
		//add_action( 'pre_get_posts', array($this,'archive_filter_sort'));		
		add_filter( 'query_vars', array($this,'add_filter_queryvars') );		
		add_action( 'wp_loaded', array($this,'hydrate'));		
	}
	
	/* Output debug information 
	--------------------------------------------------------------*/	
	protected function dbg( $msg, $var ) {
			if ( class_exists('PC') ) {
				PC::debug(array( $msg => $var ) );
			}
	}
	
	/* Custom query variable for taxonomy filter
	--------------------------------------------- */		
	function add_filter_queryvars($vars) {
	  $vars[] = 'fcat';
	  $vars[] .= 'fterm';
	  $vars[] .= 'ocat';
	  $vars[] .= 'oterm';
	  return $vars;
	}	

	/* Class attributes initialization
	--------------------------------------------- */		
	function hydrate() {
		$this->orderby = get_query_var('orderby','ASC');
		$this->fcat = get_query_var('fcat');
		$this->fterm = get_query_var('fterm');
	}			
	
	public function archive_filter_sort($query) {
	  // Select any archive. For custom post type use: is_post_type_archive( $post_type )
	  //if (is_archive() || is_search() ): => ne pas utiliser car résultats de recherche non relevants
	  if ( !is_archive() ) return;
			 
		// Filter entries based on custom args
//		$tax= get_query_var('filter');
//		$term= get_query_var('term');
//
//		if ( !empty($tax) && !empty($term) ) {				
//			echo 'TAX' . $tax;
//			echo 'TAX TERM' . $term;
//			$tax_query = array(
//				array(
//					'taxonomy' => $tax,
//					'field'    => 'slug',
//					'terms'    => $term,
//				),
//			);
//			//$query->set( 'tax_query', $tax_query );
//		}		
	
		// Change the archive post orderby and sort order from slug

		//echo 'ORDERBY' . $orderby;

		if ($this->orderby=='rating') {
			PHP_Debug::trace('RATING !!!!');
			$orderby = 'meta_value_num';
			$meta_key = 'user_rating_global';
			$order = 'DESC';
		}
		else {	
			$meta_key='';
			$order=get_query_var('order','ASC');
		}
		
		$query->set( 'orderby', $orderby );
		$query->set( 'meta_key', $meta_key );
		$query->set( 'order', $order );

	}
	
	protected function initial_is_vowel($word) {
		$first = strtolower($word[0]);
		return in_array($first,self::$vowels);
	}
	
	protected function is_plural($word) {
		$last = strtolower($word[strlen($word)-1]);
		return ($last=='s');	
	}	
	
	protected function get_cuisine_caption($origin) {
		//PC::debug(array('origin'=>$origin));
//		if ($this->is_plural($origin))
//			$msg = _x('Cuisine from %s','plural','foodiepro');
//		elseif ($this->initial_is_vowel($origin))
//			$msg = _x('Cuisine from %s','vowel','foodiepro');
//		else
//			$msg = _x('Cuisine from %s','consonant','foodiepro');
//		$msg = sprintf($msg,$origin);

		// Simplified to $origin, since caption length exceeds button...
		//... size with "cuisine from" + some countries
		return $origin; 
	}
	



}
