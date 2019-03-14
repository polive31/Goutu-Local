<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSR_Assets {
	
	private static $ratedPostTypes;
	private static $ratingCats;
	private static $ratingGlobal;

	private static $Plugin_path;
	private static $Plugin_uri;	
	
	public function __construct() {
		/* Allows to access the class's functions from a submission or ajax callback
		In this case, the main class is not created, so the only way is to create a new CSR_Assets instance
		in order to force the hydrate */
		self::hydrate();
	}
	
	/* Register stylesheet, will be enqueued in the shortcode itself  */
	public static function register_star_rating_assets() {
		custom_register_style( 	'custom-star-rating', 'assets/css/custom-star-rating.css', self::$Plugin_uri, self::$Plugin_path, array(), CHILD_THEME_VERSION );
		$args=array(
			'handle'	=> 'custom-star-rating',
			'file' 		=> 'assets/js/custom-star-rating.js',
			'uri' 		=> self::$Plugin_uri,
			'path' 		=> self::$Plugin_path,
			'footer' 	=> true,
			'deps' 		=> array(),
			'data' 		=> array(
				'name'			=> 'csr',
				'emptyComment' 	=> __('Please enter a text before submitting your comment.', 'foodiepro'),
				'emptyAuthor' 	=> __('Please provide your name before submitting your comment.', 'foodiepro'),
				'invalidEmail' 	=> __('Please provide a valid email adress before submitting your comment.', 'foodiepro'),
			)
		);
		custom_register_script( $args );
	}
	
	
	// Initialize all strings needing a translation (doesn't work in __construct)
	public static function hydrate() {
		self::$Plugin_path = plugin_dir_path( dirname( __FILE__ ) );
		self::$Plugin_uri = plugin_dir_url( dirname( __FILE__ ) );
		self::$ratedPostTypes = array( 'recipe' );

		self::$ratingCats = array( 
			'rating' => array (
				'weight' => 2,
				'title'=> __('Dish','custom-star-rating','custom-star-rating'),
				'legend'=> __('Appreciation of the dish','custom-star-rating'),
				'question'=> __('How did you like this dish ?','custom-star-rating'),
				'caption' => array(
					__('Disappointing','custom-star-rating'),
					__('Average','custom-star-rating'),
					__('Good','custom-star-rating'),
					__('Very good','custom-star-rating'),
					__('Delicious','custom-star-rating'),
					)	
				),
				'global'=>array (
					'title'=> __('Overall','custom-star-rating'),
					'legend'=> __('Global appreciation of the dish','custom-star-rating'),
					'caption' => array(
					__('Disappointing','custom-star-rating'),
					__('Average','custom-star-rating'),
					__('Good','custom-star-rating'),
					__('Very good','custom-star-rating'),
					__('Delicious','custom-star-rating'),
				)	
			),
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
		);
	}




	/* Translate ratings taxonomy from WPURP
	--------------------------------------------------------------*/	
    static function translate_ratings_taxonomy( $args ) {
        $name = __( 'Evaluations', 'foodiepro' );
        $singular = __( 'Evaluation', 'foodiepro' );

        $name_lower = strtolower($name);

        $args['labels']['name'] = $name;
        $args['labels']['singular_name'] = $singular;
        $args['rewrite'] = array('slug'=>__('rating','foodiepro'),'with_front' => false);

        return $args;
    }	
	

	/* Get Rated Post Types
	------------------------------------------------------------*/	
	public static function post_types() {
		return self::$ratedPostTypes;
	}

	/* Get Rating Categories
		- $cat_ids = 'all', 'global', <catN>, array(<cat2>, <cat5>, ...)
		- $global = false, true => only valid with 'all'
	------------------------------------------------------------*/	
	public static function rating_cats( $cat_ids='all', $global=false ) {
		$cats=array();
		if ($cat_ids=='all') {
			$cats = self::$ratingCats;
			if ( !$global ) unset( $cats['global'] );
		}
		elseif ( is_array($cat_ids) ) {
			foreach ( $cat_ids as $id ) {
				$cats[$id]=self::$ratingCats[$id];
			}
		}
		else
			$cats[$cat_ids]=self::$ratingCats[$cat_ids];

		return $cats;
	}
	
	
	/* Get Rating caption
	------------------------------------------------------------*/
	public static function get_rating_caption($val, $cat) {
		//$val = intval($rating_val);
		if ($val==0) return __('Not rated','custom-star-rating');
		$val=floor($val-1);
		if ( isset( self::$ratingCats[$cat]['caption'] ) ) {
			$caption = self::$ratingCats[$cat]['caption'][$val]; 
			return $caption;
		}

	}
	
}

