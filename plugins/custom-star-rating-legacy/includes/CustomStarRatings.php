<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatings {
	
	public $ratingCats;

	const RATING_CATEGORIES = array( 
		array (
			'id'=>'rating',
			'weight' => 2,
			'title'=>'Overall rating',
			'question'=>'How did you like this dish ?',
		),
		array( 
			'id'=>'clarity',
			'weight' => 1,
			'title'=>'Clarity',
			'question'=>'How clear was the recipe ?',
		),
	);
	
	public function __construct() {
		//$this->ratingCats = self::RATING_CATEGORIES;
	
		$this->ratingCats = array( 
		array (
			'id'=>'rating',
			'weight' => 2,
			'title'=>'Overall rating',
			'question'=>'How did you like this dish ?',
		),
		array( 
			'id'=>'clarity',
			'weight' => 1,
			'title'=>'Clarity',
			'question'=>'How clear was the recipe ?',
		),
	);
	
	}
	
	public function get_cats( $id ) {
		if ( ! isset( $id ) ) return $this->ratingCats;
		if ( isset($this->ratingCats[$id]) ) return $this->ratingCats[$id];
		return false;
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
public function get_rating_stats( $user_ratings) {
	if ( ! empty($user_ratings) ) {
  	$votes = count( $user_ratings );
  	$total = 0;
  	$avg_rating = 0;

  	foreach( $user_ratings as $user_rating ) {
  		$total += $user_rating['rating'];
  	}
  
	  if( $votes !== 0 ) {
	    $avg_rating = $total / $votes; // TODO Just an average for now, implement some more functions later
	    $avg_rating = round( $avg_rating, 1 );
	  }  	
	}
	else {
		$votes='0';
		$avg_rating='0';
	}
  
  return array(
      'votes' => $votes,
      'rating' => $avg_rating,
  );
}



/* Get the user ip (from WP Beginner)
-------------------------------------------------------------*/
public function get_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} 
	elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} 
	else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	return apply_filters( 'wpb_get_ip', $ip );

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

