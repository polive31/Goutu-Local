<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CSR_Meta {
	
	/* ===========================================================================
	/* ===================         META UPDATE           =========================
	/* ===========================================================================


	/* Post rating meta structure :								
		'user_ratings' = array( 
							'user' => user ID (0 if unregistered user)
							'ip' => user IP address
							'cat1' => rating for category "cat1"
							...
							'catN' => rating for category "catN"
						)

		'user_rating_cat1' = val1
		...
		'user_rating_catN' = valN
		'user_rating_global' = val
	*/
	
	
	/* Comment rating meta structure :
		'user_rating_cat1' = val1
		...
		'user_rating_catN' = valN
	*/
	


	/* New post submission callback
	/* Add ratings default value (required for proper sorting in archives)
	-------------------------------------------------------------*/ 
	public function add_default_rating() {
		 if ( is_singular( CSR_Assets::post_types() ) && (! wp_is_post_revision( $post->ID )) ) {
			foreach (self::$ratingCats as $slug=>$values) {
				$this->update_post_meta($post->ID, 'user_rating_' . $slug, '0');
			}
			$this->update_post_meta($post->ID, 'user_rating_global', '0');
		}	
	}



	/* Update comment callback
	-------------------------------------------------------------*/ 	
	public function update_comment_post_meta($comment_id,$comment_approved,$comment) {
		$post_id = $comment['comment_post_ID'];									
		$current_post_type = get_post_type( $post_id );

		/* CSR_Assets::post_types() cannot be called statically because we are in a submit callback
		and the CSR_Assets::hydrate function will not be called in this case */ 
		$Rating_Assets = new CSR_Assets();
		if (in_array( $current_post_type, $Rating_Assets->post_types() )) {
			/* Update comment meta and get corresponding rating */
			$new_rating = $this->update_comment_meta_user_rating( $comment_id );
			/* Update post meta with the new rating and get updated ratings table */
			// $user_ratings = $this->update_post_meta_user_ratings( $post_id, $new_rating);
			/* Update post meta with the new rating values per category */
			$this->update_post_meta_user_rating( $post_id );
		}
	}


	/* Update comment meta
	-------------------------------------------------------------*/ 	
	public function update_comment_meta_user_rating( $comment_id ) {
		$user_rating = array();		
		foreach (CSR_Assets::rating_cats() as $cat=>$values) {
			if ( isset( $_POST[ 'rating-' . $cat ] ) )  {
				$rating_form_value = $_POST[ 'rating-' . $cat ];
				//otherwise let the cell empty, important for stats function
				add_comment_meta($comment_id, 'user_rating_' . $cat, $rating_form_value );
				$user_rating[$cat] = $rating_form_value;	
			}
		}
		return $user_rating;
	}
	
	
	/* Update "user_rating" post meta for each rating category
	-------------------------------------------------------------*/ 	
	public function update_post_meta_user_rating( $post_id ) {								
		$ratings=array();
		$count=array();
		$cats=CSR_Assets::rating_cats();
		$votes=0;
		
		$args = array(
					'post_id' 	=> $post_id,
					'status' 	=> 'all',
					'fields'	=> 'ids',
					'status'	=> 'approve',
				);
		$comment_ids = get_comments( $args );
		/* Loop through all rating categories  */
		foreach ( $cats as $cat=>$values) {
			/* Loop through all post comments */
			foreach ( $comment_ids as $comment_id) {	
				$value = get_comment_meta( $comment_id, 'user_rating_'.$cat, true);
				if ($value) {
					$ratings[$cat] += $value;	
					$ratings['global'] += $values['weight']*$value;	
					$count[$cat]++;
					$count['global'] += $values['weight'];
				}	
			}
			update_post_meta( $post_id, 'user_rating_'.$cat, round( $ratings[$cat]/$count[$cat], 1) );		
			update_post_meta( $post_id, 'user_votes_'.$cat, $count[$cat] );		
			$votes = ($count[$cat]>$votes)?$count[$cat]:$votes;	
		}
		update_post_meta( $post_id, 'user_rating_global', round( $ratings['global']/$count['global'], 1) );		
		update_post_meta( $post_id, 'user_votes_global', $votes );		
	}
			
			
	/* ===========================================================================
	/* ===================         GETTERS              =======================
	/* ===========================================================================
	
	/* Get Comment Rating
	------------------------------------------------------------*/
	static function get_comment_rating($comment_id, $cat_id) {
		$rating = get_comment_meta($comment_id, 'user_rating_' . $cat_id, true);
		return $rating;
	}
	
	/* Get Post Rating
	------------------------------------------------------------*/
	static function get_post_rating($post_id, $cat_id='global') {
		$rating = get_post_meta( $post_id , 'user_rating_' . $cat_id, true );
		return $rating;
	}

	/* Get Votes Count
	------------------------------------------------------------*/
	static function get_votes_count($post_id, $cat_id='global') {
		$rating = get_post_meta( $post_id , 'user_votes_' . $cat_id, true );
		return $rating;
	}
	



	/* Get the user ip (from WP Beginner)
	-------------------------------------------------------------*/
	static function get_user_ip() {
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



	/* ===========================================================================
	/* ===================         DEPRECATED              =======================
	/* ===========================================================================
	
	/* Update post meta
	-------------------------------------------------------------*/ 
	// public function update_post_meta_user_ratings( $post_id, $new_rating ) {
		
	// 	$user_ratings = get_post_meta( $post_id, 'user_ratings' );
	// 	$user_id = ( is_user_logged_in() )?get_current_user_id():0;
	// 	$user_ip = $this->get_user_ip();

	// 	/* Search this user's previous rating and delete it */
	// 	foreach ( $user_ratings as $id => $user_rating ) {
	// 		if ( ( $user_id!=0 && $user_rating['user']==$user_id ) || ( $user_id==0 && $user_rating['ip']==$user_ip ) )  {
	// 			delete_post_meta($post_id, 'user_ratings', $user_rating);
	// 			unset( $user_ratings[$id] );
	// 		}
	// 	}
		
	// 	/* Complete rating array with user IP & user ID */
	// 	$new_rating['user'] = $user_id;
	// 	$new_rating['ip'] = $user_ip;
	// 	add_post_meta($post_id, 'user_ratings', $new_rating);
		
	// 	$user_ratings[]=$new_rating;
		
	// 	/* return the updated value of the 'user_rating' meta */
	// 	return $user_ratings;
	// }

	
	/* Calculate rating stats
	-------------------------------------------------------------*/
	// static function get_rating_stats( $cat_ratings ) {
	// 	/* cat_ratings is the list of user ratings for one category */
	// 	$votes='';
	// 	$avg_rating='';	
	// 	if ( ! empty($cat_ratings) ) {
	// 		$total = array_sum( $cat_ratings );
	// 		$votes = count( array_filter( $cat_ratings ) );

	// 		if( $votes !== 0 ) {
	// 		$avg_rating = $total / $votes; // TODO Just an average for now, implement some more functions later
	// 		$avg_rating = round( $avg_rating, 1 );
	// 		} 	
	// 	}
	// 	return array(
	// 		'votes' => $votes,
	// 		'rating' => $avg_rating,
	// 	);
	// }

}