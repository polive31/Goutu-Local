<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/* Rating caption
------------------------------------------------------------*/
function rating_caption($val) {
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


/* Add new user rating */
function add_user_rating( $user_ratings, $new_rating_val) {
	$user_ip = get_user_ip();
	$nb_users = count( $user_ratings ) + 1;
	$user_ratings[] = array(
		'user' => $nb_users,
		'ip'=>$user_ip,
		'rating'=> $new_rating_val,
	);
	return '';
}


/* Calculate rating stats
-------------------------------------------------------------*/
function get_rating_stats( $user_ratings) {
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


/* Get the user ip (from WPURP)
-------------------------------------------------------------*/
// Source: http://stackoverflow.com/questions/6717926/function-to-get-user-ip-address
function wpurp_get_user_ip() {
  foreach( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key ) {
    if( array_key_exists( $key, $_SERVER ) === true ) {
      foreach( array_map( 'trim', explode( ',', $_SERVER[$key] ) ) as $ip ) {
        if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
            return $ip;
        }
      }
    }
  }
  return 'unknown';
}


/* Get the user ip (from WP Beginner)
-------------------------------------------------------------*/
function get_user_ip() {
if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
//check ip from share internet
$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
//to check ip is pass from proxy
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
$ip = $_SERVER['REMOTE_ADDR'];
}
return apply_filters( 'wpb_get_ip', $ip );
}


/* Output stars
-------------------------------------------------------------*/

function output_stars($stars, $half) {
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



?>