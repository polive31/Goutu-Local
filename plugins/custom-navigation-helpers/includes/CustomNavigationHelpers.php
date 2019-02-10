<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomNavigationHelpers {
	
    const QUERY_VARS = array(
        'post_type' , 
		'author' 	,		
        'ingredient', 
        'course'    ,
        'cuisine'   ,  
        'season'    , 
        'occasion'  , 
        'diet'      , 
        'difficult' ,
        'category'  ,  
        'post_tag'  , 
	);
	
    const TAXONOMY = array( 
        'ingredient'    => array('orderby'=> 'name'), 
        'course'        => array('orderby'=> 'description'), 
        'cuisine'       => array( 'orderby'=> 'name'), 
        'season'        => array( 'orderby'=> 'description'), 
        'occasion'      => array( 'orderby'=> 'description'), 
        'diet'          => array( 'orderby'=> 'description'), 
        'difficult'     => array('orderby'=> 'description'), 
        'category'      => array( 'orderby'=> 'name'), 
		'post_tag'      => array( 'orderby'=> 'name')
	);	

	protected $orderby;
	protected $query='';
	protected $queryvars=array();
	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	
	
	public function __construct() {	
		// IMPORTANT : use wp as a hook, otherwise the archive will not be set yet and errors will occur
		add_action( 'wp', array($this,'hydrate'));		
		// add_filter( 'genesis_attr_content', 'add_columns_class_to_content' );
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		add_action('wp_enqueue_scripts', array($this, 'enqueue_masonry_scripts'));
		// Filters	
		// add_filter( 'query_vars', array($this,'archive_filter_queryvars') );					
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
		foreach (self::QUERY_VARS as $var) {
		$this->queryvars[$var] = get_query_var( $var, false);
		}
	}		

	public function enqueue_masonry_scripts() {
	  	if ( is_archive() || is_search() ) {	
			wp_enqueue_script( 'jquery-masonry' );
			custom_enqueue_script( 'masonry-layout', '/assets/js/masonry-layout.js', self::$PLUGIN_URI, self::$PLUGIN_PATH, array( 'jquery', 'jquery-masonry' ), CHILD_THEME_VERSION, true);
	  	};
	}

	public function archive_filter_sort($query) {
	  // Select any archive. For custom post type use: is_post_type_archive( $post_type )
	  //if (is_archive() || is_search() ): => ne pas utiliser car r�sultats de recherche non relevants
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

	// Taxonomy information
    // public static function is_multiselect($tax) {
    //     if (!isset(self::TAXONOMY[$tax])) return;
    //     return self::TAXONOMY[$tax]['multiselect'];
    // }

    // public static function is_hierarchical($tax) {
    //     if (!isset(self::TAXONOMY[$tax])) return;
    //     return self::TAXONOMY[$tax]['hierarchical'];
    // }

    // public static function is_required($tax) {
    //     if (!isset(self::TAXONOMY[$tax])) return;
    //     return self::TAXONOMY[$tax]['required'];
    // }

    public static function orderby($tax) {
        if (!isset(self::TAXONOMY[$tax])) return;
        return self::TAXONOMY[$tax]['orderby'];
	}  
		


	/* Custom query variable for taxonomy filter
	--------------------------------------------- */		
	public function archive_filter_queryvars($vars) {
	  $vars[] = 'filter';
	  $vars[] .= 'filter_term';
	  return $vars;
	}	
	
	protected function is_plural($word) {
		$last = strtolower($word[strlen($word)-1]);
		return ($last=='s');	
	}	

}
