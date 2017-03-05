<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatings {
	
	const RATED_POST_TYPES = array( 'recipe' );
	
	public $ratedPostTypes;
	const RATING_CATEGORIES  = array( 
		array (
			'id'=>'rating',
			'weight' => 2,
			'title'=> 'Dish',
			'question'=> 'How did you like this dish ?',
		),
		array( 
			'id'=>'clarity',
			'weight' => 1,
			'title'=> 'Clarity',
			'question'=> 'How clear was the recipe ?',
		),
	);

	public function __construct() {
		
//		foreach (self::RATING_CATEGORIES as $id=>$cat) {
//			$this->ratingCats[$id] = array(
//				'id' => $cat['id'],
//				'weight' => $cat['weight'],
//				'title' => __( $cat['title'], 'custom-star-rating' ),
//				'question' => __( $cat['question'], 'custom-star-rating' ),
//			);
//		}
		
		$this->ratingCats = self::RATING_CATEGORIES;
		$this->ratedPostTypes = self::RATED_POST_TYPES;

		//add_action( 'genesis_before_content', array($this,'display_debug_info') );
	}
	
	/* Output debug information 
	--------------------------------------------------------------*/	
	protected function dbg( $msg, $var ) {
			if ( class_exists('PC') ) {
				PC::debug(array( $msg => $var ) );
			}
	}


	/* Dump main variables
	--------------------------------------------------------------*/	
	public function display_debug_info() {
		$this->dbg('In Custom Rating Main Class !', '' );
		$this->dbg('Rated types: ', $this->ratedPostTypes );
		$this->dbg('ratingCats : ', $this->ratingCats );
	}	
	
	
	/* Rating caption
	------------------------------------------------------------*/
	public function rating_caption($val) {
		//$val = intval($rating_val);
		switch ($val) {
			case "0":
				return __('Not rated','custom-star-rating');
			case $val <= "1":
				return __('Really not good','custom-star-rating');
			case $val <= "2":
				return __('Not so good','custom-star-rating');
			case $val <= "3":
				return __('Rather good','custom-star-rating');
			case $val <= "4":
				return __('Very good','custom-star-rating');
			case $val <= "5":
				return __('Delicious','custom-star-rating');
		}
	}


	/* Calculate rating stats
	-------------------------------------------------------------*/
	public function get_rating_stats( $cat_ratings ) {
		/* cat_ratings is the list of user ratings for one category */
		$this->dbg('In Get Rating Stats function','');
		$this->dbg('User Ratings array : ',$cat_ratings);
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



	/* Output stars
	-------------------------------------------------------------*/
	public function output_stars($stars, $half) {
		$html = '';
		for ($i = 1; $i <= $stars; $i++) {
			$html .= '<i class="fa fa-star"></i>';
		}
		for ($i = $stars+1; $i <= 5; $i++) {
			if ( ($i == ($stars+1) ) && $half ) {
				$html .= '<i class="fa fa-star-half-o"></i>';
			}
			else {
				$html .= '<i class="fa fa-star-o"></i>';
			}
		}
		return $html;
	}



	
}
