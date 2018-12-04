<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatings {
	
	protected static $ratedPostTypes;
	protected static $ratingCats;
	protected static $ratingGlobal;

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;		

	public function __construct() {
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		add_action('init', array( $this , 'hydrate' ));
		add_filter( 'wpurp_register_ratings_taxonomy', array( $this, 'translate_ratings_taxonomy' ) );

		//self::$s_ratingCats = self::$ratingCats;
		//self::$s_ratingGlobal = self::$ratingGlobal;
		//add_action( 'genesis_before_content', array($this,'display_debug_info') );
		add_action( 'wp_enqueue_scripts', array($this, 'load_custom_rating_stylesheet' ) );
	}

	/* Chargement des feuilles de style custom et polices */
	public function load_custom_rating_stylesheet() {
		$uri = self::$PLUGIN_URI . 'assets/';
  		$path = self::$PLUGIN_PATH . 'assets/'; 
		custom_enqueue_style( 'custom-star-ratings', $uri, $path, 'custom-star-rating.css', array(), CHILD_THEME_VERSION );
	}


	// Initialize all strings needing a translation (doesn't work in __construct)
	public function hydrate() {
		self::$ratedPostTypes = array( 'recipe' );
		self::$ratingCats = array( 
			'rating' => array (
				'weight' => 2,
				'title'=> __('Dish','custom-star-rating','custom-star-rating'),
				'legend'=> __('Appreciation of the dish','custom-star-rating'),
				'question'=> __('How did you like this dish ?','custom-star-rating'),
				'caption' => array(
								__('Disappointing','custom-star-rating'),
								__('Rather good','custom-star-rating'),
								__('Good','custom-star-rating'),
								__('Very good','custom-star-rating'),
								__('Delicious','custom-star-rating'),
				)	
			),
		);
		/*
		'clarity' => array(
			'weight' => 1,
			'title'=> __('Recipe','custom-star-rating'),
			'legend'=> __('Clarity of the recipe'),
			'question'=> __('How clear was the recipe ?','custom-star-rating'),
			'caption' => array(
							__('Confusing','custom-star-rating'),
							__('Not so clear','custom-star-rating'),
							__('Rather clear','custom-star-rating'),
							__('Very clear','custom-star-rating'),
							__('Crystal clear even for kitchen dummies','custom-star-rating'),
			)	
		),*/
		self::$ratingGlobal = array( 
			'global'=>array (
				'title'=> __('Overall','custom-star-rating'),
				'caption' => array(
								__('Disappointing','custom-star-rating'),
								__('Average','custom-star-rating'),
								__('Good','custom-star-rating'),
								__('Very good','custom-star-rating'),
								__('Fabulous','custom-star-rating'),
				)	
			)
		);
	}

	
	/* Output debug information 
	--------------------------------------------------------------*/	
	protected function dbg( $msg, $var ) {
			if ( class_exists('PC') ) {
				PC::debug(array( $msg => $var ) );
			}
	}


	public static function rated_post_types() {
		return self::$ratedPostTypes;
	}


	/* Dump main variables
	--------------------------------------------------------------*/	
	protected function display_debug_info() {
		//$this->dbg('In Custom Rating Main Class !', '' );
		//$this->dbg('Rated types: ', self::$ratedPostTypes );
		//$this->dbg('ratingCats : ', self::$ratingCats );
	}	


	/* Translate ratings taxonomy from WPURP
	--------------------------------------------------------------*/	
    public function translate_ratings_taxonomy( $args ) {
        $name = __( 'Evaluations', 'foodiepro' );
        $singular = __( 'Evaluation', 'foodiepro' );

        $name_lower = strtolower($name);

        $args['labels']['name'] = $name;
        $args['labels']['singular_name'] = $singular;
        $args['rewrite'] = array('slug'=>__('rating','foodiepro'),'with_front' => false);

        return $args;
    }	
	
	
	public static function getRatingCats( $global ) {
		$global = isset( $global ) ? $global : false;
		$cats = self::$ratingCats;
		if ( $global ) $cats['global']=self::$ratingGlobal;
		return $cats;
	}
	
	
	/* Rating caption
	------------------------------------------------------------*/
	protected function rating_caption($val,$cat) {
		//$val = intval($rating_val);
		if ($val==0) return __('Not rated','custom-star-rating');
		$val=ceil($val)-1;
		if ( $cat=='global' ) {
			return __(self::$ratingGlobal['global']['caption'][$val], 'custom-star-rating');
		}
		elseif ( isset( self::$ratingCats[$cat]['caption'] )) {
			return __(self::$ratingCats[$cat]['caption'][$val], 'custom-star-rating');
		}
		return '';
	}


	/* Calculate rating stats
	-------------------------------------------------------------*/
	protected function get_rating_stats( $cat_ratings ) {
		/* cat_ratings is the list of user ratings for one category */
		//$this->dbg('In Get Rating Stats function','');
		//$this->dbg('User Ratings array : ',$cat_ratings);
		$votes='';
		$avg_rating='';	
		if ( ! empty($cat_ratings) ) {
			$total = array_sum( $cat_ratings );
			$votes = count( array_filter( $cat_ratings ) );

		  if( $votes !== 0 ) {
		    $avg_rating = $total / $votes; // TODO Just an average for now, implement some more functions later
		    $avg_rating = round( $avg_rating, 1 );
		  } 	
	  }
	  return array(
		  'votes' => $votes,
		  'rating' => $avg_rating,
	  );
	}




	
}

