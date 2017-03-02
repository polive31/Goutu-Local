<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomStarRatingsPostsComments extends CustomStarRatings {
	
	public function __construct() {
		parent::__construct();
		add_action( 'genesis_before_content', array($this,'display_debug_info') );
		add_action( 'comment_post',array($this,'update_comment_post_meta',10,3) );
		add_action( 'save_post', array($this,'csr_add_default_rating', 10, 2 ) );
	}
	
		/* Output debug information 
		--------------------------------------------------------------*/	
	public function display_debug_info() {
		//if ( is_single() ) {	
			//echo '<pre>' . print_r( $this->get_cats(), false ) . '</pre>';	
			PC::debug(array('In Custom Rating Post Comments !' ) );
			PC::debug(array('get_cats() : '=> $this->get_cats('') ) );
			PC::debug(array('ratingCats : '=> $this->ratingCats ) );
		//}
	}


	/* Add field 'rate' to the comments meta on submission using PHP
	------------------------------------------------------------ */
	public function update_comment_post_meta($comment_id,$comment_approved,$comment) {
		PC::debug('In comment post !');
		
		$new_rating = $this->update_comment_meta( $comment_id );
		$post_id = $comment['comment_post_ID'];									
		$user_ratings = $this->update_post_meta_user_ratings( $post_id, $new_rating);
		$this->update_post_meta_user_rating( $post_id, $user_ratings );

	}

		/* Add ratings default value on post save 
		-------------------------------------------------------------*/ 

	public function csr_add_default_rating( $id, $post ) {
	 	if ( ! wp_is_post_revision($post->ID) ) {
	 		//PC:debug('Default rating add');
			$this->update_post_meta($post->ID, 'user_rating', '0');
	 	}
	}
	
	public function update_comment_meta( $comment_id ) {
		
		$new_rating = '';
		
		foreach ($this->ratingCats as $id->$cat) {
			if ( isset( $_POST[ 'rating-' . $id ] ) )  {
				$rating_form_value = $_POST[ 'rating-' . $id ];
				//otherwise let the cell empty, important for stats function
				add_comment_meta($comment_id, 'user_rating_' . $cat['id'], $rating_form_value );
				$new_rating[ $cat['id'] ] = $rating_form_value;	
			}
		}
		PC::debug(array('Rating :'=>$rating));
	
		return $new_rating;
	
	}
	
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
		PC::debug(array('User Ratings Table :'=>$user_ratings));

		$user_id = ( is_user_logged_in() )?get_current_user_id():0;
		$user_ip = $this->get_user_ip();

		/* Search and delete previous rating from same user */
		foreach ( $user_ratings as $id => $user_rating ) {
			if ( ( $user_id!=0 && $user_rating['user']==$user_id ) || ( $user_id==0 && $user_rating['ip']==$user_ip ) )  {
				//PC::debug(array('Previous rating from same user '=>$id));
				delete_post_meta($post_id, 'user_ratings', $user_rating);
				unset( $user_ratings[$id] );
			}
		}
		
		/* Complete rating array with user IP & user ID */
		$new_rating['user'] = $user_id;
		$new_rating['ip'] = $user_ip;
		PC::debug(array('New User Rating :'=>$new_rating ) );
		add_post_meta($post_id, 'user_ratings', $new_rating);
		
		$user_ratings[]=$new_rating;
		
		return $user_ratings;
	}
	
	
	public function update_post_meta_user_rating( $post_id, $user_ratings ) {
		
		$global_rating=0;
		$global_count=0;
		foreach ($this->ratingCats as $id->$cat) {
			/* $stats = array( 
						'rating' => average rating 
						'votes' => number of votes
						)
			------------------------------------------------------------*/										
			$stats = $this->get_rating_stats( $user_ratings[ $cat['id'] ] );
			update_post_meta( $post_id, 'user_rating_' . $cat['id'], $stats['rating'] );
			$global_rating += $stats['rating']*$cat['weight'];	
			$global_count += $cat['weight'];	
		}
		update_post_meta( $post_id, 'user_rating_global', $global_rating/$global_count );		
	}

}