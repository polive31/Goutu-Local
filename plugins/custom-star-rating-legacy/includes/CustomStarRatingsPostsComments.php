<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomStarRatingsPostsComments extends CustomStarRatings {
	
	public function __construct() {
		parent::__construct();
		add_action( 'genesis_before_content', array($this,'display_debug_info') );
		add_action( 'comment_post',array($this,'update_comment_post_meta_php',10,3) );
		add_action( 'save_post', array($this,'wpurp_add_default_rating', 10, 2 ) );
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

	public function update_comment_post_meta_php($comment_id,$comment_approved,$comment) {
		
		PC::debug('In comment post !');
		$post_id = $comment['comment_post_ID'];

		foreach ($this->ratingCats as $id->$cat) {
			PC::debug(array('Cat in foreach loop :'=>$cat));
			
			if ( ! isset( $_POST[ 'rating-' . $id ] ) ) 
				$rating = $_POST[ 'rating-' . $id ];
			PC::debug(array('Rating :'=>$rating));

			add_comment_meta($comment_id, 'user_rating_' . $cat['name'], $rating);

		}

		/* POST META UPDATE
		------------------------------------------------------*/
		$user_ratings = get_post_meta( $post_id, 'user_ratings' );
		PC::debug(array('User Ratings Table :'=>$user_ratings));

		if ( is_user_logged_in() )
			$user_id = get_current_user_id();
		else {
			$user_id = 0;
		}
		$user_ip = $this->get_user_ip();
		PC::debug(array('User IP :'=>$user_ip));
		
		/* Search and delete previous rating from same user */
		foreach ( $user_ratings as $rating_id => $user_rating ) {
				
			PC::debug(array('Rating #'=>$rating_id));
			PC::debug(array('Content'=>$user_rating));
			
			if ( ( $user_id!=0 && $user_rating['user']==$user_id ) || ( $user_id==0 && $user_rating['ip']==$user_ip ) )  {
				PC::debug(array('Previous rating from same user !!!'=>$rating_id));
				delete_post_meta($post_id, 'user_ratings', $user_rating);
			} 
			
		}

		/* Update ratings table in post meta*/
		$user_rating = array(
			'user' 	=>$user_id,
			'ip'		=>$user_ip,
			'rating'=>$rating,
		);
		//PC::debug(array('New User Rating :'=>$new_user_rating ) );
		add_post_meta($post_id, 'user_ratings', $user_rating);
		
		/* Update post meta for average rating */
		$user_ratings[]=$user_rating;
		$stats = $this->get_rating_stats( $user_ratings );
		//PC:debug(array('Stats :'=>$stats) );
		update_post_meta($post_id, 'user_rating', $stats['rating']);
		
	}

		/* Add ratings default value on post save 
		-------------------------------------------------------------*/ 

	public function wpurp_add_default_rating( $id, $post ) {
	 	if ( ! wp_is_post_revision($post->ID) ) {
	 		//PC:debug('Default rating add');
			update_post_meta($post->ID, 'user_rating', '0');
	 	}
	}


}