<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomStarRatingsMetaUpdate extends CustomStarRatings {
	
	public function __construct() {
		parent::__construct();
		add_action( 'comment_post',array($this,'update_comment_post_meta'), 10, 3 );
		//add_action( 'save_post', array($this, array('add_default_rating', 10, 2) ) );
		add_action( 'save_post', array($this, 'add_default_rating' ) );
		//add_action( 'genesis_before_content', array($this,'display_debug_info') );
	}


	/* Add field 'rate' to the comments meta on submission using PHP
	------------------------------------------------------------ */
	public function update_comment_post_meta($comment_id,$comment_approved,$comment) {
		$this->dbg('*** In Update Comment Post Meta ***', '' );

		$post_id = $comment['comment_post_ID'];									
		$current_post_type = get_post_type( $post_id );

		if (in_array( $current_post_type, $this->ratedPostTypes )) {
			
			$this->dbg('*** Post Type accepted for rating submission ***', '' );
			
			$new_rating = $this->update_comment_meta( $comment_id );
			$this->dbg('New rating', $new_rating );
			
			$user_ratings = $this->update_post_meta_user_ratings( $post_id, $new_rating);
			$this->dbg('Updated user ratings at end of update_post_meta function :',$user_ratings);
			
			$this->update_post_meta_user_rating( $post_id, $user_ratings );
		
		}

	}

	/* Add ratings default value on new post submission (required for proper sorting in archives)
	-------------------------------------------------------------*/ 
	//public function add_default_rating( $id, $post ) {
	public function add_default_rating() {
	 	if ( ! wp_is_post_revision() && is_singular( $this->ratedPostTypes ) ) {
	 		//PC:debug('Default rating add');
			foreach ($this->ratingCats as $id=>$cat) {
				$this->update_post_meta($post->ID, 'user_rating_' . $cat['id'], '0');
	 		}
	 	}
	}


	/* Update comment meta
	-------------------------------------------------------------*/ 	
	public function update_comment_meta( $comment_id ) {
		$new_rating = '';		
		foreach ($this->ratingCats as $id=>$cat) {
			if ( isset( $_POST[ 'rating-' . $id ] ) )  {
				$rating_form_value = $_POST[ 'rating-' . $id ];
				//$this->dbg('Extracted rating form value for category ' . $cat['id'] . ' : ', $rating_form_value);
				//otherwise let the cell empty, important for stats function
				add_comment_meta($comment_id, 'user_rating_' . $cat['id'], $rating_form_value );
				$new_rating[ $cat['id'] ] = $rating_form_value;	
			}
		}
		//$this->dbg('New Rating at end of update_comment_meta : ',$new_rating);
		return $new_rating;
	}
	
	
	/* Update "user_ratings" post meta
	-------------------------------------------------------------*/ 
	public function update_post_meta_user_ratings( $post_id, $new_rating ) {
		/* User Ratings table structure
		------------------------------------------------------------										
		$user_ratings = array( 
		'user' => average rating for category "name1"
		'ip' => average rating for category "name1"
		'name1' => rating for category "name1"
			...
		'nameN' => rating for category "nameN"
		)
		------------------------------------------------------------*/	
		
		$user_ratings = get_post_meta( $post_id, 'user_ratings' );
		$user_id = ( is_user_logged_in() )?get_current_user_id():0;
		$user_ip = $this->get_user_ip();

		/* Search and delete previous rating from same user */
		foreach ( $user_ratings as $id => $user_rating ) {
			if ( ( $user_id!=0 && $user_rating['user']==$user_id ) || ( $user_id==0 && $user_rating['ip']==$user_ip ) )  {
				delete_post_meta($post_id, 'user_ratings', $user_rating);
				unset( $user_ratings[$id] );
			}
		}
		
		/* Complete rating array with user IP & user ID */
		$new_rating['user'] = $user_id;
		$new_rating['ip'] = $user_ip;
		$this->dbg('New User Rating :',$new_rating);
		add_post_meta($post_id, 'user_ratings', $new_rating);
		
		$user_ratings[]=$new_rating;
		
		//$this->dbg('Updated user ratings at end of update_post_meta function :',$user_ratings);
		return $user_ratings;
	}
	
	/* Update "user_rating" post meta for each rating category
	-------------------------------------------------------------*/ 	
	public function update_post_meta_user_rating( $post_id, $user_ratings ) {
		
		$this->dbg('In update post meta user rating', '');									
		$this->dbg(' $user_ratings table ', $user_ratings);									
		$global_rating=0;
		$global_count=0;
		foreach ($this->ratingCats as $id=>$cat) {
			/* $stats = array( 
						'rating' => average rating 
						'votes' => number of votes
						)
			------------------------------------------------------------*/	
			$cat_id = $cat['id'];
			$this->dbg(' In category loop, step : ', $cat_id);			
			$this->dbg(' User Ratings column for this category : ', array_column($user_ratings,$cat_id) );			
			
			$stats = $this->get_rating_stats( array_column($user_ratings,$cat_id) );
			$this->dbg(' Stats for this category : ', $stats );			
			
			update_post_meta( $post_id, 'user_rating_' . $cat_id, $stats['rating'] );
			$global_rating += $stats['rating']*$cat['weight'];	
			$global_count += $cat['weight'];	
		}
		$this->dbg(' Global rating : ', $global_rating );			
		$this->dbg(' Global count : ', $global_count );			
		update_post_meta( $post_id, 'user_rating_global', round( $global_rating/$global_count, 1) );		
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

}