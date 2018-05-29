<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomArchive {
	
	protected static $vowels = array('a','e','i','o','u');
	protected $orderby;
	protected $query='';
	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	
	
	public function __construct() {	
		// IMPORTANT : use wp as a hook, otherwise the archive will not be set yet and errors will occur
		add_action( 'wp', array($this,'hydrate'));		
		// add_filter( 'genesis_attr_content', 'add_columns_class_to_content' );
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		add_action('wp_enqueue_scripts', array($this, 'enqueue_masonry_scripts'));		
	}


	/* Class attributes initialization
	--------------------------------------------- */		
	public function hydrate() {
		if ( !is_archive() ) return;
		// if (is_author()) {
		// 	$this->query=get_user_by( 'slug', $userid );
		// }
		$this->query = get_queried_object();
		$this->orderby = get_query_var('orderby','ASC');
	}		

	public function enqueue_masonry_scripts() {
	  	if ( is_archive() || is_search() ) {	
			wp_enqueue_script( 'jquery-masonry' );
			$js_uri = self::$PLUGIN_URI . '/assets/js/';
			$js_path = self::$PLUGIN_PATH . '/assets/js/';
			custom_enqueue_script( 'masonry-layout', $js_uri, $js_path, 'masonry-layout.js', array( 'jquery-masonry' ), CHILD_THEME_VERSION, true);
	  	};
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
			// PHP_Debug::trace('RATING !!!!');
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
	

	/* Helper functions
	---------------------------------------------------------------------------*/
	
	protected function initial_is_vowel($word) {
		$first = strtolower($word[0]);
		return in_array($first,self::$vowels);
	}
	
	protected function is_plural($word) {
		$last = strtolower($word[strlen($word)-1]);
		return ($last=='s');	
	}	
	
	protected function is_season($season) {
		//get current month
		$currentMonth=DATE("m");
		 
		if (empty($season)) return false;
		
		//retrieve season
		if ($currentMonth>="03" && $currentMonth<="05")
		  $currentSeason = "0";
		elseif ($currentMonth>="06" && $currentMonth<="08")
		  $currentSeason = "1";
		elseif ($currentMonth>="09" && $currentMonth<="11")
		  $currentSeason = "2";
		else
		  $currentSeason = "3";	
		  
		return ($season[0] == $currentSeason);
	}
	
	protected function is_fest($occasion) {
		
		if (empty($occasion)) return false;
		
		$currentMonth=DATE("m");
		$needle = '2-fetes';
		
		foreach ($occasion as $obj) {
			if ($obj->slug == $needle) {
				if ( $currentMonth==11 || $currentMonth==12 ) {
					return true;
				}
			}
		}
	}
	
	protected function is_veg($diet) {
		if (empty($diet)) return false;
		return ($diet[0] == "0");
	}

}
