<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Comment with rating input shortcode
-----------------------------------------------*/
add_shortcode( 'comment-rating-form', 'display_comment_form_with_rating' );
function display_comment_form_with_rating() {
	$args = array (
		//'title_reply' => __( '', '' ), //Default: __( 'Leave a Reply� )
		'label_submit' => __( 'Send', 'custom-star-rating' ), //default=�Post Comment�
		'comment_field' => output_comment_form_html_php($post_id), 
		'logged_in_as' => '', //Default: __( 'Leave a Reply to %s� )
		'title_reply_to' => __( 'Reply Title', 'custom-star-rating' ), //Default: __( 'Leave a Reply to %s� )
		'cancel_reply_link' => __( 'Cancel', 'custom-star-rating' ) //Default: __( �Cancel reply� )
		);
	
  ob_start();
  
  //display_rating_form();
  comment_form($args);
  
  $cr_form = ob_get_contents();
  ob_end_clean();
  
  return $cr_form;
}


/* Output post rating shortcode 
---------------------------------------------*/

add_shortcode( 'display-star-rating', 'display_star_rating_shortcode' );
function display_star_rating_shortcode() {
	// Rating
	//$rating = get_post_meta( get_the_id(), 'recipe_user_ratings_rating' );
	//print_r($rating);
	$rating = get_rating_stats( get_post_meta( get_the_id(), 'recipe_user_ratings' ) );
	//print_r($rating);
	ob_start();
	?>
	
	<div class="rating" id="stars-<?php echo $rating['stars'];?>"></div>
	<?php 
	if ( $rating['votes']!=0 ) {
		$rating_plural=$rating['votes']==1?__('review','foodiepro'):__('reviews','foodiepro'); 
		echo '<div class="rating-details">(' . $rating['votes'] . ' ' . $rating_plural . ')</div>'; //. ' | ' . __('Rate this recipe','foodiepro') . 
	}
	//else {
		//echo '<div class="rating-details">' . __('Be the first to rate this recipe !','foodiepro') . '</div>';
	//}

  $html = ob_get_contents();
  ob_end_clean();

	return $html;
}

?>